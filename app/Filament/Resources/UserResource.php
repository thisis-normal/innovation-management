<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;

class UserResource extends Resource
{
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?int $navigationSort = 7;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Họ và tên')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('username')
                    ->label('Tên đăng nhập')
                    ->disabled()
                    ->dehydrated(false),
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->disabled()
                    ->maxLength(255),

                // Wrap password fields in a Section
                Section::make('Đổi mật khẩu')
                    ->schema([
                        Forms\Components\TextInput::make('current_password')
                            ->label('Mật khẩu hiện tại')
                            ->password()
                            ->revealable()
                            ->dehydrated(false)
                            ->rules(['required_with:password'])
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, $set, Forms\Get $get) {
                                if (empty($state)) {
                                    $set('password', null);
                                    $set('password_confirmation', null);
                                    return;
                                }

                                if (!Hash::check($state, auth()->user()->password)) {
                                    $set('password', null);
                                    $set('password_confirmation', null);
                                    $set('current_password_valid', false);
                                } else {
                                    $set('current_password_valid', true);
                                }
                            })
                            ->validationMessages([
                                'required_with' => 'Vui lòng nhập mật khẩu hiện tại để đổi mật khẩu mới',
                            ])
                            ->suffixIcon(fn ($get) => $get('current_password') ?
                                ($get('current_password_valid') ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle') : null)
                            ->suffixIconColor(fn ($get) => $get('current_password_valid') ? 'success' : 'danger')
                            ->helperText(fn ($get) => $get('current_password') && !$get('current_password_valid') ?
                                'Mật khẩu hiện tại không đúng' : null)
                            ->extraInputAttributes(fn ($get) => [
                                'class' => $get('current_password') && !$get('current_password_valid') ? 'border-danger-600 ring-danger-600' : ''
                            ]),

                        Forms\Components\TextInput::make('password')
                            ->label('Mật khẩu mới')
                            ->password()
                            ->revealable()
                            ->disabled(fn ($get) => !$get('current_password') ||
                                !Hash::check($get('current_password'), auth()->user()->password))
                            ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                            ->dehydrated(fn ($state) => filled($state))
                            ->maxLength(255),

                        Forms\Components\TextInput::make('password_confirmation')
                            ->label('Xác nhận mật khẩu')
                            ->password()
                            ->revealable()
                            ->disabled(fn ($get) => !$get('current_password') ||
                                !Hash::check($get('current_password'), auth()->user()->password))
                            ->required(fn ($get) => filled($get('password')))
                            ->same('password')
                            ->dehydrated(false),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('username'),
                Tables\Columns\TextColumn::make('email'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
