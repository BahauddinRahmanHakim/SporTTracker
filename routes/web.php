<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Firebase\JWT\JWT;

Route::get('/', function () {
    return view('index');
});

Route::get('auth/google', function () {
    return Socialite::driver('google')->redirect();
});

Route::get('auth/google/callback', function () {
    $googleUser = Socialite::driver('google')->user();

    $user = \App\Models\User::where('email', $googleUser->getEmail())->first();
    if (!$user) {
        $baseUsername = Str::slug($googleUser->getName() ?: explode('@', $googleUser->getEmail())[0]);
        $username = $baseUsername;
        $counter = 1;
        while (\App\Models\User::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }
        $user = \App\Models\User::create([
            'username' => $username,
            'name' => $googleUser->getName(),
            'email' => $googleUser->getEmail(),
            'password' => bcrypt(Str::random(16)),
            'avatar' => $googleUser->getAvatar(),
        ]);
    }

    // Generate JWT
    $key = env('JWT_SECRET');
    $payload = [
        'sub' => $user->id,
        'username' => $user->username,
        'iat' => time(),
        'exp' => time() + (60 * 60 * 24) // 1 day
    ];
    $token = JWT::encode($payload, $key, 'HS256');

    // Redirect to SPA with token in URL
    return redirect('/?token=' . $token);
});
