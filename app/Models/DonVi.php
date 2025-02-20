<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonVi extends Model
{
    use HasFactory;

    protected $table = 'don_vi';

    protected $fillable = [
        'ten_don_vi',
        'mo_ta',
        'don_vi_cha_id',
        'trang_thai',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'trang_thai' => 'tinyint(1)'
    ];

    public function donViCha()
    {
        return $this->belongsTo(DonVi::class, 'don_vi_cha_id');
    }

    public function donViCon()
    {
        return $this->hasMany(DonVi::class, 'don_vi_cha_id');
    }

    public function sangKiens()
    {
        return $this->hasMany(SangKien::class, 'ma_don_vi');
    }

    public function nguoiDungs()
    {
        return $this->belongsToMany(User::class, 'lnk_nguoi_dung_don_vi', 'don_vi_id', 'nguoi_dung_id');
    }
}
