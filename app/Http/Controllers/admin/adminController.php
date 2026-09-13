<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Core\Propflyer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    public function agentLogin($id)
    {

        include(app_path().'/admin/agent/login.php');
        return redirect('/member/dashboard');
    }

    public function returnToAdmin()
    {

        include(app_path().'/admin/agent/returnToAdmin.php');
        return redirect('/admin/dashboard');
    }

    public function flyerCamps($flyerId)
    {

        include(app_path().'/admin/flyer/camps.php');
        return view('admin.flyer.camps', [
            'data' => $data
        ]);

    }

    public function flyerEdit($flyerId)
    {
        $flyer = Propflyer::findOrFail($flyerId);

        // Impersonate the flyer's owning agent (same login.php used by
        // agentLogin), then hand off to the member wizard's own
        // smart-resume logic so it lands on whichever step this flyer
        // is actually at, instead of guessing a step here.
        $id = $flyer->propagent_id;
        include(app_path().'/admin/agent/login.php');

        return redirect('/member/flyer/resume?flyerId='.$flyer->id);
    }
}