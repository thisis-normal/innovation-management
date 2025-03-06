<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SangKienResource\Pages;
use App\Models\SangKien;
use App\Models\LoaiSangKien;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SangKienResource extends Resource
{
    protected static ?string $model = SangKien::class;

    protected static ?string $navigationIcon = 'heroicon-o-light-bulb';
    protected static ?string $modelLabel = 'Sáng kiến';
    protected static ?string $pluralModelLabel = 'Sáng kiến';
    protected static ?string $navigationLabel = 'Quản lý sáng kiến';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('ten_sang_kien')
                    ->label('Tên sáng kiến')
                    ->required()
                    ->columnSpan('full'),
                Forms\Components\Select::make('ma_loai_sang_kien')
                    ->label('Loại sáng kiến')
                    ->relationship('loaiSangKien', 'ten_loai_sang_kien')
                    ->required()
                    ->preload()
                    ->searchable()
                    ->native(false),
                Forms\Components\RichEditor::make('mo_ta')
                    ->label('Mô tả')
                    ->required()
                    ->columnSpan('full'),
                Forms\Components\RichEditor::make('truoc_khi_ap_dung')
                    ->label('Trước khi áp dụng')
                    ->required()
                    ->columnSpan('full'),
                Forms\Components\RichEditor::make('sau_khi_ap_dung')
                    ->label('Sau khi áp dụng')
                    ->required()
                    ->columnSpan('full'),
                Forms\Components\Textarea::make('ghi_chu')
                    ->label('Ghi chú')
                    ->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ten_sang_kien')
                    ->label('Tên sáng kiến')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('loaiSangKien.ten_loai_sang_kien')
                    ->label('Loại sáng kiến')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('mo_ta')
                    ->label('Mô tả')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Tác giả')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('donVi.ten_don_vi')
                    ->label('Đơn vị')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('trangThaiSangKien.ten_trang_thai')
                    ->label('Trạng thái')
                    ->badge()
                    ->sortable(),
            ])
            ->filters([
                //
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
            ])
            ->paginated([
                5, 10, 25, 50, 'all'
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
            'index' => Pages\ListSangKiens::route('/'),
            'create' => Pages\CreateSangKien::route('/create'),
            'edit' => Pages\EditSangKien::route('/{record}/edit'),
        ];
    }
}
