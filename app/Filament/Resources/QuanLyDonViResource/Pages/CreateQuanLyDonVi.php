<?php

namespace App\Filament\Resources\QuanLyDonViResource\Pages;

use App\Filament\Resources\QuanLyDonViResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions\Action;

class CreateQuanLyDonVi extends CreateRecord
{
    protected static string $resource = QuanLyDonViResource::class;
    protected static ?string $title = 'Tạo mới đơn vị';
    protected static ?string $breadcrumb = 'Tạo mới';

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
