<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DotDanhGia extends Model
{
    use HasFactory;

    /**
     * Tên bảng trong cơ sở dữ liệu
     *
     * @var string
     */
    protected $table = 'dot_danh_gia';

    /**
     * Các thuộc tính có thể gán giá trị hàng loạt
     *
     * @var array
     */
    protected $fillable = [
        'nam',
        'so_dot',
        'mo_ta',
        'created_by',
        'updated_by',
    ];

    /**
     * Các thuộc tính cần ép kiểu
     *
     * @var array
     */
    protected $casts = [
        'nam' => 'integer',
        'so_dot' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Lấy tất cả tiêu chí đánh giá thuộc đợt đánh giá này
     */
    public function tieuChiDanhGias(): HasMany
    {
        return $this->hasMany(TieuChiDanhGia::class, 'dot_danh_gia_id');
    }

    /**
     * Lấy người tạo
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Lấy người cập nhật
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function isViewAction(): bool
    {
        return request()->route()->getName() === 'filament.resources.quan-ly-danh-gia.view';
    }
}
