<?php

namespace App\Filament\Resources\QuanLyDanhGiaResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class TieuChiDanhGiasRelationManager extends RelationManager
{
    protected static string $relationship = 'tieuChiDanhGias';
    protected static ?string $title = 'Tiêu chí đánh giá';
    protected static ?string $recordTitleAttribute = 'ten_tieu_chi';

    protected bool $allowsDuplicates = true;

    public function isTableRecordsDeletable(): bool
    {
        return !request()->routeIs('filament.resources.quan-ly-danh-gia.view');
    }

    protected function canCreate(): bool
    {
        return !request()->routeIs('filament.resources.quan-ly-danh-gia.view');
    }

    public function canEdit(Model $record): bool
    {
        return !request()->routeIs('filament.resources.quan-ly-danh-gia.view');
    }

    protected function canDelete(Model $record): bool
    {
        return !request()->routeIs('filament.resources.quan-ly-danh-gia.view');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('ten_tieu_chi')
                    ->label('Tên tiêu chí')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('mo_ta')
                    ->label('Mô tả')
                    ->maxLength(255),

                Forms\Components\TextInput::make('diem_toi_da')
                    ->label('Điểm tối đa')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100),

                Forms\Components\Hidden::make('created_by')
                    ->default(fn () => Auth::id()),
                Forms\Components\Hidden::make('updated_by')
                    ->default(fn () => Auth::id()),
            ]);
    }

    public function table(Table $table): Table
    {
        $isViewMode = request()->routeIs('filament.resources.quan-ly-danh-gia.view');

        $table = $table
            ->columns([
                Tables\Columns\TextColumn::make('ten_tieu_chi')
                    ->label('Tên tiêu chí')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('mo_ta')
                    ->label('Mô tả')
                    ->limit(50)
                    ->searchable(),

                Tables\Columns\TextColumn::make('diem_toi_da')
                    ->label('Điểm tối đa')
                    ->sortable(),
            ]);

        if (!$isViewMode) {
            $table->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Thêm tiêu chí'),

                Tables\Actions\Action::make('save_changes')
                    ->label('Lưu thay đổi')
                    ->action(function () {
                        $totalPoints = $this->ownerRecord->tieuChiDanhGias()->sum('diem_toi_da');

                        if (abs($totalPoints - 100) > 0.01) {
                            $message = $totalPoints > 100
                                ? "Tổng điểm vượt quá 100. Hiện tại: {$totalPoints} điểm."
                                : "Tổng điểm chưa đủ 100. Hiện tại: {$totalPoints} điểm.";

                            Notification::make()
                                ->danger()
                                ->title('Lỗi tổng điểm')
                                ->body($message)
                                ->send();

                            return;
                        }

                        $this->ownerRecord->tieuChiDanhGias()->saveMany(
                            $this->ownerRecord->tieuChiDanhGias
                        );

                        Notification::make()
                            ->success()
                            ->title('Đã lưu thành công')
                            ->body('Các tiêu chí đã được cập nhật.')
                            ->send();
                    })
                    ->color('success')
                    ->icon('heroicon-o-check'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Sửa'),
                Tables\Actions\DeleteAction::make()
                    ->label('Xóa'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Xóa các mục đã chọn'),
                ]),
            ]);
        }

        return $table;
    }
}
