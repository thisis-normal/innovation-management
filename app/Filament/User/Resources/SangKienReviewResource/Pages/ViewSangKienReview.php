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
                Grid::make(2)
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

                                TextEntry::make('user.name')
                                    ->label('Tác giả')
                                    ->icon('heroicon-o-user'),

                                TextEntry::make('trangThaiSangKien.ten_trang_thai')
                                    ->label('Trạng thái')
                                    ->badge()
                                    ->color(fn ($record) => match ($record->trangThaiSangKien->ma_trang_thai) {
                                        'draft' => Color::Gray,
                                        'pending_manager', 'pending_secretary' => Color::Amber,
                                        'Checking' => Color::Blue,
                                        'Reviewing' => Color::Indigo,
                                        'Scoring1' => Color::Lime,
                                        'Scoring2' => Color::Emerald,
                                        'Approved' => Color::Green,
                                        default => Color::Red,
                                    }),
                            ])
                            ->columnSpan(1),

                        Section::make('Tệp đính kèm')
                            ->icon('heroicon-o-paper-clip')
                            ->collapsible()
                            ->schema([
                                TextEntry::make('taiLieuSangKien')
                                    ->label(false)
                                    ->html()
                                    ->formatStateUsing(function ($record) {
                                        // Lấy trực tiếp từ relationship
                                        $taiLieuCollection = $this->record->taiLieuSangKien;

                                        if (!$taiLieuCollection || $taiLieuCollection->isEmpty()) {
                                            return '<div class="text-gray-500 italic">Không có tệp đính kèm</div>';
                                        }

                                        return $taiLieuCollection->map(function ($taiLieu) {
                                            $fileName = basename($taiLieu->file_path);
                                            $downloadUrl = asset('storage/' . $taiLieu->file_path);
                                            return "
                                                <div class='flex items-center gap-2 p-2 rounded-lg border border-gray-200 mb-2'>
                                                    <div class='flex-1'>
                                                        <div class='text-sm font-medium text-gray-900'>{$fileName}</div>
                                                    </div>
                                                    <a href='{$downloadUrl}' download class='inline-flex items-center justify-center rounded-lg px-3 py-2 text-sm font-medium text-primary-600 hover:text-primary-500 hover:bg-gray-100'>
                                                        <svg class='w-5 h-5 mr-1' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                                                            <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4'/>
                                                        </svg>
                                                        Tải xuống
                                                    </a>
                                                </div>
                                            ";
                                        })->join('');
                                    }),
                            ])
                            ->columnSpan(1),
                    ]),

                Section::make('Nội dung sáng kiến')
                    ->icon('heroicon-o-document-text')
                    ->collapsible()
                    ->schema([
                        TextEntry::make('truoc_khi_ap_dung')
                            ->label('Trước khi áp dụng')
                            ->html()
                            ->prose()
                            ->extraAttributes([
                                'class' => 'p-4 bg-gray-50 rounded-lg border border-gray-200',
                            ])
                            ->columnSpanFull(),

                        TextEntry::make('mo_ta')
                            ->label('Mô tả')
                            ->html()
                            ->prose()
                            ->extraAttributes([
                                'class' => 'p-4 bg-gray-50 rounded-lg border border-gray-200',
                            ])
                            ->columnSpanFull(),

                        TextEntry::make('sau_khi_ap_dung')
                            ->label('Sau khi áp dụng')
                            ->html()
                            ->prose()
                            ->extraAttributes([
                                'class' => 'p-4 bg-gray-50 rounded-lg border border-gray-200',
                            ])
                            ->columnSpanFull(),
                    ]),

                Section::make('Ghi chú')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->collapsible()
                    ->schema([
                        TextEntry::make('ghi_chu')
                            ->label(false)
                            ->markdown()
                            ->extraAttributes([
                                'class' => 'p-4 bg-gray-50 rounded-lg border border-gray-200',
                            ])
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => !empty($record->ghi_chu)),
            ]);
    }
}
