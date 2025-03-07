<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Providers\Filament\UserPanelProvider;
use Exception;
use Filament\Facades\Filament;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Removed delete action
        ];
    }

    /**
     * @throws Exception
     */
    public function mount($record): void
    {
        parent::mount($record);
        $currentUser = Auth::user();
        $currentPanelId = Filament::getCurrentPanel()->getId();
        if ($currentPanelId === 'user' && $this->record->id !== $currentUser->id) {
            Notification::make()
                ->title('Cảnh báo!')
                ->body('Bạn không được phép chỉnh sửa thông tin người dùng khác trong trang này.')
                ->danger()
                ->send();
            $this->redirect(Filament::getPanel('user')->getUrl());
        }
    }

    protected function afterSave(): void
    {
        Notification::make()
            ->title('Cập nhật thông tin thành công')
            ->success()
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }
}
