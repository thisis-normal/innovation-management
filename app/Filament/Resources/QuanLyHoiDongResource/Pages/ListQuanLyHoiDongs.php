<?php

namespace App\Filament\Resources\QuanLyHoiDongResource\Pages;

use App\Filament\Resources\QuanLyHoiDongResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListQuanLyHoiDongs extends ListRecords
{
    protected static string $resource = QuanLyHoiDongResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
