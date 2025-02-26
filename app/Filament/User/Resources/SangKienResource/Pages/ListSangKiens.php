<?php

namespace App\Filament\User\Resources\SangKienResource\Pages;

use App\Filament\User\Resources\SangKienResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSangKiens extends ListRecords
{
    protected static string $resource = SangKienResource::class;
    protected static ?string $breadcrumb = 'Danh sách sáng kiến';

    protected function getHeaderActions(): array
    {
        return [
//            Actions\CreateAction::make(),
        ];
    }
}
