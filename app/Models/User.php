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
use Illuminate\Support\Facades\Hash;

/**
 * @property integer $id
 * @property Collection|VaiTro[] $roles
 * @property Collection|DonVi[] $ma_don_vi
 */
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasCustomRelations;
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
            // Kiểm tra qua vai trò của người dùng
            return $this->lnkNguoiDungVaiTros()
                ->whereHas('vaiTro', function ($query) {
                    $query->where('ma_vai_tro', 'admin');
                })
                ->exists();
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

    // Quan hệ với HoiDongThamDinh (là trưởng hội đồng)
    public function hoiDongLamTruong(): HasMany
    {
        return $this->hasMany(HoiDongThamDinh::class, 'ma_truong_hoi_dong');
    }

    // Quan hệ với ThanhVienHoiDong
    public function thanhVienHoiDongs(): HasMany
    {
        return $this->hasMany(ThanhVienHoiDong::class, 'ma_nguoi_dung');
    }

    // Quan hệ với HoiDongThamDinh thông qua ThanhVienHoiDong
    public function hoiDongs(): BelongsToMany
    {
        return $this->belongsToMany(HoiDongThamDinh::class, 'thanh_vien_hoi_dong', 'ma_nguoi_dung', 'ma_hoi_dong')
                    ->withTimestamps();
    }

    public function lnkNguoiDungDonVis(): HasMany
    {
        return $this->hasMany(LnkNguoiDungDonVi::class, 'nguoi_dung_id');
    }

    public function lnkNguoiDungVaiTros(): HasMany
    {
        return $this->hasMany(LnkNguoiDungVaiTro::class, 'nguoi_dung_id', 'id');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(VaiTro::class, 'lnk_nguoi_dung_vai_tro', 'nguoi_dung_id', 'vai_tro_id')
            ->withPivot(['nguoi_tao', 'nguoi_cap_nhat'])
            ->withTimestamps();
    }
    public function hasRole($roles): bool
    {
        if (is_string($roles)) {
            return $this->lnkNguoiDungVaiTros()
                ->whereHas('vaiTro', function ($query) use ($roles) {
                    $query->where('ma_vai_tro', $roles);
                })
                ->exists();
        }

        if (is_array($roles)) {
            return $this->lnkNguoiDungVaiTros()
                ->whereHas('vaiTro', function ($query) use ($roles) {
                    $query->whereIn('ma_vai_tro', $roles);
                })
                ->exists();
        }

        return false;
    }

    public function donVis(): BelongsToMany
    {
        return $this->belongsToMany(DonVi::class, 'lnk_nguoi_dung_don_vi', 'nguoi_dung_id', 'don_vi_id')
            ->withPivot(['nguoi_tao', 'nguoi_cap_nhat'])
            ->withTimestamps();
    }

    public function syncDonVis(array $donViIds): void
    {
        $pivotData = array_fill_keys($donViIds, [
            'nguoi_tao' => Auth::id() ?? 1,
            'nguoi_cap_nhat' => Auth::id() ?? 1,
        ]);

        $this->donVis()->sync($pivotData);
    }

    public function donVi(): BelongsToMany
    {
        return $this->donVis()->oldest('lnk_nguoi_dung_don_vi.created_at')->limit(1);
    }

    public function getMaDonViAttribute(): ?int
    {
        return $this->donVi->first()?->id;
    }
}
