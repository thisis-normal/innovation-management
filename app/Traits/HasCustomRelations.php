<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait HasCustomRelations
{
    protected static function bootHasCustomRelations()
    {
        static::created(function (Model $model) {
            if (request()->has('roles')) {
                $roles = request()->input('roles');
                foreach ($roles as $roleId) {
                    $model->lnkNguoiDungVaiTros()->create([
                        'vai_tro_id' => $roleId,
                        'nguoi_tao' => Auth::id(),
                        'nguoi_cap_nhat' => Auth::id(),
                    ]);
                }
            }

            if (request()->has('lnkNguoiDungDonVis')) {
                $donVis = request()->input('lnkNguoiDungDonVis.don_vi_id');
                foreach ($donVis as $donViId) {
                    $model->lnkNguoiDungDonVis()->create([
                        'don_vi_id' => $donViId,
                        'nguoi_tao' => Auth::id(),
                        'nguoi_cap_nhat' => Auth::id(),
                    ]);
                }
            }
        });
    }
}
