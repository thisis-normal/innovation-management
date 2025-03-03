<?php

namespace App\Filament\Resources\QuanLyVaiTroResource\Pages;

use App\Filament\Resources\QuanLyVaiTroResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListQuanLyVaiTros extends ListRecords
{
    protected static string $resource = QuanLyVaiTroResource::class;
    protected static ?string $breadcrumb = 'Danh sách quản lý vai trò';
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Thêm mới vai trò'),
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
