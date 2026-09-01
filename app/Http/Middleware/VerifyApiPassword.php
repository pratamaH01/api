<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyApiPassword
{
    public function handle(Request $request, Closure $next): Response
    {
        $sent = trim((string) $request->header('X-Api-Password', ''));
        $expected = (string) config('services.api_password');

        if ($sent === '' || $expected === '' || !hash_equals($expected, $sent)) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak: password API salah atau tidak dikirim',
            ], 401);
        }

        return $next($request);
    }
}
