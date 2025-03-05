<?php

namespace App\Filament\Resources\QuanLyHoiDongResource\Pages;

use App\Filament\Resources\QuanLyHoiDongResource;
use App\Models\ThanhVienHoiDong;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms;
use App\Models\User;

class CreateQuanLyHoiDong extends CreateRecord
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
                        ->required()
                        ->reactive(),

                    Forms\Components\Select::make('thanh_vien')
                        ->label('Thành viên hội đồng')
                        ->multiple()
                        ->options(function (callable $get) {
                            $truongHoiDongId = $get('ma_truong_hoi_dong');
                            return User::query()
                                ->when($truongHoiDongId, fn($query) =>
                                    $query->whereNotIn('id', [$truongHoiDongId])
                                )
                                ->pluck('name', 'id');
                        })
                        ->searchable()
                        ->preload(),

                    Forms\Components\DatePicker::make('ngay_bat_dau')
                        ->label('Ngày bắt đầu')
                        ->required(),

                    Forms\Components\DatePicker::make('ngay_ket_thuc')
                        ->label('Ngày kết thúc')
                        ->after('ngay_bat_dau')
                        ->required(),

                    Forms\Components\Select::make('trang_thai')
                        ->label('Trạng thái')
                        ->options([
                            1 => 'Hoạt động',
                            0 => 'Không hoạt động',
                        ])
                        ->default(1)
                        ->required(),
                ])
                ->columns(2),
        ];
    }

    protected function afterCreate(): void
    {
        $hoiDong = $this->record;
        $thanhVien = $this->data['thanh_vien'] ?? [];

        // Thêm các thành viên được chọn
        foreach ($thanhVien as $userId) {
            if ($userId && $userId != $hoiDong->ma_truong_hoi_dong) {
                ThanhVienHoiDong::create([
                    'ma_hoi_dong' => $hoiDong->id,
                    'ma_nguoi_dung' => $userId,
                ]);
            }
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
