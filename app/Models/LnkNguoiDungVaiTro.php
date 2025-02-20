<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LnkNguoiDungVaiTro extends Model
{
    use HasFactory;

    protected $table = 'lnk_nguoi_dung_vai_tro';

    protected $fillable = [
        'nguoi_dung_id',
        'vai_tro_id',
        'nguoi_tao',
        'nguoi_cap_nhat',
        'created_at',
        'updated_at'
    ];

    public function nguoiDung()
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }

    public function vaiTro()
    {
        return $this->belongsTo(VaiTro::class, 'vai_tro_id');
    }
}
