<?php

namespace App\Filament\Duy\Resources;

use App\Filament\Duy\Resources\SangkienResource\Pages;
use App\Models\Sangkien;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SangkienResource extends Resource
{
    protected static ?string $model = Sangkien::class;

    protected static ?string $navigationIcon = 'heroicon-o-light-bulb';

    protected static ?string $navigationLabel = 'Sáng kiến';

    protected static ?string $navigationGroup = 'Quản lý';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->label('Tiêu đề')
                            ->placeholder('Nhập tiêu đề sáng kiến')
                            ->maxLength(255),

                        Forms\Components\RichEditor::make('description')
                            ->required()
                            ->label('Mô tả')
                            ->placeholder('Nhập mô tả chi tiết về sáng kiến'),

                        Forms\Components\Select::make('status')
                            ->required()
                            ->label('Trạng thái')
                            ->options([
                                'draft' => 'Nháp',
                                'submitted' => 'Đã nộp',
                                'approved' => 'Đã duyệt',
                                'rejected' => 'Từ chối'
                            ])
                            ->default('draft'),

                        Forms\Components\Select::make('category')
                            ->required()
                            ->label('Danh mục')
                            ->options([
                                'technical' => 'Kỹ thuật',
                                'process' => 'Quy trình',
                                'management' => 'Quản lý',
                                'other' => 'Khác'
                            ]),

                        Forms\Components\DateTimePicker::make('submitted_date')
                            ->label('Ngày nộp'),

                        Forms\Components\DateTimePicker::make('approved_date')
                            ->label('Ngày duyệt'),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Tiêu đề')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category')
                    ->label('Danh mục')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->colors([
                        'warning' => 'draft',
                        'primary' => 'submitted',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('submitted_date')
                    ->label('Ngày nộp')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('approved_date')
                    ->label('Ngày duyệt')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'draft' => 'Nháp',
                        'submitted' => 'Đã nộp',
                        'approved' => 'Đã duyệt',
                        'rejected' => 'Từ chối'
                    ]),
                Tables\Filters\SelectFilter::make('category')
                    ->label('Danh mục')
                    ->options([
                        'technical' => 'Kỹ thuật',
                        'process' => 'Quy trình',
                        'management' => 'Quản lý',
                        'other' => 'Khác'
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSangkiens::route('/'),
            'create' => Pages\CreateSangkien::route('/create'),
            'edit' => Pages\EditSangkien::route('/{record}/edit'),
        ];
    }
}
