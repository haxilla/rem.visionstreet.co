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
        $messageNumber = (int) $messageNumber;

        $mailboxPath = '{mail.realtye-mails.com:110/pop3/notls}INBOX';

        $mailbox = @imap_open(
            $mailboxPath,
            'members@realtye-mails.com',
            env('BOUNCEBOX_PASSWORD')
        );

        if (!$mailbox) {
            dd([
                'mailboxPath' => $mailboxPath,
                'errors' => imap_errors(),
                'last_error' => imap_last_error(),
            ]);
        }

        $overview = imap_fetch_overview($mailbox, $messageNumber, 0)[0] ?? null;
        $body = imap_body($mailbox, $messageNumber);

        imap_close($mailbox);

        return view('admin.bounces.view', [
            'messageNumber' => $messageNumber,
            'overview' => $overview,
            'body' => $body,
        ]);
    }

    public function groupDelete(Request $request)
    {
        dd($request->input('messages', []));
    }
}