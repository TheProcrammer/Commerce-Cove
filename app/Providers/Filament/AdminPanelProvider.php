<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Enums\RolesEnum;
use illuminate\Database\Eloquent\Model;


class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        //In this section you can customize your filament admin panel. Go to the docs for more info.
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
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
                // auth serves as a middleware for the authentication for the declared 
                //roles which is Admin and Vendor..
                'auth',
                // Declaring the format of the roles. And declaring access for admins.
                sprintf('role:%s|%s', 
                RolesEnum::Admin->value, 
                RolesEnum::Vendor->value),
            ])

            // Commented out this middleware as it is the login form for filament and what I wanted to use
            // is the one that I created above. I wanted to have only one login form.
            // admin/login
            // ->authMiddleware([
            //     Authenticate::class,
            // ])
            ;
    }

    // Declaring this function to be able to apply all the models declared on the database. As Laravel
    // has a default protection against unguarded assignment.
    public function boot()
    {
        Model::unguard();
    }
}
