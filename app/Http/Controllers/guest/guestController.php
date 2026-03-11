<?php

namespace App\Http\Controllers\guest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class guestController extends Controller
{

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

        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended('/admin/dashboard');
        }

        return back()->withErrors([
            'username' => 'Invalid credentials.',
        ])->onlyInput('username');
    }

    public function memberLoginForm()
    {
        return view('member.login');
    }


    public function memberLogin(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('member')->attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended('/member/dashboard');
        }

        return back()->withErrors([
            'username' => 'Invalid credentials.',
        ])->onlyInput('username');
    }

    public function memberLoginModal(Request $request)
    {
       return $request->ajax()
        // Do not visit modal route directly, 
        // Redirect to index with modal query parameter
        ? view('member.login-modal')
        : redirect('/?modal=login');
    }

    public function index(){

        require app_path('code/users_rebuild.php');
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
            $segmentsPath = trim((string) $request->route('segments', ''), '/');    
            $parts        = ($segmentsPath === '') ? [] : explode('/', $segmentsPath);
            dd($parts);

    }

}