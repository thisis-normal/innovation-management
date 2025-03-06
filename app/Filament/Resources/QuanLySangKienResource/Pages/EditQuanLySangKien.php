<?php

namespace App\Filament\Resources\QuanLySangKienResource\Pages;

use App\Filament\Resources\QuanLySangKienResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditQuanLySangKien extends EditRecord
{
    protected static string $resource = QuanLySangKienResource::class;

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
