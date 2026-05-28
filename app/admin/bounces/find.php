<?php

use Illuminate\Support\Facades\DB;

$email = trim(strtolower($_POST['email'] ?? ''));

$connections = [
    'azemails' => 'remote_emailgroups_azemails',
    'arizonaemails' => 'remote_emailgroups_arizonaemails',
];

$tables = [
    'azphxmetro',
    'azphxne',
    'azphxse',
    'azphxwv',
    'enorthernazcounties',
    'esouthernazcounties',
];

$foundByDb = [];

foreach ($connections as $dbLabel => $connection) {
    $foundByDb[$dbLabel] = [];

    foreach ($tables as $table) {
        $rows = DB::connection($connection)
            ->table($table)
            ->whereRaw('LOWER(email) = ?', [$email])
            ->get();

        foreach ($rows as $row) {
            $foundByDb[$dbLabel][] = [
                'dbLabel' => $dbLabel,
                'connection' => $connection,
                'table' => $table,
                'row' => $row,
            ];
        }
    }
}

echo '<div style="font-family:Arial,sans-serif; max-width:1100px; margin:20px auto; color:#111827;">';
echo '<h2>Email Lookup Result</h2>';
echo '<p><strong>Searched Email:</strong> ' . e($email) . '</p>';

$hasProblem = false;

foreach ($foundByDb as $dbLabel => $matches) {
    if (count($matches) !== 1) {
        $hasProblem = true;
    }
}

if ($hasProblem) {
    echo '<div style="padding:14px; background:#fef3c7; border:1px solid #f59e0b; margin-bottom:18px;">';
    echo '<strong>Needs Review:</strong> Each remote database should contain exactly one matching record. ';
    echo 'Zero matches or multiple matches on the same remote database require manual review.';
    echo '</div>';

    foreach ($foundByDb as $dbLabel => $matches) {
        echo '<div style="margin-bottom:18px; padding:14px; border:1px solid #d1d5db; background:#fff;">';
        echo '<h3 style="margin-top:0;">' . e($dbLabel) . ' — ' . count($matches) . ' match(es)</h3>';

        if (count($matches) === 0) {
            echo '<div style="padding:10px; background:#fee2e2; border:1px solid #ef4444;">No matching record found.</div>';
            echo '</div>';
            continue;
        }

        foreach ($matches as $match) {
            $row = $match['row'];

            echo '<div style="margin-top:12px; padding:12px; border:1px solid #e5e7eb; background:#f9fafb;">';
            echo '<strong>Table:</strong> ' . e($match['table']) . '<br>';
            echo '<strong>EID:</strong> ' . e($row->eid ?? '') . '<br>';
            echo '<strong>Email:</strong> ' . e($row->email ?? '') . '<br>';
            echo '<strong>Name:</strong> ' . e($row->FullName ?? '') . '<br>';
            echo '<strong>Office:</strong> ' . e($row->Officename ?? '') . '<br>';
            echo '<strong>License #:</strong> ' . e($row->agentLicenseNum ?? '') . '<br>';
            echo '<strong>License Checked:</strong> ' . e($row->checkLicDate ?? '') . '<br>';
            echo '<strong>Status:</strong> ' . e($row->agentLicStatus ?? '') . '<br>';
            echo '</div>';
        }

        echo '</div>';
    }

    echo '</div>';
    return;
}

$azMatch = $foundByDb['azemails'][0];
$arizonaMatch = $foundByDb['arizonaemails'][0];

$row = $azMatch['row'];

?>

