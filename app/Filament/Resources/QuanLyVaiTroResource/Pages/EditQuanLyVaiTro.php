<?php

namespace App\Filament\Resources\QuanLyVaiTroResource\Pages;

use App\Filament\Resources\QuanLyVaiTroResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditQuanLyVaiTro extends EditRecord
{
    protected static string $resource = QuanLyVaiTroResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
