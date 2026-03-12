<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Ensure flyer_code column exists
|--------------------------------------------------------------------------
*/

if (!Schema::hasColumn('propflyers', 'flyer_code')) {
    Schema::table('propflyers', function (Blueprint $table) {
        $table->string('flyer_code', 50)->nullable()->after('state');
    });
}

/*
|--------------------------------------------------------------------------
| Ensure flyer_codes table exists
|--------------------------------------------------------------------------
*/

if (!Schema::hasTable('flyer_codes')) {
    Schema::create('flyer_codes', function (Blueprint $table) {
        $table->id();
        $table->char('state', 2);
        $table->string('letters', 10)->default('A');
        $table->integer('number')->default(0);
        $table->timestamps();
        $table->unique('state');
    });
}

/*
|--------------------------------------------------------------------------
| Backfill missing flyer_code values
|--------------------------------------------------------------------------
*/

$allowedLetters = [
    'A','B','C','D','E','F','G','H',
    'J','K','M','N','P','R','T','U',
    'V','W','X','Y','Z'
];

$flyers = DB::table('propflyers')
    ->whereNull('flyer_code')
    ->whereNotNull('state')
    ->where('state', '!=', '')
    ->orderBy('id')
    ->get();

foreach ($flyers as $flyer) {
    $state = strtoupper(trim((string) $flyer->state));

    if (!preg_match('/^[A-Z]{2}$/', $state)) {
        continue;
    }

    DB::transaction(function () use ($flyer, $state, $allowedLetters) {
        $sequence = DB::table('flyer_codes')
            ->where('state', $state)
            ->lockForUpdate()
            ->first();

        if (!$sequence) {
            DB::table('flyer_codes')->insert([
                'state' => $state,
                'letters' => 'A',
                'number' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $sequence = DB::table('flyer_codes')
                ->where('state', $state)
                ->lockForUpdate()
                ->first();
        }

        $letters = $sequence->letters;
        $number  = (int) $sequence->number;

        if ($number < 99) {
            $number++;
        } else {
            $number = 1;

            $chars = str_split($letters);
            $changed = false;

            for ($i = count($chars) - 1; $i >= 0; $i--) {
                $pos = array_search($chars[$i], $allowedLetters, true);

                if ($pos !== false && $pos < count($allowedLetters) - 1) {
                    $chars[$i] = $allowedLetters[$pos + 1];

                    for ($j = $i + 1; $j < count($chars); $j++) {
                        $chars[$j] = $allowedLetters[0];
                    }

                    $changed = true;
                    break;
                }
            }

            if (!$changed) {
                $chars = array_fill(0, count($chars) + 1, $allowedLetters[0]);
            }

            $letters = implode('', $chars);
        }

        $newCode = $state . $letters . str_pad($number, 2, '0', STR_PAD_LEFT);

        DB::table('propflyers')
            ->where('id', $flyer->id)
            ->update([
                'flyer_code' => $newCode,
            ]);

        DB::table('flyer_codes')
            ->where('state', $state)
            ->update([
                'letters' => $letters,
                'number' => $number,
                'updated_at' => now(),
            ]);
    });
}

/*
|--------------------------------------------------------------------------
| Normal index code continues here
|--------------------------------------------------------------------------
*/