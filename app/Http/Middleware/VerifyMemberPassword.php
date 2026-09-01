<?php

namespace App\Http\Middleware;

use App\Models\Member;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyMemberPassword
{
    public function handle(Request $request, Closure $next): Response
    {
        // Password dikirim lewat header 'password', bisa juga lewat body
        // kalau method-nya POST dan customer kirim di JSON body.
        $password = trim((string) $request->header('password', $request->input('password', '')));

        if ($password === '') {
            return response()->json([
                'success' => false,
                'message' => 'Header/field password wajib diisi',
            ], 401);
        }

        $hash = hash('sha256', $password);

        $member = Member::where('api_password_hash', $hash)->first();

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Password salah',
            ], 401);
        }

        // Simpan member yang sudah terverifikasi, supaya controller tinggal
        // pakai $request->attributes->get('member') tanpa perlu id_member/pin lagi.
        $request->attributes->set('member', $member);

        return $next($request);
    }
}
