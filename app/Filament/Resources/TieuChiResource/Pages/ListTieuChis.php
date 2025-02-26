<?php

namespace App\Filament\Resources\TieuChiResource\Pages;

use App\Filament\Resources\TieuChiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTieuChis extends ListRecords
{
    protected static string $resource = TieuChiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Thêm tiêu chí mới'),
        ];
    }
}
