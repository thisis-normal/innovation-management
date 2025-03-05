<?php

namespace App\Filament\Resources\QuanLyHoiDongResource\Pages;

use App\Filament\Resources\QuanLyHoiDongResource;
use App\Models\ThanhVienHoiDong;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms;
use App\Models\User;

class EditQuanLyHoiDong extends EditRecord
{
    protected static string $resource = QuanLyHoiDongResource::class;

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Card::make()
                ->schema([
                    Forms\Components\TextInput::make('ten_hoi_dong')
                        ->label('Tên hội đồng')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\Select::make('ma_truong_hoi_dong')
                        ->label('Trưởng hội đồng')
                        ->options(User::all()->pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->required(),

                    Forms\Components\Select::make('thanh_vien')
                        ->label('Thành viên hội đồng')
                        ->multiple()
                        ->options(function ($record) {
                            return User::query()
                                ->whereNotIn('id', [$record->ma_truong_hoi_dong])
                                ->pluck('name', 'id');
                        })
                        ->searchable()
                        ->preload()
                        ->default(function ($record) {
                            return $record->thanhVienHoiDongs()
                                ->where('ma_nguoi_dung', '!=', $record->ma_truong_hoi_dong)
                                ->pluck('ma_nguoi_dung')
                                ->toArray();
                        })
                        ->helperText('Chọn các thành viên khác của hội đồng'),
                ])
                ->columns(1),
        ];
    }

    protected function afterSave(): void
    {
        $hoiDong = $this->record;
        $thanhVien = $this->data['thanh_vien'] ?? [];

        // Xóa tất cả thành viên cũ (trừ trưởng hội đồng)
        ThanhVienHoiDong::where('ma_hoi_dong', $hoiDong->id)
            ->where('ma_nguoi_dung', '!=', $hoiDong->ma_truong_hoi_dong)
            ->delete();

        // Thêm lại các thành viên mới
        foreach ($thanhVien as $userId) {
            if ($userId && $userId != $hoiDong->ma_truong_hoi_dong) {
                ThanhVienHoiDong::create([
                    'ma_hoi_dong' => $hoiDong->id,
                    'ma_nguoi_dung' => $userId,
                ]);
            }
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
