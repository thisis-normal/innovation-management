<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VaiTro extends Model
{
    use HasFactory;

    protected $table = 'vai_tro';

    protected $fillable = [
        'ma_vai_tro',
        'ten_vai_tro',
        'mo_ta',
        'trang_thai',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'trang_thai' => 'boolean'
    ];

    public function nguoiDungs()
    {
        return $this->belongsToMany(User::class, 'lnk_nguoi_dung_vai_tro', 'vai_tro_id', 'nguoi_dung_id');
    }

    public function menus()
    {
        return $this->hasMany(Menu::class, 'vai_tro_id');
    }
}
