<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\ThamDinhResource\Pages;
use App\Models\SangKien;
use App\Models\TrangThaiSangKien;
use App\Models\ThanhVienHoiDong;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use ZipArchive;
use Exception;

class ThamDinhResource extends Resource
{
    protected static ?string $model = SangKien::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Thẩm định sáng kiến';
    protected static ?string $modelLabel = 'Thẩm định sáng kiến';
    protected static ?string $pluralModelLabel = 'Thẩm định sáng kiến';
    protected static ?string $slug = 'tham-dinh-sang-kien';
    protected static ?int $navigationSort = 3;

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();

        $query = parent::getEloquentQuery()->with(['loaiSangKien']);

        // Nếu là thư ký, cho xem tất cả
        if ($user->hasRole('secretary')) {
            return $query->whereHas('trangThaiSangKien', function ($query) {
                $query->where('ma_trang_thai', 'pending_council');
            });
        }

        // Lấy các hội đồng mà user là thành viên
        $hoiDongIds = ThanhVienHoiDong::where('ma_nguoi_dung', $user->id)
            ->pluck('ma_hoi_dong');

        return $query
            ->whereIn('ma_hoi_dong', $hoiDongIds)
            ->whereHas('trangThaiSangKien', function ($query) {
                $query->where('ma_trang_thai', 'pending_council');
            });
    }

    public static function table(Table $table): Table
    {
        return $table
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
                        // Kiểm tra sự tồn tại của hoiDongThamDinh
                        if (!$record->hoiDongThamDinh) {
                            return $record->trangThaiSangKien->ten_trang_thai;
                        }

                        // Lấy số lượng thành viên đã phê duyệt cho sáng kiến cụ thể này
                        $approvedCount = $record->thanhVienHoiDongs()
                            ->wherePivot('da_duyet', true)
                            ->wherePivot('ma_sang_kien', $record->id) // Đảm bảo chỉ tính cho sáng kiến hiện tại
                            ->count();

                        // Lấy tổng số thành viên của hội đồng
                        $totalMembers = $record->hoiDongThamDinh->thanhVienHoiDongs()->count();

                        return "Chờ hội đồng phê duyệt ({$approvedCount}/{$totalMembers})";
                    })
                    ->badge()
                    ->color(fn ($record) => 'warning'),

                TextColumn::make('taiLieuSangKien.file_path')
                    ->label('File đính kèm')
                    ->limit(20)
                    ->state(function ($record) {
                        // Check if relationship exists and has items
                        if (!$record->taiLieuSangKien || $record->taiLieuSangKien->isEmpty()) {
                            return 'Không có file nào được tải lên.';
                        }
                        // Return the array of file paths directly
                        return $record->taiLieuSangKien->pluck('file_path')->toArray();
                    })
                    ->listWithLineBreaks(),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Phê duyệt')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Phê duyệt sáng kiến')
                    ->modalDescription('Bạn có chắc chắn muốn phê duyệt sáng kiến này?')
                    ->action(function (SangKien $record) {
                        // Kiểm tra sự tồn tại của hoiDongThamDinh
                        if (!$record->hoiDongThamDinh) {
                            Notification::make()
                                ->title('Lỗi')
                                ->body('Sáng kiến chưa được phân công hội đồng')
                                ->danger()
                                ->send();
                            return;
                        }

                        $user = Auth::user();
                        $thanhVien = ThanhVienHoiDong::where('ma_hoi_dong', $record->ma_hoi_dong)
                            ->where('ma_nguoi_dung', $user->id)
                            ->first();

                        if ($thanhVien) {
                            // Cập nhật trạng thái phê duyệt trong bảng trung gian
                            $thanhVien->sangKiens()->syncWithoutDetaching([
                                $record->id => ['da_duyet' => true]
                            ]);

                            // Kiểm tra số lượng thành viên đã duyệt cho sáng kiến này
                            $approvedCount = $record->thanhVienHoiDongs()
                                ->wherePivot('da_duyet', true)
                                ->wherePivot('ma_sang_kien', $record->id)
                                ->count();
                            $totalMembers = $record->hoiDongThamDinh->thanhVienHoiDongs()->count();

                            if ($approvedCount >= ceil($totalMembers / 2)) {
                                // Chuyển sang trạng thái chấm điểm vòng 1
                                $newStatusId = TrangThaiSangKien::where('ma_trang_thai', 'scoring1')->first()->id;
                                $record->ma_trang_thai_sang_kien = $newStatusId;
                                $record->save();
                            }

                            Notification::make()
                                ->title('Phê duyệt thành công')
                                ->success()
                                ->send();
                        }
                    })
                    ->visible(function (SangKien $record) {
                        if (!$record->hoiDongThamDinh) {
                            return false;
                        }

                        $user = Auth::user();
                        $thanhVien = ThanhVienHoiDong::where('ma_hoi_dong', $record->ma_hoi_dong)
                            ->where('ma_nguoi_dung', $user->id)
                            ->first();

                        // Kiểm tra xem thành viên đã phê duyệt sáng kiến này chưa
                        return $thanhVien && !$record->thanhVienHoiDongs()
                            ->where('ma_nguoi_dung', $user->id)
                            ->wherePivot('da_duyet', true)
                            ->exists();
                    }),

                Action::make('reject')
                    ->label('Từ chối')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Từ chối sáng kiến')
                    ->modalDescription('Bạn có chắc chắn muốn từ chối sáng kiến này?')
                    ->action(function (SangKien $record) {
                        // Kiểm tra sự tồn tại của hoiDongThamDinh
                        if (!$record->hoiDongThamDinh) {
                            Notification::make()
                                ->title('Lỗi')
                                ->body('Sáng kiến chưa được phân công hội đồng')
                                ->danger()
                                ->send();
                            return;
                        }

                        $user = Auth::user();
                        $thanhVien = ThanhVienHoiDong::where('ma_hoi_dong', $record->ma_hoi_dong)
                            ->where('ma_nguoi_dung', $user->id)
                            ->first();

                        if ($thanhVien) {
                            // Cập nhật trạng thái từ chối trong bảng trung gian
                            $thanhVien->sangKiens()->syncWithoutDetaching([
                                $record->id => ['da_duyet' => false]
                            ]);

                            // Kiểm tra số lượng thành viên đã từ chối cho sáng kiến này
                            $rejectedCount = $record->thanhVienHoiDongs()
                                ->wherePivot('da_duyet', false)
                                ->wherePivot('ma_sang_kien', $record->id)
                                ->count();
                            $totalMembers = $record->hoiDongThamDinh->thanhVienHoiDongs()->count();

                            if ($rejectedCount > $totalMembers / 2) {
                                // Chuyển sang trạng thái từ chối bởi hội đồng
                                $newStatusId = TrangThaiSangKien::where('ma_trang_thai', 'rejected_council')->first()->id;
                                $record->ma_trang_thai_sang_kien = $newStatusId;
                                $record->save();
                            }

                            Notification::make()
                                ->title('Đã từ chối sáng kiến')
                                ->success()
                                ->send();
                        }
                    })
                    ->visible(function (SangKien $record) {
                        if (!$record->hoiDongThamDinh) {
                            return false;
                        }

                        $user = Auth::user();
                        $thanhVien = ThanhVienHoiDong::where('ma_hoi_dong', $record->ma_hoi_dong)
                            ->where('ma_nguoi_dung', $user->id)
                            ->first();

                        // Kiểm tra xem thành viên đã từ chối sáng kiến này chưa
                        return $thanhVien && !$record->thanhVienHoiDongs()
                            ->where('ma_nguoi_dung', $user->id)
                            ->wherePivot('da_duyet', false)
                            ->exists();
                    }),

                Action::make('Download')
                    ->label('Tải xuống')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->visible(function ($record) {
                        return $record->taiLieuSangKien && $record->taiLieuSangKien->isNotEmpty();
                    })
                    ->action(function ($record) {
                        try {
                            if (!$record->taiLieuSangKien || $record->taiLieuSangKien->isEmpty()) {
                                Notification::make()
                                    ->title('Không có tệp để tải xuống')
                                    ->warning()
                                    ->send();
                                return null;
                            }

                            $zip = new ZipArchive();
                            $zipName = 'innovation-files-' . $record->id . '.zip';
                            $zipPath = storage_path('app/public/' . $zipName);

                            $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

                            foreach ($record->taiLieuSangKien as $file) {
                                $filePath = storage_path('app/public/' . $file->file_path);
                                if (file_exists($filePath)) {
                                    $zip->addFile($filePath, basename($file->file_path));
                                }
                            }

                            $zip->close();

                            Notification::make()
                                ->title('Tải xuống thành công')
                                ->success()
                                ->send();

                            return response()->download($zipPath)->deleteFileAfterSend();
                        } catch (Exception $e) {
                            Notification::make()
                                ->title('Tải xuống thất bại')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Tables\Actions\ViewAction::make()
                    ->label('Xem chi tiết'),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListThamDinh::route('/'),
            'view' => Pages\ViewThamDinh::route('/{record}'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = Auth::user();

        // Kiểm tra xem user có phải là thành viên của bất kỳ hội đồng nào
        $isHoiDongMember = ThanhVienHoiDong::where('ma_nguoi_dung', $user->id)->exists();

        // Cho phép thư ký và thành viên hội đồng xem
        return $user->hasRole('secretary') || $isHoiDongMember;
    }

    public static function canCreate(): bool
    {
        return false; // Không cho phép tạo mới từ resource này
    }

    public static function canDelete(Model $record): bool
    {
        return false; // Không cho phép xóa
    }

    public static function canEdit(Model $record): bool
    {
        return false; // Không cho phép sửa trực tiếp
    }

    public static function canView(Model $record): bool
    {
        $user = Auth::user();

        // Thư ký có thể xem tất cả
        if ($user->hasRole('secretary')) {
            return true;
        }

        // Kiểm tra xem user có phải là thành viên của hội đồng này không
        return ThanhVienHoiDong::where('ma_hoi_dong', $record->ma_hoi_dong)
            ->where('ma_nguoi_dung', $user->id)
            ->exists();
    }
}
