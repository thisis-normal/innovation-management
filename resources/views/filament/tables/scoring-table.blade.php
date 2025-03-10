<?php
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions;
?>

<div class="mt-8">
    <h2 class="text-xl font-bold mb-4">Danh sách sáng kiến cần chấm điểm</h2>

    <x-filament::table>
        <x-slot name="header">
            <x-filament::table.header-cell>
                Tên sáng kiến
            </x-filament::table.header-cell>
            <x-filament::table.header-cell>
                Tác giả
            </x-filament::table.header-cell>
            <x-filament::table.header-cell>
                Hội đồng thẩm định
            </x-filament::table.header-cell>
            <x-filament::table.header-cell>
                Trạng thái
            </x-filament::table.header-cell>
            <x-filament::table.header-cell>
                Thao tác
            </x-filament::table.header-cell>
        </x-slot>

        @forelse($records as $record)
            <x-filament::table.row>
                <x-filament::table.cell>
                    {{ $record->ten_sang_kien }}
                </x-filament::table.cell>
                <x-filament::table.cell>
                    {{ $record->user->name }}
                </x-filament::table.cell>
                <x-filament::table.cell>
                    {{ $record->hoiDongThamDinh->ten_hoi_dong }}
                </x-filament::table.cell>
                <x-filament::table.cell>
                    @if($record->trangThaiSangKien->ma_trang_thai === 'scoring1')
                        @php
                            $completedCount = \App\Models\DiemCaNhan::where('ma_sang_kien', $record->id)->count();
                            $totalMembers = $record->hoiDongThamDinh->thanhVienHoiDongs()->count();
                        @endphp
                        <x-filament::badge color="info">
                            Chấm điểm vòng 1 ({{ $completedCount }}/{{ $totalMembers }})
                        </x-filament::badge>
                    @else
                        @php
                            $diemHoiDong = \App\Models\DiemHoiDong::where('ma_sang_kien', $record->id)->exists();
                        @endphp
                        <x-filament::badge color="success">
                            {{ $diemHoiDong ? "Đã chấm điểm vòng 2" : "Chờ chấm điểm vòng 2" }}
                        </x-filament::badge>
                    @endif
                </x-filament::table.cell>
                <x-filament::table.cell>
                    <div class="flex items-center gap-2">
                        @if($record->trangThaiSangKien->ma_trang_thai === 'scoring1')
                            <x-filament::button
                                size="sm"
                                color="info"
                                icon="heroicon-o-star"
                                wire:click="callTableAction('score_individual', '{{ $record->id }}')"
                            >
                                Chấm điểm
                            </x-filament::button>
                        @endif

                        @if($record->trangThaiSangKien->ma_trang_thai === 'scoring2')
                            <x-filament::button
                                size="sm"
                                color="success"
                                icon="heroicon-o-academic-cap"
                                wire:click="callTableAction('score_council', '{{ $record->id }}')"
                            >
                                Chấm điểm hội đồng
                            </x-filament::button>
                        @endif

                        <x-filament::button
                            size="sm"
                            icon="heroicon-o-eye"
                            wire:click="callTableAction('view', '{{ $record->id }}')"
                        >
                            Xem chi tiết
                        </x-filament::button>
                    </div>
                </x-filament::table.cell>
            </x-filament::table.row>
        @empty
            <x-filament::table.row>
                <x-filament::table.cell colspan="5" class="text-center py-4">
                    Không có sáng kiến nào cần chấm điểm
                </x-filament::table.cell>
            </x-filament::table.row>
        @endforelse
    </x-filament::table>
</div>
