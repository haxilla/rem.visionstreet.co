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

$results = [];

foreach ($connections as $label => $connection) {
    foreach ($tables as $table) {
        $matches = DB::connection($connection)
            ->table($table)
            ->whereRaw('LOWER(email) = ?', [$email])
            ->get();

        $results[$label][$table] = [
            'count' => $matches->count(),
            'rows' => $matches,
        ];
    }
}

dd([
    'searched_email' => $email,
    'results' => $results,
]);