<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class adminController extends Controller
{
    /** Max depth for /admin/{segments?} */
    private const MAX_SEGMENTS = 5;

    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function segments(Request $request)
    {
        $segmentsPath = trim((string) $request->route('segments', ''), '/');    
        $parts        = ($segmentsPath === '') ? [] : explode('/', $segmentsPath);

        // Prepend 'admin' so dynamic_index resolves to admin.* views
        array_unshift($parts, 'admin');

        $result = require __DIR__ . '/../parts/dynamic_index.php';

        if ($result instanceof \Symfony\Component\HttpFoundation\Response) {
            return $result;
        }

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
        return redirect()->back();

    }

    public function agentLogin($id)
    {

        include(app_path().'/admin/agent/login.php');
        return redirect('/member/dashboard');
    }
}