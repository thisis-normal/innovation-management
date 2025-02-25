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

                TextColumn::make('ket_qua')
                    ->label('Xếp Loại')
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

                SelectFilter::make('ket_qua')
                    ->label('Xếp Loại')
                    ->options([
                        'A' => 'Loại A',
                        'B' => 'Loại B',
                        'C' => 'Loại C',
                    ])
                    ->multiple()
                    ->query(function (Builder $query, array $data): Builder {
                        return $query;
                    })
                    ->indicateUsing(function (array $data): array {
                        if (!$data['values'] || count($data['values']) === 0) {
                            return [];
                        }

                        $options = [
                            'A' => 'Loại A',
                            'B' => 'Loại B',
                            'C' => 'Loại C',
                        ];

                        return Collection::wrap($data['values'])
                            ->map(function (string $value) use ($options): Indicator {
                                $label = $options[$value] ?? $value;

                                return Indicator::make("Xếp loại: {$label}");
                            })
                            ->all();
                    }),

                Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Từ ngày'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Đến ngày'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
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
                            ->disabled(),
                        Forms\Components\TextInput::make('donVi.ten_don_vi')
                            ->label('Đơn vị')
                            ->disabled(),
                        Forms\Components\TextInput::make('trangThaiSangKien.ten_trang_thai')
                            ->label('Trạng thái')
                            ->disabled(),
                    ])
                    ->modalHeading('Chi tiết sáng kiến')
                    ->modalWidth('5xl'),
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
        $query = parent::getEloquentQuery();

        // Xử lý lọc được chọn trước khi áp dụng
        $request = request();
        $filters = $request->get('tableFilters', []);

        // Xử lý lọc trạng thái
        if (!empty($filters['ma_trang_thai_sang_kien']['values'])) {
            $query->whereIn('ma_trang_thai_sang_kien', $filters['ma_trang_thai_sang_kien']['values']);
        }

        // Xử lý lọc đơn vị
        if (!empty($filters['ma_don_vi']['values'])) {
            $query->whereIn('ma_don_vi', $filters['ma_don_vi']['values']);
        }

        // Xử lý lọc xếp loại
        if (!empty($filters['ket_qua']['values'])) {
            $query->whereIn('ket_qua', $filters['ket_qua']['values']);
        }

        // Xử lý lọc ngày tạo
        if (!empty($filters['created_at'])) {
            if (!empty($filters['created_at']['from'])) {
                $query->whereDate('created_at', '>=', $filters['created_at']['from']);
            }
            if (!empty($filters['created_at']['until'])) {
                $query->whereDate('created_at', '<=', $filters['created_at']['until']);
            }
        }

        return $query;
    }
}
