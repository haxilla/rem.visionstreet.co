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
        $headers  = imap_fetchheader($mailbox, $messageNumber);
        $rawBody  = imap_body($mailbox, $messageNumber);
        $structure = imap_fetchstructure($mailbox, $messageNumber);

        $htmlBody = '';
        $textBody = '';
        $partsReport = [];

        $decodePart = function ($content, $encoding) {
            if ($encoding == 3) {
                return base64_decode($content);
            }

            if ($encoding == 4) {
                return quoted_printable_decode($content);
            }

            return $content;
        };

        $walkParts = function ($parts, $prefix = '') use (
            &$walkParts,
            &$partsReport,
            &$htmlBody,
            &$textBody,
            $mailbox,
            $messageNumber,
            $decodePart
        ) {
            foreach ($parts as $index => $part) {
                $partNumber = $prefix === ''
                    ? (string) ($index + 1)
                    : $prefix . '.' . ($index + 1);

                $typeMap = [
                    0 => 'TEXT',
                    1 => 'MULTIPART',
                    2 => 'MESSAGE',
                    3 => 'APPLICATION',
                    4 => 'AUDIO',
                    5 => 'IMAGE',
                    6 => 'VIDEO',
                    7 => 'OTHER',
                ];

                $type = $typeMap[$part->type ?? 7] ?? 'OTHER';
                $subtype = strtoupper($part->subtype ?? '');
                $encoding = $part->encoding ?? 0;

                $filename = '';

                if (!empty($part->dparameters)) {
                    foreach ($part->dparameters as $param) {
                        if (strtolower($param->attribute ?? '') === 'filename') {
                            $filename = $param->value ?? '';
                        }
                    }
                }

                if ($filename === '' && !empty($part->parameters)) {
                    foreach ($part->parameters as $param) {
                        if (strtolower($param->attribute ?? '') === 'name') {
                            $filename = $param->value ?? '';
                        }
                    }
                }

                $content = '';

                if (($part->type ?? null) !== 1) {
                    $content = imap_fetchbody($mailbox, $messageNumber, $partNumber);
                    $decodedContent = $decodePart($content, $encoding);

                    if (($part->type ?? null) === 0 && $subtype === 'HTML' && trim($htmlBody) === '') {
                        $htmlBody = $decodedContent;
                    }

                    if (($part->type ?? null) === 0 && $subtype === 'PLAIN' && trim($textBody) === '') {
                        $textBody = $decodedContent;
                    }

                    $partsReport[] = [
                        'partNumber' => $partNumber,
                        'type' => $type,
                        'subtype' => $subtype,
                        'encoding' => $encoding,
                        'filename' => $filename,
                        'bytes' => strlen($content),
                        'preview' => mb_substr(trim($decodedContent), 0, 1500),
                    ];
                } else {
                    $partsReport[] = [
                        'partNumber' => $partNumber,
                        'type' => $type,
                        'subtype' => $subtype,
                        'encoding' => $encoding,
                        'filename' => $filename,
                        'bytes' => 0,
                        'preview' => '',
                    ];
                }

                if (!empty($part->parts)) {
                    $walkParts($part->parts, $partNumber);
                }
            }
        };

        if (!empty($structure->parts)) {
            $walkParts($structure->parts);
        } else {
            $content = $decodePart($rawBody, $structure->encoding ?? 0);

            if (strtoupper($structure->subtype ?? '') === 'HTML') {
                $htmlBody = $content;
            } else {
                $textBody = $content;
            }

            $partsReport[] = [
                'partNumber' => 'body',
                'type' => 'SINGLE',
                'subtype' => strtoupper($structure->subtype ?? ''),
                'encoding' => $structure->encoding ?? 0,
                'filename' => '',
                'bytes' => strlen($rawBody),
                'preview' => mb_substr(trim($content), 0, 1500),
            ];
        }

        imap_close($mailbox);

        $bodyType = trim($htmlBody) !== '' ? 'html' : 'text';
        $body = trim($htmlBody) !== '' ? $htmlBody : $textBody;

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