<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use App\Filament\Auth\Login;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Blade;
use Filament\Navigation\NavigationItem;
use Filament\Navigation\NavigationGroup;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;


class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->maxContentWidth(MaxWidth::Full)
            ->renderHook(
                'panels::body.end',
                fn (): string => Blade::render('@livewire(\'username-prompt\')')
            )
            // ->brandName('Basic')
            // ->font('Quicksand')
            ->font('Space Grotesk')
            ->brandLogo(asset('images/Site Logo.png'))
            ->darkModeBrandLogo(asset('images/Site Logo - Dark Mode.png'))
            ->favicon(asset('images/PMSi Logo(transparent).png'))
            ->sidebarCollapsibleOnDesktop()
            //->sidebarFullyCollapsibleOnDesktop()
            ->spa(true)
            ->topNavigation(false)
            //->unsavedChangesAlerts()
            ->id('admin')
            ->path('admin')
            ->login(Login::class) 
            // ->registration()
            ->darkMode(true)
            ->globalSearch(true)
            ->globalSearchKeyBindings(['ctrl+h', 'ctrl+h'])
            ->globalSearchFieldKeyBindingSuffix()
            ->breadcrumbs(true)
            ->colors([
                'primary' => Color::Emerald,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
            ])
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
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Administration')
                    ->icon('heroicon-m-wrench-screwdriver'),
                NavigationGroup::make()
                    ->label('PMSi')
                    ->icon('heroicon-m-building-office'),
                NavigationGroup::make()
                    ->label('Social Media')
                    ->icon('heroicon-m-rectangle-group'),
                NavigationGroup::make()
                    ->label('Settings')
                    ->icon('heroicon-m-cog-6-tooth'),
            ])
            ->navigationItems([
                NavigationItem::make('Facebook')
                    ->url('https://facebook.com', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->group('Social Media'),
                NavigationItem::make('TikTok')
                    ->url('https://tiktok.com', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->group('Social Media'),
                NavigationItem::make('Instagram')
                    ->url('https://instagram.com', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->group('Social Media'),
                NavigationItem::make('Twitter / X')
                    ->url('https://x.com', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->group('Social Media'),
            ])
            ->plugins([
                //
            ])
            ->userMenuItems([
                //
            ]);

    }
}
