<?php

$mailboxPath = '{mail.realtye-mails.com:110/pop3/notls}INBOX';

$mailbox = @imap_open(
    $mailboxPath,
    'members@realtye-mails.com',
    env('BOUNCEBOX_PASSWORD')
);

if (!$mailbox) {

    $data = [
        'connected' => false,
        'mailboxPath' => $mailboxPath,
        'errors' => imap_errors(),
        'last_error' => imap_last_error(),
    ];

    return;
}

$total = imap_num_msg($mailbox);

$perPage = 50;
$page = max((int) request()->get('page', 1), 1);

$latestMessageNumber = $total - (($page - 1) * $perPage);

if ($latestMessageNumber < 1) {
    $latestMessageNumber = 1;
}

$oldestMessageNumber = max(
    $latestMessageNumber - $perPage + 1,
    1
);

$messages = [];

for ($i = $latestMessageNumber; $i >= $oldestMessageNumber; $i--) {

    $overview = imap_fetch_overview($mailbox, $i, 0)[0] ?? null;

    if (!$overview) {
        continue;
    }

    $messages[] = [

        'messageNumber' => $i,

        'subject' => trim(
            imap_utf8($overview->subject ?? '(No subject)')
        ),

        'from' => trim(
            imap_utf8($overview->from ?? '')
        ),

        'date' => $overview->date ?? '',

        'seen' => !empty($overview->seen),

    ];
}

imap_close($mailbox);

$data = [

    'connected' => true,

    'count' => $total,

    'messages' => $messages,

    'page' => $page,

    'perPage' => $perPage,

    'hasOlder' => $oldestMessageNumber > 1,

    'hasNewer' => $page > 1,

];