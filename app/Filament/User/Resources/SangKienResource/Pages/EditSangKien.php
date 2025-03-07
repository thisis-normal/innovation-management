<?php

namespace App\Filament\User\Resources\SangKienResource\Pages;

use App\Filament\User\Resources\SangKienResource;
use App\Models\TaiLieuSangKien;
use App\Models\TrangThaiSangKien;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditSangKien extends EditRecord
{
    protected static string $resource = SangKienResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    public function mount($record): void
    {
        parent::mount($record);

        $user = Auth::user();
        // Check if the innovation's status is pending (pending_secretary)
        if ($this->record->trangThaiSangKien->ma_trang_thai === 'pending_secretary'
            && $this->record->ma_tac_gia === $user->id) {
            Notification::make()
                ->title('Không thể chỉnh sửa sáng kiến trong trạng thái này')
                ->warning()
                ->send();
            $this->redirect(SangKienResource::getUrl('index'));
        }
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

    /**
     * Override the default updated notification title.
     */
    protected function getSavedNotificationTitle(): ?string
    {
        return 'Sáng kiến đã được cập nhật và chuyển về trạng thái bản nháp.';
    }
}
