<?php

namespace App\Filament\User\Resources\SangKienReviewResource\Pages;

use App\Filament\User\Resources\SangKienReviewResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSangKienReviews extends ListRecords
{
    protected static string $resource = SangKienReviewResource::class;
    protected static ?string $title = 'Phê duyệt sáng kiến';
    protected static ?string $breadcrumb = 'Duyệt sáng kiến';

    protected function getHeaderActions(): array
    {
        return [
//            Actions\CreateAction::make(),
        ];
    }
}
