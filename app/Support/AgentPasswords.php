<?php

namespace App\Support;

use App\Mail\AgentPasswordResetMail;
use App\Models\Core\AgentPasswordReset;
use App\Models\Core\Propagent;
use App\Models\Core\Propflyer;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

/**
 * The forced password reset for agents arriving from the old system.
 *
 * Who has reset: propagents.passwordResetAt (added by hand with raw SQL).
 * NULL = has not reset yet, so at sign-in they are NOT let in - even with the
 * right old password - and are emailed a one-time link instead. Following the
 * link (which proves they control the login email) is how they set a new
 * password and get a date in that column. Until the column exists nobody is
 * forced, the same way loginBlocked is handled.
 */
class AgentPasswords
{
    /** How long an emailed link works. */
    public const LINK_MINUTES = 60;

    /** Per agent: at most this many links an hour, and never twice within a minute. */
    private const MAX_PER_HOUR    = 5;
    private const MIN_GAP_SECONDS = 60;

    private const DB_FORMAT = 'Y-m-d H:i:s';

    /** True once the passwordResetAt column has been added to propagents. */
    public static function columnAvailable($agent): bool
    {
        return array_key_exists('passwordResetAt', $agent->getAttributes());
    }

    /**
     * Every account that uses this login email. Some agents have several (the same
     * email was registered more than once over the years). The email - i.e. the
     * mailbox - is what proves who they are, so one password is set for all of an
     * email's accounts together. (Admins are meant to merge such accounts into one -
     * the "Duplicate Logins" tab on the Agents page lists them.)
     * (MySQL compares the email case-insensitively, so "Bob@x.com" = "bob@x.com".)
     */
    public static function accountsForEmail(?string $email): Collection
    {
        $email = trim((string) $email);

        return $email === '' ? collect() : Propagent::where('xxAgtUname', $email)->orderBy('id')->get();
    }

    /**
     * Which account a sign-in opens when the SAME email has several (until an admin has
     * merged them - see the "Duplicate Logins" tab on the Agents page): the one with the
     * most flyers, i.e. the one the agent actually uses, and the oldest account on a tie.
     */
    public static function primaryAccount(Collection $accounts)
    {
        if ($accounts->count() <= 1) {
            return $accounts->first();
        }

        $flyers = Propflyer::whereIn('propagent_id', $accounts->pluck('id')->all())
            ->selectRaw('propagent_id, COUNT(*) as total')
            ->groupBy('propagent_id')
            ->pluck('total', 'propagent_id');

        return $accounts
            ->sortBy(fn ($a) => [-1 * (int) ($flyers[$a->id] ?? 0), (int) $a->id])
            ->first();
    }

    /** Is this account blocked from signing in? */
    public static function isBlocked($agent): bool
    {
        return (int) ($agent->loginBlocked ?? 0) === 1;
    }

    /**
     * Does this typed password open this account? OLD-SYSTEM PASSWORDS ARE NEVER
     * ACCEPTED: an account that hasn't set a password on this site (passwordResetAt
     * empty) can't sign in whatever is stored for it, so a leaked old password is
     * worth nothing here. Only the emailed link (proof of the mailbox) gets an
     * agent from "old site" to "has a password".
     */
    public static function passwordOpens($agent, string $typed): bool
    {
        if (static::resetRequired($agent) || blank($agent->password)) {
            return false;
        }

        try {
            return Hash::check($typed, $agent->password);
        } catch (\Throwable $e) {
            // a stored value that isn't a valid hash simply doesn't match
            return false;
        }
    }

    /** Must this agent set a new password before they can sign in? */
    public static function resetRequired($agent): bool
    {
        return static::columnAvailable($agent) && blank($agent->passwordResetAt);
    }

    /**
     * The minimum standard for a new password: 12+ characters with upper and
     * lower case, a number and a symbol, and not one that has appeared in a
     * known data breach (checked with Have I Been Pwned by sending only the
     * first 5 characters of the password's hash - the password never leaves).
     */
    public static function rule(): Password
    {
        return Password::min(12)->mixedCase()->numbers()->symbols()->uncompromised(3);
    }

    /** Human-readable version of rule(), shown next to the field. */
    public static function requirements(): array
    {
        return [
            'At least 12 characters',
            'An uppercase and a lowercase letter',
            'A number',
            'A symbol (for example ! @ # $ %)',
        ];
    }

