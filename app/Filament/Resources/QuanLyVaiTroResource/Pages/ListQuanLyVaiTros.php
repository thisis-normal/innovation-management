<?php

namespace App\Filament\Resources\QuanLyVaiTroResource\Pages;

use App\Filament\Resources\QuanLyVaiTroResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListQuanLyVaiTros extends ListRecords
{
    protected static string $resource = QuanLyVaiTroResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Thêm mới vai trò'),
        ];
    }
}
