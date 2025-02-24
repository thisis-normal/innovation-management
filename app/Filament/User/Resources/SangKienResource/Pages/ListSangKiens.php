<?php

namespace App\Filament\User\Resources\SangKienResource\Pages;

use App\Filament\User\Resources\SangKienResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSangKiens extends ListRecords
{
    protected static string $resource = SangKienResource::class;

    protected function getHeaderActions(): array
    {
        return [
//            Actions\CreateAction::make(),
        ];
    }
}
