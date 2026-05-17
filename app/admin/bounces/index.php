<?php

$mailboxPath = '{mail.realtye-mails.com:110/pop3/notls}INBOX';

$mailbox = @imap_open(
    $mailboxPath,
    'members',
    'D4vidb0wi3!'
);

if (!$mailbox) {
    dd([
        'mailboxPath' => $mailboxPath,
        'errors' => imap_errors(),
        'last_error' => imap_last_error(),
    ]);
}

$count = imap_num_msg($mailbox);

imap_close($mailbox);

dd("Connected successfully. Message count: " . $count);