<?php

use Illuminate\Support\Facades\DB;

$tables = [
    'azphxmetro',
    'azphxne',
    'azphxse',
    'azphxwv',
    'enorthernazcounties',
    'esouthernazcounties',
];

$azTable       = request()->input('az_table');
$arizonaTable  = request()->input('arizona_table');
$eid           = request()->input('eid');
$messageNumber = request()->input('messageNumber');

if (
    !in_array($azTable, $tables, true) ||
    !in_array($arizonaTable, $tables, true) ||
    empty($eid)
) {
    abort(400, 'Invalid delete request.');
}

$deletedAz = DB::connection('remote_emailgroups_azemails')
    ->table($azTable)
    ->where('eid', $eid)
    ->delete();

$deletedArizona = DB::connection('remote_emailgroups_arizonaemails')
    ->table($arizonaTable)
    ->where('eid', $eid)
    ->delete();

$deletedBounceMessage = false;
$bounceDeleteError = null;

if (!empty($messageNumber)) {
    $mailboxPath = '{mail.realtye-mails.com:110/pop3/notls}INBOX';

    $mailbox = @imap_open(
        $mailboxPath,
        'members@realtye-mails.com',
        env('BOUNCEBOX_PASSWORD')
    );

    if ($mailbox) {
        if (@imap_delete($mailbox, (int) $messageNumber)) {
            @imap_expunge($mailbox);
            $deletedBounceMessage = true;
        } else {
            $bounceDeleteError = imap_last_error();
        }

        @imap_close($mailbox);
    } else {
        $bounceDeleteError = imap_last_error();
    }
}

$allDatabaseDeletesWorked = ((int) $deletedAz > 0 && (int) $deletedArizona > 0);

$bounceWasExpected = !empty($messageNumber);
$bounceDeleteWorked = !$bounceWasExpected || $deletedBounceMessage;

$message =
    'EID: ' . $eid .
    ', Deleted from AZ: ' . $deletedAz .
    ', Deleted from Arizona: ' . $deletedArizona .
    ', Bounce Message Deleted: ' . ($deletedBounceMessage ? 'Yes' : 'No');

if ($bounceDeleteError) {
    $message .= ', Bounce Delete Error: ' . $bounceDeleteError;
}

if ($allDatabaseDeletesWorked && $bounceDeleteWorked) {
    redirect('/admin/bounces')
        ->with('success', 'Agent & Bounce Message deleted. ' . $message)
        ->send();

    exit;
}

redirect('/admin/bounces')
    ->with('error', 'Delete finished with a problem. ' . $message)
    ->send();

exit;