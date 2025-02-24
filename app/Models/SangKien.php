<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SangKien extends Model
{
    use HasFactory;

    protected $table = 'sang_kien';

    protected $fillable = [
        'ten_sang_kien',
        'mo_ta',
        'ma_tac_gia',
        'ma_don_vi',
        'ma_trang_thai_sang_kien',
        'hien_trang',
        'ket_qua',
        'ghi_chu'
    ];

    protected $casts = [
        'files' => 'array',
    ];
    // Relationships
    public function donVi(): BelongsTo
    {
        return $this->belongsTo(DonVi::class, 'ma_don_vi');
    }
    public function diemHoiDongs()
    {
        return $this->hasMany(DiemHoiDong::class, 'ma_sang_kien');
    }
    public function diemCaNhans()
    {
        return $this->hasMany(DiemCaNhan::class, 'ma_sang_kien');
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ma_tac_gia', 'id');
    }
    public function taiLieuSangKien(): HasMany
    {
        return $this->hasMany(TaiLieuSangKien::class, 'sang_kien_id');
    }
    public function trangThaiSangKien(): BelongsTo
    {
        return $this->belongsTo(TrangThaiSangKien::class, 'ma_trang_thai_sang_kien', 'id');
    }
}
