<?php

use Illuminate\Support\Facades\DB;

$email = trim(strtolower(request()->input('email', '')));

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

$hasProblem = false;

foreach ($foundByDb as $matches) {
    if (count($matches) !== 1) {
        $hasProblem = true;
    }
}

$azMatch = null;
$arizonaMatch = null;
$row = null;

if (!$hasProblem) {
    $azMatch = $foundByDb['azemails'][0];
    $arizonaMatch = $foundByDb['arizonaemails'][0];
    $row = $azMatch['row'];
}

$data = compact(
    'email',
    'foundByDb',
    'hasProblem',
    'azMatch',
    'arizonaMatch',
    'row'
);