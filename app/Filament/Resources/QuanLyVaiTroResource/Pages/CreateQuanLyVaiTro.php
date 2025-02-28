<?php

namespace App\Filament\Resources\QuanLyVaiTroResource\Pages;

use App\Filament\Resources\QuanLyVaiTroResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateQuanLyVaiTro extends CreateRecord
{
    protected static string $resource = QuanLyVaiTroResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Đảm bảo ma_vai_tro được gửi đi
        if (empty($data['ma_vai_tro'])) {
            throw new \Exception('Mã vai trò không được để trống');
        }

        return $data;
    }
}
