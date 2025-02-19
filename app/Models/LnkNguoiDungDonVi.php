<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LnkNguoiDungDonVi extends Model
{
    use HasFactory;

    protected $table = 'lnk_nguoi_dung_don_vi';

    protected $fillable = [
        'nguoi_dung_id',
        'don_vi_id',
        'nguoi_tao',
        'nguoi_cap_nhat',
        'created_at',
        'updated_at'
    ];

    public function nguoiDung()
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }

    public function donVi()
    {
        return $this->belongsTo(DonVi::class, 'don_vi_id');
    }
}
