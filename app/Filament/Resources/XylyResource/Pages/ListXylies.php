<?php

namespace App\Filament\Resources\XylyResource\Pages;

use App\Filament\Resources\XylyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListXylies extends ListRecords
{
    protected static string $resource = XylyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
