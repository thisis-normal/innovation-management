<?php

namespace App\Filament\Resources\QuanLyLoaiSangKienResource\Pages;

use App\Filament\Resources\QuanLyLoaiSangKienResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListQuanLyLoaiSangKiens extends ListRecords
{
    protected static string $resource = QuanLyLoaiSangKienResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
