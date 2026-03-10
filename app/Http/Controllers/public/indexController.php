<?php

namespace App\Http\Controllers\public;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class indexController extends Controller
{

public function login(Request $request)
{
    $credentials = $request->validate([
        'username' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();

        return redirect()->intended('/admin/dashboard');
    }

    return back()->withErrors([
        'email' => 'Invalid credentials.',
    ])->onlyInput('email');
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