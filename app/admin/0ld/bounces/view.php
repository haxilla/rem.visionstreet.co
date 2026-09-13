<?php

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
$rawFullMessage = $headers . "\r\n\r\n" . $rawBody;
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
                'preview' => mb_substr(trim($decodedContent), 0, 10000),
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
        'preview' => mb_substr(trim($content), 0, 10000),
    ];
}

/*
|--------------------------------------------------------------------------
| Try to expose possible recipient emails from full bounce content
|--------------------------------------------------------------------------
*/

$rawSearchText = $rawFullMessage . "\n\n" . implode("\n\n", array_column($partsReport, 'preview'));

$possibleRecipients = [];

$patterns = [
    '/Final-Recipient:\s*rfc822;\s*([^\s<>,;]+)/i',
    '/Original-Recipient:\s*rfc822;\s*([^\s<>,;]+)/i',
    '/X-Failed-Recipients:\s*([^\s<>,;]+)/i',
    '/Diagnostic-Code:.*?([A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,})/is',
    '/Action:\s*failed.*?([A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,})/is',
    '/([A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,})/i',
];

foreach ($patterns as $pattern) {
    if (preg_match_all($pattern, $rawSearchText, $matches)) {
        foreach ($matches[1] as $match) {
            $emailMatch = strtolower(trim($match, " \t\r\n<>;,"));

            if (filter_var($emailMatch, FILTER_VALIDATE_EMAIL)) {
                $possibleRecipients[$emailMatch] = true;
            }
        }
    }
}

$possibleRecipients = array_keys($possibleRecipients);
sort($possibleRecipients);

imap_close($mailbox);

$bodyType = trim($htmlBody) !== '' ? 'html' : 'text';
$body = trim($htmlBody) !== '' ? $htmlBody : $textBody;