<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuanLyLoaiSangKienResource\Pages;
use App\Filament\Resources\QuanLyLoaiSangKienResource\RelationManagers;
use App\Models\LoaiSangKien;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class QuanLyLoaiSangKienResource extends Resource
{
    protected static ?string $model = LoaiSangKien::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $modelLabel = 'Loại sáng kiến';
    protected static ?string $pluralModelLabel = 'Loại sáng kiến';
    protected static ?string $navigationLabel = 'Quản lý loại sáng kiến';
    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('ten_loai_sang_kien')
                    ->label('Tên loại sáng kiến')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('mo_ta')
                    ->label('Mô tả')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ten_loai_sang_kien')
                    ->label('Tên loại sáng kiến')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('mo_ta')
                    ->label('Mô tả')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
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
            ])
            ->searchable();
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
            'index' => Pages\ListQuanLyLoaiSangKiens::route('/'),
            'create' => Pages\CreateQuanLyLoaiSangKien::route('/create'),
            'edit' => Pages\EditQuanLyLoaiSangKien::route('/{record}/edit'),
        ];
    }
}
