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


        return view('admin.bounces.view', [
            'messageNumber' => $messageNumber,
            'overview' => $overview,
            'headers' => $headers,
            'rawBody' => $rawBody,
            'structure' => $structure,
            'partsReport' => $partsReport,
            'body' => $body,
            'bodyType' => $bodyType,
        ]);
    }

    public function groupDelete(Request $request)
    {
        dd($request->input('messages', []));
    }
}