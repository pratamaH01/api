<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TopupController extends Controller
{
    private string $h2hBaseUrl = 'http://116.206.196.148:8080/api/h2h';
    private string $h2hPin = '112233';
    private string $h2hPassword = '112233';

    public function process(Request $request, string $produk)
    {
        /** @var Member $member */
        $member = $request->attributes->get('member');

        $produkKey = strtolower($produk);
        $products = config('topup_products', []);

        if (!array_key_exists($produkKey, $products)) {
            return response()->json([
                'success' => false,
                'message' => "Produk '{$produk}' tidak didukung",
            ], 404);
        }

        $productConfig = $products[$produkKey];
        $kodeProdukH2h = $productConfig['code'];
        $min = $productConfig['min'] ?? null;
        $max = $productConfig['max'] ?? null;

        $validated = $request->validate([
            'destination_no' => 'required|string|max:20',
            'nominal'        => 'required|numeric|min:1',
        ]);

        $nominal = (float) $validated['nominal'];

        if ($min !== null && $nominal < $min) {
            return response()->json([
                'success' => false,
                'message' => "Nominal minimal untuk {$produk} adalah " . number_format($min, 0, ',', '.'),
            ], 422);
        }

        if ($max !== null && $nominal > $max) {
            return response()->json([
                'success' => false,
                'message' => "Nominal maksimal untuk {$produk} adalah " . number_format($max, 0, ',', '.'),
            ], 422);
        }

        $idTrans = now()->format('YmdHis') . Str::padLeft((string) random_int(0, 999), 3, '0');

        try {
            $response = Http::timeout(30)->get($this->h2hBaseUrl, [
                'cmd'             => 'topup',
                'idcust'          => $member->id_member,
                'pin'             => $this->h2hPin,
                'password'        => $this->h2hPassword,
                'idtrans'         => $idTrans,
                'produk'          => $kodeProdukH2h,
                'sub_produk'      => '',
                'id_bill'         => '',
                'destination_no'  => $validated['destination_no'],
                'nominal'         => $validated['nominal'],
            ]);
        } catch (\Throwable $e) {
            Log::error('Topup gagal konek ke h2h', ['produk' => $produkKey, 'error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghubungi server topup, coba lagi',
            ], 502);
        }

        return response()->json([
            'success' => $response->successful(),
            'idtrans' => $idTrans,
            'produk'  => strtoupper($produkKey),
            'message' => $response->successful() ? 'Transaksi sedang diproses' : 'Gagal mengirim transaksi, coba lagi',
        ], $response->status());
    }
}