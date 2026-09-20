<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Core\AdminSetting;
use App\Models\Core\Propagent;
use App\Models\Core\Propdelivnow;
use App\Models\Core\Propflyer;
use App\Models\Core\Propflyerstat;
use App\Support\AgentCampaigns;
use App\Support\AgentImages;
use App\Support\AgentKnownEmails;
use App\Support\AgentNameDuplicates;
use App\Support\AgentNames;
use App\Support\AgentPasswords;
use App\Support\AgentProfile;
use App\Support\AgentTime;
use App\Support\DuplicateAccounts;
use App\Support\ImageOptimizer;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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
        $candidates = Propagent::whereIn('id', $ids)
            ->whereNull('startDate')
            ->where(function ($query) {
                $query->whereNull('remCreds')->orWhere('remCreds', '<=', 0);
            })
            ->get();

        // ...and nothing they own that deleting would orphan or destroy: flyers, orders,
        // campaign records (see DuplicateAccounts). Those are flagged in the list and skipped.
        $blockers   = DuplicateAccounts::blockersFor($candidates);
        $deletable  = $candidates->filter(fn ($account) => DuplicateAccounts::canDelete($blockers[(int) $account->id]))
            ->pluck('id')->all();

        $deleted = $deletable === [] ? 0 : Propagent::whereIn('id', $deletable)->delete();
        $skipped = count($ids) - $deleted;

        $message = 'Deleted ' . $deleted . ($deleted === 1 ? ' agent.' : ' agents.');

        if ($skipped > 0) {
            $message .= " Skipped {$skipped} (they have a start date, credits, flyers, orders or campaign records, or were already gone).";
        }

        return redirect()->back()->with('status', $message);
    }

    /**
     * Delete one agent from their own page (POST only): only an agent with NO start date
     * and NO credits - the same rule as the "No Start Date" list's bulk delete, checked
     * here again so a stale page or a hand-made request can't delete anyone else. (A
     * duplicate account is deleted from the Duplicate Logins tools, see agentDeleteDuplicate.)
     *
     * Like the bulk delete this removes the agent's row (Propagent isn't soft-deleting);
     * their unused password links go too. It does not touch their flyers.
     */
    public function agentDelete(Request $request, $id)
    {
        $agent = Propagent::findOrFail($id);

        if (!is_null($agent->startDate) || (int) ($agent->remCreds ?? 0) > 0) {
            return redirect()->route('admin.agentView', $agent->id)
                ->withErrors(['deleteAgent' => 'Not deleted: only an agent with no start date and no credits can be deleted.']);
        }

        // ...and only if it owns nothing that deleting would orphan or destroy.
        $blockers = DuplicateAccounts::blockersFor(collect([$agent]))[(int) $agent->id];

        if (!DuplicateAccounts::canDelete($blockers)) {
            return redirect()->route('admin.agentView', $agent->id)
                ->withErrors(['deleteAgent' => 'Not deleted: this account still has ' . DuplicateAccounts::describe($blockers) . '.']);
        }

        $name = $agent->agtFullName ?: trim(($agent->agtFirst ?? '') . ' ' . ($agent->agtLast ?? '')) ?: 'No name';

        $agent->delete();
        AgentNameDuplicates::forgetCount();   // a shared name may have just been resolved

        try {
            \App\Models\Core\AgentPasswordReset::where('propagent_id', $agent->id)->delete();
        } catch (\Throwable $e) {
            Log::warning('Could not clear password links for deleted agent ' . $agent->id . ': ' . $e->getMessage());
        }

        Log::info('Admin deleted an agent', [
            'admin_id' => Auth::guard('admin')->id(),
            'agent_id' => $agent->id,
            'name'     => $name,
            'email'    => $agent->xxAgtUname,
            'ip'       => $request->ip(),
        ]);

        return redirect(session('admin_agents_list_url', url('/admin/agents')))
            ->with('status', "Deleted account #{$agent->id} {$name}.");
    }

    /**
     * Save the "Contact" card on an agent's page: names, contact email, phones, website.
     * (This is the CONTACT email shown on flyers - the login email is separate and is
     * only changed with the lost-mailbox tool, because it decides who can sign in.)
     *
     * Validation and saving are shared with the agent's own Agent Info page (AgentProfile),
     * including the rule that the name on flyers is made from first + last.
     */
    public function agentContactSave(Request $request, $id)
    {
        $agent = Propagent::findOrFail($id);

        $data = $request->validate(AgentProfile::contactRules());

        try {
            AgentProfile::saveContact($agent, $data);
        } catch (\Throwable $e) {
            Log::error('Admin contact save failed for agent ' . $agent->id . ': ' . $e->getMessage());

            return redirect()->route('admin.agentView', $agent->id)->withInput()
                ->withErrors(['contact' => 'Could not save the contact details (a value may be too long for its field).']);
        }

        return redirect()->to(route('admin.agentView', $agent->id) . '#contact')
            ->with('status', 'Contact details saved.');
    }

    /**
     * Save the "Address & Office" card: brokerage, street / city / state / ZIP (on the
     * agent's office record) and the licence details (MLS ID, board, designations, county).
     * Shared with the agent's own Agent Info page (AgentProfile).
     */
    public function agentOfficeSave(Request $request, $id)
    {
        $agent = Propagent::with('theAgtOffice')->findOrFail($id);

        $data = $request->validate(AgentProfile::officeRules($agent->theAgtOffice) + AgentProfile::licenseRules($agent));

        try {
            AgentProfile::saveOffice($agent, $data);
        } catch (\Throwable $e) {
            Log::error('Admin office save failed for agent ' . $agent->id . ': ' . $e->getMessage());

            return redirect()->route('admin.agentView', $agent->id)->withInput()
                ->withErrors(['office' => 'Could not save the address and office (a value may be too long for its field).']);
        }

        return redirect()->to(route('admin.agentView', $agent->id) . '#office')
            ->with('status', 'Address and office saved.');
    }

    public function agentView($id)
    {

        include(app_path().'/admin/agent/view.php');

        // where their photo / logo are and whether the files are really there
        $photo     = AgentImages::photo($agent);
        $logo      = AgentImages::logo($agent);
        $hasOffice = (bool) $agent->theAgtOffice;

        // the "Other Known Emails" section (empty, and the setup SQL shown, until the table exists)
        $knownEmails          = AgentKnownEmails::for($agent->id);
        $knownEmailsAvailable = AgentKnownEmails::available();

        return view('admin.agents.show', compact('agent', 'flyerCount', 'campaignCount', 'campaignsInQueue', 'orders', 'photo', 'logo', 'hasOffice', 'sameEmailAccounts', 'deleteBlockers', 'knownEmails', 'knownEmailsAvailable'));

    }

    /**
     * Add an "other known email" to an agent by hand (POST): an address this agent is known by
     * besides their login and contact emails. It is only a record - it is not a login.
     */
    public function agentKnownEmailAdd(Request $request, $id)
    {
        $agent = Propagent::findOrFail($id);
        $back  = fn () => redirect()->to(route('admin.agentView', $agent->id) . '#known-emails');

        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'note'  => ['nullable', 'string', 'max:120'],
        ], [
            'email.required' => 'Enter the email address to add.',
            'email.email'    => 'That is not a valid email address.',
        ]);

        if (!AgentKnownEmails::available()) {
            return $back()->withErrors(['knownEmail' => 'The "other known emails" table has not been created yet - run the SQL shown in this section first.']);
        }

        $email = AgentKnownEmails::normalize($data['email']);

        if ($email === null) {
            return $back()->withErrors(['knownEmail' => 'That is not a valid email address.'])->withInput();
        }

        if (array_key_exists($email, AgentKnownEmails::emailsOf($agent))) {
            return $back()->withErrors(['knownEmail' => 'That is already this agent\'s login, contact or username email.'])->withInput();
        }

        $row = \App\Models\Core\AgentKnownEmail::firstOrCreate(
            ['propagent_id' => $agent->id, 'email' => $email],
            ['source' => mb_substr('Added by an admin' . (filled($data['note'] ?? null) ? ': ' . trim($data['note']) : ''), 0, 255), 'created_at' => now()]
        );

        return $back()->with('status', $row->wasRecentlyCreated ? "Saved {$email} under Other Known Emails." : "{$email} was already listed.");
    }

    /** Remove one of an agent's "other known emails" (POST). {id} is the agent; "row" is the record. */
    public function agentKnownEmailRemove(Request $request, $id)
    {
        $agent = Propagent::findOrFail($id);
        $back  = fn () => redirect()->to(route('admin.agentView', $agent->id) . '#known-emails');

        $data = $request->validate(['row' => ['required', 'integer']]);

        if (!AgentKnownEmails::available()) {
            return $back()->withErrors(['knownEmail' => 'The "other known emails" table has not been created yet.']);
        }

        $row = \App\Models\Core\AgentKnownEmail::where('propagent_id', $agent->id)->where('id', (int) $data['row'])->first();

        if (!$row) {
            return $back()->withErrors(['knownEmail' => 'That email is not on this agent.']);
        }

        $row->delete();

        return $back()->with('status', "Removed {$row->email} from Other Known Emails.");
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

        $before = $agent->startDate;

        $agent->startDate = $validated['startDate'];
        $agent->save();

        // A start date is meant to be the first-purchase date and stay put, so any change to one
        // is recorded (who, which agent, before and after).
        Log::info('Admin set an agent start date', [
            'admin_id' => Auth::guard('admin')->id(),
            'agent_id' => $agent->id,
            'before'   => $before ? \Illuminate\Support\Carbon::parse($before)->format('Y-m-d') : null,
            'after'    => $validated['startDate'],
            'ip'       => $request->ip(),
        ]);

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

        // The old login email is not thrown away: it is saved under Other Known Emails on each account
        // (before it is overwritten below), so an agent can still be found by it. If the table isn't
        // there yet the change still goes ahead - a locked-out agent must not be stuck on it - and the
        // message says the old address was not saved (it is in the log either way).
        $keepOld    = AgentKnownEmails::available();
        $oldSaved   = false;

        DB::transaction(function () use ($targets, $new, $keepOld, &$oldSaved) {
            foreach ($targets as $account) {
                AgentTime::apply($account);

                if ($keepOld) {
                    $oldSaved = AgentKnownEmails::save($account->id, $account->xxAgtUname, 'Previous login email (changed by an admin)') || $oldSaved;
                }

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

        // if the new login was one of these accounts' other known emails it is now their login, not an "other"
        AgentKnownEmails::forget($targets->pluck('id')->all(), $new);

        $status = "Login email changed to {$new} for {$count} " . ($count === 1 ? 'account' : 'accounts')
            . ' and the old password cleared.'
            . ($oldSaved
                ? " The old login email ({$old}) is kept under Other Known Emails."
                : (!$keepOld ? " The old login email ({$old}) was NOT saved: the Other Known Emails table has not been created yet (the SQL is on the agent's page)." : ''));

        if ($result === 'sent') {
            return $back()->with('status', $status . ' A link to create a new password was emailed to ' . $new . '.');
        }

        return $back()->with('status', $status)
            ->withErrors(['loginEmail' => 'The password link was NOT sent (' . $result . '). Use "Send password reset email" to try again.']);
    }

    /** Are the merge tools working on accounts that share a NAME (by=name) rather than a login email? */
    private function byName(Request $request): bool
    {
        return $request->input('by') === 'name';
    }

    /**
     * The accounts the merge tools treat as "the same agent": those on this agent's login email
     * (the default) or, with by=name, those that share this agent's name - the exact match the
     * "Duplicate Names" tab lists (App\Support\AgentNameDuplicates).
     */
    private function mergeGroup(Propagent $agent, Request $request)
    {
        return $this->byName($request)
            ? AgentNameDuplicates::accountsFor($agent)
            : AgentPasswords::accountsForEmail($agent->xxAgtUname);
    }

    /**
     * Two accounts with one NAME can be two different people (unlike two on one login email), so
     * every by-name action needs the admin's explicit "these are the same person". Checked here on
     * the server, not just on the page.
     */
    private function nameMergeNotConfirmed(Request $request): bool
    {
        return $this->byName($request) && !$request->boolean('confirm_same_person');
    }

    /** How the merge tools name the thing the accounts share, for their messages. */
    private function sharedWord(Request $request): string
    {
        return $this->byName($request) ? 'has this name' : 'uses this login email';
    }

    /**
     * MERGE DUPLICATE ACCOUNTS, step 1 of 2 (GET): tick flyers and pick which account
     * receives them. Only for accounts that share one login email - or, with ?by=name, one name
     * (the "Duplicate Names" tab) - if the agent has no duplicate there is nothing to merge and
     * this refuses.
     *
     * Every flyer of every account in the group is listed (deleted ones too, marked, so
     * their campaign history can travel with them); a flyer whose campaign is being
     * delivered right now can't be moved and is shown disabled.
     */
    public function agentMerge(Request $request, $id)
    {
        $agent    = Propagent::findOrFail($id);
        $byName   = $this->byName($request);
        $accounts = $this->mergeGroup($agent, $request);

        if ($accounts->count() < 2) {
            return redirect()->route('admin.agentView', $agent->id)
                ->withErrors(['merge' => 'No other account ' . $this->sharedWord($request) . ', so there is nothing to merge.']);
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

        $busy     = $this->flyersBeingDelivered($flyers->pluck('id')->all());
        $blockers = DuplicateAccounts::blockersFor($accounts);
        $known    = AgentKnownEmails::forMany($accounts->pluck('id')->map(fn ($accountId) => (int) $accountId)->all());

        $rows = $accounts->map(function ($account) use ($flyers, $busy, $blockers, $known) {
            $mine = $flyers->where('propagent_id', $account->id)->values();
            $b    = $blockers[(int) $account->id] ?? ['flyers' => 0, 'reasons' => []];

            return [
                'id'      => $account->id,
                'name'    => $account->agtFullName ?: trim(($account->agtFirst ?? '') . ' ' . ($account->agtLast ?? '')) ?: 'No name',
                'office'  => optional($account->theAgtOffice)->officeName,
                // what tells two people of one name apart (shown on a by-name merge)
                'login'   => $account->xxAgtUname,
                'email'   => $account->agtEmail,
                'phone'   => $account->agtMainPhone,
                'known'   => $known[(int) $account->id] ?? [],
                'place'   => trim(($account->agtCity ?? '') . ' ' . ($account->agtState ?? '')),
                'start'   => $account->startDate,
                'credits' => (int) ($account->remCreds ?? 0),
                // the Delete button shows only for an account with no flyers (and nothing else to lose)
                'can_delete' => DuplicateAccounts::canDelete($b),
                'reasons'    => $b['reasons'],
                'orders'     => $b['orders'] ?? 0,
                'campaigns'  => $b['campaigns'] ?? 0,
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

        return view('admin.agents.merge', [
            'agent'         => $agent,
            'byName'        => $byName,
            'nameKey'       => $byName ? AgentNameDuplicates::key($agent) : null,
            'accounts'      => $rows,
            'confirmDelete' => AdminSetting::confirmAgentDeletion(),
            'earliest'      => $this->earliestStart($accounts),
        ]);
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
        $accounts = $this->mergeGroup($agent, $request);
        $back     = fn () => redirect()->route('admin.agentMerge', [$agent->id] + ($this->byName($request) ? ['by' => 'name'] : []));

        if ($accounts->count() < 2) {
            return redirect()->route('admin.agentView', $agent->id)
                ->withErrors(['merge' => 'No other account ' . $this->sharedWord($request) . ', so there is nothing to merge.']);
        }

        if ($this->nameMergeNotConfirmed($request)) {
            return $back()->withErrors(['merge' => 'Tick the box confirming these accounts belong to the same person first - a shared name alone is not enough.']);
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
            return $back()->withErrors(['merge' => 'That account does not share ' . ($this->byName($request) ? 'this name.' : 'this login email.')]);
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

        // accounts of one NAME can have different emails: those have to be kept, so no by-name merge
        // runs until the "other known emails" table exists to keep them in
        if ($this->byName($request) && !AgentKnownEmails::available()) {
            return $back()->withErrors(['merge' => 'Nothing was moved: the "other known emails" table does not exist yet, so the old accounts\' emails could not be kept. Create it first (the SQL is on any agent\'s page, under Other Known Emails).']);
        }

        try {
            $tables = $this->flyerLinkedTables();
        } catch (\Throwable $e) {
            Log::error('Merge: could not list the flyer tables: ' . $e->getMessage());

            return $back()->withErrors(['merge' => 'Could not work out which tables hold flyer data, so nothing was moved.']);
        }

        $counts      = [];
        $emailsSaved = [];
        $destOffice  = optional($dest->theAgtOffice)->officeID;

        try {
            DB::transaction(function () use ($movable, $accounts, $dest, $tables, $destOffice, &$counts, &$emailsSaved) {
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

                    // the account these flyers came from is likely to be deleted next: keep its emails on
                    // this one (inside the same transaction - if they can't be kept, nothing moves)
                    $sourceAccount = $accounts->firstWhere('id', (int) $sourceId);

                    if ($sourceAccount && AgentKnownEmails::available()) {
                        $emailsSaved = array_merge($emailsSaved, AgentKnownEmails::remember(
                            $dest,
                            $sourceAccount,
                            'Merged from #' . $sourceAccount->id . ' ' . ($sourceAccount->agtFullName ?: trim(($sourceAccount->agtFirst ?? '') . ' ' . ($sourceAccount->agtLast ?? '')))
                        ));
                    }
                }
            });
        } catch (\Throwable $e) {
            Log::error('Merge failed, nothing moved: ' . $e->getMessage());

            return $back()->withErrors(['merge' => 'The move failed and nothing was changed. (' . $e->getMessage() . ')']);
        }

        Log::info('Admin moved flyers between duplicate accounts', [
            'admin_id'    => Auth::guard('admin')->id(),
            'matched_by'  => $this->byName($request) ? 'name (admin confirmed same person)' : 'login email',
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

        if ($emailsSaved !== []) {
            $message .= ' Kept the old account\'s email(s) under Other Known Emails: ' . implode(', ', array_unique($emailsSaved)) . '.';
        }

        $message .= $this->startDateHint($accounts, $dest);

        return $back()->with('status', $message);
    }

    /**
     * Move a duplicate account's ORDER HISTORY and CAMPAIGN HISTORY into another account
     * on the same login email (POST only) - what stops an empty account being deleted
     * once its flyers have been moved. {id} is the account they are moved OUT of;
     * "destination" is the account that receives them.
     *
     * Only for an account with no flyers left: campaign records that belong to a flyer
     * follow that flyer (see agentMergeSave), so what remains here is history with no
     * live flyer behind it. Refused while one of those campaigns is being delivered.
     * Credits are not touched - set them on the agent's page.
     */
    public function agentMoveRecords(Request $request, $id)
    {
        $source   = Propagent::findOrFail($id);
        $accounts = $this->mergeGroup($source, $request);
        $fail     = fn (string $message) => redirect()->back()->withErrors(['merge' => $message]);

        if ($this->nameMergeNotConfirmed($request)) {
            return $fail('Tick the box confirming these accounts belong to the same person first - a shared name alone is not enough.');
        }

        $data = $request->validate(['destination' => ['required', 'integer']], [
            'destination.required' => 'Choose the account to move the records into first.',
        ]);

        $dest = $accounts->firstWhere('id', (int) $data['destination']);

        if ($accounts->count() < 2 || !$dest || (int) $dest->id === (int) $source->id) {
            return $fail('Choose a different account that ' . ($this->byName($request) ? 'has the same name.' : 'uses the same login email.'));
        }

        $blockers = DuplicateAccounts::blockersFor(collect([$source]))[(int) $source->id];

        if ($blockers['flyers'] > 0) {
            return $fail('Move this account\'s flyers first - its records follow its flyers.');
        }

        $delivering = DB::table('remuserdb.propdelivnow')
            ->where('propagent_id', $source->id)
            ->whereNotNull('emStart')
            ->whereNull('emComplete')
            ->exists();

        if ($delivering) {
            return $fail('One of this account\'s campaigns is being delivered right now. Try again when it has finished.');
        }

        if ($this->byName($request) && !AgentKnownEmails::available()) {
            return $fail('Nothing was moved: the "other known emails" table does not exist yet, so this account\'s emails could not be kept. Create it first (the SQL is on any agent\'s page, under Other Known Emails).');
        }

        $counts = [];

        try {
            DB::transaction(function () use ($source, $dest, &$counts) {
                foreach (['allorders' => 'allorders', 'propdelivnow' => 'remuserdb.propdelivnow', 'propdelivs' => 'remuserdb.propdelivs'] as $label => $table) {
                    $counts[$label] = DB::table($table)
                        ->where('propagent_id', $source->id)
                        ->update(['propagent_id' => $dest->id]);
                }

                // this account is about to be emptied and deleted: keep its emails on the one receiving its history
                if (AgentKnownEmails::available()) {
                    AgentKnownEmails::remember(
                        $dest,
                        $source,
                        'Merged from #' . $source->id . ' ' . ($source->agtFullName ?: trim(($source->agtFirst ?? '') . ' ' . ($source->agtLast ?? '')))
                    );
                }
            });
        } catch (\Throwable $e) {
            Log::error('Move records failed, nothing moved: ' . $e->getMessage());

            return $fail('The move failed and nothing was changed. (' . $e->getMessage() . ')');
        }

        Log::info('Admin moved order/campaign history between duplicate accounts', [
            'admin_id' => Auth::guard('admin')->id(),
            'from'     => $source->id,
            'to'       => $dest->id,
            'rows'     => $counts,
            'ip'       => $request->ip(),
        ]);

        $name = $dest->agtFullName ?: trim(($dest->agtFirst ?? '') . ' ' . ($dest->agtLast ?? '')) ?: 'the account';

        return redirect()->back()->with('status',
            "Moved {$counts['allorders']} order(s) and " . ($counts['propdelivnow'] + $counts['propdelivs'])
            . " campaign record(s) from #{$source->id} into #{$dest->id} {$name}."
            . $this->startDateHint($accounts, $dest)
        );
    }

    /** [Y-m-d, account id] of the earliest start date among these accounts, or null when none has one. */
    private function earliestStart($accounts): ?array
    {
        $best = null;

        foreach ($accounts as $account) {
            if (!$account->startDate) {
                continue;
            }

            $date = \Illuminate\Support\Carbon::parse($account->startDate)->format('Y-m-d');

            if ($best === null || $date < $best[0]) {
                $best = [$date, (int) $account->id];
            }
        }

        return $best;
    }

    /** A sentence pointing out that the receiving account started later than another one (so it needs backdating). */
    private function startDateHint($accounts, $dest): string
    {
        $earliest = $this->earliestStart($accounts);

        if (!$earliest) {
            return '';
        }

        $current = $dest->startDate ? \Illuminate\Support\Carbon::parse($dest->startDate)->format('Y-m-d') : null;

        if ($current !== null && $current <= $earliest[0]) {
            return '';
        }

        return ' Its start date ' . ($current ? \Illuminate\Support\Carbon::parse($current)->format('m/d/Y') : '(none)')
            . " is later than #{$earliest[1]}'s " . \Illuminate\Support\Carbon::parse($earliest[0])->format('m/d/Y')
            . ' - use "Backdate start date" to match the original.';
    }

    /**
     * Backdate the receiving account's start date to the EARLIEST one in the group (POST only).
     * When a newer duplicate is kept, it has to carry the original account's start date. The date
     * is worked out here from the accounts themselves, never taken from the page, and only ever
     * moves earlier.
     */
    public function agentMergeStartDate(Request $request, $id)
    {
        $agent    = Propagent::findOrFail($id);
        $accounts = $this->mergeGroup($agent, $request);
        $fail     = fn (string $message) => redirect()->back()->withErrors(['merge' => $message]);

        if ($this->nameMergeNotConfirmed($request)) {
            return $fail('Tick the box confirming these accounts belong to the same person first - a shared name alone is not enough.');
        }

        $data = $request->validate(['destination' => ['required', 'integer']], [
            'destination.required' => 'Choose the account whose start date should change first.',
        ]);

        $dest = $accounts->firstWhere('id', (int) $data['destination']);

        if ($accounts->count() < 2 || !$dest) {
            return $fail('Choose an account that ' . ($this->byName($request) ? 'has this name.' : 'uses this login email.'));
        }

        $earliest = $this->earliestStart($accounts);

        if (!$earliest) {
            return $fail('None of these accounts has a start date to copy.');
        }

        $current = $dest->startDate ? \Illuminate\Support\Carbon::parse($dest->startDate)->format('Y-m-d') : null;

        if ($current !== null && $current <= $earliest[0]) {
            return redirect()->back()->with('status', "#{$dest->id} already has the earliest start date in this group.");
        }

        AgentTime::apply($dest);

        $dest->startDate = $earliest[0];
        $dest->save();

        Log::info('Admin backdated a start date while merging duplicate accounts', [
            'admin_id' => Auth::guard('admin')->id(),
            'agent_id' => $dest->id,
            'before'   => $current,
            'after'    => $earliest[0],
            'from'     => $earliest[1],
            'ip'       => $request->ip(),
        ]);

        return redirect()->back()->with('status',
            "Start date for #{$dest->id} set to " . \Illuminate\Support\Carbon::parse($earliest[0])->format('m/d/Y')
            . " (the earliest in this group, from #{$earliest[1]})."
        );
    }

    /**
     * Delete a leftover duplicate account (POST only).
     *
     * Only for an account that shares its login email with another account (never the
     * last one on an email), and only once it has NO flyers - deleted flyers count, they
     * still hold campaign history (move them first with the merge page). It also refuses
     * an account with credits, order history or campaign history, since deleting those
     * would destroy real records. All of it is re-checked here, not just on the page.
     *
     * Like the existing agent delete, this removes the agent's row (Propagent isn't
     * soft-deleting); its unused password links go with it. Uploaded photo / logo files
     * are left on disk.
     */
    public function agentDeleteDuplicate(Request $request, $id)
    {
        $agent  = Propagent::findOrFail($id);
        $others = $this->mergeGroup($agent, $request)
            ->reject(fn ($account) => (int) $account->id === (int) $agent->id)
            ->values();

        $fail = fn (string $message) => redirect()->back()->withErrors(['deleteAccount' => $message]);

        if ($this->nameMergeNotConfirmed($request)) {
            return $fail('Not deleted: confirm these accounts belong to the same person first - a shared name alone is not enough. (Deleting also removes this account\'s login.)');
        }

        if ($others->isEmpty()) {
            return $fail('Not deleted: no other account ' . $this->sharedWord($request) . ', so this is the agent\'s only account.');
        }

        // worked out before the delete: where a by-name delete returns to
        $nameKey = $this->byName($request) ? AgentNameDuplicates::key($agent) : null;

        $blockers = DuplicateAccounts::blockersFor(collect([$agent]))[(int) $agent->id];

        if ($blockers['flyers'] > 0) {
            return $fail('Not deleted: this account still has ' . $blockers['flyers'] . ' ' . ($blockers['flyers'] === 1 ? 'flyer' : 'flyers') . ' (deleted ones count). Move them first.');
        }

        if ($blockers['reasons'] !== []) {
            return $fail('Not deleted: this account has ' . implode(', ', $blockers['reasons']) . '.');
        }

        $name = $agent->agtFullName ?: trim(($agent->agtFirst ?? '') . ' ' . ($agent->agtLast ?? '')) ?: 'No name';

        // The account's emails must not vanish with it: save them on the account that stays - the one
        // chosen on the merge page (?keep=), else the one with the most flyers. A by-name delete
        // refuses when they can't be saved; a by-login one goes ahead (it shares that login email).
        $keeper     = $others->firstWhere('id', (int) $request->input('keep')) ?: $this->primaryAccount($others);
        $savedMails = [];

        if (AgentKnownEmails::available()) {
            try {
                $savedMails = AgentKnownEmails::remember($keeper, $agent, 'Merged from #' . $agent->id . ' ' . $name);
            } catch (\Throwable $e) {
                Log::error('Could not keep the emails of duplicate account ' . $agent->id . ': ' . $e->getMessage());

                return $fail('Not deleted: this account\'s emails could not be saved on the account that stays. (' . $e->getMessage() . ')');
            }
        } elseif ($this->byName($request)) {
            return $fail('Not deleted: the "other known emails" table does not exist yet, so this account\'s emails could not be kept. Create it first (the SQL is on any agent\'s page, under Other Known Emails).');
        }

        $agent->delete();
        AgentNameDuplicates::forgetCount();   // a shared name may have just been resolved

        // Tidy the account's unused password links. Best effort: the table comes from the
        // password-reset SQL, and deleting an account must not depend on it existing.
        try {
            \App\Models\Core\AgentPasswordReset::where('propagent_id', $agent->id)->delete();
        } catch (\Throwable $e) {
            Log::warning('Could not clear password links for deleted agent ' . $agent->id . ': ' . $e->getMessage());
        }

        Log::info('Admin deleted a duplicate agent account', [
            'admin_id' => Auth::guard('admin')->id(),
            'matched_by' => $this->byName($request) ? 'name (admin confirmed same person)' : 'login email',
            'agent_id' => $agent->id,
            'name'     => $name,
            'email'    => $agent->xxAgtUname,
            'ip'       => $request->ip(),
        ]);

        $message = "Deleted account #{$agent->id} {$name}.";

        if ($savedMails !== []) {
            $message .= ' Its email(s) (' . implode(', ', $savedMails) . ') are kept under Other Known Emails on #' . $keeper->id . ' ' . ($keeper->agtFullName ?: trim(($keeper->agtFirst ?? '') . ' ' . ($keeper->agtLast ?? ''))) . '.';
        }

        // Where next: still duplicates left on this email -> stay in the tool; otherwise the list.
        // The list link carries the group's anchor, so the page scrolls back to where you were.
        if ($this->byName($request)) {
            $to = ($others->count() >= 2 && $request->input('from') === 'merge')
                ? route('admin.agentMerge', [$others->first()->id, 'by' => 'name'])
                : url('/admin/agents?duplicateNames=1') . ($nameKey ? '#' . AgentNameDuplicates::anchor($nameKey) : '');
        } elseif ($others->count() >= 2 && $request->input('from') === 'merge') {
            $to = route('admin.agentMerge', $others->first()->id);
        } else {
            $to = url('/admin/agents?duplicates=1') . '#' . AgentPasswords::groupAnchor($agent->xxAgtUname);
        }

        return redirect($to)->with('status', $message);
    }

    /** Of these accounts, the one to treat as the agent's main one: the most flyers (deleted ones count), then the oldest. */
    private function primaryAccount($accounts)
    {
        $counts = Propflyer::withTrashed()
            ->whereIn('propagent_id', $accounts->pluck('id')->all())
            ->selectRaw('propagent_id, COUNT(*) as total')
            ->groupBy('propagent_id')
            ->pluck('total', 'propagent_id');

        return $accounts->sortBy(fn ($account) => [-(int) ($counts[$account->id] ?? 0), (int) $account->id])->first();
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
            ? "Authorized {$approved} " . ($approved === 1 ? 'area' : 'areas') . '.'
            : 'Nothing was waiting to be authorized on this flyer.';

        return redirect()->route('admin.flyerCamps', $flyerId)->with('status', $message);
    }

    /**
     * Undo campaignApprove(): put every approved-but-not-started request on
     * this flyer back to awaiting approval (authorized = 0). Anything the
     * mail system has already started or finished is left alone.
     */
    public function campaignUnapprove($flyerId)
    {
        // The update stamps updated_at - in the flyer's agent's timezone.
        AgentTime::apply(Propagent::find(Propflyer::whereKey($flyerId)->value('propagent_id')));

        $unapproved = Propdelivnow::where('propflyer_id', $flyerId)
            ->whereNull('emStart')
            ->whereNull('emComplete')
            ->where('authorized', 1)
            ->update(['authorized' => 0]);

        $message = $unapproved
            ? "Unauthorized {$unapproved} " . ($unapproved === 1 ? 'area' : 'areas') . '. ' . ($unapproved === 1 ? 'It is' : 'They are') . ' waiting to be authorized again.'
            : 'Nothing authorized and waiting to send on this flyer.';

        return redirect()->route('admin.flyerCamps', $flyerId)->with('status', $message);
    }

    /**
     * Set the email subject on every campaign of this flyer that has not
     * been completed yet (waiting and in progress), from the Email Subject
     * card. Completed campaigns keep the subject they were actually sent
     * with; a single one can still be corrected from its own card
     * (campaignSubject()).
     */
    public function flyerSubject(Request $request, $flyerId)
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
        ]);

        abort_unless(Propflyer::whereKey($flyerId)->exists(), 404, 'This flyer has been deleted.');

        // The update stamps updated_at - in the flyer's agent's timezone.
        AgentTime::apply(Propagent::find(Propflyer::whereKey($flyerId)->value('propagent_id')));

        $updated = Propdelivnow::where('propflyer_id', $flyerId)
            ->whereNull('emComplete')
            ->update(['emSubject' => $validated['subject']]);

        $message = $updated
            ? "Subject updated on {$updated} " . ($updated === 1 ? 'campaign' : 'campaigns') . '.'
            : 'There are no waiting or in-progress campaigns to update.';

        return redirect()->route('admin.flyerCamps', $flyerId)->with('status', $message);
    }

    /**
     * Set the email subject on ONE campaign (its own Edit panel), whatever
     * its stage. Blank clears it, like the agent's own send form allows.
     */
    public function campaignSubject(Request $request, $cid)
    {
        $validated = $request->validate([
            'subject' => ['nullable', 'string', 'max:255'],
        ]);

        $campaign = Propdelivnow::findOrFail($cid);

        // The save stamps updated_at - in the flyer's agent's timezone.
        AgentTime::apply(Propagent::find($campaign->propagent_id));

        $campaign->emSubject = $validated['subject'] ?? null;
        $campaign->save();

        return redirect()->route('admin.flyerCamps', $campaign->propflyer_id)
            ->with('status', 'Subject updated.');
    }

    /**
     * Authorize or unauthorize ONE waiting campaign (the toggle on its status
     * badge). The wanted state is posted (1 / 0) rather than flipped, so a
     * double click or a stale page can't flip it the wrong way. A campaign the
     * mail system has already started or finished is left alone.
     */
    public function campaignAuthorize(Request $request, $cid)
    {
        $validated = $request->validate([
            'authorized' => ['required', 'in:0,1'],
        ]);

        $campaign = Propdelivnow::findOrFail($cid);
        $back     = redirect()->route('admin.flyerCamps', $campaign->propflyer_id);

        if ($campaign->emStart || $campaign->emComplete) {
            return $back->withErrors(['That campaign has already started, so it can no longer be authorized or unauthorized.']);
        }

        // The save stamps updated_at - in the flyer's agent's timezone.
        AgentTime::apply(Propagent::find($campaign->propagent_id));

        $campaign->authorized = (int) $validated['authorized'];
        $campaign->save();

        $name = $campaign->emArea_display ?: $campaign->emArea;

        return $back->with('status', $name . ($campaign->authorized ? ' is authorized.' : ' is no longer authorized.'));
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
            "Added {$area['label']} (" . number_format($totalEmails) . ' contacts) at no charge. It is waiting to be authorized with this flyer\'s other areas.'
        );
    }

    /**
     * Admin-only: correct a campaign's requested / started / completed dates
     * (for campaigns entered by hand from the old server). The dates ARE the
     * campaign's status - no start = waiting, a start = in progress, a
     * completion = completed - so editing them moves it between the lists on
     * the flyer page. Values are plain wall-clock times, like the legacy data.
     */
    public function campaignDates(Request $request, $cid)
    {
        $campaign = Propdelivnow::findOrFail($cid);

        $format = 'date_format:Y-m-d\TH:i:s,Y-m-d\TH:i';

        $validated = $request->validate([
            'emRequest'  => ['required', $format],
            'emStart'    => ['nullable', $format],
            'emComplete' => ['nullable', $format],
        ], [
            'emRequest.required' => 'A requested date is required.',
            '*.date_format'      => 'That date was not understood.',
        ]);

        $requested = Carbon::parse($validated['emRequest']);
        $started   = filled($validated['emStart'] ?? null) ? Carbon::parse($validated['emStart']) : null;
        $completed = filled($validated['emComplete'] ?? null) ? Carbon::parse($validated['emComplete']) : null;

        $problem = null;

        if ($completed && !$started) {
            $problem = 'A completed campaign needs a started date too.';
        } elseif ($started && $started->lt($requested)) {
            $problem = 'The started date cannot be before the requested date.';
        } elseif ($completed && $completed->lt($started)) {
            $problem = 'The completed date cannot be before the started date.';
        }

        if ($problem) {
            return redirect()->route('admin.flyerCamps', $campaign->propflyer_id)
                ->withErrors([$problem])
                ->withInput();
        }

        // The save stamps updated_at - in the flyer's agent's timezone.
        AgentTime::apply(Propagent::find($campaign->propagent_id));

        $campaign->emRequest  = $requested->format('Y-m-d H:i:s');
        $campaign->emStart    = $started?->format('Y-m-d H:i:s');
        $campaign->emComplete = $completed?->format('Y-m-d H:i:s');
        $campaign->save();

        $message = 'Dates updated.' . $this->raiseLastDelivery($campaign->propflyer_id, $completed);

        return redirect()->route('admin.flyerCamps', $campaign->propflyer_id)
            ->with('status', $message);
    }

    /**
     * The flyer's "last sent" (propflyerstats.xLastDeliveryDate) is normally
     * set by the mailer when a delivery completes. When an admin dates a
     * campaign complete by hand, move it FORWARD to that date if it is newer
     * than what is stored - never backwards, so correcting or clearing an
     * older campaign can't undo a genuinely later delivery. Returns a sentence
     * for the status message ('' when nothing changed).
     */
    private function raiseLastDelivery($flyerId, ?Carbon $completed): string
    {
        if (!$completed) {
            return '';
        }

        if (!Propflyerstat::where('propflyer_id', $flyerId)->exists()) {
            return ' The flyer has no stats record, so its last sent date was not set.';
        }

        // raw value: legacy rows can hold NULL or a zero date
        $stored = Propflyerstat::where('propflyer_id', $flyerId)->value('xLastDeliveryDate');

        try {
            $storedDate = $stored ? Carbon::parse($stored) : null;
        } catch (\Throwable $e) {
            $storedDate = null;
        }

        if ($storedDate && $storedDate->year > 1970 && !$completed->gt($storedDate)) {
            return '';
        }

        Propflyerstat::where('propflyer_id', $flyerId)
            ->update(['xLastDeliveryDate' => $completed->format('Y-m-d H:i:s')]);

        return ' Flyer last sent updated to ' . $completed->format('M j, Y') . '.';
    }

    /**
     * QUICK EMAIL: send a copy of this flyer's email, with its current email
     * subject, to the Test email address in the admin Settings.
     */
    public function flyerQuickEmail($flyerId)
    {
        $to = AdminSetting::trialEmail();

        if ($to === '') {
            return redirect()->route('admin.flyerCamps', $flyerId)
                ->withErrors(['There is no email address in Settings yet. Add the Test email address there, or use Custom Email.']);
        }

        return $this->sendFlyerEmail($flyerId, $to, $this->currentEmailSubject($flyerId));
    }

    /**
     * CUSTOM EMAIL: the same flyer email, sent to an address and with a subject
     * the admin has typed in (both are prefilled with the Settings address and
     * the current subject, and only sent once submitted).
     */
    public function flyerCustomEmail(Request $request, $flyerId)
    {
        $validated = $request->validate([
            'to'      => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
        ], [
            'to.required'      => 'Enter the email address to send to.',
            'to.email'         => 'That is not a valid email address.',
            'subject.required' => 'Enter an email subject.',
        ]);

        return $this->sendFlyerEmail($flyerId, $validated['to'], $validated['subject']);
    }

    /**
     * The subject a flyer's email currently has: the newest campaign's, or - when
     * none has one - the flyer's address, so a copy is never sent without a subject.
     */
    private function currentEmailSubject($flyerId): string
    {
        $subject = Propdelivnow::where('propflyer_id', $flyerId)
            ->whereNotNull('emSubject')
            ->where('emSubject', '!=', '')
            ->orderByDesc('emRequest')
            ->value('emSubject');

        return $subject ?: (string) Propflyer::whereKey($flyerId)->value('xFullStreet');
    }

    /**
     * Render the flyer as its EMAIL (the same templates as the on-screen flyer, in
     * their email mode - absolute image / "view online" links, no editing hooks)
     * and send it to one address with the given subject. Nothing is charged, no
     * campaign is created, and nobody is emailed but $to.
     */
    private function sendFlyerEmail($flyerId, string $to, string $subject)
    {
        abort_unless(Propflyer::whereKey($flyerId)->exists(), 404, 'This flyer has been deleted.');

        $back = redirect()->route('admin.flyerCamps', $flyerId);

        try {
            // the flyer with everything its template reads ($propInfo, needs $flyerId)
            include app_path('queries/flyerdetails.php');

            $html = view('admin.flyer.emailBody', [
                'propInfo' => $propInfo,
                'subject'  => $subject,
            ])->render();

            // A plain-text version travels with the HTML one (an HTML-only message is a
            // small mark against it with mail filters, Gmail's included).
            $address = trim($propInfo->xCity . ', ' . $propInfo->state . ' ' . ($propInfo->xZip ?: $propInfo->xxZip));

            $text = $subject . "\n\n"
                . $propInfo->xFullStreet . "\n"
                . $address . "\n"
                . ($propInfo->xListPrice ? '$' . number_format($propInfo->xListPrice) . "\n" : '')
                . ($propInfo->url_slug ? "\nView this flyer online: " . url('/homedetails/' . $propInfo->url_slug) . "\n" : '');

            Mail::html($html, function ($message) use ($to, $subject, $text) {
                $message->to($to)->subject($subject)->text($text);
            });
        } catch (\Throwable $e) {
            report($e);

            return $back->withErrors(['The email could not be sent: ' . $e->getMessage()]);
        }

        return $back->with('status', "Sent a copy of this flyer email to {$to} with the subject \"{$subject}\".");
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