<?php

namespace App\Filament\Resources\QuanLyDonViResource\Pages;

use App\Filament\Resources\QuanLyDonViResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditQuanLyDonVi extends EditRecord
{
    protected static string $resource = QuanLyDonViResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
