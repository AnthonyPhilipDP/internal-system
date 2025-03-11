<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use App\Filament\Auth\Login;
use Filament\Navigation\MenuItem;
use Filament\Support\Colors\Color;
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
use Joaopaulolndev\FilamentEditProfile\Pages\EditProfilePage;
use Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin;


class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->renderHook(
                'panels::body.end',
                fn (): string => Blade::render('@livewire(\'username-prompt\')')
            )
            // ->brandName('Basic')
            ->brandLogo(asset('images/Site Logo.png'))
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
            ->darkMode(false)
            ->globalSearch(true)
            ->globalSearchKeyBindings(['ctrl+h', 'ctrl+h'])
            ->globalSearchFieldKeyBindingSuffix()
            ->breadcrumbs(false)
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
                Widgets\AccountWidget::class,
                //Widgets\FilamentInfoWidget::class,
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
                FilamentEditProfilePlugin::make()
                    ->shouldShowBrowserSessionsForm(false)
                    ->setTitle('My Profile')
                    ->setNavigationLabel('My Profile')
                    ->setNavigationGroup('Settings')
                    ->setIcon('heroicon-o-user')
                    ->setSort(10)
                    ->shouldRegisterNavigation(false)
                    ->shouldShowDeleteAccountForm(false)
                    ->shouldShowAvatarForm(
                        value: true,
                        directory: 'avatars',
                        rules: 'mimes:jpeg,png|max:1024'
                    )
            ])
            ->userMenuItems([
                MenuItem::make()
                    ->label('Settings')
                    ->url(fn (): string => EditProfilePage::getUrl())
                    ->icon('heroicon-m-cog-6-tooth'),
            ]);

    }
}
