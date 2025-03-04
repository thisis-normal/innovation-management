<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrangThaiSangKien extends Model
{
    use HasFactory;

    protected $table = 'trang_thai_sang_kien';

    protected $fillable = [
        'ma_trang_thai',
        'ten_trang_thai',
        'mo_ta',
        'order',
        'is_active',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function sangKien(): HasMany
    {
        return $this->hasMany(SangKien::class, 'ma_trang_thai_sang_kien', 'id');
    }
}
