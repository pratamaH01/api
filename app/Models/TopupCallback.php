<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TopupCallback extends Model
{
    protected $table = 'topup_callbacks';

    public $timestamps = false;

    protected $fillable = [
        'ref_id', 'trx_id', 'status', 'produk', 'msisdn',
        'message', 'raw_payload', 'received_at',
    ];
}