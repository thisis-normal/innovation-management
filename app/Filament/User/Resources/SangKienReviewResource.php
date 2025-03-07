<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\SangKienReviewResource\Pages;
use App\Models\SangKien;
use App\Models\TrangThaiSangKien;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use App\Models\HoiDongThamDinh;
use Filament\Forms\Components\Select;

class SangKienReviewResource extends Resource
{
    protected static ?string $model = SangKien::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-circle';
    protected static ?string $navigationLabel = 'Phê duyệt sáng kiến';
    protected static ?string $pluralModelLabel = 'Phê duyệt sáng kiến';
    protected static ?string $slug = 'duyet-sang-kien';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationGroup = 'Xử lý sáng kiến';
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();

        if (!$user) {
            return $query->whereRaw('1 = 0'); // No user, show nothing
        }
//        if ($user->hasRole('admin')) { //Assumed role name
//            return $query; // Admin sees all
//        }
        $roles = $user->roles->pluck('ma_vai_tro')->toArray();

        // If user has both roles, show all records with status 'pending_manager' and 'pending_secretary'
        if (in_array('manager', $roles) && in_array('secretary', $roles)) {
            $pendingManagerId = TrangThaiSangKien::query()->where('ma_trang_thai', 'pending_manager')->first()->id;
            $pendingSecretaryId = TrangThaiSangKien::query()->where('ma_trang_thai', 'pending_secretary')->first()->id;
            return $query->whereIn('ma_trang_thai_sang_kien', [$pendingManagerId, $pendingSecretaryId]);
        }

        // If user is manager, show all records with status 'pending_manager'
        if (in_array('manager', $roles)) {
            $trangThaiId = TrangThaiSangKien::query()->where('ma_trang_thai', 'pending_manager')->first()->id;
            return $query->where('ma_trang_thai_sang_kien', $trangThaiId);
        }

