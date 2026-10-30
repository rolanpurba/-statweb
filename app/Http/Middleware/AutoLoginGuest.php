<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AutoLoginGuest
{
    public function handle(Request $request, Closure $next): Response
    {
        // Kalau user sudah login (baik guest atau akun asli), lanjut saja
        if (Auth::check()) {
            return $next($request);
        }

        // Cek apakah ada guest_token di cookie (user pernah berkunjung sebelumnya)
        $guestToken = $request->cookie('statweb_guest_token');

        if ($guestToken) {
            $user = User::where('guest_token', $guestToken)->where('is_guest', true)->first();
            if ($user) {
                Auth::login($user, true);
                return $next($request);
            }
        }

        // Belum ada guest_token, buat guest user baru
        $token = Str::random(40);
        $user = User::create([
            'name'       => 'Guest-' . strtoupper(Str::random(6)),
            'email'      => 'guest_' . Str::random(16) . '@statweb.local',
            'password'   => bcrypt(Str::random(32)),
            'is_guest'   => true,
            'guest_token'=> $token,
        ]);

        Auth::login($user, true);

        // Simpan token di cookie 1 tahun biar user yang sama dikenali lagi
        Cookie::queue('statweb_guest_token', $token, 60 * 24 * 365);

        return $next($request);
    }
}
