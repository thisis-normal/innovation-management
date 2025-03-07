<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoaiSangKien extends Model
{
    protected $table = 'loai_sang_kien';

    protected $fillable = [
        'ten_loai_sang_kien',
        'mo_ta',
    ];

    // Relationship với bảng sang_kien (nếu có)
    public function sangKien(): HasMany
    {
        return $this->hasMany(SangKien::class, 'ma_loai_sang_kien');
    }
}
