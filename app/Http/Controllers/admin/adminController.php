<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Core\AdminSetting;
use App\Models\Core\Propagent;
use App\Models\Core\Propdelivnow;
use App\Models\Core\Propflyer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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


    public function agentDelete($id)
    {

        include(app_path().'/admin/agent/delete.php');
        return redirect()->back();

    }

    public function agentView($id)
    {

        include(app_path().'/admin/agent/view.php');
        return view('admin.agents.show', compact('agent', 'flyerCount', 'campaignCount', 'orders'));

    }

    /**
     * "Send password reset email" - a stand-in for now. There is no email
     * sending set up yet, so this sends NOTHING and says so plainly; the
     * button, route and CSRF-protected POST already exist so only the
     * sending itself needs adding here later.
     */
    public function agentPasswordReset($id)
    {
        $agent = Propagent::findOrFail($id);

        // TODO: when email sending exists, create a reset token / link for
        // $agent (login username = $agent->xxAgtUname) and email it here.

        return redirect()->route('admin.agentView', $agent->id)
            ->with('status', 'Password reset email is not set up yet - nothing was sent.');
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
     * member send-setup flow). The row is created already approved, so
     * it is ready for the mail system straight away.
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
        // The database's own clock, like the legacy system (see
        // save_sendsetup.php) - not the app's UTC now().
        $campaign->emRequest      = DB::selectOne('SELECT NOW() AS now_local')->now_local;
        // How the legacy system marked a free, admin-added area:
        $campaign->campLabel      = 'admin';
        $campaign->admin_add      = 1;
        $campaign->free           = 1;
        $campaign->authorized     = 1;
        $campaign->save();

        return redirect()->route('admin.flyerCamps', $flyer->id)->with(
            'status',
            "Added {$area['label']} (" . number_format($totalEmails) . ' contacts) at no charge. It is approved and ready to send.'
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