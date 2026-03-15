<?php

$users = \App\Models\User::whereNull('password')
    ->whereNotNull('password_plain')
    ->where('password_plain', '!=', '')
    ->get();

foreach ($users as $user) {
    $user->password = \Hash::make($user->password_plain);
    $user->save();
}