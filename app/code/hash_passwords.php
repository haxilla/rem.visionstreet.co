<?php

$batchSize = 5; // Adjust as needed

$users = \App\Models\Core\Propagent::whereNull('password')
    ->whereNotNull('agtPswd')
    ->where('agtPswd', '!=', '')
    ->limit($batchSize)
    ->get();

if ($users->isEmpty()) {

    echo '<!doctype html><html><body style="font-family:Arial;padding:20px">';
    echo '<h3>password backfill complete</h3>';
    echo '</body></html>';

    exit;
}

foreach ($users as $user) {
    $user->password = \Hash::make($user->agtPswd);
    $user->save();
}

$remaining = DB::table('propagents')
    ->whereNull('password')
    ->whereNotNull('agtPswd')
    ->where('agtPswd', '!=', '')
    ->count();

echo '<!doctype html>';
echo '<html><head>';
echo '<meta http-equiv="refresh" content="0.5">';
echo '</head><body style="font-family:Arial;padding:20px">';

echo '<h3>PASSWORD HASH Batching IN PROGRESS</h3>';
echo '<p>Processed: ' . count($users) . '</p>';
echo '<p>Remaining: ' . $remaining . '</p>';

echo '</body></html>';

exit;

