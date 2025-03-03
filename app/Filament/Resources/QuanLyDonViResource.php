<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuanLyDonViResource\Pages;
use App\Filament\Resources\QuanLyDonViResource\RelationManagers;
use App\Models\DonVi;
use CodeWithDennis\FilamentSelectTree\SelectTree;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;

class QuanLyDonViResource extends Resource
{
    protected static ?string $model = DonVi::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationLabel = 'Quản lý đơn vị';
    protected static ?string $modelLabel = 'Quản lý đơn vị';
    protected static ?string $pluralModelLabel = 'Quản lý đơn vị';
    protected static ?string $slug = 'quan-ly-don-vi';
    protected static ?int $navigationSort = 3;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('ten_don_vi')
                    ->required()
                    ->maxLength(255)
                    ->label('Tên đơn vị'),

                Forms\Components\Textarea::make('mo_ta')
                    ->maxLength(65535)
                    ->label('Mô tả'),

                SelectTree::make('don_vi_cha_id')
                    ->label('Đơn vị cha')
                    ->relationship('donViCha', 'ten_don_vi', 'don_vi_cha_id')
                    ->searchable()
                    ->enableBranchNode()
                    ->defaultOpenLevel(2)
                    ->disabledOptions(function ($record) {
                        if (!$record) return [];
                        return [$record->id];
                    }),

                Forms\Components\Toggle::make('trang_thai')
                    ->label('Trạng thái')
                    ->default(true)
                    ->onColor('success')
                    ->offColor('danger'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ten_don_vi')
                    ->label('Tên đơn vị')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('mo_ta')
                    ->label('Mô tả')
                    ->limit(50)
                    ->searchable(),

                Tables\Columns\TextColumn::make('donViCha.ten_don_vi')
                    ->label('Đơn vị cha')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\IconColumn::make('trang_thai')
                    ->label('Trạng thái')
                    ->boolean()
                    ->alignCenter()
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
                        1 => 'Hoạt động',
                        0 => 'Không hoạt động',
                    ]),

                Tables\Filters\SelectFilter::make('don_vi_cha_id')
                    ->label('Đơn vị cha')
                    ->relationship('donViCha', 'ten_don_vi')
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Chỉnh sửa'),
                Tables\Actions\DeleteAction::make()
                    ->label('Xóa')
                    ->modalHeading('Xóa đơn vị')
                    ->modalDescription('Bạn có chắc chắn muốn xóa đơn vị này?')
                    ->modalSubmitActionLabel('Xóa')
                    ->modalCancelActionLabel('Hủy bỏ'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->modalHeading('Xóa đơn vị đã chọn')
                        ->modalDescription('Bạn có chắc chắn muốn xóa những đơn vị đã chọn?')
                        ->modalSubmitActionLabel('Xóa')
                        ->modalCancelActionLabel('Hủy bỏ'),
                ]),
            ])
            ->paginated([
                'reorderRecordsTriggerAction' => false,
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
            'index' => Pages\ListQuanLyDonVis::route('/'),
            'create' => Pages\CreateQuanLyDonVi::route('/create'),
            'edit' => Pages\EditQuanLyDonVi::route('/{record}/edit'),
        ];
    }
}
