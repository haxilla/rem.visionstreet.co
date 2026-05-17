<?php

$mailbox = imap_open(
    '{mail.realtye-mails.com:110/pop3/notls/novalidate-cert}INBOX',
    'members@realtye-mails.com',
    'PASSWORD'
);

if (!$mailbox) {
    dd(imap_errors(), imap_last_error());
}

$count = imap_num_msg($mailbox);

imap_close($mailbox);

dd("Connected successfully. Message count: " . $count);