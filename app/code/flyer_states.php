<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

$tableName = 'propflyers';

// Add state column if missing
if (!Schema::hasColumn($tableName, 'state')) {
    Schema::table($tableName, function (Blueprint $table) {
        $table->char('state', 2)->nullable()->after('xState');
    });
}

// Minimal inclusive prefixes for messy xState values
$rules = [
    'AL' => ['al', 'alab'],
    'AK' => ['ak', 'alas'],
    'AZ' => ['az', 'ari','arz'],
    'AR' => ['ark'],
    'CA' => ['ca', 'cal'],
    'CO' => ['col'],
    'CT' => ['ct', 'con'],
    'DE' => ['de', 'del'],
    'FL' => ['fl', 'flo'],
    'GA' => ['ga', 'geo'],
    'HI' => ['hi', 'haw'],
    'ID' => ['id', 'ida'],
    'IL' => ['il', 'ill'],
    'IN' => ['in', 'ind'],
    'IA' => ['ia', 'iow'],
    'KS' => ['ks', 'kan'],
    'KY' => ['ky', 'ken'],
    'LA' => ['la', 'lou'],
    'ME' => ['me', 'mai'],
    'MD' => ['md', 'mar'],
    'MA' => ['mas'],
    'MI' => ['mic'],
    'MN' => ['mn', 'min'],
    'MS' => ['ms', 'missi'],
    'MO' => ['mo', 'miz','misso'],
    'MT' => ['mt', 'mon'],
    'NE' => ['neb'],
    'NV' => ['nv', 'nev'],
    'NH' => ['nh', 'new h', 'n h'],
    'NJ' => ['nj', 'new j', 'n j'],
    'NM' => ['nm', 'new m', 'n m'],
    'NY' => ['ny', 'new y', 'n y'],
    'NC' => ['nc', 'north c', 'n c'],
    'ND' => ['nd', 'north d', 'n d'],
    'OH' => ['oh', 'ohi'],
    'OK' => ['ok', 'okl'],
    'OR' => ['or', 'ore'],
    'PA' => ['pa', 'pen'],
    'RI' => ['ri', 'rho'],
    'SC' => ['sc', 'south c', 's c'],
    'SD' => ['sd', 'south d', 's d'],
    'TN' => ['tn', 'ten'],
    'TX' => ['tx', 'tex'],
    'UT' => ['ut', 'uta'],
    'VT' => ['vt', 'ver'],
    'VA' => ['va', 'vir'],
    'WA' => ['wa', 'was'],
    'WV' => ['wv', 'west v', 'w v'],
    'WI' => ['wi', 'wis'],
    'WY' => ['wy', 'wyo'],
];

$case = [];

foreach ($rules as $abbr => $prefixes) {
    foreach ($prefixes as $prefix) {
        $prefix = strtolower($prefix);
        $escapedPrefix = str_replace("'", "''", $prefix);
        $case[] = "WHEN LCASE(TRIM(`xState`)) LIKE '{$escapedPrefix}%' THEN '{$abbr}'";
    }
}

$caseSql = implode("\n        ", $case);

DB::statement("
    UPDATE `{$tableName}`
    SET `state` = CASE
        {$caseSql}
        ELSE `state`
    END
    WHERE (`state` IS NULL OR `state` = '')
      AND `xState` IS NOT NULL
      AND TRIM(`xState`) != ''
");