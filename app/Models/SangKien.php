<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    // Relationships
    public function tacGia(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ma_tac_gia');
    }

    public function donVi(): BelongsTo
    {
        return $this->belongsTo(DonVi::class, 'ma_don_vi');
    }

    public function trangThai(): BelongsTo
    {
        return $this->belongsTo(TrangThaiSangKien::class, 'ma_trang_thai_sang_kien');
    }

    public function taiLieus()
    {
        return $this->hasMany(TaiLieuSangKien::class, 'sang_kien_id');
    }

    public function diemHoiDongs()
    {
        return $this->hasMany(DiemHoiDong::class, 'ma_sang_kien');
    }

    public function diemCaNhans()
    {
        return $this->hasMany(DiemCaNhan::class, 'ma_sang_kien');
    }
}
