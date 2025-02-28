<?php

namespace App\Filament\Resources\QuanLyNguoiDungResource\Pages;

use App\Filament\Resources\QuanLyNguoiDungResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditQuanLyNguoiDung extends EditRecord
{
    protected static string $resource = QuanLyNguoiDungResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $data;
    }

    protected function afterSave(): void
    {
        $user = $this->record;

        // Xử lý vai trò
        if (!empty($this->data['vai_tro_ids'])) {
            $user->lnkNguoiDungVaiTros()->delete();
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
            $user->lnkNguoiDungDonVis()->delete();
            foreach ($this->data['don_vi_ids'] as $donViId) {
                $user->lnkNguoiDungDonVis()->create([
                    'don_vi_id' => $donViId,
                    'nguoi_tao' => Auth::id(),
                    'nguoi_cap_nhat' => Auth::id(),
                ]);
            }
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
