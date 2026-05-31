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
$eid           = request()->input('az_eid');
$messageNumber = request()->input('messageNumber');

if (!in_array($azTable, $tables, true) || !in_array($arizonaTable, $tables, true) || empty($eid)) {
    abort(400, 'Invalid delete preview request.');
}

$azRow = DB::connection('remote_emailgroups_azemails')
    ->table($azTable)
    ->where('eid', $eid)
    ->first();

$arizonaRow = DB::connection('remote_emailgroups_arizonaemails')
    ->table($arizonaTable)
    ->where('eid', $eid)
    ->first();

$data = compact(
    'azTable',
    'arizonaTable',
    'eid',
    'messageNumber',
    'azRow',
    'arizonaRow'
);