<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

public function index()
{

    /*
    |--------------------------------------------------------------------------
    | Check if flyercode column exists
    |--------------------------------------------------------------------------
    */

    if (!Schema::connection('remuserdb')->hasColumn('propflyers', 'flyer_code')) {

        /*
        |--------------------------------------------------------------------------
        | Create the column
        |--------------------------------------------------------------------------
        */

        Schema::connection('remuserdb')->table('propflyers', function ($table) {
            $table->string('flyer_code', 50)->nullable()->after('state');
        });


        /*
        |--------------------------------------------------------------------------
        | Run backfill only after column created
        |--------------------------------------------------------------------------
        */

        $allowedLetters = [
            'A','B','C','D','E','F','G','H',
            'J','K','M','N','P','R','T','U',
            'V','W','X','Y','Z'
        ];


        $flyers = DB::connection('remuserdb')
            ->table('propflyers')
            ->whereNull('flyer_code')
            ->orderBy('state')
            ->orderBy('id')
            ->get();


        foreach ($flyers as $flyer) {

            $state = strtoupper(trim($flyer->state));

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
                $number  = (int)$sequence->number;


                if ($number < 99) {

                    $number++;

                } else {

                    $number = 1;

                    $chars = str_split($letters);

                    $changed = false;

                    for ($i = count($chars) - 1; $i >= 0; $i--) {

                        $pos = array_search($chars[$i], $allowedLetters, true);

                        if ($pos < count($allowedLetters) - 1) {

                            $chars[$i] = $allowedLetters[$pos + 1];

                            for ($j = $i + 1; $j < count($chars); $j++) {
                                $chars[$j] = $allowedLetters[0];
                            }

                            $changed = true;
                            break;
                        }
                    }

                    if (!$changed) {

                        $chars = array_fill(
                            0,
                            count($chars) + 1,
                            $allowedLetters[0]
                        );
                    }

                    $letters = implode('', $chars);
                }


                $newCode = $state . $letters . $number;


                DB::connection('remuserdb')
                    ->table('propflyers')
                    ->where('id', $flyer->id)
                    ->update([
                        'flyercode' => $newCode
                    ]);


                DB::table('flyer_sequences')
                    ->where('state', $state)
                    ->update([
                        'letters' => $letters,
                        'number' => $number,
                        'updated_at' => now(),
                    ]);

            });

        }

    }


    /*
    |--------------------------------------------------------------------------
    | Normal index code continues here
    |--------------------------------------------------------------------------
    */

}