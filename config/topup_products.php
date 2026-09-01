<?php

// Daftar produk topup yang didukung.
// 'code'  = kode produk yang dikirim ke h2h
// 'min'   = nominal minimal topup (silakan ubah sendiri sesuai kebutuhan)
// 'max'   = nominal maksimal topup, isi null kalau tidak ada batas atas

return [
    'dana' => [
        'code' => 'DDANA',
        'min'  => 5000,
        'max'  => null,
    ],
    'shopeepay' => [
        'code' => 'DSHOP',
        'min'  => 5000,
        'max'  => 200000,
    ],
    'shopeepay2' => [
        'code' => 'BSHOP',
        'min'  => 5000,
        'max'  => null,
    ],
    'gopay' => [
        'code' => 'BGOPAY',
        'min'  => 5000,
        'max'  => null,
    ],
    'link' => [
        'code' => 'BLINK',
        'min'  => 5000,
        'max'  => null,
    ],
    'ovo' => [
        'code' => 'BOVO',
        'min'  => 5000,
        'max'  => null,
    ],
];