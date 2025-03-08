<?php

namespace App\Filament\User\Resources\SangKienResource\Pages;

use App\Filament\User\Resources\SangKienResource;
use App\Models\TaiLieuSangKien;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateSangKien extends CreateRecord
{
    protected static string $resource = SangKienResource::class;
    protected static ?string $navigationLabel = 'Tạo mới sáng kiến';
    protected static ?string $title = 'Tạo mới sáng kiến';
    protected static ?string $breadcrumb = 'Tạo mới';
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Remove 'files' so it is not stored in the main table
        unset($data['files']);

        // Ensure ma_don_vi is set
        if (!isset($data['ma_don_vi'])) {
            $data['ma_don_vi'] = auth()->user()->ma_don_vi;
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        // Get the form state after creation
        $data = $this->form->getState();
        // Check if files were uploaded
        if (! empty($data['files'])) {
            foreach ($data['files'] as $filePath) {
                TaiLieuSangKien::query()->create([
                    'sang_kien_id' => $this->record->id,
                    'file_path'   => $filePath,
                ]);
            }
        }
    }

    // Thêm method để kiểm tra trước khi hiển thị form
    protected function beforeFill(): void
    {
        $user = auth()->user();

        if (!$user->ma_don_vi) {
            Notification::make()
                ->title('Lỗi')
                ->body('Bạn chưa được gán đơn vị. Vui lòng liên hệ quản trị viên.')
                ->danger()
                ->send();

            $this->redirect(SangKienResource::getUrl('index'));
        }
    }
}
