<?php

namespace App\Http\Controllers\member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class memberController extends Controller
{
    /** Max depth for /member/{segments?} */
    private const MAX_SEGMENTS = 5;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:member,admin,super');
    }

    public function segments(Request $request)
    {
        // Route param: "segments" separates by section
        $segmentsPath = trim((string) $request->route('segments', ''), '/');    
        $parts        = ($segmentsPath === '') ? [] : explode('/', $segmentsPath);

        //sets view names & app files
        dd("member $parts");
        
    }   

}