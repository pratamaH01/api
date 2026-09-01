<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TopupCallback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CallbackController extends Controller
{
    public function handle(Request $request)
    {
        // Terima data baik lewat GET (query string) maupun POST (body/JSON)
        $data = $request->all();

        // Selalu catat mentah-mentah ke log file, apapun formatnya,
        // supaya tidak ada data yang hilang walau field-nya tidak dikenali.
        Log::channel('single')->info('Callback topup diterima', [
            'method' => $request->method(),
            'data'   => $data,
        ]);

        // Coba tangkap field-field umum (nama field bisa beda-beda tergantung
        // sistem h2h; sesuaikan alias di bawah kalau nama field aslinya beda).
        $refId  = $data['refid'] ?? $data['ref_id'] ?? $data['idtrans'] ?? $data['reqid'] ?? null;
        $trxId  = $data['trxid'] ?? $data['trx_id'] ?? null;
        $status = $data['status'] ?? $data['statuscode'] ?? null;
        $produk = $data['produk'] ?? $data['kodeproduk'] ?? null;
        $msisdn = $data['msisdn'] ?? $data['destination_no'] ?? $data['tujuan'] ?? null;
        $msg    = $data['msg'] ?? $data['message'] ?? null;

        try {
            TopupCallback::create([
                'ref_id'      => $refId,
                'trx_id'      => $trxId,
                'status'      => $status,
                'produk'      => $produk,
                'msisdn'      => $msisdn,
                'message'     => $msg,
                'raw_payload' => json_encode($data, JSON_UNESCAPED_UNICODE),
                'received_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // Kalau gagal simpan ke DB, tetap sudah aman karena sudah dicatat
            // di log file di atas. Jangan sampai error ini bikin h2h mengira
            // callback gagal diterima.
            Log::channel('single')->error('Gagal simpan callback ke DB', [
                'error' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Callback diterima',
        ]);
    }
}