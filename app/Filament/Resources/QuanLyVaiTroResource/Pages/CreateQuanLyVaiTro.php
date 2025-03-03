<?php

namespace App\Filament\Resources\QuanLyVaiTroResource\Pages;

use App\Filament\Resources\QuanLyVaiTroResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions\Action;

class CreateQuanLyVaiTro extends CreateRecord
{
    protected static string $resource = QuanLyVaiTroResource::class;
    protected static ?string $title = 'Tạo mới vai trò';
    protected static ?string $breadcrumb = 'Tạo mới';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Đảm bảo ma_vai_tro được gửi đi
        if (empty($data['ma_vai_tro'])) {
            throw new \Exception('Mã vai trò không được để trống');
        }

        return $data;
    }

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->label('Lưu');
    }

    protected function getCreateAnotherFormAction(): Action
    {
        return parent::getCreateAnotherFormAction()
            ->label('Lưu và tạo mới khác');
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()
            ->label('Huỷ bỏ');
    }
}
