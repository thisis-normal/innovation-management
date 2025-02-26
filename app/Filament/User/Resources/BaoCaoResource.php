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
use Filament\Tables\Actions\Action;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\TrangThaiSangKien;
use App\Models\DonVi;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Filters\Indicator;
use Illuminate\Support\Collection;
use Filament\Actions\Exports\Models\Export;
use Filament\Actions\Exports\Exporter;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use App\Exports\SangKienExporter;

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
                TextColumn::make('ten_sang_kien')
                    ->label('Tên Sáng Kiến')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('mo_ta')
                    ->label('Mô Tả')
                    ->limit(50)
                    ->searchable(),

                TextColumn::make('user.name')
                    ->label('Tác Giả')
                    ->sortable(),

                TextColumn::make('donVi.ten_don_vi')
                    ->label('Đơn Vị')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('trangThaiSangKien.ten_trang_thai')
                    ->label('Trạng Thái')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Ngày Tạo')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('ma_trang_thai_sang_kien')
                    ->label('Trạng Thái')
                    ->relationship('trangThaiSangKien', 'ten_trang_thai')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->query(function (Builder $query, array $data): Builder {
                        if (!empty($data['values'])) {
                            $query->whereIn('ma_trang_thai_sang_kien', $data['values']);
                        }
                        return $query;
                    })
                    ->indicateUsing(function (array $data): array {
                        if (!$data['values'] || count($data['values']) === 0) {
                            return [];
                        }

                        return Collection::wrap($data['values'])
                            ->map(function (string $value): Indicator {
                                $trangThai = TrangThaiSangKien::find($value);
                                $label = $trangThai ? $trangThai->ten_trang_thai : $value;

                                return Indicator::make("Trạng thái: {$label}");
                            })
                            ->all();
                    }),

                SelectFilter::make('ma_don_vi')
                    ->label('Đơn Vị')
                    ->relationship('donVi', 'ten_don_vi')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->query(function (Builder $query, array $data): Builder {
                        if (!empty($data['values'])) {
                            $query->whereIn('ma_don_vi', $data['values']);
                        }
                        return $query;
                    })
                    ->indicateUsing(function (array $data): array {
                        if (!$data['values'] || count($data['values']) === 0) {
                            return [];
                        }

                        return Collection::wrap($data['values'])
                            ->map(function (string $value): Indicator {
                                $donVi = DonVi::find($value);
                                $label = $donVi ? $donVi->ten_don_vi : $value;

                                return Indicator::make("Đơn vị: {$label}");
                            })
                            ->all();
                    }),

                Filter::make('created_at')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('from')
                                    ->label('Từ ngày'),
                                Forms\Components\DatePicker::make('until')
                                    ->label('Đến ngày'),
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (!empty($data['from'])) {
                            $query->whereDate('created_at', '>=', $data['from']);
                        }
                        if (!empty($data['until'])) {
                            $query->whereDate('created_at', '<=', $data['until']);
                        }
                        return $query;
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['from'] ?? null) {
                            $indicators[] = Indicator::make('Từ ngày: ' . Carbon::parse($data['from'])->format('d/m/Y'));
                        }

                        if ($data['until'] ?? null) {
                            $indicators[] = Indicator::make('Đến ngày: ' . Carbon::parse($data['until'])->format('d/m/Y'));
                        }

                        return $indicators;
                    }),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormWidth('2xl')
            ->filtersFormColumns(3)
            ->filtersApplyAction(
                fn (Action $action) => $action
                    ->button()
                    ->label('Tìm kiếm'),
            )
            ->deferFilters()
            ->deselectAllRecordsWhenFiltered(false)
            ->emptyStateHeading('Không tìm thấy báo cáo liên quan')
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Xem chi tiết')
                    ->form([
                        Forms\Components\TextInput::make('ten_sang_kien')
                            ->label('Tên sáng kiến')
                            ->disabled(),
                        Forms\Components\RichEditor::make('hien_trang')
                            ->label('Hiện trạng')
                            ->disabled(),
                        Forms\Components\RichEditor::make('mo_ta')
                            ->label('Mô tả')
                            ->disabled(),
                        Forms\Components\RichEditor::make('ket_qua')
                            ->label('Kết quả')
                            ->disabled(),
                        Forms\Components\TextInput::make('user.name')
                            ->label('Tác giả')
                            ->formatStateUsing(fn ($record) => $record->user?->name)
                            ->disabled(),
                        Forms\Components\TextInput::make('donVi.ten_don_vi')
                            ->label('Đơn vị')
                            ->formatStateUsing(fn ($record) => $record->donVi?->ten_don_vi)
                            ->disabled(),
                        Forms\Components\TextInput::make('trangThaiSangKien.ten_trang_thai')
                            ->label('Trạng thái')
                            ->formatStateUsing(fn ($record) => $record->trangThaiSangKien?->ten_trang_thai)
                            ->disabled(),
                    ])
                    ->modalHeading('Chi tiết sáng kiến')
                    ->modalWidth('5xl'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    ExportBulkAction::make()
                        ->label('Xuất dữ liệu')
                        ->color('success')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->exporter(SangKienExporter::class)
                        ->formats([
                            'xlsx' => 'Excel',
                            'csv' => 'CSV',
                        ])
                ]),
            ])
            ->headerActions([
                ExportAction::make()
                    ->label('Xuất dữ liệu')
                    ->color('success')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->exporter(SangKienExporter::class)
                    ->formats([
                        'xlsx' => 'Excel',
                        'csv' => 'CSV',
                    ])
            ])
            ->defaultSort('created_at', 'desc');
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}
