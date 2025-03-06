<?php

namespace App\Filament\Resources\QuanLySangKienResource\Pages;

use App\Filament\Resources\QuanLySangKienResource;
use Filament\Resources\Pages\CreateRecord;

class CreateQuanLySangKien extends CreateRecord
{
    protected static string $resource = QuanLySangKienResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
