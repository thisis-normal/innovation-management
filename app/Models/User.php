<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Exception;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;
    private mixed $email;
    private mixed $password;
    private mixed $username;
    private mixed $name;
    private mixed $email_verified_at;
    private mixed $remember_token;
    private mixed $trang_thai_hoat_dong;
    private mixed $created_at;
    private mixed $updated_at;
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
     * @throws Exception
     */
    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->hasRole('admin');
        }
        return true;
    }

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

    public function thanhVienHoiDongs(): HasMany
    {
        return $this->hasMany(ThanhVienHoiDong::class, 'ma_nguoi_dung', 'id');
    }

    public function lnkNguoiDungDonVis(): HasMany
    {
        return $this->hasMany(LnkNguoiDungDonVi::class, 'nguoi_dung_id', 'id');
    }

    public function lnkNguoiDungVaiTros(): HasMany
    {
        return $this->hasMany(LnkNguoiDungVaiTro::class, 'nguoi_dung_id', 'id');
    }
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(VaiTro::class, 'lnk_nguoi_dung_vai_tro', 'nguoi_dung_id', 'vai_tro_id');
    }
    public function hasRole(string $role): bool
    {
        return $this->roles()->where('ten_vai_tro', $role)->exists();
    }
}
