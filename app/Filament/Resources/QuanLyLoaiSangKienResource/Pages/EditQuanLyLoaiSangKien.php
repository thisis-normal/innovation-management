<?php

namespace App\Filament\Resources\QuanLyLoaiSangKienResource\Pages;

use App\Filament\Resources\QuanLyLoaiSangKienResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditQuanLyLoaiSangKien extends EditRecord
{
    protected static string $resource = QuanLyLoaiSangKienResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
