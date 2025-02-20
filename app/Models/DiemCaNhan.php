<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiemCaNhan extends Model
{
    use HasFactory;

    protected $table = 'diem_ca_nhan';

    protected $fillable = [
        'ma_sang_kien',
        'ma_thanh_vien',
        'diem',
        'nhan_xet',
        'created_at',
        'updated_at'
    ];

    public function sangKien()
    {
        return $this->belongsTo(SangKien::class, 'ma_sang_kien');
    }

    public function thanhVien()
    {
        return $this->belongsTo(ThanhVienHoiDong::class, 'ma_thanh_vien');
    }
}
