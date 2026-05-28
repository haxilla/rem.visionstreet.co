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

$found = [];

foreach ($connections as $dbLabel => $connection) {
    foreach ($tables as $table) {
        $rows = DB::connection($connection)
            ->table($table)
            ->whereRaw('LOWER(email) = ?', [$email])
            ->get();

        foreach ($rows as $row) {
            $found[] = [
                'dbLabel' => $dbLabel,
                'connection' => $connection,
                'table' => $table,
                'row' => $row,
            ];
        }
    }
}

echo '<div style="font-family:Arial; max-width:1100px; margin:20px auto;">';
echo '<h2>Email Lookup Result</h2>';
echo '<p><strong>Searched:</strong> ' . e($email) . '</p>';

if (count($found) === 0) {
    echo '<div style="padding:15px; background:#fee2e2; border:1px solid #ef4444;">No matching records found.</div>';
    echo '</div>';
    return;
}

if (count($found) > 1) {
    echo '<div style="padding:15px; background:#fef3c7; border:1px solid #f59e0b;">';
    echo '<strong>Multiple records found.</strong> Choose carefully before editing.';
    echo '</div>';

    foreach ($found as $match) {
        $row = $match['row'];

        echo '<div style="margin-top:15px; padding:15px; border:1px solid #ddd;">';
        echo '<strong>DB:</strong> ' . e($match['dbLabel']) . '<br>';
        echo '<strong>Table:</strong> ' . e($match['table']) . '<br>';
        echo '<strong>ID:</strong> ' . e($row->eid ?? '') . '<br>';
        echo '<strong>Email:</strong> ' . e($row->email ?? '') . '<br>';
        echo '<strong>Name:</strong> ' . e($row->FullName ?? '') . '<br>';
        echo '<strong>Office:</strong> ' . e($row->Officename ?? '') . '<br>';
        echo '</div>';
    }

    echo '</div>';
    return;
}

$match = $found[0];
$row = $match['row'];

?>

<div style="font-family:Arial; max-width:1100px; margin:20px auto;">

    <div style="padding:15px; background:#dcfce7; border:1px solid #22c55e; margin-bottom:20px;">
        <strong>Exactly one record found.</strong><br>
        Database: <?= e($match['dbLabel']) ?><br>
        Table: <?= e($match['table']) ?><br>
        Record ID: <?= e($row->eid ?? '') ?>
    </div>

    <form method="POST" action="/admin/bounces/update-recipient" style="display:grid; gap:14px;">

        <?= csrf_field() ?>

        <input type="hidden" name="connection" value="<?= e($match['connection']) ?>">
        <input type="hidden" name="table" value="<?= e($match['table']) ?>">
        <input type="hidden" name="eid" value="<?= e($row->eid ?? '') ?>">

        <h3>Editable Fields</h3>

        <label>
            Email<br>
            <input type="email" name="email" value="<?= e($row->email ?? '') ?>" style="width:100%; padding:8px;">
        </label>

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

        <label>
            Office Phone<br>
            <input type="text" name="officephone" value="<?= e($row->officephone ?? '') ?>" style="width:100%; padding:8px;">
        </label>

        <h3>Reference Info</h3>

        <div style="background:#f8fafc; padding:15px; border:1px solid #ddd;">
            <strong>Name:</strong> <?= e($row->FullName ?? '') ?><br>
            <strong>First:</strong> <?= e($row->FirstName ?? '') ?><br>
            <strong>Last:</strong> <?= e($row->LastName ?? '') ?><br>
            <strong>List Ref:</strong> <?= e($row->listref ?? '') ?><br>
            <strong>License #:</strong> <?= e($row->agentLicenseNum ?? '') ?><br>
            <strong>License Checked:</strong> <?= e($row->checkLicDate ?? '') ?><br>
            <strong>Agent Status:</strong> <?= e($row->agentLicStatus ?? '') ?><br>
            <strong>Send OK:</strong> <?= e($row->sendOK ?? '') ?><br>
            <strong>Bounce Count:</strong> <?= e($row->bounceCount ?? '') ?><br>
            <strong>Suspend Count:</strong> <?= e($row->suspendCount ?? '') ?>
        </div>

        <button type="submit" style="padding:12px 18px; background:#1d4ed8; color:white; border:0; cursor:pointer;">
            Save Changes
        </button>

    </form>

</div>