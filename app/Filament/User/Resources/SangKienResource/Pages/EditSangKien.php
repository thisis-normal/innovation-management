<?php

namespace App\Filament\User\Resources\SangKienResource\Pages;

use App\Filament\User\Resources\SangKienResource;
use App\Models\TaiLieuSangKien;
use App\Models\TrangThaiSangKien;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSangKien extends EditRecord
{
    protected static string $resource = SangKienResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Remove 'files' so it is not stored in the main table
        unset($data['files']);

        // Lấy ID của trạng thái draft
        $draftStatusId = TrangThaiSangKien::query()
            ->where('ma_trang_thai', 'draft')
            ->first()
            ->id;

        // Set trạng thái về draft
        $data['ma_trang_thai_sang_kien'] = $draftStatusId;

        return $data;
    }

    protected function afterSave(): void
    {
        $record = $this->record;
        // Get the uploaded files from the form state
        $files = $this->data['files'] ?? [];
        // Get the current file entries
        $existingRecords = TaiLieuSangKien::query()->where('sang_kien_id', $record->id)->get();
        $existingFilePaths = $existingRecords->pluck('file_path')->toArray();

        // Track which entries to delete
        $idsToDelete = [];

        // Find files to delete
        foreach ($existingRecords as $existingRecord) {
            if (!in_array($existingRecord->file_path, $files)) {
                $idsToDelete[] = $existingRecord->id;
            }
        }

        // Delete removed files
        if (!empty($idsToDelete)) {
            TaiLieuSangKien::query()->whereIn('id', $idsToDelete)->delete();
        }

        // Add new files
        foreach ($files as $filePath) {
            if (!in_array($filePath, $existingFilePaths)) {
                TaiLieuSangKien::query()->create([
                    'sang_kien_id' => $record->id,
                    'file_path' => $filePath,
                ]);
            }
        }
        // Redirect to index page
        $this->redirect(SangKienResource::getUrl());
    }
    protected function getSavedNotificationMessage(): ?string
    {
        return 'Cập nhật thành công';
    }
}
