<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SangKien extends Model
{
    use HasFactory;

    protected $table = 'sang_kien';

    protected $fillable = [
        'ten_sang_kien',
        'mo_ta',
        'truoc_khi_ap_dung',
        'sau_khi_ap_dung',
        'ma_tac_gia',
        'ma_don_vi',
        'ma_trang_thai_sang_kien',
        'ma_hoi_dong',
        'ghi_chu',
        'ma_loai_sang_kien',
    ];

    protected $casts = [
        'files' => 'array',
    ];

    // Thêm method mới để kiểm tra trạng thái có thể edit hay không
    public function canEdit(): bool
    {
        $editableStatuses = [
            'draft',
            'rejected_manager',
            'rejected_secretary',
            'rejected_council'
        ];

        return in_array($this->trangThaiSangKien->ma_trang_thai, $editableStatuses);
    }

    // Relationships
    public function donVi(): BelongsTo
    {
        return $this->belongsTo(DonVi::class, 'ma_don_vi');
    }
    public function diemHoiDongs()
    {
        return $this->hasMany(DiemHoiDong::class, 'ma_sang_kien');
    }
    public function diemCaNhans()
    {
        return $this->hasMany(DiemCaNhan::class, 'ma_sang_kien');
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ma_tac_gia', 'id');
    }
    public function taiLieuSangKien(): HasMany
    {
        return $this->hasMany(TaiLieuSangKien::class, 'sang_kien_id');
    }
    public function trangThaiSangKien(): BelongsTo
    {
        return $this->belongsTo(TrangThaiSangKien::class, 'ma_trang_thai_sang_kien', 'id');
    }
    public function loaiSangKien()
    {
        return $this->belongsTo(LoaiSangKien::class, 'ma_loai_sang_kien');
    }
    public function hoiDongThamDinh(): BelongsTo
    {
        return $this->belongsTo(HoiDongThamDinh::class, 'ma_hoi_dong');
    }
}
