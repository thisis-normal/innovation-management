<?php

namespace App\Filament\Resources\TieuChiResource\Pages;

use App\Filament\Resources\TieuChiResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions\Action;

class CreateTieuChi extends CreateRecord
{
    protected static string $resource = TieuChiResource::class;

    protected static ?string $navigationLabel = 'Tạo mới tiêu chí';
    protected static ?string $title = 'Tạo mới tiêu chí';
    protected static ?string $breadcrumb = 'Tạo mới';

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Đã tạo tiêu chí thành công';
    }

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
}
