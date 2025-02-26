<?php

namespace App\Filament\Resources\TieuChiResource\Pages;

use App\Filament\Resources\TieuChiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTieuChi extends EditRecord
{
    protected static string $resource = TieuChiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Xóa')
                ->modalHeading('Xóa tiêu chí')
                ->modalDescription('Bạn có chắc chắn muốn xóa tiêu chí này?')
                ->modalSubmitActionLabel('Xóa')
                ->modalCancelActionLabel('Hủy'),
        ];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Đã lưu thay đổi thành công';
    }
}
