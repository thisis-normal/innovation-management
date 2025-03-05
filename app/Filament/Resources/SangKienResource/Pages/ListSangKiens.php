<?php

namespace App\Filament\Resources\SangKienResource\Pages;

use App\Filament\Resources\SangKienResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSangKiens extends ListRecords
{
    protected static string $resource = SangKienResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Thêm sáng kiến'),
        ];
    }
}
