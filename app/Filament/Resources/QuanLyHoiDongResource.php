<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuanLyHoiDongResource\Pages;
use App\Filament\Resources\QuanLyHoiDongResource\RelationManagers\ThanhVienHoiDongsRelationManager;
use App\Models\HoiDongThamDinh;
use App\Models\User;
use App\Models\DonVi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\QuanLyHoiDongResource\Pages\ViewQuanLyHoiDong;
use CodeWithDennis\FilamentSelectTree\SelectTree;

class QuanLyHoiDongResource extends Resource
{
    protected static ?string $model = HoiDongThamDinh::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $modelLabel = 'Hội Đồng';
    protected static ?string $slug = 'quan-ly-hoi-dong';
    protected static ?string $pluralModelLabel = 'Hội Đồng';
    protected static ?string $navigationLabel = 'Quản lý Hội Đồng';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('ten_hoi_dong')
                            ->label('Tên Hội Đồng')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Grid::make()
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
                                    ->reactive()
                                    ->columnSpan(1),

                                Forms\Components\Select::make('ma_truong_hoi_dong')
                                    ->label('Trưởng Hội Đồng')
                                    ->options(function (callable $get, $record) {
                                        $donViId = $get('don_vi_id');
                                        if (!$donViId && !$record) {
                                            return [];
                                        }

                                        if (!$donViId && $record) {
                                            $donViId = $record->don_vi_id;
                                        }

                                        if (!$donViId) {
                                            return [];
                                        }

                                        return User::whereHas('donVis', function ($query) use ($donViId) {
                                            $query->where('don_vi.id', $donViId);
                                        })->pluck('name', 'id');
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->reactive()
                                    ->disabled(fn (callable $get, $record) => !$get('don_vi_id') && !($record && $record->don_vi_id))
                                    ->helperText(fn (callable $get, $record) => !$get('don_vi_id') && !($record && $record->don_vi_id) ? 'Vui lòng chọn đơn vị trước' : '')
                                    ->columnSpan(1),
                            ])
                            ->columns(2),

                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\DatePicker::make('ngay_bat_dau')
                                    ->label('Ngày bắt đầu')
                                    ->required()
                                    ->columnSpan(1),

                                Forms\Components\DatePicker::make('ngay_ket_thuc')
                                    ->label('Ngày kết thúc')
                                    ->after('ngay_bat_dau')
                                    ->required()
                                    ->columnSpan(1),
                            ])
                            ->columns(2),

                        Forms\Components\Toggle::make('trang_thai')
                            ->label('Trạng thái')
                            ->onColor('success')
                            ->offColor('danger')
                            ->default(true)
                            ->required(),

                        Forms\Components\Textarea::make('ghi_chu')
                            ->label('Ghi chú')
                            ->maxLength(65535),
                    ])
                    ->columns([
                        'sm' => 1,
                        'lg' => 1,
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ten_hoi_dong')
                    ->label('Tên hội đồng')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('donVi.ten_don_vi')
                    ->label('Đơn vị')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('truongHoiDong.name')
                    ->label('Trưởng hội đồng')
                    ->sortable(),

                Tables\Columns\TextColumn::make('ngay_bat_dau')
                    ->label('Ngày bắt đầu')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('ngay_ket_thuc')
                    ->label('Ngày kết thúc')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\IconColumn::make('trang_thai')
                    ->label('Trạng thái')
                    ->boolean()
                    ->alignCenter()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('trang_thai')
                    ->label('Trạng thái')
                    ->options([
                        1 => 'Hoạt động',
                        0 => 'Không hoạt động',
                    ]),

                Tables\Filters\SelectFilter::make('don_vi_id')
                    ->label('Đơn vị')
                    ->relationship('donVi', 'ten_don_vi'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            ])
            ->paginated([
                5, 10, 25, 50, 'all'
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ThanhVienHoiDongsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuanLyHoiDongs::route('/'),
            'create' => Pages\CreateQuanLyHoiDong::route('/create'),
            'edit' => Pages\EditQuanLyHoiDong::route('/{record}/edit'),
            'view' => Pages\ViewQuanLyHoiDong::route('/{record}'),
        ];
    }
}
