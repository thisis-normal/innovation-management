<?php

namespace App\Filament\Resources\TieuChiResource\Pages;

use App\Filament\Resources\TieuChiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTieuChis extends ListRecords
{
    protected static string $resource = TieuChiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Thêm tiêu chí mới'),
        ];
    }

    public function getEmptyStateHeading(): ?string
    {
        return 'Không có dữ liệu';
    }

    public function getEmptyStateDescription(): ?string
    {
        return 'Bạn có thể tạo bản ghi mới bằng cách nhấn vào nút bên dưới.';
    }

    public function getEmptyStateIcon(): ?string
    {
        return 'heroicon-o-information-circle';
    }
}
