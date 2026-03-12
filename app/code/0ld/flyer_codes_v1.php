<?php

use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Allowed letters (skip I, L, O, Q)
|--------------------------------------------------------------------------
*/

$allowedLetters = [
    'A','B','C','D','E','F','G','H',
    'J','K','M','N','P','R','T','U',
    'V','W','X','Y','Z'
];

/*
|--------------------------------------------------------------------------
| Get flyers missing codes
|--------------------------------------------------------------------------
*/

$flyers = DB::table('flyers')
    ->whereNull('flyer_code')
    ->orderBy('state')
    ->orderBy('id')
    ->get();


/*
|--------------------------------------------------------------------------
| Loop through flyers
|--------------------------------------------------------------------------
*/

foreach ($flyers as $flyer) {

    $state = strtoupper(trim($flyer->state));

    if (!preg_match('/^[A-Z]{2}$/', $state)) {
        continue;
    }


    DB::transaction(function () use ($flyer, $state, $allowedLetters) {

        /*
        |--------------------------------------------------------------------------
        | Load sequence row for this state
        |--------------------------------------------------------------------------
        */

        $sequence = DB::table('flyer_sequences')
            ->where('state', $state)
            ->lockForUpdate()
            ->first();


        /*
        |--------------------------------------------------------------------------
        | If state not found, create starting row
        |--------------------------------------------------------------------------
        */

        if (!$sequence) {

            DB::table('flyer_sequences')->insert([
                'state'      => $state,
                'letters'    => 'A',
                'number'     => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $sequence = DB::table('flyer_sequences')
                ->where('state', $state)
                ->lockForUpdate()
                ->first();
        }


        /*
        |--------------------------------------------------------------------------
        | Current values
        |--------------------------------------------------------------------------
        */

        $letters = $sequence->letters;
        $number  = (int) $sequence->number;


        /*
        |--------------------------------------------------------------------------
        | Increment number
        |--------------------------------------------------------------------------
        */

        if ($number < 99) {

            $number++;

        } else {

            $number = 1;

            $chars = str_split($letters);

            $changed = false;

            for ($i = count($chars) - 1; $i >= 0; $i--) {

                $pos = array_search($chars[$i], $allowedLetters, true);

                if ($pos === false) {
                    throw new Exception("Invalid letter in sequence");
                }

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


        /*
        |--------------------------------------------------------------------------
        | Build code
        |--------------------------------------------------------------------------
        */

        $newCode = $state . $letters . $number;


        /*
        |--------------------------------------------------------------------------
        | Save to flyer
        |--------------------------------------------------------------------------
        */

        DB::table('flyers')
            ->where('id', $flyer->id)
            ->update([
                'flyer_code' => $newCode
            ]);


        /*
        |--------------------------------------------------------------------------
        | Update sequence table
        |--------------------------------------------------------------------------
        */

        DB::table('flyer_sequences')
            ->where('state', $state)
            ->update([
                'letters'    => $letters,
                'number'     => $number,
                'updated_at' => now(),
            ]);
    });

}