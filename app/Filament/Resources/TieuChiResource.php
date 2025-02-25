<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TieuChiResource\Pages;
use App\Models\TieuChi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TieuChiResource extends Resource
{
    protected static ?string $model = TieuChi::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Tiêu chí chấm điểm';
    protected static ?string $modelLabel = 'Tiêu chí chấm điểm';
    protected static ?string $pluralModelLabel = 'Tiêu chí chấm điểm';
    protected static ?string $slug = 'tieu-chi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('stt')
                    ->label('Số thứ tự')
                    ->placeholder('Nhập số thứ tự')
                    ->maxLength(255),

                Forms\Components\TextInput::make('ten_tieu_chi')
                    ->label('Tên tiêu chí')
                    ->placeholder('Nhập tên tiêu chí')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('ghi_chu')
                    ->label('Ghi chú')
                    ->placeholder('Nhập ghi chú')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('stt')
                    ->label('Số thứ tự')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('ten_tieu_chi')
                    ->label('Tên tiêu chí')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('ghi_chu')
                    ->label('Ghi chú')
                    ->limit(50)
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Ngày cập nhật')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListTieuChis::route('/'),
            'create' => Pages\CreateTieuChi::route('/create'),
            'edit' => Pages\EditTieuChi::route('/{record}/edit'),
        ];
    }
}
