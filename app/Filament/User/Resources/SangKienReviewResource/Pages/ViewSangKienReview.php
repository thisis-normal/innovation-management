<?php

namespace App\Filament\User\Resources\SangKienReviewResource\Pages;

use App\Filament\User\Resources\SangKienReviewResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Support\Enums\FontWeight;
use Filament\Infolists\Components\Grid;
use Filament\Support\Colors\Color;

class ViewSangKienReview extends ViewRecord
{
    protected static string $resource = SangKienReviewResource::class;
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
                                        'class' => 'prose max-w-none bg-white p-4 rounded-lg border border-gray-200',
                                    ]),

                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('truoc_khi_ap_dung')
                                            ->label('Trước khi áp dụng')
                                            ->html()
                                            ->extraAttributes([
                                                'class' => 'prose max-w-none bg-gray-50 p-4 rounded-lg border border-gray-200',
                                            ]),

                                        TextEntry::make('sau_khi_ap_dung')
                                            ->label('Sau khi áp dụng')
                                            ->html()
                                            ->extraAttributes([
                                                'class' => 'prose max-w-none bg-gray-50 p-4 rounded-lg border border-gray-200',
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
                                            return '<div class="text-gray-500 italic">Không có tệp đính kèm</div>';
                                        }

                                        $html = '<div class="grid grid-cols-2 gap-4">';
                                        foreach ($record->taiLieuSangKien as $taiLieu) {
                                            $fileName = basename($taiLieu->file_path);
                                            $fileExtension = strtoupper(pathinfo($fileName, PATHINFO_EXTENSION));

                                            $colorClass = match($fileExtension) {
                                                'PDF' => 'bg-red-50 text-red-700 ring-red-600/10',
                                                'DOC', 'DOCX' => 'bg-blue-50 text-blue-700 ring-blue-600/10',
                                                'XLS', 'XLSX' => 'bg-green-50 text-green-700 ring-green-600/10',
                                                default => 'bg-gray-50 text-gray-700 ring-gray-600/10'
                                            };

                                            $html .= "
                                                <div class='group relative flex items-center space-x-3 rounded-lg border p-4 shadow-sm hover:border-gray-300 hover:shadow-md transition-all duration-200'>
                                                    <div class='min-w-0 flex-1'>
                                                        <div class='flex items-center space-x-3'>
                                                            <span class='{$colorClass} flex h-12 w-12 shrink-0 items-center justify-center rounded-lg ring-1'>
                                                                <span class='text-xs font-medium'>{$fileExtension}</span>
                                                            </span>
                                                            <div class='flex-1 min-w-0'>
                                                                <p class='text-sm font-medium text-gray-900 truncate' title='{$fileName}'>
                                                                    {$fileName}
                                                                </p>
                                                            </div>
                                                            <div class='flex-shrink-0'>
                                                                <a href='/storage/{$taiLieu->file_path}'
                                                                   class='inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 group-hover:ring-gray-400 transition-all duration-200'
                                                                   download>
                                                                    <svg class='mr-2 h-4 w-4 text-gray-400 group-hover:text-gray-500' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
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
                                        return $html;
                                    }),
                            ])
                            ->columnSpan(2),
                    ]),
            ]);
    }
}
