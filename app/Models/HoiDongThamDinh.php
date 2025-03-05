<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HoiDongThamDinh extends Model
{
    use HasFactory;

    protected $table = 'hoi_dong_tham_dinh';

    protected $fillable = [
        'ten_hoi_dong',
        'ma_truong_hoi_dong',
        'ngay_bat_dau',
        'ngay_ket_thuc',
        'trang_thai',
    ];

    protected $casts = [
        'ngay_bat_dau' => 'date',
        'ngay_ket_thuc' => 'date',
        'trang_thai' => 'integer',
    ];

    // Quan hệ với User (trưởng hội đồng)
    public function truongHoiDong(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ma_truong_hoi_dong');
    }

    // Quan hệ với ThanhVienHoiDong
    public function thanhVienHoiDongs(): HasMany
    {
        return $this->hasMany(ThanhVienHoiDong::class, 'ma_hoi_dong');
    }

    // Quan hệ với User thông qua ThanhVienHoiDong
    public function thanhViens()
    {
        return $this->belongsToMany(User::class, 'thanh_vien_hoi_dong', 'ma_hoi_dong', 'ma_nguoi_dung')
                    ->withTimestamps();
    }

    // Quan hệ với DiemHoiDong
    public function diemHoiDongs(): HasMany
    {
        return $this->hasMany(DiemHoiDong::class, 'ma_hoi_dong');
    }

    public function sangKiens(): HasMany
    {
        return $this->hasMany(SangKien::class, 'ma_hoi_dong');
    }

    protected static function booted()
    {
        static::created(function ($hoiDong) {
            // Tự động thêm trưởng hội đồng vào bảng thanh_vien_hoi_dong
            ThanhVienHoiDong::create([
                'ma_hoi_dong' => $hoiDong->id,
                'ma_nguoi_dung' => $hoiDong->ma_truong_hoi_dong,
            ]);
        });
    }
}
