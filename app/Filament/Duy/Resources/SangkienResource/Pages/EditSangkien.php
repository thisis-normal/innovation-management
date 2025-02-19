<?php

namespace App\Filament\Duy\Resources\SangkienResource\Pages;

use App\Filament\Duy\Resources\SangkienResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSangkien extends EditRecord
{
    protected static string $resource = SangkienResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
