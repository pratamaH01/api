<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;

class PasswordController extends Controller
{
    public function change(Request $request)
    {
        // Member sudah terverifikasi lewat password LAMA oleh VerifyMemberPassword
        /** @var Member $member */
        $member = $request->attributes->get('member');

        $validated = $request->validate([
            'password_baru' => 'required|string|min:6',
        ]);

        $member->api_password_hash = hash('sha256', $validated['password_baru']);
        $member->save();

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diganti',
        ]);
    }
}
