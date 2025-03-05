<?php

namespace App\Filament\Resources\QuanLyHoiDongResource\RelationManagers;

use App\Models\ThanhVienHoiDong;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ThanhVienHoiDongsRelationManager extends RelationManager
{
    protected static string $relationship = 'thanhVienHoiDongs';

    protected static ?string $title = 'Thành viên hội đồng';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('ma_nguoi_dung')
                    ->label('Thành viên')
                    ->options(function (RelationManager $livewire) {
                        // Lấy danh sách người dùng chưa là thành viên của hội đồng này
                        $hoiDongId = $livewire->getOwnerRecord()->id;
                        $truongHoiDongId = $livewire->getOwnerRecord()->ma_truong_hoi_dong;

                        $existingUserIds = ThanhVienHoiDong::where('ma_hoi_dong', $hoiDongId)
                            ->pluck('ma_nguoi_dung')
                            ->toArray();

                        return User::query()
                            ->whereNotIn('id', array_merge($existingUserIds, [$truongHoiDongId]))
                            ->pluck('name', 'id');
                    })
                    ->searchable()
                    ->preload()
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Tên thành viên')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('is_chairman')
                    ->label('Vai trò')
                    ->getStateUsing(function (Model $record, RelationManager $livewire): bool {
                        $truongHoiDongId = $livewire->getOwnerRecord()->ma_truong_hoi_dong;
                        return $record->ma_nguoi_dung == $truongHoiDongId;
                    })
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Trưởng hội đồng' : 'Thành viên')
                    ->colors([
                        'success' => true,
                        'primary' => false,
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày thêm')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Thêm thành viên')
                    ->mutateFormDataUsing(function (array $data): array {
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make()
                    ->visible(function (Model $record, RelationManager $livewire): bool {
                        $truongHoiDongId = $livewire->getOwnerRecord()->ma_truong_hoi_dong;
                        return $record->ma_nguoi_dung != $truongHoiDongId;
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->action(function (RelationManager $livewire, array $data): void {
                            $truongHoiDongId = $livewire->getOwnerRecord()->ma_truong_hoi_dong;

                            $recordsToDelete = ThanhVienHoiDong::whereIn('id', $data['records'])
                                ->where('ma_nguoi_dung', '!=', $truongHoiDongId)
                                ->get();

                            foreach ($recordsToDelete as $record) {
                                $record->delete();
                            }

                            $livewire->refresh();
                        }),
                ]),
            ]);
    }
}
