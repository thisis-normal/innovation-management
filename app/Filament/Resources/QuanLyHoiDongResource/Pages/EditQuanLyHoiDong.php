<?php

namespace App\Filament\Resources\QuanLyHoiDongResource\Pages;

use App\Filament\Resources\QuanLyHoiDongResource;
use App\Models\ThanhVienHoiDong;
use App\Models\User;
use App\Models\DonVi;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms;
use CodeWithDennis\FilamentSelectTree\SelectTree;

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

                    Forms\Components\Grid::make()
                        ->schema([
                            SelectTree::make('don_vi_id')
                                ->label('Đơn vị')
                                ->relationship(
                                    'donVi',
                                    'ten_don_vi',
                                    'don_vi_cha_id'
                                )
                                ->searchable()
                                ->enableBranchNode()
                                ->defaultOpenLevel(2)
                                ->required()
                                ->reactive()
                                ->columnSpan(1),

                            Forms\Components\Select::make('ma_truong_hoi_dong')
                                ->label('Trưởng hội đồng')
                                ->options(function (callable $get, $record) {
                                    $donViId = $get('don_vi_id');
                                    if (!$donViId && $record) {
                                        $donViId = $record->don_vi_id;
                                    }

                                    if (!$donViId) {
                                        return [];
                                    }

                                    return User::whereHas('donVis', function ($query) use ($donViId) {
                                        $query->where('don_vi.id', $donViId);
                                    })->pluck('name', 'id');
                                })
                                ->searchable()
                                ->preload()
                                ->required()
                                ->reactive()
                                ->disabled(fn (callable $get, $record) => !$get('don_vi_id') && !($record && $record->don_vi_id))
                                ->helperText(fn (callable $get, $record) => !$get('don_vi_id') && !($record && $record->don_vi_id) ? 'Vui lòng chọn đơn vị trước' : '')
                                ->columnSpan(1),
                        ])
                        ->columns(2),

                    Forms\Components\Grid::make()
                        ->schema([
                            Forms\Components\DatePicker::make('ngay_bat_dau')
                                ->label('Ngày bắt đầu')
                                ->required()
                                ->columnSpan(1),

                            Forms\Components\DatePicker::make('ngay_ket_thuc')
                                ->label('Ngày kết thúc')
                                ->after('ngay_bat_dau')
                                ->required()
                                ->columnSpan(1),
                        ])
                        ->columns(2),

                    Forms\Components\Toggle::make('trang_thai')
                        ->label('Trạng thái')
                        ->onColor('success')
                        ->offColor('danger')
                        ->default(true)
                        ->required(),
                ])
                ->columns(1),
        ];
    }

    protected function afterSave(): void
    {
        $hoiDong = $this->record;

        // Đảm bảo trưởng hội đồng luôn là thành viên
        ThanhVienHoiDong::firstOrCreate([
            'ma_hoi_dong' => $hoiDong->id,
            'ma_nguoi_dung' => $hoiDong->ma_truong_hoi_dong,
        ]);
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
