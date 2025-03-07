<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ThanhVienHoiDong extends Model
{
    use HasFactory;

    protected $table = 'thanh_vien_hoi_dong';

    protected $fillable = [
        'ma_hoi_dong',
        'ma_nguoi_dung',
    ];

    // Quan hệ với HoiDongThamDinh
    public function hoiDong(): BelongsTo
    {
        return $this->belongsTo(HoiDongThamDinh::class, 'ma_hoi_dong');
    }

    // Quan hệ với User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ma_nguoi_dung');
    }

    // Quan hệ với DiemCaNhan
    public function diemCaNhans(): HasMany
    {
        return $this->hasMany(DiemCaNhan::class, 'ma_thanh_vien');
    }

    // Thêm relationship với DonVi
    public function donVi()
    {
        return $this->belongsTo(DonVi::class, 'don_vi_id');
    }
}
