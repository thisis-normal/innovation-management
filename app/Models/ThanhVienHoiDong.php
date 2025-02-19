<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThanhVienHoiDong extends Model
{
    use HasFactory;

    protected $table = 'thanh_vien_hoi_dong';

    protected $fillable = [
        'ma_hoi_dong',
        'ma_nguoi_dung'
    ];

    public function hoiDong()
    {
        return $this->belongsTo(HoiDongThamDinh::class, 'ma_hoi_dong');
    }

    public function nguoiDung()
    {
        return $this->belongsTo(User::class, 'ma_nguoi_dung');
    }

    public function diemCaNhans()
    {
        return $this->hasMany(DiemCaNhan::class, 'ma_thanh_vien');
    }
}
