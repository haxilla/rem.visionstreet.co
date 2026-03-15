<?php

$users = \App\Models\User::whereNull('password')
    ->whereNotNull('plain_password')
    ->where('plain_password', '!=', '')
    ->get();

foreach ($users as $user) {
    $user->password = \Hash::make($user->plain_password);
    $user->save();
}