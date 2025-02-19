<?php

namespace App\Filament\Resources\XylyResource\Pages;

use App\Filament\Resources\XylyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditXyly extends EditRecord
{
    protected static string $resource = XylyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
