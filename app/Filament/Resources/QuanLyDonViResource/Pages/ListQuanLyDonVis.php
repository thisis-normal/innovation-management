<?php

namespace App\Filament\Resources\QuanLyDonViResource\Pages;

use App\Filament\Resources\QuanLyDonViResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListQuanLyDonVis extends ListRecords
{
    protected static string $resource = QuanLyDonViResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Thêm mới đơn vị'),
        ];
    }
}
