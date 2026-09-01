<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        /** @var Member $member */
        $member = $request->attributes->get('member');

        $reqId = $request->query('req_id');
        $limit = (int) $request->query('limit', 20);
        $limit = max(1, min($limit, 100));

        $query = Transaksi::where('id_member', $member->id_member)
            ->orderByDesc('tgl')
            ->select(['tgl', 'produk', 'tujuan', 'jml', 'saldo_awal', 'saldo_akhir', 'ket', 'req_id']);

        if ($reqId) {
            $query->where('req_id', $reqId);
        }

        $rows = $query->limit($limit)->get();

        $data = $rows->map(function ($row) {
            $isSukses = (bool) preg_match('/status:1(?!\d)/', (string) $row->ket);

            return [
                'req_id'      => $row->req_id,
                'tgl'         => $row->tgl,
                'produk'      => $row->produk,
                'tujuan'      => $row->tujuan,
                'jumlah'      => $row->jml,
                'saldo_awal'  => $row->saldo_awal,
                'saldo_akhir' => $row->saldo_akhir,
                'status'      => $isSukses ? 'SUKSES' : 'GAGAL',
            ];
        });

        return response()->json($data);
    }
}
