<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuanLyDanhGiaResource\Pages;
use App\Filament\Resources\QuanLyDanhGiaResource\RelationManagers\TieuChiDanhGiasRelationManager;
use App\Models\DotDanhGia;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class QuanLyDanhGiaResource extends Resource
{
    protected static ?string $model = DotDanhGia::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $modelLabel = 'Đợt Đánh Giá';
    protected static ?string $slug = 'quan-ly-danh-gia';
    protected static ?string $pluralModelLabel = 'Đợt Đánh Giá';
    protected static ?string $navigationLabel = 'Quản lý Đánh Giá';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('nam')
                            ->label('Năm')
                            ->required()
                            ->numeric()
                            ->default(date('Y'))
                            ->disabled()
                            ->dehydrated(),

                        Forms\Components\TextInput::make('so_dot')
                            ->label('Số đợt')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(10)
                            ->default(1),

                        Forms\Components\Textarea::make('mo_ta')
                            ->label('Mô tả')
                            ->maxLength(65535)
                            ->columnSpanFull(),

                        Forms\Components\Hidden::make('created_by')
                            ->default(fn () => auth()->id()),
                        Forms\Components\Hidden::make('updated_by')
                            ->default(fn () => auth()->id()),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nam')
                    ->label('Năm')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('so_dot')
                    ->label('Số đợt')
                    ->sortable(),

                Tables\Columns\TextColumn::make('mo_ta')
                    ->label('Mô tả')
                    ->limit(50)
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Ngày cập nhật')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('nam')
                    ->label('Năm')
                    ->options(
                        fn () => DotDanhGia::distinct()->pluck('nam', 'nam')->toArray()
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Sửa'),
                Tables\Actions\DeleteAction::make()
                    ->label('Xóa')
                    ->after(function ($record) {
                        // Lấy tất cả các đợt có số lớn hơn đợt vừa xóa
                        $dotsToUpdate = DB::table('dot_danh_gia')
                            ->where('nam', $record->nam)
                            ->where('so_dot', '>', $record->so_dot)
                            ->orderBy('so_dot')
                            ->get();

                        // Cập nhật lại số đợt
                        foreach ($dotsToUpdate as $dot) {
                            DB::table('dot_danh_gia')
                                ->where('id', $dot->id)
                                ->update(['so_dot' => $dot->so_dot - 1]);
                        }
                    }),
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
            TieuChiDanhGiasRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuanLyDanhGias::route('/'),
            'create' => Pages\CreateQuanLyDanhGia::route('/create'),
            'edit' => Pages\EditQuanLyDanhGia::route('/{record}/edit'),
            'view' => Pages\ViewQuanLyDanhGia::route('/{record}'),
        ];
    }
}
