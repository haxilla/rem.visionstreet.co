<?php

namespace App\Support;

use App\Models\Core\AgentKnownEmail;
use App\Models\Core\Propagent;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

/**
 * An agent's "other known emails": addresses they are known by besides the login email and the
 * contact email on their record. When duplicate accounts are merged the deleted account's emails
 * are saved here, on the account that stays, so an email an agent once used is never lost - support
 * can search for it and find the right agent.
 *
 * A by-name merge (accounts of one name but different emails) REFUSES to run until the table exists,
 * so nothing can be deleted without its email having somewhere to go.
 */
class AgentKnownEmails
{
    /** The table (in the same database as propagents). Run this once. */
    public const SETUP_SQL = <<<'SQL'
CREATE TABLE remuserdb.agent_known_emails (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  propagent_id INT UNSIGNED NOT NULL,
  email VARCHAR(255) NOT NULL,
  source VARCHAR(255) NULL,
  created_at DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY agent_known_emails_unique (propagent_id, email),
  KEY agent_known_emails_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
SQL;

    /** Whether the table has been created. */
    public static function available(): bool
    {
        static $available = null;

        if ($available === null) {
            try {
                $model     = new AgentKnownEmail();
                $available = Schema::connection($model->getConnectionName())->hasTable($model->getTable());
            } catch (\Throwable $e) {
                $available = false;
            }
        }

        return $available;
    }

    /** A cleaned-up email (trimmed, lowercase), or null when it isn't one. */
    public static function normalize($email): ?string
    {
        $email = strtolower(trim((string) $email));

        return ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) ? $email : null;
    }

    /**
     * Every email an account has of its own: login email, contact email and (when it is one) the
     * username.
     *
     * @return array<string,string> email => what it is
     */
    public static function emailsOf($account): array
    {
        $emails = [];

        foreach (['xxAgtUname' => 'login email', 'agtEmail' => 'contact email', 'agtUname' => 'username'] as $column => $what) {
            $email = self::normalize($account->{$column} ?? null);

            if ($email !== null && !isset($emails[$email])) {
                $emails[$email] = $what;
            }
        }

        return $emails;
    }

    /**
     * Save every email of $from on $keeper's "other known emails" (except ones $keeper already has of
     * its own, or already lists). Throws if it cannot be saved - callers must not go on to delete $from.
     *
     * @return string[] the emails newly saved
     */
    public static function remember(Propagent $keeper, Propagent $from, string $why): array
    {
        $own   = array_keys(self::emailsOf($keeper));
        $saved = [];

        foreach (self::emailsOf($from) as $email => $what) {
            if (in_array($email, $own, true)) {
                continue;
            }

            $row = AgentKnownEmail::firstOrCreate(
                ['propagent_id' => $keeper->id, 'email' => $email],
                ['source' => mb_substr("{$why} - its {$what}", 0, 255), 'created_at' => now()]
            );

            if ($row->wasRecentlyCreated) {
                $saved[] = $email;
            }
        }

        return $saved;
    }

    /** Save one email as another known email of an agent. True when it was newly saved; false when it was already listed or isn't an email. Throws if the table is missing. */
    public static function save($agentId, $email, string $source): bool
    {
        $email = self::normalize($email);

        if ($email === null) {
            return false;
        }

        return AgentKnownEmail::firstOrCreate(
            ['propagent_id' => $agentId, 'email' => $email],
            ['source' => mb_substr($source, 0, 255), 'created_at' => now()]
        )->wasRecentlyCreated;
    }

    /** Take an email off an agent's other known emails (it has just become their login, so it is no longer "other"). */
    public static function forget(array $agentIds, $email): void
    {
        $email = self::normalize($email);

        if ($email !== null && $agentIds !== [] && self::available()) {
            AgentKnownEmail::whereIn('propagent_id', $agentIds)->where('email', $email)->delete();
        }
    }

    /** One agent's other known emails, oldest first (empty when the table doesn't exist yet). */
    public static function for($agentId): Collection
    {
        if (!self::available()) {
            return collect();
        }

        return AgentKnownEmail::where('propagent_id', $agentId)->orderBy('id')->get();
    }

    /**
     * The other known emails of several agents at once.
     *
     * @return array<int, string[]> agent id => emails
     */
    public static function forMany(array $agentIds): array
    {
        if ($agentIds === [] || !self::available()) {
            return [];
        }

        return AgentKnownEmail::whereIn('propagent_id', $agentIds)
            ->get(['propagent_id', 'email'])
            ->groupBy('propagent_id')
            ->map(fn ($rows) => $rows->pluck('email')->all())
            ->all();
    }
}
