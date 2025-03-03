<?php

namespace App\Filament\Resources\QuanLyDonViResource\Pages;

use App\Filament\Resources\QuanLyDonViResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListQuanLyDonVis extends ListRecords
{
    protected static string $resource = QuanLyDonViResource::class;
    protected static ?string $breadcrumb = 'Danh sách quản lý đơn vị';
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Thêm mới đơn vị'),
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
