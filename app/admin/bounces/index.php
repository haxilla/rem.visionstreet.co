<?php

$mailbox = imap_open(
        '{mail.realtye-mails.com:110/pop3/notls}INBOX',
        'members@realtye-mails.com',
        'D4vidb0wi3!'
    );

if (!$mailbox) {
    return imap_last_error();
}

$count = imap_num_msg($mailbox);

imap_close($mailbox);

dd("Connected successfully. Message count: " . $count);