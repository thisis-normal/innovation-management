<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TieuChi extends Model
{
    protected $table = 'tieu_chi';

    protected $fillable = [
        'stt',
        'ten_tieu_chi',
        'ghi_chu'
    ];

    protected $casts = [
        'du_lieu_tieu_chi' => 'json',
    ];
}
