<?php

namespace App\Filament\Resources\QuanLyDonViResource\Pages;

use App\Filament\Resources\QuanLyDonViResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\Action;

class EditQuanLyDonVi extends EditRecord
{
    protected static string $resource = QuanLyDonViResource::class;
    protected static ?string $title = 'Cập nhật đơn vị';
    protected static ?string $breadcrumb = 'Cập nhật';
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Xóa')
                ->modalHeading('Xóa đơn vị')
                ->modalDescription('Bạn có chắc chắn muốn xóa đơn vị này?')
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
