<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class bounceboxController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function view($messageNumber)
    {
        dd($messageNumber);
    }

    public function groupDelete(Request $request)
    {
        dd($request->input('messages', []));
    }
}