        // If user is secretary, show all records with status 'pending_secretary'
        if (in_array('secretary', $roles)) {
            $trangThaiId = TrangThaiSangKien::query()->where('ma_trang_thai', 'pending_secretary')->first()->id;
            return $query->where('ma_trang_thai_sang_kien', $trangThaiId);
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ten_sang_kien')->label('Tên sáng kiến')->searchable()->sortable(),
                TextColumn::make('truoc_khi_ap_dung')
                    ->label('Trước khi áp dụng')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->state(fn ($record) => strip_tags($record->truoc_khi_ap_dung)),
                TextColumn::make('mo_ta')
                    ->label('Mô tả')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->state(fn ($record) => strip_tags($record->mo_ta)),
                TextColumn::make('sau_khi_ap_dung')
                    ->label('Sau khi áp dụng')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->state(fn ($record) => strip_tags($record->sau_khi_ap_dung)),
                TextColumn::make('user.name')->label('Tác giả')->searchable()->sortable(),

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

                TextColumn::make('trangThaiSangKien.ten_trang_thai')
                    ->label('Trạng thái')
                    ->badge()
                    ->color(fn ($record) => match ($record->trangThaiSangKien->ma_trang_thai) {
                        'draft' => 'gray', // Neutral gray for drafts
                        'pending_manager', 'pending_secretary' => 'amber', // Amber (yellow-orange) for pending actions
                        'Checking' => 'calm-blue', // Calm blue for checking
                        'Reviewing' => 'indigo', // Indigo for reviewing
                        'Scoring1' => 'lime', // Bright lime green for initial scoring
                        'Scoring2' => 'emerald', // Rich emerald green for secondary scoring
                        'Approved' => 'green', // Vibrant green for approved items
                        default => 'red', // Bold red for rejected or unknown states
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Xem chi tiết')
                    ->icon('heroicon-o-eye'),

                Tables\Actions\Action::make('approve')
                    ->label('Phê duyệt')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->modalHeading(fn (SangKien $record) => 'Phê duyệt sáng kiến: ' . $record->ten_sang_kien)
                    ->modalDescription(function () {
                        if (auth()->user()->hasRole('secretary')) {
                            return 'Vui lòng chọn hội đồng thẩm định và xác nhận việc phê duyệt sáng kiến này.';
                        }
                        return 'Vui lòng xác nhận việc phê duyệt sáng kiến này.';
                    })
                    ->form(function () {
                        $formFields = [
                            Forms\Components\Textarea::make('note')
                                ->label('Ghi chú (không bắt buộc)')
                                ->placeholder('Nhập ghi chú của bạn ở đây...')
                                ->maxLength(1000),
                        ];

                        // Chỉ hiển thị select hội đồng khi người dùng là thư ký
                        if (auth()->user()->hasRole('secretary')) {
                            array_unshift($formFields,
                                Select::make('hoi_dong_id')
                                    ->label('Chọn Hội đồng thẩm định')
                                    ->options(function () {
                                        return HoiDongThamDinh::where('trang_thai', true)
                                            ->get()
                                            ->pluck('ten_hoi_dong', 'id');
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->helperText('Chọn hội đồng sẽ thẩm định sáng kiến này')
                            );
                        }

                        return $formFields;
                    })
                    ->action(function (SangKien $record, array $data) {
                        // Store the note if provided
                        if (!empty($data['note'])) {
                            $record->ghi_chu = $data['note'];
                        }

                        // Get the current status of the record
                        $currentStatus = $record->ma_trang_thai_sang_kien;
                        $user = auth()->user();

                        $pendingSecretaryId = TrangThaiSangKien::query()
                            ->where('ma_trang_thai', 'pending_secretary')
                            ->first()->id;
                        $pendingManagerId = TrangThaiSangKien::query()
                            ->where('ma_trang_thai', 'pending_manager')
                            ->first()->id;
                        $reviewingId = TrangThaiSangKien::query()
                            ->where('ma_trang_thai', 'Reviewing')
                            ->first()->id;

                        if ($user->hasRole('manager') && $currentStatus === $pendingManagerId) {
                            $record->ma_trang_thai_sang_kien = $pendingSecretaryId;
                        } elseif ($user->hasRole('secretary') && $currentStatus === $pendingSecretaryId) {
                            // Nếu là thư ký, cập nhật hội đồng và trạng thái
                            $record->ma_trang_thai_sang_kien = $reviewingId;
                            $record->ma_hoi_dong = $data['hoi_dong_id']; // Lưu ID hội đồng được chọn
                        }

                        $record->save();

                        $message = $user->hasRole('secretary')
                            ? 'Sáng kiến đã được phê duyệt và chuyển đến hội đồng thẩm định'
                            : 'Sáng kiến đã được phê duyệt';

                        Notification::make()
                            ->title($message)
                            ->success()
                            ->send();
                    })
                    ->visible(fn () => auth()->user()->hasRole('manager') || auth()->user()->hasRole('secretary')),

                Tables\Actions\Action::make('reject')
                    ->label('Từ chối')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->modalHeading(fn (SangKien $record) => 'Từ chối sáng kiến: ' . $record->title)
                    ->modalDescription('Vui lòng cung cấp lý do từ chối sáng kiến này.')
                    ->form([
                        Forms\Components\Textarea::make('note')
                            ->label('Lý do từ chối')
                            ->placeholder('Nhập lý do từ chối ở đây...')
                            ->required()
                            ->maxLength(1000),
                    ])
                    ->action(function (SangKien $record, array $data) {
                        // Store the note if provided
                        if (!empty($data['note'])) {
                            $record->ghi_chu = $data['note'];
                        }
                        // Get the current status of the record
                        $currentStatus = $record->ma_trang_thai_sang_kien;

                        // Update status based on current user role
                        $user = auth()->user();
                        if ($user->hasRole('secretary')) {
                            // Get id from trang_thai_sang_kien table where ma_trang_thai = 'pending_secretary'
                            $pendingSecretaryId = TrangThaiSangKien::query()->where('ma_trang_thai', 'pending_secretary')->first()->id;
                            if ($currentStatus === $pendingSecretaryId) {
                                //set new ma_trang_thai_sang_kien into ID which has ma_trang_thai = 'Reviewing'
                                $record->ma_trang_thai_sang_kien = TrangThaiSangKien::query()->where('ma_trang_thai', 'rejected_secretary')->first()->id;
                            }
                        } elseif ($user->hasRole('manager')) {
                            // Get id from trang_thai_sang_kien table where ma_trang_thai = 'pending_manager'
                            $pendingManagerId = TrangThaiSangKien::query()->where('ma_trang_thai', 'pending_manager')->first()->id;
                            if ($currentStatus === $pendingManagerId) {
                                $record->ma_trang_thai_sang_kien = TrangThaiSangKien::query()->where('ma_trang_thai', 'rejected_manager')->first()->id;
                            }
                        }
                        $record->save();

                        Notification::make()
                            ->title('Sáng kiến đã bị từ chối')
                            ->danger()
                            ->send();
                    })
                    ->visible(function () {
                        // Only show for appropriate role and status
                        if (auth()->user()->hasRole('manager') || auth()->user()->hasRole('secretary')) {
                            return true;
                        }
                        return false;
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSangKienReviews::route('/'),
            'view' => Pages\ViewSangKienReview::route('/{record}'),
        ];
    }
}