    /**
     * Extra checks the generic rule can't do: not the password they had before
     * (the hashed one or the old-system plain-text one) and nothing built from
     * their email name or their own name. Returns the problem, or null if fine.
     */
    public static function personalProblem($agent, string $new): ?string
    {
        if (filled($agent->password) && Hash::check($new, $agent->password)) {
            return 'Please choose a password you have not used before.';
        }

        if (filled($agent->agtPswd) && hash_equals((string) $agent->agtPswd, $new)) {
            return 'Please choose a password you have not used before.';
        }

        $lower = mb_strtolower($new);

        $parts = [
            Str::before((string) $agent->xxAgtUname, '@'),
            $agent->agtFirst,
            $agent->agtLast,
        ];

        foreach ($parts as $part) {
            $part = mb_strtolower(trim((string) $part));

            if (mb_strlen($part) >= 4 && str_contains($lower, $part)) {
                return 'Your password can\'t contain your name or email name.';
            }
        }

        return null;
    }

    /** "chris.visionstreet@gmail.com" -> "c•••••••••••••••@gmail.com" */
    public static function maskEmail(string $email): string
    {
        [$name, $domain] = array_pad(explode('@', $email, 2), 2, '');

        return mb_substr($name, 0, 1) . str_repeat('•', max(3, min(mb_strlen($name) - 1, 8))) . '@' . $domain;
    }

    /**
     * Email the agent a fresh one-time link (any earlier unused link stops
     * working). $source is 'login', 'forgot' or 'admin' - admins are exempt from
     * the per-agent limits.
     *
     * Returns: sent | limited | blocked | noemail | failed
     */
    public static function sendLink(Propagent $agent, string $source, ?string $ip = null): string
    {
        $email = trim((string) $agent->xxAgtUname);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'noemail';
        }

        // A blocked agent gets nothing, however the link was asked for.
        if ((int) ($agent->loginBlocked ?? 0) === 1) {
            return 'blocked';
        }

        $now = Carbon::now('UTC');

        if ($source !== 'admin') {
            $recent = AgentPasswordReset::where('propagent_id', $agent->id)
                ->where('created_at', '>=', $now->copy()->subHour()->format(self::DB_FORMAT))
                ->orderByDesc('id')
                ->pluck('created_at');

            if ($recent->count() >= self::MAX_PER_HOUR) {
                return 'limited';
            }

            if ($recent->isNotEmpty()
                && Carbon::parse($recent->first(), 'UTC')->gt($now->copy()->subSeconds(self::MIN_GAP_SECONDS))) {
                return 'limited';
            }
        }

        $token = Str::random(64);

        // Only the newest link works.
        AgentPasswordReset::where('propagent_id', $agent->id)
            ->whereNull('used_at')
            ->update(['used_at' => $now->format(self::DB_FORMAT)]);

        $row = AgentPasswordReset::create([
            'propagent_id' => $agent->id,
            'token_hash'   => hash('sha256', $token),
            'source'       => $source,
            'requested_ip' => $ip,
            'expires_at'   => $now->copy()->addMinutes(self::LINK_MINUTES)->format(self::DB_FORMAT),
            'created_at'   => $now->format(self::DB_FORMAT),
        ]);

        try {
            Mail::to($email)->send(new AgentPasswordResetMail(
                $agent,
                rtrim((string) config('app.url'), '/') . '/member/password/set/' . $token,
                self::LINK_MINUTES,
            ));
        } catch (\Throwable $e) {
            // Don't leave a live link nobody received.
            $row->delete();
            Log::error('Password reset email failed for agent ' . $agent->id . ': ' . $e->getMessage());

            return 'failed';
        }

        return 'sent';
    }

    /** The link's row if it exists, hasn't been used and hasn't expired. */
    public static function findValid(string $token): ?AgentPasswordReset
    {
        $row = AgentPasswordReset::where('token_hash', hash('sha256', $token))->first();

        if (!$row || $row->used_at !== null) {
            return null;
        }

        if (Carbon::parse($row->expires_at, 'UTC')->lte(Carbon::now('UTC'))) {
            return null;
        }

        return $row;
    }

    /** Every unused link for the agent stops working (used after a successful reset). */
    public static function voidAll(int $agentId): void
    {
        // Before the agent_password_resets table has been created there are no links to
        // cancel, and that must not stop an admin action (moving / deleting an account).
        try {
            AgentPasswordReset::where('propagent_id', $agentId)
                ->whereNull('used_at')
                ->update(['used_at' => Carbon::now('UTC')->format(self::DB_FORMAT)]);
        } catch (\Throwable $e) {
            Log::warning('Could not cancel password links for agent ' . $agentId . ': ' . $e->getMessage());
        }
    }
}
