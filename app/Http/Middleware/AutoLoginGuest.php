<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AutoLoginGuest
{
    // Pre-computed bcrypt hash untuk password dummy guest (menghemat 100-300ms CPU per request)
    private const GUEST_DUMMY_PASSWORD_HASH = '$2y$04$CfN.YkYp0oHhS8eO1jBv4uG3hX9qGbe3ZJ5E4bE4lYjK.C5B3R0d5';

    public function handle(Request $request, Closure $next): Response
    {
        // Abaikan request statis, health check, dan auth form
        if ($request->is('login', 'register', 'forgot-password', 'reset-password*', 'confirm-password', 'up', 'build/*', 'storage/*', 'favicon.ico')) {
            return $next($request);
        }

        // Kalau user sudah login (baik guest atau akun asli), lanjut saja
        if (Auth::check()) {
            return $next($request);
        }

        // Cek apakah ada guest_token di cookie (user pernah berkunjung sebelumnya)
        $guestToken = $request->cookie('statweb_guest_token');

        if ($guestToken && is_string($guestToken) && strlen($guestToken) === 40) {
            $user = User::where('guest_token', $guestToken)->where('is_guest', true)->first();
            if ($user) {
                Auth::login($user, true);
                return $next($request);
            }
        }

        // Rate limiting pembuatan guest account: maksimal 20 akun baru per menit per IP (proteksi DoS)
        $throttleKey = 'guest_create:' . ($request->ip() ?: 'unknown');
        if (RateLimiter::tooManyAttempts($throttleKey, 20)) {
            return $next($request);
        }
        RateLimiter::hit($throttleKey, 60);

        // Buat guest user baru dengan efisiensi tinggi
        $token = Str::random(40);
        $user = User::create([
            'name'        => 'Guest-' . strtoupper(Str::random(6)),
            'email'       => 'guest_' . Str::random(16) . '@statweb.local',
            'password'    => self::GUEST_DUMMY_PASSWORD_HASH,
            'is_guest'    => true,
            'guest_token' => $token,
        ]);

        Auth::login($user, true);

        // Simpan token di cookie 1 tahun biar user yang sama dikenali lagi
        Cookie::queue('statweb_guest_token', $token, 60 * 24 * 365);

        return $next($request);
    }
}
