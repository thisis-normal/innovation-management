<?php

namespace App\Filament\Resources\SangKienResource\Pages;

use App\Filament\Resources\SangKienResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSangKien extends EditRecord
{
    protected static string $resource = SangKienResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Xóa'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
