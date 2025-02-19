<?php

namespace App\Filament\Resources;

use App\Filament\Resources\XylyResource\Pages;
use App\Filament\Resources\XylyResource\RelationManagers;
use App\Models\Xyly;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class XylyResource extends Resource
{
    protected static ?string $model = Xyly::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    // Thêm thuộc tính này để chỉ định panel
    protected static ?string $panel = 'duy'; // Hoặc 'admin' tùy theo panel bạn muốn hiển thị

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
                //
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
            'index' => Pages\ListXylies::route('/'),
            'create' => Pages\CreateXyly::route('/create'),
            'edit' => Pages\EditXyly::route('/{record}/edit'),
        ];
    }
}
