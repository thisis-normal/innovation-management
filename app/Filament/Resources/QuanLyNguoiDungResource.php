<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuanLyNguoiDungResource\Pages;
use App\Filament\Resources\QuanLyNguoiDungResource\RelationManagers;
use App\Models\User;
use App\Models\VaiTro;
use App\Models\DonVi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use CodeWithDennis\FilamentSelectTree\SelectTree;
use Filament\Actions\Action;

class QuanLyNguoiDungResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Quản lý người dùng';
    protected static ?string $modelLabel = 'Quản lý người dùng';
    protected static ?string $pluralModelLabel = 'Quản lý người dùng';
    protected static ?string $slug = 'quan-ly-nguoi-dung';
    protected static ?int $navigationSort = 1;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('username')
                            ->label('Tên đăng nhập')
                            ->required()
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('name')
                            ->label('Họ và tên')
                            ->required(),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('password')
                            ->label('Mật khẩu')
                            ->password()
                            ->revealable()
                            ->dehydrateStateUsing(function ($state) {
                                if (empty($state)) {
                                    return null; // Trả về null nếu password trống
                                }
                                return Hash::make($state);
                            })
                            ->required(fn (string $context): bool => $context === 'create'),
                        Forms\Components\Select::make('vai_tro_ids')
                            ->label('Vai trò')
                            ->multiple()
                            ->options(function() {
                                return \App\Models\VaiTro::where('trang_thai', true)
                                    ->pluck('ten_vai_tro', 'id');
                            })
                            ->preload()
                            ->required(),
                        SelectTree::make('don_vi_ids')
                            ->label('Đơn vị')
                            ->relationship('donVis', 'ten_don_vi', 'don_vi_cha_id')
                            ->searchable()
                            ->enableBranchNode()
                            ->defaultOpenLevel(2)
                            ->saveRelationshipsUsing(function (User $record, array $state) {
                                $record->syncDonVis($state);
                            })
                            ->rules([
                                function() {
                                    return function($attribute, $value, $fail) {
                                        if (empty($value)) return;

                                        foreach ($value as $donViId) {
                                            $donVi = DonVi::find($donViId);
                                            if (!$donVi) continue;

                                            // Kiểm tra xem có chọn cả cha và con không
                                            foreach ($value as $otherId) {
                                                if ($donViId === $otherId) continue;

                                                $otherDonVi = DonVi::find($otherId);
                                                if (!$otherDonVi) continue;

                                                // Nếu đơn vị hiện tại là con và đã chọn đơn vị cha
                                                if ($donVi->don_vi_cha_id !== null &&
                                                    in_array($donVi->don_vi_cha_id, $value)) {
                                                    $fail("Không thể chọn cả đơn vị cha và đơn vị con.");
                                                    return;
                                                }

                                                // Nếu đơn vị hiện tại là cha và đã chọn đơn vị con
                                                if ($donVi->donViCon->contains('id', $otherId)) {
                                                    $fail("Không thể chọn cả đơn vị cha và đơn vị con.");
                                                    return;
                                                }
                                            }
                                        }
                                    };
                                }
                            ]),
                    ])
                    ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('username')
                    ->label('Tên đăng nhập')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Họ và tên')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('lnkNguoiDungVaiTros.vaiTro.ten_vai_tro')
                    ->label('Vai trò')
                    ->badge()
                    ->separator(','),
                Tables\Columns\TextColumn::make('donVis.ten_don_vi')
                    ->label('Đơn vị')
                    ->badge()
                    ->separator(','),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('roles')
                    ->label('Vai trò')
                    ->relationship('roles', 'ten_vai_tro')
                    ->multiple()
                    ->preload(),
                Tables\Filters\SelectFilter::make('don_vi')
                    ->label('Đơn vị')
                    ->relationship('donVis', 'ten_don_vi')
                    ->multiple()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Chỉnh sửa'),
                Tables\Actions\DeleteAction::make()
                    ->label('Xóa')
                    ->modalHeading('Xóa người dùng')
                    ->modalDescription('Bạn có chắc chắn muốn xóa người dùng này?')
                    ->modalSubmitActionLabel('Xóa')
                    ->modalCancelActionLabel('Hủy bỏ'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->modalHeading('Xóa người dùng đã chọn')
                        ->modalDescription('Bạn có chắc chắn muốn xóa những người dùng đã chọn?')
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
            'index' => Pages\ListQuanLyNguoiDungs::route('/'),
            'create' => Pages\CreateQuanLyNguoiDung::route('/create'),
            'edit' => Pages\EditQuanLyNguoiDung::route('/{record}/edit'),
        ];
    }

    protected static function getDefaultTableRecordsPerPageSelectOption(): int
    {
        return 10;
    }
}
