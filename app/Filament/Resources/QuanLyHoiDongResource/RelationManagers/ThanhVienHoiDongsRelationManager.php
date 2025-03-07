<?php

namespace App\Filament\Resources\QuanLyHoiDongResource\RelationManagers;

use App\Models\ThanhVienHoiDong;
use App\Models\User;
use App\Models\DonVi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use CodeWithDennis\FilamentSelectTree\SelectTree;

class ThanhVienHoiDongsRelationManager extends RelationManager
{
    protected static string $relationship = 'thanhVienHoiDongs';

    protected static ?string $title = 'Thành viên hội đồng';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $createButtonLabel = 'Thêm thành viên';
    protected static ?string $modalHeading = 'Thêm thành viên hội đồng';
    protected static ?string $modalCreateButtonLabel = 'Thêm';
    protected static ?string $modalCancelButtonLabel = 'Hủy bỏ';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                SelectTree::make('don_vi_id')
                    ->label('Đơn vị')
                    ->relationship(
                        'donVi',
                        'ten_don_vi',
                        'don_vi_cha_id'
                    )
                    ->searchable()
                    ->enableBranchNode()
                    ->defaultOpenLevel(2)
                    ->required()
                    ->reactive(),

                Forms\Components\Select::make('ma_nguoi_dung')
                    ->label('Thành viên')
                    ->options(function (callable $get, RelationManager $livewire) {
                        $donViId = $get('don_vi_id');
                        if (!$donViId) {
                            return [];
                        }

                        // Lấy danh sách người dùng chưa là thành viên của hội đồng này
                        $hoiDongId = $livewire->getOwnerRecord()->id;
                        $truongHoiDongId = $livewire->getOwnerRecord()->ma_truong_hoi_dong;

                        $existingUserIds = ThanhVienHoiDong::where('ma_hoi_dong', $hoiDongId)
                            ->pluck('ma_nguoi_dung')
                            ->toArray();

                        return User::query()
                            ->whereHas('donVis', function ($query) use ($donViId) {
                                $query->where('don_vi.id', $donViId);
                            })
                            ->whereNotIn('id', array_merge($existingUserIds, [$truongHoiDongId]))
                            ->pluck('name', 'id');
                    })
                    ->searchable()
                    ->preload()
                    ->required()
                    ->disabled(fn (callable $get) => !$get('don_vi_id'))
                    ->helperText(fn (callable $get) => !$get('don_vi_id') ? 'Vui lòng chọn đơn vị trước' : ''),
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

                Tables\Columns\TextColumn::make('user.donVis.ten_don_vi')
                    ->label('Đơn vị')
                    ->searchable(),

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
            ->filters([
                Tables\Filters\SelectFilter::make('don_vi')
                    ->label('Đơn vị')
                    ->relationship('user.donVis', 'ten_don_vi'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Thêm thành viên')
                    ->mutateFormDataUsing(function (array $data): array {
                        unset($data['don_vi_id']); // Loại bỏ don_vi_id khỏi dữ liệu lưu
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
