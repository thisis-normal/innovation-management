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
        'trang_thai' => 'boolean'
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

    public static function getTreeData()
    {
        $allDonVi = self::all();

        return $allDonVi->map(function ($donVi) {
            return [
                'id' => $donVi->id,
                'parent_id' => $donVi->don_vi_cha_id,
                'title' => $donVi->ten_don_vi,
            ];
        })->toArray();
    }

    public static function getTreeOptions($excludeId = null)
    {
        $allDonVis = self::all();
        $options = [];

        // Lấy các đơn vị gốc
        $rootDonVis = $allDonVis->whereNull('don_vi_cha_id')->sortBy('ten_don_vi');

        foreach ($rootDonVis as $donVi) {
            if ($donVi->id == $excludeId) continue;

            // Đơn vị gốc luôn dùng icon folder
            $options[$donVi->id] = "📁 " . $donVi->ten_don_vi;

            // Lấy đơn vị con cấp 1
            $children = $allDonVis->where('don_vi_cha_id', $donVi->id)->sortBy('ten_don_vi');
            foreach ($children as $child) {
                if ($child->id == $excludeId) continue;

                // Đơn vị con cấp 1 dùng icon folder nếu có con, ngược lại dùng icon file
                $hasGrandChildren = $allDonVis->where('don_vi_cha_id', $child->id)->count() > 0;
                $icon = $hasGrandChildren ? "📁" : "📄";
                $options[$child->id] = "    └─ " . $icon . " " . $child->ten_don_vi;

                // Lấy đơn vị con cấp 2
                $grandChildren = $allDonVis->where('don_vi_cha_id', $child->id)->sortBy('ten_don_vi');
                foreach ($grandChildren as $grandChild) {
                    if ($grandChild->id == $excludeId) continue;
                    $options[$grandChild->id] = "        └─ " . "📄 " . $grandChild->ten_don_vi;
                }
            }
        }

        return $options;
    }

    public function getAllParentIds()
    {
        $parentIds = [];
        $current = $this;

        while ($current->don_vi_cha_id !== null) {
            $parentIds[] = $current->don_vi_cha_id;
            $current = $current->donViCha;
        }

        return $parentIds;
    }

    public function getAllChildIds()
    {
        $childIds = [];
        $this->addChildIds($this, $childIds);
        return $childIds;
    }

    private function addChildIds($donVi, &$childIds)
    {
        foreach ($donVi->donViCon as $child) {
            $childIds[] = $child->id;
            $this->addChildIds($child, $childIds);
        }
    }
}
