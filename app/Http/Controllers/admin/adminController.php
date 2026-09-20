<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Core\AdminSetting;
use App\Models\Core\Propagent;
use App\Models\Core\Propdelivnow;
use App\Models\Core\Propflyer;
use App\Support\AgentCampaigns;
use App\Support\AgentImages;
use App\Support\AgentPasswords;
use App\Support\AgentTime;
use App\Support\ImageOptimizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class adminController extends Controller
{
    /** Max depth for /admin/{segments?} */
    private const MAX_SEGMENTS = 5;

    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/admin/login');
    }

    public function segments(Request $request)
    {
        $segmentsPath = trim((string) $request->route('segments', ''), '/');    
        $parts        = ($segmentsPath === '') ? [] : explode('/', $segmentsPath);

        // Prepend 'admin' so dynamic_index resolves to admin.* views
        array_unshift($parts, 'admin');

        require_once __DIR__ . '/../parts/dynamic_index.php';

        // ---- partial vs full ----
        $isPartial = $request->header('X-Pageswap') === '1';
        if ($isPartial) {
            // return just the fragment for pageswap
            return response()
                ->view($viewName, compact('data'))
                ->header('Vary', 'X-Pageswap');}
                // cache safety

        // full chrome + the same fragment on refresh/direct visit
        return response()
            ->view($viewName, [         
                'data'        => $data,
                'contentView' => $viewName,
            ])->header('Vary', 'X-Pageswap');
        
    }   


    /**
     * Delete the agents ticked on the "No Start Date" list (POST only).
     *
     * The old single-agent delete also removed the agent's row from a REMOTE
     * database (remote_realtyemails.emailagents); that connection no longer
     * exists, so it threw before the local delete ever ran - that step is gone.
     *
     * Only agents that never had a start date are deleted, whatever ids are
     * sent (checked again here, not just on the page); any others are skipped
     * and counted. This is a real delete (Propagent isn't soft-deleting). The
     * confirmation prompt is a browser-side prompt controlled by the "Confirm
     * agent deletion" admin setting.
     */
    public function agentsDeleteMany(Request $request)
    {
        $validated = $request->validate([
            // a page holds 25; the cap just guards against an absurd request
            'ids'   => ['required', 'array', 'min:1', 'max:200'],
            'ids.*' => ['integer', 'min:1'],
        ], [
            'ids.required' => 'Tick at least one agent to delete.',
            'ids.min'      => 'Tick at least one agent to delete.',
        ]);

        // the phone list and the table both carry each agent, so ids can repeat
        $ids = array_values(array_unique(array_map('intval', $validated['ids'])));

        // Only agents on the "No Start Date" list: no start date AND no credits.
        // (Agents who have credits are on their own review-only tab; this check
        // is here so a stale page or a hand-made request can't delete them.)
        $deleted = Propagent::whereIn('id', $ids)
            ->whereNull('startDate')
            ->where(function ($query) {
                $query->whereNull('remCreds')->orWhere('remCreds', '<=', 0);
            })
            ->delete();
        $skipped = count($ids) - $deleted;

        $message = 'Deleted ' . $deleted . ($deleted === 1 ? ' agent.' : ' agents.');

        if ($skipped > 0) {
            $message .= " Skipped {$skipped} (they have a start date or credits, or were already gone).";
        }

        return redirect()->back()->with('status', $message);
    }

    public function agentView($id)
    {

        include(app_path().'/admin/agent/view.php');

        // where their photo / logo are and whether the files are really there
        $photo     = AgentImages::photo($agent);
        $logo      = AgentImages::logo($agent);
        $hasOffice = (bool) $agent->theAgtOffice;

        return view('admin.agents.show', compact('agent', 'flyerCount', 'campaignCount', 'campaignsInQueue', 'orders', 'photo', 'logo', 'hasOffice', 'sameEmailAccounts'));

    }

    /**
     * Every campaign an agent has, grouped by flyer: each flyer's address is a
     * heading with that flyer's campaigns listed under it. Flyers are ordered by
     * their most recent campaign activity, newest first. A flyer the agent has
     * since deleted is still listed (marked as deleted) so its campaigns aren't
     * lost from the history.
     */
    public function agentCampaigns($id)
    {
        $agent     = Propagent::findOrFail($id);
        $campaigns = AgentCampaigns::forAgent($agent->id);

        // withTrashed: a soft-deleted flyer keeps its campaign history. Its default
        // photo (for the thumbnail), photo folder and view counter come along.
        $flyers = Propflyer::withTrashed()
            ->with([
                'thePhotos' => fn ($query) => $query->where('def', 1),
                'theMeta',
                'theStats',
            ])
            ->whereIn('id', $campaigns->pluck('flyer_id')->unique()->all())
            ->get(['id', 'xFullStreet', 'xCity', 'state', 'xZip', 'deleted_at'])
            ->keyBy('id');

        $groups = $campaigns->groupBy('flyer_id')->map(function ($items, $flyerId) use ($flyers) {
            $flyer = $flyers->get($flyerId);

            // thumbnail: the default photo - the 500px copy when there is one
            $photo = $flyer ? ($flyer->thePhotos->firstWhere('resized', 500) ?? $flyer->thePhotos->first()) : null;
            $meta  = $flyer?->theMeta;

            $thumb = ($photo && $meta && $meta->zipDir && $meta->mlsDir && $photo->photoName)
                ? "/hqphotos/{$meta->zipDir}/{$meta->mlsDir}/{$photo->photoName}"
                : null;

            return [
                'flyerId'    => $flyerId,
                'title'      => $flyer ? ($flyer->xFullStreet ?: 'Untitled flyer') : 'Flyer no longer exists',
                'place'      => $flyer ? trim(($flyer->xCity ?? '') . ' ' . ($flyer->state ?? '') . ' ' . ($flyer->xZip ?? '')) : '',
                'deleted'    => !$flyer || $flyer->trashed(),
                'thumb'      => $thumb,
                // emails actually delivered = the recipients of this flyer's FINISHED campaigns
                'emailsSent' => (int) $items->where('status', 'completed')->sum(fn ($r) => (int) $r['emails']),
                // total flyer hits (page views) - null when the flyer no longer exists
                'hits'       => $flyer ? (int) optional($flyer->theStats)->xWebViews : null,
                'campaigns'  => $items->sortByDesc('sort')->values(),
                'latest'     => $items->max('sort'),
            ];
        })->sortByDesc('latest')->values();

        return view('admin.agents.campaigns', compact('agent', 'groups', 'campaigns'));
    }

    /**
     * Add or change an agent's photo or logo ($kind is "photo" or "logo").
     *
     * Follows the member area's own upload (save_modal_agentcontact.php): an
     * image up to 5 MB, saved as {agentId}agtphoto-/agtlogo-{random}.{ext} in the
     * same folders the flyers read from, and the previous file is deleted.
     * SVG is deliberately not accepted - it is served straight from the site and
     * can carry script. A logo needs the agent's office record (the flyers build
     * its address from it), so without one it is refused.
     */
    public function agentImageUpload(Request $request, $id, $kind)
    {
        $agent = Propagent::with('theAgtOffice')->findOrFail($id);

        [$column, $prefix, $label, $dir, $box] = $this->imageSpec($agent, $kind);

        if ($dir === null) {
            return redirect()->route('admin.agentView', $agent->id)->withErrors([
                'image' => 'This agent has no office record, so a logo can\'t be added yet.',
            ]);
        }

        $request->validate([
            'image' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:5120'],
        ], [
            'image.required' => "Choose a {$label} to upload.",
            'image.image'    => "The {$label} must be an image (JPG, PNG, GIF or WebP).",
            'image.mimes'    => "The {$label} must be a JPG, PNG, GIF or WebP image.",
            'image.max'      => "The {$label} can't be larger than 5 MB.",
            'image.uploaded' => "The {$label} couldn't be uploaded - it may be larger than the server allows.",
        ]);

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $previous = basename((string) $agent->{$column});
        $file     = $request->file('image');
        $original = (int) $file->getSize();

        // Shrink and re-encode before storing: the flyer is emailed, so every
        // recipient downloads this image (see App\Support\ImageOptimizer).
        try {
            [$bytes, $extension] = ImageOptimizer::optimize($file->getRealPath(), $box, $kind === 'logo');
        } catch (\RuntimeException $e) {
            return redirect()->route('admin.agentView', $agent->id)->withErrors(['image' => $e->getMessage()]);
        }

        $filename = $agent->id . $prefix . '-' . strtoupper(bin2hex(random_bytes(16))) . '.' . $extension;

        if (@file_put_contents("{$dir}/{$filename}", $bytes) === false) {
            return redirect()->route('admin.agentView', $agent->id)
                ->withErrors(['image' => "The {$label} couldn't be saved - the server folder isn't writable."]);
        }

        @chmod("{$dir}/{$filename}", 0644);

        // remove the old file - only from this same folder (an older copy in a
        // legacy folder is left alone)
        if ($previous !== '' && is_file("{$dir}/{$previous}")) {
            @unlink("{$dir}/{$previous}");
        }

        // the save stamps updated_at - in the agent's timezone
        AgentTime::apply($agent);

        $agent->{$column} = $filename;
        $agent->save();

        $name = $agent->agtFullName ?: ($agent->xxAgtUname ?: 'this agent');

        return redirect()->route('admin.agentView', $agent->id)->with(
            'status',
            ($previous !== '' ? 'Changed' : 'Added') . " the {$label} for {$name}"
                . ' (' . ImageOptimizer::humanSize($original) . ' → ' . ImageOptimizer::humanSize(strlen($bytes)) . ').'
        );
    }

    /** Clear an agent's photo or logo: forget the file name and delete the file. */
    public function agentImageClear($id, $kind)
    {
        $agent = Propagent::with('theAgtOffice')->findOrFail($id);

        [$column, , $label, $dir] = $this->imageSpec($agent, $kind);

        $previous = basename((string) $agent->{$column});

        if ($dir !== null && $previous !== '' && is_file("{$dir}/{$previous}")) {
            @unlink("{$dir}/{$previous}");
        }

        // the save stamps updated_at - in the agent's timezone
        AgentTime::apply($agent);

        // NULL, the same "none" the Agents-page filters look for
        $agent->{$column} = null;
        $agent->save();

        $name = $agent->agtFullName ?: ($agent->xxAgtUname ?: 'this agent');

        return redirect()->route('admin.agentView', $agent->id)
            ->with('status', ucfirst($label) . " cleared for {$name}.");
    }

    /** [database column, file-name prefix, label, folder on disk (null = none possible), max size box] */
    private function imageSpec(Propagent $agent, string $kind): array
    {
        return $kind === 'photo'
            ? ['agtPhoto', 'agtphoto', 'photo', AgentImages::photoDir($agent), ImageOptimizer::PHOTO_BOX]
            : ['agtLogo',  'agtlogo',  'logo',  AgentImages::logoDir($agent),  ImageOptimizer::LOGO_BOX];
    }

    /**
     * Set an agent's remaining credits (remCreds) to the number typed in the
     * field on the agent page. "Priority credits" (pCreds) is a separate
     * balance and is left alone by this.
     *
     * The typed number simply becomes the balance. Logged (who, which agent,
     * before and after), since there is no other record of who changed one.
     */
    public function agentCredits(Request $request, $id)
    {
        $validated = $request->validate([
            'credits' => ['required', 'integer', 'min:0', 'max:100000'],
        ], [
            'credits.required' => 'Enter the number of credits.',
            'credits.integer'  => 'Credits must be a whole number.',
            'credits.min'      => 'Credits can\'t be negative.',
            'credits.max'      => 'That is more credits than allowed.',
        ]);

        $agent = Propagent::findOrFail($id);

        // the save stamps updated_at - in the agent's timezone
        AgentTime::apply($agent);

        $before = (int) ($agent->remCreds ?? 0);
        $after  = (int) $validated['credits'];

        $agent->remCreds = $after;
        $agent->save();

        if ($before !== $after) {
            Log::info('Agent credits changed by admin', [
                'admin_id' => Auth::guard('admin')->id(),
                'agent_id' => $agent->id,
                'before'   => $before,
                'after'    => $after,
            ]);
        }

        $name = $agent->agtFullName ?: ($agent->xxAgtUname ?: 'this agent');

        return redirect()->route('admin.agentView', $agent->id)->with(
            'status',
            $before === $after
                ? "Credits for {$name} are already {$after}."
                : "Credits for {$name} changed from {$before} to {$after}."
        );
    }

    /**
     * Set or change an agent's start date (a date picker on the agent page).
     * An agent with a start date is listed under "Agents With Start Date"
     * instead of "No Start Date". A date is required - it can't be cleared
     * here, because clearing it would put an agent back in the list that
     * "Delete selected" works on.
     */
    public function agentStartDate(Request $request, $id)
    {
        $validated = $request->validate([
            'startDate' => ['required', 'date_format:Y-m-d', 'after:2000-01-01', 'before:2100-01-01'],
        ], [
            'startDate.required'    => 'Pick a start date.',
            'startDate.date_format' => 'Pick a valid start date.',
            'startDate.after'       => 'The start date must be after 2000.',
            'startDate.before'      => 'The start date must be before 2100.',
        ]);

        $agent = Propagent::findOrFail($id);

        // the save stamps updated_at - in the agent's timezone
        AgentTime::apply($agent);

        $agent->startDate = $validated['startDate'];
        $agent->save();

        $name = $agent->agtFullName ?: ($agent->xxAgtUname ?: 'this agent');

        return redirect()->route('admin.agentView', $agent->id)->with(
            'status',
            "Start date for {$name} set to "
                . \Illuminate\Support\Carbon::parse($validated['startDate'])->format('m/d/Y') . '.'
        );
    }

    /**
     * "Send password reset email": emails the agent a fresh one-time link, to
     * the login address on file (see AgentPasswords). It never shows or sets a
     * password - the agent chooses their own. Any earlier unused link stops
     * working. Admins are not held to the per-agent send limits.
     */
    public function agentPasswordReset(Request $request, $id)
    {
        $agent = Propagent::findOrFail($id);
        $to    = $agent->xxAgtUname;

        try {
            $result = AgentPasswords::sendLink($agent, 'admin', $request->ip());
        } catch (\Throwable $e) {
            // Most likely the agent_password_resets table hasn't been created yet.
            Log::error('Admin password reset failed for agent ' . $agent->id . ': ' . $e->getMessage());

            return redirect()->route('admin.agentView', $agent->id)
                ->withErrors(['passwordReset' => 'Could not send - has the agent_password_resets table been created in the database?']);
        }

        if ($result === 'sent') {
            return redirect()->route('admin.agentView', $agent->id)
                ->with('status', "Password link emailed to {$to}. It works once and expires in " . AgentPasswords::LINK_MINUTES . ' minutes.');
        }

        $why = [
            'noemail' => 'this agent has no valid login email on file.',
            'blocked' => 'this agent\'s login is blocked - unblock it first.',
            'failed'  => 'the email could not be sent (see the log).',
        ][$result] ?? 'nothing was sent.';

        return redirect()->route('admin.agentView', $agent->id)
            ->withErrors(['passwordReset' => 'Not sent: ' . $why]);
    }

    /**
     * Move an agent's login to a NEW email - for the agent who no longer has access
     * to the email they registered with (so the emailed password link can never reach
     * them). The admin is the identity check here (phone, MLS ID, licence...), so this
     * hands the account to whoever owns the new address.
     *
     * Because control of the old mailbox is exactly what's in doubt, the account is
     * treated as recovered, not just edited: its password is cleared, it goes back to
     * "no new-site password yet", any unused links are cancelled, a link to create a
     * password goes to the NEW address, and the OLD address is told (best effort) in
     * case somebody other than the agent asked for this. Who / what / when goes to the log.
     *
     * "All accounts" moves every account that shares the login email (they always get
     * one password together), so they don't end up split across two emails.
     */
    public function agentLoginEmail(Request $request, $id)
    {
        $agent = Propagent::findOrFail($id);

        $data = $request->validate([
            'new_email' => ['required', 'email', 'max:100'],
        ]);

        $new = trim($data['new_email']);
        $old = trim((string) $agent->xxAgtUname);

        $back = fn () => redirect()->route('admin.agentView', $agent->id);

        if (strcasecmp($new, $old) === 0) {
            return $back()->withErrors(['loginEmail' => 'That is already this account\'s login email.']);
        }

        $group = AgentPasswords::accountsForEmail($old);

        if ($group->where('id', $agent->id)->isEmpty()) {
            $group->push($agent);
        }

        $targets = $request->boolean('all_accounts') ? $group : collect([$agent]);

        // An email already used by some OTHER account would put these accounts in that
        // person's account (and give them the same password). Not without a human deciding.
        $clash = AgentPasswords::accountsForEmail($new)->reject(fn ($a) => $targets->contains('id', $a->id));

        if ($clash->isNotEmpty()) {
            $who = $clash->map(fn ($a) => '#' . $a->id . ' ' . ($a->agtFullName ?: 'No name'))->implode(', ');

            return $back()->withErrors(['loginEmail' => "{$new} is already the login email of another account ({$who}). Pick a different email, or sort that out first."]);
        }

        DB::transaction(function () use ($targets, $new) {
            foreach ($targets as $account) {
                AgentTime::apply($account);

                $account->xxAgtUname = $new;
                $account->password   = null;

                if (AgentPasswords::columnAvailable($account)) {
                    $account->passwordResetAt = null;
                }

                $account->save();

                AgentPasswords::voidAll($account->id);
            }
        });

        Log::info('Admin changed an agent login email', [
            'admin_id' => Auth::guard('admin')->id(),
            'agents'   => $targets->pluck('id')->all(),
            'from'     => $old,
            'to'       => $new,
            'ip'       => $request->ip(),
        ]);

        // Tell the old address, if it can be told.
        if (filter_var($old, FILTER_VALIDATE_EMAIL)) {
            try {
                \Illuminate\Support\Facades\Mail::to($old)->send(
                    new \App\Mail\AgentLoginEmailChangedMail($agent, $old, AgentPasswords::maskEmail($new))
                );
            } catch (\Throwable $e) {
                Log::warning('Login-email-changed notice failed for agent ' . $agent->id . ': ' . $e->getMessage());
            }
        }

        $agent->refresh();
        $count  = $targets->count();
        $result = AgentPasswords::sendLink($agent, 'admin', $request->ip());

        $status = "Login email changed to {$new} for {$count} " . ($count === 1 ? 'account' : 'accounts')
            . ' and the old password cleared.';

        if ($result === 'sent') {
            return $back()->with('status', $status . ' A link to create a new password was emailed to ' . $new . '.');
        }

        return $back()->with('status', $status)
            ->withErrors(['loginEmail' => 'The password link was NOT sent (' . $result . '). Use "Send password reset email" to try again.']);
    }

    /**
     * MERGE DUPLICATE ACCOUNTS, step 1 of 2 (GET): tick flyers and pick which account
     * receives them. Only for accounts that share one login email - if the agent has no
     * duplicate there is nothing to merge and this refuses.
     *
     * Every flyer of every account on the email is listed (deleted ones too, marked, so
     * their campaign history can travel with them); a flyer whose campaign is being
     * delivered right now can't be moved and is shown disabled.
     */
    public function agentMerge($id)
    {
        $agent    = Propagent::findOrFail($id);
        $accounts = AgentPasswords::accountsForEmail($agent->xxAgtUname);

        if ($accounts->count() < 2) {
            return redirect()->route('admin.agentView', $agent->id)
                ->withErrors(['merge' => 'No other account uses this login email, so there is nothing to merge.']);
        }

        $flyers = Propflyer::withTrashed()
            ->whereIn('propagent_id', $accounts->pluck('id')->all())
            ->with([
                'thePhotos' => fn ($query) => $query->where('def', 1),
                'theMeta',
                'theStats',
            ])
            ->orderByDesc('id')
            ->get(['id', 'propagent_id', 'xFullStreet', 'xCity', 'state', 'xZip', 'creationDate', 'created_at', 'deleted_at']);

        $busy = $this->flyersBeingDelivered($flyers->pluck('id')->all());

        $rows = $accounts->map(function ($account) use ($flyers, $busy) {
            $mine = $flyers->where('propagent_id', $account->id)->values();

            return [
                'id'      => $account->id,
                'name'    => $account->agtFullName ?: trim(($account->agtFirst ?? '') . ' ' . ($account->agtLast ?? '')) ?: 'No name',
                'office'  => optional($account->theAgtOffice)->officeName,
                'start'   => $account->startDate,
                'credits' => (int) ($account->remCreds ?? 0),
                'flyers'  => $mine->map(function ($flyer) use ($busy) {
                    $photo = $flyer->thePhotos->firstWhere('resized', 500) ?? $flyer->thePhotos->first();
                    $meta  = $flyer->theMeta;

                    return [
                        'id'        => $flyer->id,
                        'title'     => $flyer->xFullStreet ?: 'Untitled flyer',
                        'place'     => trim(($flyer->xCity ?? '') . ' ' . ($flyer->state ?? '') . ' ' . ($flyer->xZip ?? '')),
                        'created'   => $flyer->creationDate ?: $flyer->created_at,
                        'last_sent' => optional($flyer->theStats)->xLastDeliveryDate,
                        'hits'      => (int) optional($flyer->theStats)->xWebViews,
                        'deleted'   => $flyer->trashed(),
                        'busy'      => isset($busy[$flyer->id]),
                        'thumb'     => ($photo && $meta && $meta->zipDir && $meta->mlsDir && $photo->photoName)
                            ? "/hqphotos/{$meta->zipDir}/{$meta->mlsDir}/{$photo->photoName}"
                            : null,
                    ];
                })->all(),
            ];
        });

        return view('admin.agents.merge', ['agent' => $agent, 'accounts' => $rows]);
    }

    /**
     * MERGE DUPLICATE ACCOUNTS, step 2 (POST): move the ticked flyers into the chosen
     * account.
     *
     * A flyer is not just one row: its photos, style, meta, remarks, map and campaign
     * history each carry the agent's id too. So the move finds EVERY table in the database
     * that has both propflyer_id and propagent_id and changes the agent on all of them
     * for these flyers, inside one transaction (all or nothing). Agent-level things stay
     * where they are: credits, start date, photo, logo, office.
     *
     * Guarded on the server, not just on the page: only flyers of accounts that share
     * this login email, never into an account that doesn't, never a flyer already in the
     * destination, and never a flyer that is being delivered right now.
     */
    public function agentMergeSave(Request $request, $id)
    {
        $agent    = Propagent::findOrFail($id);
        $accounts = AgentPasswords::accountsForEmail($agent->xxAgtUname);
        $back     = fn () => redirect()->route('admin.agentMerge', $agent->id);

        if ($accounts->count() < 2) {
            return redirect()->route('admin.agentView', $agent->id)
                ->withErrors(['merge' => 'No other account uses this login email, so there is nothing to merge.']);
        }

        $data = $request->validate([
            'destination' => ['required', 'integer'],
            'flyers'      => ['required', 'array', 'min:1', 'max:1000'],
            'flyers.*'    => ['integer', 'min:1'],
        ], [
            'flyers.required' => 'Tick at least one flyer to move.',
            'flyers.min'      => 'Tick at least one flyer to move.',
        ]);

        $dest = $accounts->firstWhere('id', (int) $data['destination']);

        if (!$dest) {
            return $back()->withErrors(['merge' => 'That account does not use this login email.']);
        }

        $ids     = array_values(array_unique(array_map('intval', $data['flyers'])));
        $sources = $accounts->pluck('id')->reject(fn ($accountId) => (int) $accountId === (int) $dest->id)->values()->all();

        $flyers = Propflyer::withTrashed()
            ->whereIn('id', $ids)
            ->whereIn('propagent_id', $sources)
            ->get(['id', 'propagent_id']);

        $notEligible = count($ids) - $flyers->count();

        $busy    = $this->flyersBeingDelivered($flyers->pluck('id')->all());
        $movable = $flyers->reject(fn ($flyer) => isset($busy[$flyer->id]))->values();

        if ($movable->isEmpty()) {
            return $back()->withErrors(['merge' => 'Nothing was moved: the ticked flyers are not in the other accounts, or are being delivered right now.']);
        }

        try {
            $tables = $this->flyerLinkedTables();
        } catch (\Throwable $e) {
            Log::error('Merge: could not list the flyer tables: ' . $e->getMessage());

            return $back()->withErrors(['merge' => 'Could not work out which tables hold flyer data, so nothing was moved.']);
        }

        $counts     = [];
        $destOffice = optional($dest->theAgtOffice)->officeID;

        try {
            DB::transaction(function () use ($movable, $accounts, $dest, $tables, $destOffice, &$counts) {
                // the moved rows are stamped in the receiving agent's timezone
                AgentTime::apply($dest);

                foreach ($movable->groupBy('propagent_id') as $sourceId => $group) {
                    $flyerIds  = $group->pluck('id')->all();
                    $srcOffice = optional($accounts->firstWhere('id', (int) $sourceId)?->theAgtOffice)->officeID;

                    $counts['propflyers'] = ($counts['propflyers'] ?? 0)
                        + Propflyer::withTrashed()
                            ->whereIn('id', $flyerIds)
                            ->where('propagent_id', $sourceId)
                            ->update(['propagent_id' => $dest->id]);

                    // a flyer's own office id only mirrors its agent's office: keep it in step
                    if ($srcOffice && $destOffice && $srcOffice != $destOffice) {
                        Propflyer::withTrashed()
                            ->whereIn('id', $flyerIds)
                            ->where('officeID', $srcOffice)
                            ->update(['officeID' => $destOffice]);
                    }

                    foreach ($tables as $table) {
                        $n = DB::table("remuserdb.{$table}")
                            ->whereIn('propflyer_id', $flyerIds)
                            ->where('propagent_id', $sourceId)
                            ->update(['propagent_id' => $dest->id]);

                        $counts[$table] = ($counts[$table] ?? 0) + $n;
                    }
                }
            });
        } catch (\Throwable $e) {
            Log::error('Merge failed, nothing moved: ' . $e->getMessage());

            return $back()->withErrors(['merge' => 'The move failed and nothing was changed. (' . $e->getMessage() . ')']);
        }

        Log::info('Admin moved flyers between duplicate accounts', [
            'admin_id'    => Auth::guard('admin')->id(),
            'to'          => $dest->id,
            'flyers'      => $movable->pluck('id')->all(),
            'from'        => $movable->pluck('propagent_id')->unique()->values()->all(),
            'rows_by_tbl' => $counts,
            'ip'          => $request->ip(),
        ]);

        $moved   = $movable->count();
        $name    = $dest->agtFullName ?: trim(($dest->agtFirst ?? '') . ' ' . ($dest->agtLast ?? '')) ?: 'the account';
        $message = "Moved {$moved} " . ($moved === 1 ? 'flyer' : 'flyers') . " into #{$dest->id} {$name}.";

        $related = collect($counts)->except('propflyers')->filter()->map(fn ($n, $t) => "{$t} {$n}")->implode(', ');

        if ($related !== '') {
            $message .= " Their related records moved too ({$related}).";
        }

        $skipped = $notEligible + ($flyers->count() - $moved);

        if ($skipped > 0) {
            $message .= " Skipped {$skipped} (already in that account, not in these accounts, or being delivered right now).";
        }

        return $back()->with('status', $message);
    }

    /** [flyerId => true] for flyers with a campaign that has started delivering but not finished. */
    private function flyersBeingDelivered(array $flyerIds): array
    {
        if ($flyerIds === []) {
            return [];
        }

        return DB::table('remuserdb.propdelivnow')
            ->whereIn('propflyer_id', $flyerIds)
            ->whereNotNull('emStart')
            ->whereNull('emComplete')
            ->pluck('propflyer_id')
            ->flip()
            ->map(fn () => true)
            ->all();
    }

    /**
     * Every table in the database that ties a row to BOTH a flyer and an agent
     * (columns propflyer_id and propagent_id): photos, style, meta, remarks, map,
     * campaign history, stats... Found from the database itself so a table that only
     * the old system knew about isn't left pointing at the old account.
     *
     * @return string[]
     */
    private function flyerLinkedTables(): array
    {
        $rows = DB::select(
            "SELECT DISTINCT a.TABLE_NAME AS name
               FROM information_schema.COLUMNS a
               JOIN information_schema.COLUMNS f
                 ON f.TABLE_SCHEMA = a.TABLE_SCHEMA AND f.TABLE_NAME = a.TABLE_NAME AND f.COLUMN_NAME = 'propflyer_id'
               JOIN information_schema.TABLES t
                 ON t.TABLE_SCHEMA = a.TABLE_SCHEMA AND t.TABLE_NAME = a.TABLE_NAME AND t.TABLE_TYPE = 'BASE TABLE'
              WHERE a.TABLE_SCHEMA = 'remuserdb' AND a.COLUMN_NAME = 'propagent_id'
              ORDER BY a.TABLE_NAME"
        );

        return collect($rows)->pluck('name')
            ->filter(fn ($name) => preg_match('/^[A-Za-z0-9_]+$/', $name))
            ->values()
            ->all();
    }

    /**
     * Block / unblock an agent's login. The flag is propagents.loginBlocked
     * (added by hand with raw SQL). It is enforced at sign-in
     * (guestController::memberLogin) and on every request in the member
     * area (EnsureAgentNotBlocked), so blocking also ends a session that
     * is already open.
     */
    public function agentLoginBlock($id)
    {
        $agent = Propagent::findOrFail($id);

        // The save stamps updated_at - in the agent's timezone.
        AgentTime::apply($agent);

        // The column is missing from the loaded row until the SQL has been run.
        if (!array_key_exists('loginBlocked', $agent->getAttributes())) {
            return redirect()->route('admin.agentView', $agent->id)
                ->withErrors(['loginBlocked' => 'The loginBlocked column has not been added to the database yet.']);
        }

        $block = (int) $agent->loginBlocked !== 1;

        $agent->loginBlocked = $block ? 1 : 0;
        $agent->save();

        $name = $agent->agtFullName ?: ($agent->xxAgtUname ?: 'this agent');

        return redirect()->route('admin.agentView', $agent->id)
            ->with('status', $block ? "Login blocked for {$name}." : "Login unblocked for {$name}.");
    }

    public function agentLogin(Request $request, $id)
    {
        $this->rememberImpersonationOrigin($request);

        include(app_path().'/admin/agent/login.php');
        return redirect('/member/dashboard');
    }

    public function agentFlyerCreate(Request $request, $id)
    {
        // Same impersonation as agentLogin, but drops the admin straight
        // into a brand-new flyer draft for this agent (no flyerId) - for
        // the rare case of an admin recreating a flyer on an agent's
        // behalf (e.g. from a known address after old records were
        // lost), rather than reviewing/editing one that already exists.
        $this->rememberImpersonationOrigin($request);

        include(app_path().'/admin/agent/login.php');

        return redirect('/member/flyer/create');
    }

    public function returnToAdmin()
    {
        $returnUrl = session('impersonation_return_url', '/admin/dashboard');

        include(app_path().'/admin/agent/returnToAdmin.php');
        return redirect($returnUrl);
    }

    /**
     * Remember which admin page impersonation was launched from (via the
     * HTTP Referer, since every "impersonate" link lives on some admin
     * page), so returnToAdmin() can send the admin back there instead of
     * always landing on the dashboard. Only same-site /admin/* referers
     * are trusted, to avoid an open redirect if this header were ever
     * spoofed.
     */
    private function rememberImpersonationOrigin(Request $request): void
    {
        $referer = $request->headers->get('referer');

        if ($referer && str_starts_with($referer, url('/admin'))) {
            session(['impersonation_return_url' => $referer]);
        } else {
            session()->forget('impersonation_return_url');
        }
    }

    public function flyerCamps($flyerId)
    {
        // An agent can soft-delete a flyer once it has been sent, while its
        // completed campaigns still show in the admin lists. Give a clean
        // message instead of the "flyer not found" debug dump.
        abort_unless(Propflyer::whereKey($flyerId)->exists(), 404, 'This flyer has been deleted.');

        include(app_path().'/admin/flyer/camps.php');
        return view('admin.flyer.camps', [
            'data' => $data
        ]);

    }

    /**
     * Approve every request still waiting on this flyer. "Approved" is
     * propdelivnow.authorized = 1, which is what the mail system looks
     * for. Only rows that haven't started or completed are touched.
     */
    public function campaignApprove($flyerId)
    {
        // The update stamps updated_at - in the flyer's agent's timezone.
        AgentTime::apply(Propagent::find(Propflyer::whereKey($flyerId)->value('propagent_id')));

        $approved = Propdelivnow::where('propflyer_id', $flyerId)
            ->whereNull('emStart')
            ->whereNull('emComplete')
            ->where(function ($query) {
                $query->where('authorized', 0)->orWhereNull('authorized');
            })
            ->update(['authorized' => 1]);

        $message = $approved
            ? "Approved {$approved} " . ($approved === 1 ? 'area' : 'areas') . '.'
            : 'Nothing was waiting for approval on this flyer.';

        return redirect()->route('admin.flyerCamps', $flyerId)->with('status', $message);
    }

    /**
     * Admin-only: add an extra area to a flyer at no cost to the agent.
     * Deliberately does NOT touch the agent's credits (unlike the
     * member send-setup flow). Like every other request the row starts
     * UNAPPROVED (authorized = 0); it is approved together with the rest
     * of the flyer's waiting areas by campaignApprove().
     */
    public function campaignAddArea(Request $request, $flyerId)
    {
        $areasByDb = collect(include app_path('flyers/campaignAreas.php'))->keyBy('db');

        $validated = $request->validate([
            'area' => ['required', 'string', Rule::in($areasByDb->keys()->all())],
        ]);

        $flyer = Propflyer::findOrFail($flyerId);
        $area  = $areasByDb[$validated['area']];

        $alreadyQueued = Propdelivnow::where('propflyer_id', $flyer->id)
            ->where('emArea', $area['db'])
            ->whereNull('emComplete')
            ->exists();

        if ($alreadyQueued) {
            return redirect()->route('admin.flyerCamps', $flyer->id)
                ->withErrors(["{$area['label']} is already waiting or in progress for this flyer."]);
        }

        $totalEmails = DB::connection('rememaildb')->table($area['db'])->count();

        $campaign = new Propdelivnow();
        $campaign->propflyer_id   = $flyer->id;
        $campaign->propagent_id   = $flyer->propagent_id;
        $campaign->emArea         = $area['db'];
        $campaign->emArea_display = $area['label'];
        $campaign->emSubject      = Propdelivnow::where('propflyer_id', $flyer->id)
                                        ->orderByDesc('emRequest')
                                        ->value('emSubject');
        $campaign->totalEmails    = $totalEmails;

        // Recorded in the AGENT'S timezone (from their state), not the
        // admin's - same as the agent's own sends (see App\Support\AgentTime).
        $agent    = Propagent::find($flyer->propagent_id);
        $agentNow = AgentTime::now($agent);

        $campaign->emRequest      = $agentNow;
        $campaign->campCreated    = $agentNow;
        $campaign->created_at     = $agentNow;
        $campaign->updated_at     = $agentNow;

        // How the legacy system marks a free, admin-added area:
        $campaign->campLabel      = 'admin';
        $campaign->admin_add      = 1;
        $campaign->free           = 1;
        $campaign->authorized     = 0;   // approved later, with the flyer's other waiting areas
        $campaign->save();

        return redirect()->route('admin.flyerCamps', $flyer->id)->with(
            'status',
            "Added {$area['label']} (" . number_format($totalEmails) . ' contacts) at no charge. It is waiting for approval with this flyer\'s other areas.'
        );
    }

    /**
     * Flip trial mode on/off from the impersonation banner. Always just
     * flips - it never sends the admin anywhere. Answers JSON to the
     * banner's fetch() so the page it's on (e.g. a half-filled Details
     * form) doesn't reload; falls back to a redirect back if JavaScript
     * is off. If no test email is set yet the banner says so (the
     * Settings page is where the email is entered and validated).
     */
    public function trialToggle(Request $request)
    {
        $turnOn = !AdminSetting::trialMode();

        AdminSetting::write('trial_mode', $turnOn ? '1' : '0');

        if ($request->expectsJson()) {
            return response()->json([
                'trialMode'  => $turnOn,
                'trialEmail' => AdminSetting::trialEmail(),
            ]);
        }

        return redirect()->back();
    }

    /** Save the system-wide settings shown on /admin/settings. */
    public function settingsSave(Request $request)
    {
        $definitions = AdminSetting::definitions();

        // Validate everything first so a bad value saves nothing.
        $rules    = [];
        $messages = [];

        foreach ($definitions as $key => $definition) {
            if ($definition['type'] !== 'email') {
                continue;
            }

            $rules[$key] = ['nullable', 'email', 'max:255'];

            if (!empty($definition['required_when'])) {
                $toggle = $definition['required_when'];

                array_unshift(
                    $rules[$key],
                    Rule::requiredIf(fn () => $request->boolean($toggle))
                );

                $messages["{$key}.required"] =
                    "Enter the {$definition['label']} before turning on {$definitions[$toggle]['label']}.";
            }
        }

        $request->validate($rules, $messages);

        foreach ($definitions as $key => $definition) {
            if ($definition['type'] === 'toggle') {
                // an unchecked checkbox sends nothing, which reads as off
                AdminSetting::write($key, $request->boolean($key) ? '1' : '0');
            } elseif ($definition['type'] === 'email') {
                AdminSetting::write($key, trim((string) $request->input($key, '')));
            }
        }

        return redirect('/admin/settings')->with('status', 'Settings saved.');
    }

    public function flyerEdit(Request $request, $flyerId)
    {
        $flyer = Propflyer::findOrFail($flyerId);

        $this->rememberImpersonationOrigin($request);

        // Impersonate the flyer's owning agent (same login.php used by
        // agentLogin), then open the flyer the same way the member's own
        // dashboard does - every flyer card there (draft or complete)
        // links straight to Preview; resume.php's wizardStep-based step
        // picker is a separate "Resume" action for drafts only, and
        // wizardStep is never populated for flyers imported from the
        // legacy pre-Laravel system, so routing through it here would
        // incorrectly bounce an already-complete legacy flyer back to
        // the Details step.
        $id = $flyer->propagent_id;
        include(app_path().'/admin/agent/login.php');

        return redirect('/member/flyer/preview?flyerId='.$flyer->id);
    }
}