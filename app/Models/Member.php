<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $table = 'member';

    // Tabel ini tidak punya kolom created_at/updated_at standar Laravel
    public $timestamps = false;

    protected $fillable = [
        'id_member', 'nama', 'alamat', 'pin', 'status', 'saldo',
    ];

    // Jangan sampai kolom pin ikut ke-serialize kalau model ini
    // di-return langsung sebagai JSON di tempat lain.
    protected $hidden = [
        'pin',
    ];
}