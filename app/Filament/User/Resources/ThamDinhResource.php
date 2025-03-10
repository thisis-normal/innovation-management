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
use App\Models\DiemCaNhan;
use App\Models\DiemHoiDong;
use App\Models\TieuChi;

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
                $query->whereIn('ma_trang_thai', ['pending_council', 'scoring1', 'scoring2']);
            });
        }

        // Lấy các hội đồng mà user là thành viên
        $hoiDongIds = ThanhVienHoiDong::where('ma_nguoi_dung', $user->id)
            ->pluck('ma_hoi_dong');

        return $query
            ->whereIn('ma_hoi_dong', $hoiDongIds)
            ->whereHas('trangThaiSangKien', function ($query) {
                $query->whereIn('ma_trang_thai', ['pending_council', 'scoring1', 'scoring2']);
            });
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(function (Builder $query) {
                return $query->whereHas('trangThaiSangKien', function ($query) {
                    $query->where('ma_trang_thai', 'pending_council');
                });
            })
            ->heading('Danh sách sáng kiến cần thẩm định')
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
                        if ($record->trangThaiSangKien->ma_trang_thai === 'pending_council') {
                            $approvedCount = $record->thanhVienHoiDongs()
                                ->wherePivot('da_duyet', true)
                                ->count();
                            $totalMembers = $record->hoiDongThamDinh->thanhVienHoiDongs()->count();
                            return "Chờ hội đồng phê duyệt ({$approvedCount}/{$totalMembers})";
                        }
                        return $record->trangThaiSangKien->ten_trang_thai;
                    })
                    ->badge()
                    ->color('warning'),

                TextColumn::make('taiLieuSangKien.file_path')
                    ->label('File đính kèm')
                    ->limit(20)
                    ->state(function ($record) {
                        if (!$record->taiLieuSangKien || $record->taiLieuSangKien->isEmpty()) {
                            return 'Không có file nào được tải lên.';
                        }
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
                    ->visible(function (SangKien $record) {
                        if (!$record->hoiDongThamDinh) return false;
                        $user = Auth::user();
                        $thanhVien = ThanhVienHoiDong::where('ma_hoi_dong', $record->ma_hoi_dong)
                            ->where('ma_nguoi_dung', $user->id)
                            ->first();
                        return $thanhVien && !$record->thanhVienHoiDongs()
                            ->where('ma_nguoi_dung', $user->id)
                            ->wherePivot('da_duyet', true)
                            ->exists();
                    })
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
                    }),

                Action::make('reject')
                    ->label('Từ chối')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(function (SangKien $record) {
                        if (!$record->hoiDongThamDinh) return false;
                        $user = Auth::user();
                        $thanhVien = ThanhVienHoiDong::where('ma_hoi_dong', $record->ma_hoi_dong)
                            ->where('ma_nguoi_dung', $user->id)
                            ->first();
                        return $thanhVien && !$record->thanhVienHoiDongs()
                            ->where('ma_nguoi_dung', $user->id)
                            ->wherePivot('da_duyet', false)
                            ->exists();
                    })
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
                    }),

                Action::make('Download')
                    ->label('Tải xuống')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->visible(fn ($record) => $record->taiLieuSangKien && $record->taiLieuSangKien->isNotEmpty())
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
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->view('filament.tables.table-with-margin')
            ->appendTable(
                Table::make('scoring_table')
                    ->query(function (Builder $query) {
                        return $query->whereHas('trangThaiSangKien', function ($query) {
                            $query->whereIn('ma_trang_thai', ['scoring1', 'scoring2']);
                        });
                    })
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
                                if ($record->trangThaiSangKien->ma_trang_thai === 'scoring2') {
                                    $diemHoiDong = DiemHoiDong::where('ma_sang_kien', $record->id)->exists();
                                    return $diemHoiDong ? "Đã chấm điểm vòng 2" : "Chờ chấm điểm vòng 2";
                                }
                                return $record->trangThaiSangKien->ten_trang_thai;
                            })
                            ->badge()
                            ->color(fn ($record) => $record->trangThaiSangKien->ma_trang_thai === 'scoring1' ? 'info' : 'success'),

                        TextColumn::make('taiLieuSangKien.file_path')
                            ->label('File đính kèm')
                            ->limit(20)
                            ->state(function ($record) {
                                if (!$record->taiLieuSangKien || $record->taiLieuSangKien->isEmpty()) {
                                    return 'Không có file nào được tải lên.';
                                }
                                return $record->taiLieuSangKien->pluck('file_path')->toArray();
                            })
                            ->listWithLineBreaks(),
                    ])
                    ->actions([
                        Action::make('score_individual')
                            ->label('Chấm điểm')
                            ->icon('heroicon-o-star')
                            ->color('info')
                            ->visible(fn (SangKien $record) =>
                                $record->trangThaiSangKien->ma_trang_thai === 'scoring1' &&
                                !DiemCaNhan::where('ma_sang_kien', $record->id)
                                    ->where('ma_thanh_vien', Auth::user()->thanhVienHoiDongs->first()?->id)
                                    ->exists()
                            )
                            ->form([
                                Forms\Components\Grid::make()
                                    ->schema(function () {
                                        $tieuChis = TieuChi::all();
                                        $schema = [];

                                        foreach ($tieuChis as $tieuChi) {
                                            $schema[] = Forms\Components\TextInput::make("diem.{$tieuChi->id}")
                                                ->label($tieuChi->ten_tieu_chi)
                                                ->numeric()
                                                ->minValue(0)
                                                ->maxValue(10)
                                                ->required();
                                        }

                                        $schema[] = Forms\Components\Textarea::make('nhan_xet')
                                            ->label('Nhận xét')
                                            ->required();

                                        return $schema;
                                    }),
                            ])
                            ->action(function (SangKien $record, array $data) {
                                $thanhVien = Auth::user()->thanhVienHoiDongs->first();

                                if (!$thanhVien) {
                                    return;
                                }

                                // Tính điểm trung bình
                                $tongDiem = array_sum($data['diem']);
                                $diemTrungBinh = $tongDiem / count($data['diem']);

                                DiemCaNhan::create([
                                    'ma_sang_kien' => $record->id,
                                    'ma_thanh_vien' => $thanhVien->id,
                                    'diem' => $diemTrungBinh,
                                    'nhan_xet' => $data['nhan_xet']
                                ]);

                                // Kiểm tra nếu tất cả thành viên đã chấm điểm
                                $totalMembers = $record->hoiDongThamDinh->thanhVienHoiDongs()->count();
                                $completedCount = DiemCaNhan::where('ma_sang_kien', $record->id)->count();

                                if ($completedCount >= $totalMembers) {
                                    // Chuyển sang trạng thái chấm điểm vòng 2
                                    $record->ma_trang_thai_sang_kien = TrangThaiSangKien::where('ma_trang_thai', 'scoring2')->first()->id;
                                    $record->save();
                                }

                                Notification::make()
                                    ->title('Đã chấm điểm thành công')
                                    ->success()
                                    ->send();
                            }),

                        Action::make('score_council')
                            ->label('Chấm điểm hội đồng')
                            ->icon('heroicon-o-academic-cap')
                            ->color('success')
                            ->visible(fn (SangKien $record) =>
                                $record->trangThaiSangKien->ma_trang_thai === 'scoring2' &&
                                $record->hoiDongThamDinh->ma_truong_hoi_dong === Auth::id() &&
                                !DiemHoiDong::where('ma_sang_kien', $record->id)->exists()
                            )
                            ->form([
                                Forms\Components\Grid::make()
                                    ->schema(function (SangKien $record) {
                                        $tieuChis = TieuChi::all();
                                        $schema = [];

                                        // Hiển thị điểm trung bình của các thành viên
                                        $diemCaNhans = DiemCaNhan::where('ma_sang_kien', $record->id)->get();
                                        $diemTrungBinh = $diemCaNhans->avg('diem');

                                        $schema[] = Forms\Components\Placeholder::make('diem_trung_binh')
                                            ->label('Điểm trung bình các thành viên')
                                            ->content(number_format($diemTrungBinh, 2));

                                        foreach ($tieuChis as $tieuChi) {
                                            $schema[] = Forms\Components\TextInput::make("diem.{$tieuChi->id}")
                                                ->label($tieuChi->ten_tieu_chi)
                                                ->numeric()
                                                ->minValue(0)
                                                ->maxValue(10)
                                                ->required();
                                        }

                                        $schema[] = Forms\Components\Textarea::make('nhan_xet_chung')
                                            ->label('Nhận xét chung của hội đồng')
                                            ->required();

                                        return $schema;
                                    }),
                            ])
                            ->action(function (SangKien $record, array $data) {
                                // Tính điểm cuối cùng
                                $tongDiem = array_sum($data['diem']);
                                $diemCuoi = $tongDiem / count($data['diem']);

                                DiemHoiDong::create([
                                    'ma_sang_kien' => $record->id,
                                    'ma_hoi_dong' => $record->ma_hoi_dong,
                                    'diem_cuoi' => $diemCuoi,
                                    'nhan_xet_chung' => $data['nhan_xet_chung'],
                                    'nguoi_nhap' => Auth::id()
                                ]);

                                // Chuyển sang trạng thái đã chấm điểm
                                $record->ma_trang_thai_sang_kien = TrangThaiSangKien::where('ma_trang_thai', 'scored')->first()->id;
                                $record->save();

                                Notification::make()
                                    ->title('Đã chấm điểm hội đồng thành công')
                                    ->success()
                                    ->send();
                            }),

                        Action::make('Download')
                            ->label('Tải xuống')
                            ->icon('heroicon-o-arrow-down-tray')
                            ->visible(fn ($record) => $record->taiLieuSangKien && $record->taiLieuSangKien->isNotEmpty())
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
            );
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
