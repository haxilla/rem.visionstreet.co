<?php

namespace App\Http\Controllers\guest;
use App\Http\Controllers\Controller;
use App\Mail\AgentPasswordChangedMail;
use App\Models\Core\Propagent;
use App\Support\AgentPasswords;
use App\Support\AgentTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class guestController extends Controller
{

    private const MAX_SEGMENTS = 5;

    private function verifyRecaptcha(Request $request): bool
    {
        $result = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret'   => config('services.recaptcha.secret_key'),
            'response' => $request->input('recaptcha_token'),
            'remoteip' => $request->ip(),
        ])->json();

        return ($result['success'] ?? false) && ($result['score'] ?? 0) >= 0.5;
    }


    public function adminLoginForm()
    {
        if (Auth::guard('admin')->check()) {
            return redirect('/admin/dashboard');
        }

        return view('admin.login');
    }

    public function adminLogin(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!$this->verifyRecaptcha($request)) {
            return back()->withErrors([
                'username' => 'Verification failed, please try again.',
            ])->onlyInput('username');
        }

        if (Auth::guard('admin')->attempt([
            'adminEmail' => $credentials['username'],
            'password'   => $credentials['password'],
        ], $request->boolean('remember'))) {

            $request->session()->regenerate();
            return redirect()->intended('/admin/dashboard');
        }

        return back()->withErrors([
            'username' => 'Invalid credentials.',
        ])->onlyInput('username');
    }

    public function memberLoginForm()
    {
        if (Auth::guard('member')->check()) {
            return redirect('/member/dashboard');
        }

        include app_path('member/login.php');
        return view('member.login',
        [
            'data'     => $data,
        ]);
    }


    public function memberLogin(Request $request)
    {
        $credentials = $request->validate([
            'xxAgtUname' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!$this->verifyRecaptcha($request)) {
            return back()->withErrors([
                'xxAgtUname' => 'Verification failed, please try again.',
            ])->onlyInput('xxAgtUname');
        }

        $guard = Auth::guard('member');

        // Check the email + password WITHOUT signing in yet: an agent who hasn't
        // set a new password on this site is not let in, even with the right one.
        if ($guard->validate($credentials)) {
            $agent = $guard->getLastAttempted();

            // A blocked agent can't sign in even with the right password
            // (propagents.loginBlocked; see also EnsureAgentNotBlocked,
            // which ends sessions that are already open).
            if ((int) ($agent->loginBlocked ?? 0) === 1) {
                return back()->withErrors([
                    'xxAgtUname' => 'Your account has been blocked. Please contact support.',
                ])->onlyInput('xxAgtUname');
            }

            // First visit to the new site: the old password proved who they are
            // enough to send the link, and the link (to the mailbox on file) is
            // what lets them set a new one. See AgentPasswords.
            if (AgentPasswords::resetRequired($agent)) {
                $result = AgentPasswords::sendLink($agent, 'login', $request->ip());

                if (in_array($result, ['noemail', 'failed'], true)) {
                    return back()->withErrors([
                        'xxAgtUname' => 'We could not email you a password link. Please contact support.',
                    ])->onlyInput('xxAgtUname');
                }

                return redirect()->route('member.login')->with(
                    'status',
                    'For your security you need to create a new password on our new site. We emailed a link to '
                    . AgentPasswords::maskEmail((string) $agent->xxAgtUname)
                    . ' - it works once and expires in ' . AgentPasswords::LINK_MINUTES . ' minutes.'
                );
            }

            $guard->login($agent);
            $request->session()->regenerate();
            return redirect()->intended('/member/dashboard');
        }

        return back()->withErrors([
            'xxAgtUname' => 'Invalid credentials.',
        ])->onlyInput('xxAgtUname');
    }

    // ---- Forgot password / set a new password (emailed one-time link) ----

    public function passwordForgotForm()
    {
        return view('member.password.forgot');
    }

    public function passwordForgot(Request $request)
    {
        $email = trim((string) $request->validate([
            'xxAgtUname' => ['required', 'email'],
        ])['xxAgtUname']);

        // The answer is the same whether or not the address belongs to an agent,
        // so this page can't be used to find out who has an account.
        try {
            $agent = Propagent::where('xxAgtUname', $email)->first();

            if ($agent) {
                AgentPasswords::sendLink($agent, 'forgot', $request->ip());
            }
        } catch (\Throwable $e) {
            Log::error('Forgot password failed: ' . $e->getMessage());
        }

        return redirect()->route('member.login')->with(
            'status',
            'If that email belongs to an account, we\'ve sent a link to create a new password. It works once and expires in '
            . AgentPasswords::LINK_MINUTES . ' minutes.'
        );
    }

    public function passwordSetForm($token)
    {
        return view('member.password.set', [
            'token'        => $token,
            'invalid'      => !AgentPasswords::findValid($token),
            'requirements' => AgentPasswords::requirements(),
        ]);
    }

    public function passwordSet(Request $request, $token)
    {
        $reset = AgentPasswords::findValid($token);
        $agent = $reset ? Propagent::find($reset->propagent_id) : null;

        if (!$reset || !$agent || (int) ($agent->loginBlocked ?? 0) === 1) {
            return view('member.password.set', [
                'token'        => $token,
                'invalid'      => true,
                'requirements' => AgentPasswords::requirements(),
            ]);
        }

        $validator = Validator::make($request->all(), [
            'password' => ['required', 'string', 'max:128', 'confirmed', AgentPasswords::rule()],
        ]);

        $validator->after(function ($v) use ($agent, $request) {
            if (!$v->errors()->has('password')
                && ($problem = AgentPasswords::personalProblem($agent, (string) $request->input('password')))) {
                $v->errors()->add('password', $problem);
            }
        });

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        DB::transaction(function () use ($agent, $request) {
            // The time the reset is recorded is the agent's own (see AgentTime).
            AgentTime::apply($agent);

            $agent->password = Hash::make($request->input('password'));

            // Until the passwordResetAt column exists there is nowhere to record it.
            if (AgentPasswords::columnAvailable($agent)) {
                $agent->passwordResetAt = AgentTime::now($agent);
            }

            $agent->save();

            AgentPasswords::voidAll($agent->id);
        });

        try {
            Mail::to($agent->xxAgtUname)->send(new AgentPasswordChangedMail($agent));
        } catch (\Throwable $e) {
            Log::error('Password-changed email failed for agent ' . $agent->id . ': ' . $e->getMessage());
        }

        return redirect()->route('member.login')->with('status', 'Your new password is set. Please sign in.');
    }

    public function memberLoginModal(Request $request)
    {
        if ($request->header('X-Pageswap') === '1') {
            return view('member.login_modal');
        }

        return redirect('/?login=1');
    }

    public function index(){

        //require app_path('code/users_rebuild.php');
        //require app_path('code/flyer_states.php');
        require app_path('code/flyer_codes.php');
        //require app_path('code/hash_passwords.php');
        //require app_path('code/flyer_slug.php');
        require app_path('public/index.php');

        //return view
        return view('public.index',
        [
            'newAdds'     => $newAdds,
            'mostViews'   => $mostViews,
            'topLuxury'   => $topLuxury,
            'memberSince' => $memberSince,
        ]);
    }

    public function segment(Request $request){

        // Route param: "segments" separates by section
        $segmentsPath = trim((string) $request->route('segment', ''), '/');    

        $parts        = ($segmentsPath === '') ? [] : explode('/', $segmentsPath);
            
        //sets view names & app files
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

    public function flyerDetail($flyerId)
    {
        include app_path('queries/flyerdetails.php');
        return view('flyers.index',compact('propInfo'));
    }


    public function publicDetails($flyerslug)
    {
        include app_path('queries/publicDetails.php');
        return view('public.details',compact('details'));
    }

}