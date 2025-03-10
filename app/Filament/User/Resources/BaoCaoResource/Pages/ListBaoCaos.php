<?php

namespace App\Filament\User\Resources\BaoCaoResource\Pages;

use App\Filament\User\Resources\BaoCaoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBaoCaos extends ListRecords
{
    protected static string $resource = BaoCaoResource::class;
    protected static ?string $breadcrumb = 'Danh sách báo cáo';
    protected function getHeaderActions(): array
    {
        return [
//            Actions\CreateAction::make(),
        ];
    }
}
