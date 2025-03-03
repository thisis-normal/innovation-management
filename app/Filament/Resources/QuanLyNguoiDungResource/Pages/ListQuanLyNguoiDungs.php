<?php

namespace App\Filament\Resources\QuanLyNguoiDungResource\Pages;

use App\Filament\Resources\QuanLyNguoiDungResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListQuanLyNguoiDungs extends ListRecords
{
    protected static string $resource = QuanLyNguoiDungResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
