<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuanLyVaiTroResource\Pages;
use App\Filament\Resources\QuanLyVaiTroResource\RelationManagers;
use App\Models\VaiTro;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class QuanLyVaiTroResource extends Resource
{
    protected static ?string $model = VaiTro::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Quản lý vai trò';
    protected static ?string $modelLabel = 'Quản lý vai trò';
    protected static ?string $pluralModelLabel = 'Quản lý vai trò';
    protected static ?string $slug = 'quan-ly-vai-tro';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('ma_vai_tro')
                            ->label('Mã vai trò')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Forms\Components\TextInput::make('ten_vai_tro')
                            ->label('Tên vai trò')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('mo_ta')
                            ->label('Mô tả')
                            ->maxLength(255)
                            ->columnSpan('full'),

                        Forms\Components\Toggle::make('trang_thai')
                            ->label('Trạng thái')
                            ->default(true)
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ma_vai_tro')
                    ->label('Mã vai trò')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('ten_vai_tro')
                    ->label('Tên vai trò')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('mo_ta')
                    ->label('Mô tả')
                    ->searchable()
                    ->limit(50),

                Tables\Columns\IconColumn::make('trang_thai')
                    ->label('Trạng thái')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('trang_thai')
                    ->label('Trạng thái')
                    ->options([
                        '1' => 'Hoạt động',
                        '0' => 'Không hoạt động',
                    ]),
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
                        ->label('Xóa đã chọn'),
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
            'index' => Pages\ListQuanLyVaiTros::route('/'),
            'create' => Pages\CreateQuanLyVaiTro::route('/create'),
            'edit' => Pages\EditQuanLyVaiTro::route('/{record}/edit'),
        ];
    }
}
