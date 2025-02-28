<?php

namespace App\Filament\Resources\QuanLyNguoiDungResource\Pages;

use App\Filament\Resources\QuanLyNguoiDungResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateQuanLyNguoiDung extends CreateRecord
{
    protected static string $resource = QuanLyNguoiDungResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $data;
    }

    protected function afterCreate(): void
    {
        $user = $this->record;

        // Xử lý vai trò
        if (!empty($this->data['vai_tro_ids'])) {
            foreach ($this->data['vai_tro_ids'] as $vaiTroId) {
                $user->lnkNguoiDungVaiTros()->create([
                    'vai_tro_id' => $vaiTroId,
                    'nguoi_tao' => Auth::id(),
                    'nguoi_cap_nhat' => Auth::id(),
                ]);
            }
        }

        // Xử lý đơn vị
        if (!empty($this->data['don_vi_ids'])) {
            foreach ($this->data['don_vi_ids'] as $donViId) {
                $user->lnkNguoiDungDonVis()->create([
                    'don_vi_id' => $donViId,
                    'nguoi_tao' => Auth::id(),
                    'nguoi_cap_nhat' => Auth::id(),
                ]);
            }
        }
    }
}
