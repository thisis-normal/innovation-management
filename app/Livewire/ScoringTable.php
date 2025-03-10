<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\SangKien;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use App\Models\DiemCaNhan;
use App\Models\DiemHoiDong;

class ScoringTable extends Component implements HasTable
{
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                SangKien::query()
                    ->whereHas('trangThaiSangKien', function ($query) {
                        $query->whereIn('ma_trang_thai', ['scoring1', 'scoring2']);
                    })
            )
            ->heading('Danh sách sáng kiến cần chấm điểm')
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
                        if ($record->trangThaiSangKien->ma_trang_thai === 'scoring1') {
                            $completedCount = DiemCaNhan::where('ma_sang_kien', $record->id)->count();
                            $totalMembers = $record->hoiDongThamDinh->thanhVienHoiDongs()->count();
                            return "Chấm điểm vòng 1 ({$completedCount}/{$totalMembers})";
                        }

                        $diemHoiDong = DiemHoiDong::where('ma_sang_kien', $record->id)->exists();
                        return $diemHoiDong ? "Đã chấm điểm vòng 2" : "Chờ chấm điểm vòng 2";
                    })
                    ->badge()
                    ->color(fn ($record) => $record->trangThaiSangKien->ma_trang_thai === 'scoring1' ? 'info' : 'success'),
            ])
            ->actions([
                Action::make('score_individual')
                    ->label('Chấm điểm')
                    ->icon('heroicon-o-star')
                    ->color('info')
                    ->visible(fn ($record) => $record->trangThaiSangKien->ma_trang_thai === 'scoring1')
                    ->action(function ($record) {
                        // Copy score_individual action logic here
                    }),

                Action::make('score_council')
                    ->label('Chấm điểm hội đồng')
                    ->icon('heroicon-o-academic-cap')
                    ->color('success')
                    ->visible(fn ($record) => $record->trangThaiSangKien->ma_trang_thai === 'scoring2')
                    ->action(function ($record) {
                        // Copy score_council action logic here
                    }),

                Action::make('view')
                    ->label('Xem chi tiết')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => route('filament.user.resources.tham-dinh-sang-kien.view', ['record' => $record]))
            ])
            ->defaultSort('created_at', 'desc');
    }

    public function render()
    {
        return view('livewire.scoring-table');
    }
}
