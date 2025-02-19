<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiemHoiDong extends Model
{
    use HasFactory;

    protected $table = 'diem_hoi_dong';

    protected $fillable = [
        'ma_sang_kien',
        'ma_hoi_dong',
        'diem_cuoi',
        'nhan_xet_chung',
        'nguoi_nhap',
        'created_at',
        'updated_at'
    ];

    public function sangKien()
    {
        return $this->belongsTo(SangKien::class, 'ma_sang_kien');
    }

    public function hoiDong()
    {
        return $this->belongsTo(HoiDongThamDinh::class, 'ma_hoi_dong');
    }

    public function nguoiNhap()
    {
        return $this->belongsTo(User::class, 'nguoi_nhap');
    }
}
