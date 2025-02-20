<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'ngay_bat_dau' => 'date',
        'ngay_ket_thuc' => 'date',
        'trang_thai' => 'tinyint(1)'
    ];

    public function truongHoiDong()
    {
        return $this->belongsTo(User::class, 'ma_truong_hoi_dong');
    }

    public function thanhViens()
    {
        return $this->hasMany(ThanhVienHoiDong::class, 'ma_hoi_dong');
    }

    public function diemHoiDongs()
    {
        return $this->hasMany(DiemHoiDong::class, 'ma_hoi_dong');
    }
}
