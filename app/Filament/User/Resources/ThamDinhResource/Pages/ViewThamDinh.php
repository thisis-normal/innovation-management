<?php

namespace App\Filament\User\Resources\ThamDinhResource\Pages;

use App\Filament\User\Resources\ThamDinhResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Support\Enums\FontWeight;
use Filament\Infolists\Components\Grid;
use Filament\Support\Colors\Color;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Actions\Action;
use ZipArchive;

class ViewThamDinh extends ViewRecord
{
    protected static string $resource = ThamDinhResource::class;
    protected static ?string $title = 'Chi tiết sáng kiến';

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Thông tin cơ bản')
                    ->icon('heroicon-o-information-circle')
                    ->collapsible()
                    ->schema([
                        TextEntry::make('ten_sang_kien')
                            ->label('Tên sáng kiến')
                            ->weight(FontWeight::Bold)
                            ->size(TextEntry\TextEntrySize::Large)
                            ->columnSpanFull(),

                        Grid::make(3)
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label('Tác giả')
                                    ->icon('heroicon-o-user'),

                                TextEntry::make('donVi.ten_don_vi')
                                    ->label('Đơn vị')
                                    ->icon('heroicon-o-building-office'),

                                TextEntry::make('trangThaiSangKien.ten_trang_thai')
                                    ->label('Trạng thái')
                                    ->badge()
                                    ->formatStateUsing(function ($record) {
                                        if (!$record->hoiDongThamDinh) {
                                            return $record->trangThaiSangKien->ten_trang_thai;
                                        }

                                        $approvedCount = $record->hoiDongThamDinh->thanhVienHoiDongs()
                                            ->where('da_duyet', true)
                                            ->count();
                                        $totalMembers = $record->hoiDongThamDinh->thanhVienHoiDongs()->count();

                                        return "{$record->trangThaiSangKien->ten_trang_thai} ({$approvedCount}/{$totalMembers})";
                                    })
                                    ->color(Color::Amber),
                            ]),
                    ]),

                Grid::make(2)
                    ->schema([
                        Section::make('Nội dung sáng kiến')
                            ->description('Chi tiết về sáng kiến')
                            ->icon('heroicon-o-document-text')
                            ->collapsible()
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        TextEntry::make('loaiSangKien.ten_loai_sang_kien')
                                            ->label('Loại sáng kiến')
                                            ->badge()
                                            ->color('success')
                                            ->icon('heroicon-o-bookmark')
                                            ->formatStateUsing(function ($state, $record) {
                                                if (!$record->loaiSangKien) {
                                                    return 'Chưa phân loại';
                                                }
                                                return $record->loaiSangKien->ten_loai_sang_kien;
                                            }),
                                    ]),

                                TextEntry::make('mo_ta')
                                    ->label('Mô tả')
                                    ->html()
                                    ->extraAttributes([
                                        'class' => 'prose max-w-none bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 p-4 rounded-lg border border-gray-200 dark:border-gray-700',
                                    ]),

                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('truoc_khi_ap_dung')
                                            ->label('Trước khi áp dụng')
                                            ->html()
                                            ->extraAttributes([
                                                'class' => 'prose max-w-none bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-gray-200 p-4 rounded-lg border border-gray-200 dark:border-gray-700',
                                            ]),

                                        TextEntry::make('sau_khi_ap_dung')
                                            ->label('Sau khi áp dụng')
                                            ->html()
                                            ->extraAttributes([
                                                'class' => 'prose max-w-none bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-gray-200 p-4 rounded-lg border border-gray-200 dark:border-gray-700',
                                            ]),
                                    ]),
                            ])
                            ->columnSpan(2),

                        Section::make('Tệp đính kèm')
                            ->icon('heroicon-o-paper-clip')
                            ->description('Các tài liệu liên quan')
                            ->collapsible()
                            ->schema([
                                TextEntry::make('taiLieuSangKien')
                                    ->label(false)
                                    ->html()
                                    ->formatStateUsing(function ($record) {
                                        if (!$record->taiLieuSangKien || $record->taiLieuSangKien->isEmpty()) {
                                            return '<div class="text-gray-500 dark:text-gray-400 italic">Không có tệp đính kèm</div>';
                                        }

                                        $html = '<div class="space-y-3">';

                                        $html .= '
                                            <div class="flex items-center justify-between mb-4 bg-gray-50 dark:bg-gray-800 p-3 rounded-lg">
                                                <div class="flex items-center space-x-2">
                                                    <svg class="w-5 h-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                        ' . $record->taiLieuSangKien->count() . ' tệp đính kèm
                                                    </span>
                                                </div>
                                            </div>';

                                        $html .= '<div class="grid grid-cols-2 gap-4">';

                                        foreach ($record->taiLieuSangKien as $taiLieu) {
                                            $fileName = basename($taiLieu->file_path);
                                            $fileExtension = strtoupper(pathinfo($fileName, PATHINFO_EXTENSION));

                                            $colorClass = match($fileExtension) {
                                                'PDF' => 'bg-red-50 dark:bg-red-900/50 text-red-700 dark:text-red-300 ring-red-600/10 dark:ring-red-500/30',
                                                'DOC', 'DOCX' => 'bg-blue-50 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300 ring-blue-600/10 dark:ring-blue-500/30',
                                                'XLS', 'XLSX' => 'bg-green-50 dark:bg-green-900/50 text-green-700 dark:text-green-300 ring-green-600/10 dark:ring-green-500/30',
                                                default => 'bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-300 ring-gray-600/10 dark:ring-gray-500/30'
                                            };

                                            $html .= "
                                                <div class='group relative flex items-center space-x-3 rounded-lg border border-gray-200 dark:border-gray-700 p-4 shadow-sm hover:border-gray-300 dark:hover:border-gray-600 hover:shadow-md transition-all duration-200'>
                                                    <div class='min-w-0 flex-1'>
                                                        <div class='flex items-center space-x-3'>
                                                            <span class='{$colorClass} flex h-12 w-12 shrink-0 items-center justify-center rounded-lg ring-1'>
                                                                <span class='text-xs font-medium'>{$fileExtension}</span>
                                                            </span>
                                                            <div class='flex-1 min-w-0'>
                                                                <p class='text-sm font-medium text-gray-900 dark:text-gray-100 truncate' title='{$fileName}'>
                                                                    {$fileName}
                                                                </p>
                                                                <p class='text-xs text-gray-500 dark:text-gray-400 truncate'>
                                                                    Tải lên: " . date('d/m/Y H:i', filemtime(storage_path('app/public/' . $taiLieu->file_path))) . "
                                                                </p>
                                                            </div>
                                                            <div class='flex-shrink-0'>
                                                                <a href='/storage/{$taiLieu->file_path}'
                                                                   class='inline-flex items-center rounded-md bg-white dark:bg-gray-700 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 group-hover:ring-gray-400 dark:group-hover:ring-gray-500 transition-all duration-200'
                                                                   download>
                                                                    <svg class='mr-2 h-4 w-4 text-gray-400 dark:text-gray-300 group-hover:text-gray-500 dark:group-hover:text-gray-400' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                                                                        <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4'></path>
                                                                    </svg>
                                                                    Tải xuống
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>";
                                        }

                                        $html .= '</div>';

                                        if ($record->taiLieuSangKien->count() > 1) {
                                            $html .= "
                                                <div class='flex justify-end mt-4'>
                                                    <button type='button' onclick='downloadAllFiles()'
                                                            class='inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200'>
                                                        <svg class='w-4 h-4 mr-2' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                                                            <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4'></path>
                                                        </svg>
                                                        Tải xuống tất cả
                                                    </button>
                                                </div>";
                                        }

                                        $html .= '</div>';
                                        return $html;
                                    }),

                                Actions::make([
                                    Action::make('download_all')
                                        ->label('Tải xuống tất cả')
                                        ->icon('heroicon-o-archive-box-arrow-down')
                                        ->button()
                                        ->visible(fn ($record) => $record->taiLieuSangKien->isNotEmpty())
                                        ->action(function ($record) {
                                            try {
                                                $zip = new ZipArchive();
                                                $zipName = 'sang-kien-' . $record->id . '-files.zip';
                                                $zipPath = storage_path('app/public/' . $zipName);

                                                if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
                                                    foreach ($record->taiLieuSangKien as $taiLieu) {
                                                        $filePath = storage_path('app/public/' . $taiLieu->file_path);
                                                        if (file_exists($filePath)) {
                                                            $zip->addFile($filePath, basename($taiLieu->file_path));
                                                        }
                                                    }
                                                    $zip->close();

                                                    return response()->download($zipPath)->deleteFileAfterSend();
                                                }
                                            } catch (\Exception $e) {
                                                // Handle error
                                            }
                                        }),
                                ])
                            ])
                            ->columnSpan(2),
                    ]),

                Section::make('Thông tin hội đồng')
                    ->icon('heroicon-o-user-group')
                    ->description('Chi tiết về hội đồng thẩm định')
                    ->collapsible()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('hoiDongThamDinh.ten_hoi_dong')
                                    ->label('Tên hội đồng')
                                    ->weight(FontWeight::Bold)
                                    ->size(TextEntry\TextEntrySize::Large),

                                TextEntry::make('hoiDongThamDinh.truongHoiDong')
                                    ->label('Trưởng hội đồng')
                                    ->formatStateUsing(function ($state) {
                                        return "
                                            <div class='space-y-1'>
                                                <div class='font-medium text-gray-900 dark:text-gray-100'>{$state->name}</div>
                                                <div class='text-sm text-gray-500 dark:text-gray-400'>{$state->donVis->first()?->ten_don_vi}</div>
                                            </div>
                                        ";
                                    })
                                    ->html(),

                                TextEntry::make('hoiDongThamDinh.thanhVienHoiDongs')
                                    ->label('Tỷ lệ phê duyệt')
                                    ->formatStateUsing(function ($record) {
                                        $totalMembers = $record->hoiDongThamDinh->thanhVienHoiDongs()->count();
                                        $approvedCount = $record->hoiDongThamDinh->thanhVienHoiDongs()
                                            ->where('da_duyet', true)
                                            ->count();

                                        $percentage = $totalMembers > 0 ? round(($approvedCount / $totalMembers) * 100) : 0;

                                        return "
                                            <div class='flex flex-col items-center justify-center p-3 bg-gray-50 dark:bg-gray-800 rounded-lg'>
                                                <div class='text-2xl font-bold text-gray-900 dark:text-gray-100'>{$approvedCount}/{$totalMembers}</div>
                                                <div class='text-sm text-gray-500 dark:text-gray-400'>Tỷ lệ: {$percentage}%</div>
                                            </div>
                                        ";
                                    })
                                    ->html(),
                            ]),

                        Section::make('Danh sách thành viên')
                            ->description('Trạng thái phê duyệt của các thành viên')
                            ->collapsible()
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('hoiDongThamDinh.thanhVienHoiDongs')
                                            ->label('Đã phê duyệt')
                                            ->formatStateUsing(function ($record) {
                                                $approvedMembers = $record->hoiDongThamDinh->thanhVienHoiDongs()
                                                    ->where('da_duyet', true)
                                                    ->with(['user', 'user.donVis'])
                                                    ->get();

                                                if ($approvedMembers->isEmpty()) {
                                                    return '<div class="text-gray-500 dark:text-gray-400 italic">Chưa có thành viên nào duyệt</div>';
                                                }

                                                $html = '<div class="space-y-2">';
                                                foreach ($approvedMembers as $member) {
                                                    $html .= "
                                                        <div class='flex items-center p-2 bg-green-50 dark:bg-green-900/50 rounded-lg'>
                                                            <div class='flex-1'>
                                                                <div class='font-medium text-green-700 dark:text-green-300'>{$member->user->name}</div>
                                                                <div class='text-sm text-green-600 dark:text-green-400'>{$member->user->donVis->first()?->ten_don_vi}</div>
                                                            </div>
                                                            <div class='flex-shrink-0'>
                                                                <svg class='w-5 h-5 text-green-500 dark:text-green-400' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                                                                    <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5 13l4 4L19 7'></path>
                                                                </svg>
                                                            </div>
                                                        </div>";
                                                }
                                                $html .= '</div>';
                                                return $html;
                                            })
                                            ->html(),

                                        TextEntry::make('hoiDongThamDinh.thanhVienHoiDongs')
                                            ->label('Chưa phê duyệt')
                                            ->formatStateUsing(function ($record) {
                                                $pendingMembers = $record->hoiDongThamDinh->thanhVienHoiDongs()
                                                    ->where('da_duyet', false)
                                                    ->with(['user', 'user.donVis'])
                                                    ->get();

                                                if ($pendingMembers->isEmpty()) {
                                                    return '<div class="text-gray-500 dark:text-gray-400 italic">Không có thành viên nào chờ duyệt</div>';
                                                }

                                                $html = '<div class="space-y-2">';
                                                foreach ($pendingMembers as $member) {
                                                    $html .= "
                                                        <div class='flex items-center p-2 bg-gray-50 dark:bg-gray-800 rounded-lg'>
                                                            <div class='flex-1'>
                                                                <div class='font-medium text-gray-700 dark:text-gray-300'>{$member->user->name}</div>
                                                                <div class='text-sm text-gray-600 dark:text-gray-400'>{$member->user->donVis->first()?->ten_don_vi}</div>
                                                            </div>
                                                            <div class='flex-shrink-0'>
                                                                <svg class='w-5 h-5 text-gray-400 dark:text-gray-500' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                                                                    <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'></path>
                                                                </svg>
                                                            </div>
                                                        </div>";
                                                }
                                                $html .= '</div>';
                                                return $html;
                                            })
                                            ->html(),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
