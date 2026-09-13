<?php

$messages = $_POST['messages'] ?? [];

if (empty($messages) || !is_array($messages)) {
    return redirect('/admin/bounces')
        ->with('error', 'No messages selected.');
}

$messageNumbers = [];

foreach ($messages as $message) {
    $messageNumber = (int) $message;

    if ($messageNumber > 0) {
        $messageNumbers[] = $messageNumber;
    }
}

$messageNumbers = array_values(array_unique($messageNumbers));

if (empty($messageNumbers)) {
    return redirect('/admin/bounces')
        ->with('error', 'No valid messages selected.');
}

$mailboxPath = '{mail.realtye-mails.com:110/pop3/notls}INBOX';

$mailbox = @imap_open(
    $mailboxPath,
    'members@realtye-mails.com',
    env('BOUNCEBOX_PASSWORD')
);

if (!$mailbox) {
    return redirect('/admin/bounces')
        ->with('error', 'Could not open mailbox: ' . imap_last_error());
}

$deleted = 0;
$failed = [];

foreach ($messageNumbers as $messageNumber) {
    $result = @imap_delete($mailbox, (string) $messageNumber);

    if ($result) {
        $deleted++;
    } else {
        $failed[] = $messageNumber;
    }
}

@imap_close($mailbox, CL_EXPUNGE);

if (!empty($failed)) {
    redirect('/admin/bounces')
        ->with('error', 'Deleted ' . $deleted . ' message(s), but failed to delete: ' . implode(', ', $failed));
}

redirect('/admin/bounces')
    ->with('success', 'Selected messages deleted.')
    ->send();

exit;