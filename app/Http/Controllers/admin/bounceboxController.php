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
        $structure = imap_fetchstructure($mailbox, $messageNumber);

        $body = '';

        if (!empty($structure->parts)) {
            foreach ($structure->parts as $index => $part) {
                $partNumber = $index + 1;

                if (($part->subtype ?? '') === 'PLAIN') {
                    $body = imap_fetchbody($mailbox, $messageNumber, $partNumber);

                    if (($part->encoding ?? 0) == 3) {
                        $body = base64_decode($body);
                    } elseif (($part->encoding ?? 0) == 4) {
                        $body = quoted_printable_decode($body);
                    }

                    break;
                }
            }
        }

        if ($body === '') {
            $body = imap_body($mailbox, $messageNumber);
            $body = quoted_printable_decode($body);
        }

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