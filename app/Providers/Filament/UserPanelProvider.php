<?php

namespace App\Providers\Filament;

use Exception;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\Support\Facades\FilamentColor;
use Filament\Widgets;
use Hasnayeen\Themes\ThemesPlugin;
use Hasnayeen\Themes\Http\Middleware\SetTheme;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Navigation\MenuItem;
use App\Filament\Resources\UserResource;
use Filament\Support\Assets\Css;

class UserPanelProvider extends PanelProvider
{
    /**
     * @throws Exception
     */
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('user')
            ->path('user')
            ->authGuard('web')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->brandLogo(asset('img/logo-vnpt.png'))
            ->darkModeBrandLogo(asset('img/logo-mb.png'))
            ->brandLogoHeight('3rem')
            ->sidebarWidth('25rem')
            ->maxContentWidth(MaxWidth::Full)
            ->discoverResources(in: app_path('Filament/User/Resources'), for: 'App\\Filament\\User\\Resources')
            ->discoverPages(in: app_path('Filament/User/Pages'), for: 'App\\Filament\\User\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->resources([
                UserResource::class,
            ])
            ->discoverWidgets(in: app_path('Filament/User/Widgets'), for: 'App\\Filament\\User\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->databaseNotifications()
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                SetTheme::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugin(ThemesPlugin::make())
            ->sidebarCollapsibleOnDesktop()
            ->renderHook(
                'panels::footer',
                fn () => view('custom-footer')
            )
            ->userMenuItems([
                'profile' => MenuItem::make()
                    ->label('Chỉnh sửa thông tin')
                    ->url(fn () => UserResource::getUrl('edit', ['record' => filament()->auth()->user()?->id])),
                'admin' => MenuItem::make()
                    ->label('Trang quản trị')
                    ->url('/admin')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->visible(fn () => in_array('admin', filament()->auth()->user()?->roles->pluck('ma_vai_tro')->toArray() ?? [])),
                'logout' => MenuItem::make()
                    ->label('Đăng xuất')
                    ->url('/logout')
                    ->icon('heroicon-o-arrow-left-on-rectangle'),
            ])
            ->assets([
                Css::make('custom-styles', resource_path('css/custom.css')),
            ]);
    }
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        FilamentColor::register([
            'amber' => '#F59E0B', // Amber (yellow-orange) for pending actions
            'calm-blue' => '#3B82F6', // Calm blue for checking
            'indigo' => '#6366F1', // Indigo for reviewing
            'lime' => '#84CC16', // Bright lime green for initial scoring
            'emerald' => '#10B981', // Rich emerald green for secondary scoring
            'green' => '#22C55E', // Vibrant green for approved items
            'red' => '#EF4444', // Bold red for rejected or unknown states
        ]);
    }
}
