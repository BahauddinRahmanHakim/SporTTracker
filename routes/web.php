<?php

use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

Route::get('/', function () {
    return 'Welcome to SportTrackers!';
});

Route::get('auth/google', function () {
    return Socialite::driver('google')->redirect();
});

Route::get('auth/google/callback', function () {
    $user = Socialite::driver('google')->user();
    // $user->getId(), $user->getName(), $user->getEmail(), etc.
    // Find or create user in your DB, log them in, etc.
    return response()->json($user);
});
