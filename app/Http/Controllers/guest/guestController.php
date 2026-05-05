<?php

namespace App\Http\Controllers\guest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class guestController extends Controller
{    
    
    private const MAX_SEGMENTS = 5;


    public function adminLoginForm()
    {
        return view('admin.login');
    }   

    public function adminLogin(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('admin')->attempt([
            'adminEmail' => $credentials['username'],
            'password'   => $credentials['password'],
        ])) {

            /*
            return redirect()->intended('/admin/dashboard');
            */
            $request->session()->regenerate();
            return redirect('/admin/dashboard');
        }

        return back()->withErrors([
            'username' => 'Invalid credentials.',
        ])->onlyInput('username');
    }

    public function memberLoginForm()
    {
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

        if (Auth::guard('member')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/member/dashboard');
        }

        return back()->withErrors([
            'xxAgtUname' => 'Invalid credentials.',
        ])->onlyInput('xxAgtUname');
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
        require app_path('code/flyer_states.php');
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