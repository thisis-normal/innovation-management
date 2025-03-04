<?php

namespace App\Filament\Resources\QuanLyVaiTroResource\Pages;

use App\Filament\Resources\QuanLyVaiTroResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\Action;

class EditQuanLyVaiTro extends EditRecord
{
    protected static string $resource = QuanLyVaiTroResource::class;
    protected static ?string $title = 'Cập nhật vai trò';
    protected static ?string $breadcrumb = 'Cập nhật';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Xóa')
                ->modalHeading('Xóa vai trò')
                ->modalDescription('Bạn có chắc chắn muốn xóa vai trò này?')
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
