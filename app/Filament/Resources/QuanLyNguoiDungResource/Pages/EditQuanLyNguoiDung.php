<?php

namespace App\Filament\Resources\QuanLyNguoiDungResource\Pages;

use App\Filament\Resources\QuanLyNguoiDungResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Filament\Actions\Action;

class EditQuanLyNguoiDung extends EditRecord
{
    protected static string $resource = QuanLyNguoiDungResource::class;
    protected static ?string $title = 'Cập nhật người dùng';
    protected static ?string $breadcrumb = 'Cập nhật';

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (empty($data['password'])) {
            unset($data['password']);
        }
        return $data;
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load vai_tro_ids
        $data['vai_tro_ids'] = $this->record->lnkNguoiDungVaiTros()
            ->pluck('vai_tro_id')
            ->toArray();

        // Load don_vi_id (đổi tên từ don_vi_ids)
        $data['don_vi_id'] = $this->record->lnkNguoiDungDonVis()
            ->first()?->don_vi_id;

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
        if (!empty($this->data['don_vi_id'])) {
            // Xóa liên kết cũ
            $user->lnkNguoiDungDonVis()->delete();

            // Tạo liên kết mới
            $user->lnkNguoiDungDonVis()->create([
                'don_vi_id' => $this->data['don_vi_id'],
                'nguoi_tao' => Auth::id(),
                'nguoi_cap_nhat' => Auth::id(),
            ]);
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Xóa')
                ->modalHeading('Xóa người dùng')
                ->modalDescription('Bạn có chắc chắn muốn xóa người dùng này?')
                ->modalSubmitActionLabel('Xóa')
                ->modalCancelActionLabel('Hủy bỏ'),
        ];
    }

    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()
            ->label('Lưu');
    }

    protected function getSaveAnotherFormAction(): Action
    {
        return parent::getSaveAnotherFormAction()
            ->label('Lưu và tạo mới khác');
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()
            ->label('Huỷ bỏ');
    }
}
