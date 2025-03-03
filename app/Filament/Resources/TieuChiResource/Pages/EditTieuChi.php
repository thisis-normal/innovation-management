<?php

namespace App\Filament\Resources\TieuChiResource\Pages;

use App\Filament\Resources\TieuChiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\Action;

class EditTieuChi extends EditRecord
{
    protected static string $resource = TieuChiResource::class;
    protected static ?string $title = 'Cập nhật tiêu chí';
    protected static ?string $breadcrumb = 'Cập nhật';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Xóa')
                ->modalHeading('Xóa tiêu chí')
                ->modalDescription('Bạn có chắc chắn muốn xóa tiêu chí này?')
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

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Đã lưu thay đổi thành công';
    }
}
