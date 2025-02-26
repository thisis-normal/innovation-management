<?php

namespace App\Filament\Resources\TieuChiResource\Pages;

use App\Filament\Resources\TieuChiResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

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
}
