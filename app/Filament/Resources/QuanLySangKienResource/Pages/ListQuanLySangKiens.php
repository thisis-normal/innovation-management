<?php

namespace App\Filament\Resources\QuanLySangKienResource\Pages;

use App\Filament\Resources\QuanLySangKienResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListQuanLySangKiens extends ListRecords
{
    protected static string $resource = QuanLySangKienResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Thêm sáng kiến'),
        ];
    }
}
