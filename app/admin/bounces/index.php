<?php

dd(phpinfo());

/*
$mailbox = imap_open(
        '{mail.realtye-mails.com:110/pop3/notls}INBOX',
        'members@realtye-mails.com',
        'PASSWORD'
    );

    if (!$mailbox) {
        return imap_last_error();
    }

    $count = imap_num_msg($mailbox);

    imap_close($mailbox);

    return "Connected successfully. Message count: " . $count;
*/