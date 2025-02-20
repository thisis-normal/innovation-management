<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'name',
        'email',
        'password',
        'email_verified_at',
        'remember_token',
        'trang_thai_hoat_dong',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'trang_thai_hoat_dong' => 'tinyint(1)'
    ];

    public function thanhVienHoiDongs()
    {
        return $this->hasMany(ThanhVienHoiDong::class, 'ma_nguoi_dung', 'id');
    }

    public function lnkNguoiDungDonVis()
    {
        return $this->hasMany(LnkNguoiDungDonVi::class, 'nguoi_dung_id', 'id');
    }

    public function lnkNguoiDungVaiTros()
    {
        return $this->hasMany(LnkNguoiDungVaiTro::class, 'nguoi_dung_id', 'id');
    }
}