<div style="font-family:Arial,sans-serif; max-width:1100px; margin:20px auto; color:#111827;">

    <h2>Email Lookup Result</h2>

    <div style="padding:14px; background:#dcfce7; border:1px solid #22c55e; margin-bottom:20px;">
        <strong>Mirror Match Found:</strong> One matching record exists in each remote database.<br>
        <strong>AZEmails:</strong> <?= e($azMatch['table']) ?> / EID <?= e($azMatch['row']->eid ?? '') ?><br>
        <strong>ArizonaEmails:</strong> <?= e($arizonaMatch['table']) ?> / EID <?= e($arizonaMatch['row']->eid ?? '') ?>
    </div>

    <form method="POST" action="/admin/bounces/update-recipient" style="display:grid; gap:14px;">

        <?= csrf_field() ?>

        <input type="hidden" name="az_table" value="<?= e($azMatch['table']) ?>">
        <input type="hidden" name="az_eid" value="<?= e($azMatch['row']->eid ?? '') ?>">

        <input type="hidden" name="arizona_table" value="<?= e($arizonaMatch['table']) ?>">
        <input type="hidden" name="arizona_eid" value="<?= e($arizonaMatch['row']->eid ?? '') ?>">

        <h3 style="margin-bottom:0;">Editable Contact Fields</h3>

        <label>
            Email<br>
            <input type="email" name="email" value="<?= e($row->email ?? '') ?>" style="width:100%; padding:8px;">
        </label>

        <h3 style="margin-bottom:0;">Editable Office Fields</h3>

        <label>
            Office Name<br>
            <input type="text" name="Officename" value="<?= e($row->Officename ?? '') ?>" style="width:100%; padding:8px;">
        </label>

        <label>
            Office Address 1<br>
            <input type="text" name="officeaddress1" value="<?= e($row->officeaddress1 ?? '') ?>" style="width:100%; padding:8px;">
        </label>

        <label>
            Office Address 2<br>
            <input type="text" name="officeaddress2" value="<?= e($row->officeaddress2 ?? '') ?>" style="width:100%; padding:8px;">
        </label>

        <div style="display:grid; grid-template-columns:2fr 1fr 1fr; gap:12px;">
            <label>
                Office City<br>
                <input type="text" name="officecity" value="<?= e($row->officecity ?? '') ?>" style="width:100%; padding:8px;">
            </label>

            <label>
                Office State<br>
                <input type="text" name="officestate" value="<?= e($row->officestate ?? '') ?>" style="width:100%; padding:8px;">
            </label>

            <label>
                Office Zip<br>
                <input type="text" name="officezip" value="<?= e($row->officezip ?? '') ?>" style="width:100%; padding:8px;">
            </label>
        </div>

        <label>
            Office Phone<br>
            <input type="text" name="officephone" value="<?= e($row->officephone ?? '') ?>" style="width:100%; padding:8px;">
        </label>

        <h3 style="margin-bottom:0;">Reference Info</h3>

        <div style="background:#f8fafc; padding:15px; border:1px solid #d1d5db; line-height:1.7;">
            <strong>Full Name:</strong> <?= e($row->FullName ?? '') ?><br>
            <strong>First Name:</strong> <?= e($row->FirstName ?? '') ?><br>
            <strong>Last Name:</strong> <?= e($row->LastName ?? '') ?><br>
            <strong>List Ref:</strong> <?= e($row->listref ?? '') ?><br>
            <strong>License #:</strong> <?= e($row->agentLicenseNum ?? '') ?><br>
            <strong>License Checked:</strong> <?= e($row->checkLicDate ?? '') ?><br>
            <strong>Agent License Status:</strong> <?= e($row->agentLicStatus ?? '') ?><br>
            <strong>Send OK:</strong> <?= e($row->sendOK ?? '') ?><br>
            <strong>Bounce Count:</strong> <?= e($row->bounceCount ?? '') ?><br>
            <strong>Suspend Count:</strong> <?= e($row->suspendCount ?? '') ?><br>
            <strong>Original AZ Email:</strong> <?= e($azMatch['row']->email ?? '') ?><br>
            <strong>Original Arizona Email:</strong> <?= e($arizonaMatch['row']->email ?? '') ?>
        </div>

        <button type="submit" style="padding:12px 18px; background:#1d4ed8; color:white; border:0; cursor:pointer; font-weight:bold;">
            Save Changes to Both Databases
        </button>

    </form>

</div>