<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Exception;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\HasCustomRelations;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Hash;

/**
 * @property Collection|VaiTro[] $roles
 * @property Collection|DonVi[] $ma_don_vi
 */
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasCustomRelations, HasRoles;
    private mixed $email;
    private mixed $password;
    private mixed $username;
    private mixed $name;
    private mixed $email_verified_at;
    private mixed $remember_token;
    private mixed $created_at;
    private mixed $updated_at;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'email_verified_at',
        'remember_token',
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
    public function hasRole($roles): bool
    {
        if (is_string($roles)) {
            return $this->roles()->where('ma_vai_tro', $roles)->exists();
        }

        if (is_array($roles)) {
            return $this->roles()->whereIn('ma_vai_tro', $roles)->exists();
        }

        return false; // Return false for invalid input
    }

    public function donVis(): BelongsToMany
    {
        return $this->belongsToMany(DonVi::class, 'lnk_nguoi_dung_don_vi', 'nguoi_dung_id', 'don_vi_id')
            ->withPivot(['nguoi_tao', 'nguoi_cap_nhat'])
            ->withTimestamps();
    }

    public function syncDonVis(array $donViIds)
    {
        // Xóa tất cả liên kết hiện tại
        $this->lnkNguoiDungDonVis()->delete();

        // Tạo lại các liên kết mới
        foreach ($donViIds as $donViId) {
            $this->lnkNguoiDungDonVis()->create([
                'don_vi_id' => $donViId,
                'nguoi_tao' => Auth::id() ?? 1,
                'nguoi_cap_nhat' => Auth::id() ?? 1,
            ]);
        }
    }
}
