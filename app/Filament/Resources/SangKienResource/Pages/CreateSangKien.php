<?php

namespace App\Filament\Resources\SangKienResource\Pages;

use App\Filament\Resources\SangKienResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSangKien extends CreateRecord
{
    protected static string $resource = SangKienResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
