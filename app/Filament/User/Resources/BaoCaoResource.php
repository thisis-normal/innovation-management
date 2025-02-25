<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\BaoCaoResource\Pages;
use App\Filament\User\Resources\BaoCaoResource\RelationManagers;
use App\Models\BaoCao;
use App\Models\SangKien;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BaoCaoResource extends Resource
{
    protected static ?string $model = SangKien::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Báo Cáo';
    protected static ?string $pluralModelLabel = 'Báo Cáo';
    protected static ?string $modelLabel = 'Báo Cáo';
    protected static ?int $navigationSort = 2;

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
            'index' => Pages\ListBaoCaos::route('/'),
            'create' => Pages\CreateBaoCao::route('/create'),
            'edit' => Pages\EditBaoCao::route('/{record}/edit'),
        ];
    }
}
