<?php

namespace App\Filament\Resources\QuanLyNguoiDungResource\Pages;

use App\Filament\Resources\QuanLyNguoiDungResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListQuanLyNguoiDungs extends ListRecords
{
    protected static string $resource = QuanLyNguoiDungResource::class;
    protected static ?string $breadcrumb = 'Danh sách quản lý người dùng';
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Thêm mới người dùng'),
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
