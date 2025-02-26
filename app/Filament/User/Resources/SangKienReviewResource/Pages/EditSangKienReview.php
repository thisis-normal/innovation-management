<?php

namespace App\Filament\User\Resources\SangKienReviewResource\Pages;

use App\Filament\User\Resources\SangKienReviewResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSangKienReview extends EditRecord
{
    protected static string $resource = SangKienReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
