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
}
