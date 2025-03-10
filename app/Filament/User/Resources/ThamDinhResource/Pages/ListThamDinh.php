<?php

namespace App\Filament\User\Resources\ThamDinhResource\Pages;

use App\Filament\User\Resources\ThamDinhResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use App\Models\DiemCaNhan;
use App\Models\DiemHoiDong;
use App\Models\TrangThaiSangKien;
use Filament\Support\Enums\IconPosition;
use App\Models\SangKien;


class ListThamDinh extends ListRecords
{
    protected static string $resource = ThamDinhResource::class;
    protected static ?string $title = 'Thẩm định sáng kiến';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                SangKien::query()
                    ->with([
                        'trangThaiSangKien',
                        'user',
                        'hoiDongThamDinh',
                        'hoiDongThamDinh.thanhVienHoiDongs',
                        'thanhVienHoiDongs'
                    ])
            )
            ->columns([
                TextColumn::make('ten_sang_kien')
                    ->label('Tên sáng kiến')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Tác giả')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('hoiDongThamDinh.ten_hoi_dong')
                    ->label('Hội đồng thẩm định')
                    ->searchable(),

                TextColumn::make('trangThaiSangKien.ten_trang_thai')
                    ->label('Trạng thái')
                    ->formatStateUsing(function ($record) {
                        if (!$record->trangThaiSangKien) {
                            return 'Không xác định';
                        }

                        if (!$record->hoiDongThamDinh) {
                            return 'Chưa có hội đồng thẩm định';
                        }

                        if ($record->trangThaiSangKien->ma_trang_thai === 'pending_council') {
                            $approvedCount = $record->thanhVienHoiDongs()
                                ->wherePivot('da_duyet', true)
                                ->count();
                            $totalMembers = $record->hoiDongThamDinh->thanhVienHoiDongs()->count();
                            return "Chờ hội đồng phê duyệt ({$approvedCount}/{$totalMembers})";
                        }

                        if ($record->trangThaiSangKien->ma_trang_thai === 'scoring1') {
                            $completedCount = DiemCaNhan::where('ma_sang_kien', $record->id)->count();
                            $totalMembers = $record->hoiDongThamDinh->thanhVienHoiDongs()->count();
                            return "Chấm điểm vòng 1 ({$completedCount}/{$totalMembers})";
                        }

                        if ($record->trangThaiSangKien->ma_trang_thai === 'scoring2') {
                            $diemHoiDong = DiemHoiDong::where('ma_sang_kien', $record->id)->exists();
                            return $diemHoiDong ? "Đã chấm điểm vòng 2" : "Chờ chấm điểm vòng 2";
                        }

                        return $record->trangThaiSangKien->ten_trang_thai;
                    })
                    ->badge()
                    ->color(function ($record) {
                        if (!$record->trangThaiSangKien) {
                            return 'gray';
                        }

                        return match ($record->trangThaiSangKien->ma_trang_thai) {
                            'pending_council' => 'warning',
                            'scoring1' => 'info',
                            'scoring2' => 'success',
                            default => 'gray',
                        };
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status_type')
                    ->label('Danh sách')
                    ->options([
                        'pending' => 'Chờ thẩm định',
                        'scoring' => 'Chấm điểm'
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['value'])) {
                            $data['value'] = 'pending';
                        }
                        return $query->whereHas('trangThaiSangKien', function (Builder $query) use ($data) {
                            if ($data['value'] === 'pending') {
                                $query->where('ma_trang_thai', 'pending_council');
                            } else {
                                $query->whereIn('ma_trang_thai', ['scoring1', 'scoring2']);
                            }
                        });
                    })
                    ->default('pending')
                    ->native(false)
            ])
            ->filtersFormColumns(1)
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Phê duyệt')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->trangThaiSangKien?->ma_trang_thai === 'pending_council')
                    ->action(function ($record) {
                        // Copy approve action logic here
                    }),

                Tables\Actions\Action::make('reject')
                    ->label('Từ chối')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->trangThaiSangKien?->ma_trang_thai === 'pending_council')
                    ->action(function ($record) {
                        // Copy reject action logic here
                    }),

                Tables\Actions\Action::make('score_individual')
                    ->label('Chấm điểm')
                    ->icon('heroicon-o-star')
                    ->color('info')
                    ->visible(fn ($record) => $record->trangThaiSangKien?->ma_trang_thai === 'scoring1')
                    ->action(function ($record) {
                        // Copy score_individual action logic here
                    }),

                Tables\Actions\Action::make('score_council')
                    ->label('Chấm điểm hội đồng')
                    ->icon('heroicon-o-academic-cap')
                    ->color('success')
                    ->visible(fn ($record) => $record->trangThaiSangKien?->ma_trang_thai === 'scoring2')
                    ->action(function ($record) {
                        // Copy score_council action logic here
                    }),

                Tables\Actions\ViewAction::make()
                    ->label('Xem chi tiết'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContent);
    }
}
   
