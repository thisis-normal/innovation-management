<?php

namespace App\Filament\Resources\QuanLyNguoiDungResource\Pages;

use App\Filament\Resources\QuanLyNguoiDungResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Filament\Actions\Action;
class CreateQuanLyNguoiDung extends CreateRecord
{
    protected static string $resource = QuanLyNguoiDungResource::class;
    protected static ?string $title = 'Tạo mới người dùng';
    protected static ?string $breadcrumb = 'Tạo mới';

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->label('Lưu');
    }

    protected function getCreateAnotherFormAction(): Action
    {
        return parent::getCreateAnotherFormAction()
            ->label('Lưu và tạo mới khác');
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()
            ->label('Huỷ bỏ');
    }

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

        // Xử lý đơn vị - Sử dụng updateOrCreate để tránh trùng lặp
        if (!empty($this->data['don_vi_ids'])) {
            foreach ($this->data['don_vi_ids'] as $donViId) {
                $user->lnkNguoiDungDonVis()->updateOrCreate(
                    [
                        'nguoi_dung_id' => $user->id,
                        'don_vi_id' => $donViId,
                    ],
                    [
                        'nguoi_tao' => Auth::id(),
                        'nguoi_cap_nhat' => Auth::id(),
                    ]
                );
            }
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
