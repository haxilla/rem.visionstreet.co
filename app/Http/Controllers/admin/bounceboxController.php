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

        $htmlBody = '';
        $textBody = '';

        $decodePart = function ($content, $encoding) {

            if ($encoding == 3) {
                return base64_decode($content);
            }

            if ($encoding == 4) {
                return quoted_printable_decode($content);
            }

            return $content;
        };

        if (!empty($structure->parts)) {

            foreach ($structure->parts as $index => $part) {

                $partNumber = $index + 1;

                $subtype = strtoupper($part->subtype ?? '');

                $content = imap_fetchbody(
                    $mailbox,
                    $messageNumber,
                    $partNumber
                );

                $content = $decodePart(
                    $content,
                    $part->encoding ?? 0
                );

                if ($subtype === 'HTML' && trim($htmlBody) === '') {
                    $htmlBody = $content;
                }

                if ($subtype === 'PLAIN' && trim($textBody) === '') {
                    $textBody = $content;
                }
            }

        } else {

            $content = imap_body($mailbox, $messageNumber);

            $content = $decodePart(
                $content,
                $structure->encoding ?? 0
            );

            if (strtoupper($structure->subtype ?? '') === 'HTML') {
                $htmlBody = $content;
            } else {
                $textBody = $content;
            }
        }

        imap_close($mailbox);

        $bodyType = trim($htmlBody) !== ''
            ? 'html'
            : 'text';

        $body = trim($htmlBody) !== ''
            ? $htmlBody
            : $textBody;

        return view('admin.bouncebox.view', [

            'messageNumber' => $messageNumber,

            'overview' => $overview,

            'body' => $body,

            'bodyType' => $bodyType,

        ]);
    }

    public function groupDelete(Request $request)
    {
        dd($request->input('messages', []));
    }
}