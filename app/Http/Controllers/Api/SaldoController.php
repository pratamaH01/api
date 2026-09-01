<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;

class SaldoController extends Controller
{
    public function check(Request $request)
    {
        /** @var Member $member */
        $member = $request->attributes->get('member');

        return response()->json([
            'id_member' => $member->id_member,
            'nama'      => $member->nama,
            'saldo'     => (float) $member->saldo,
            'status'    => (int) $member->status,
        ]);
    }
}
