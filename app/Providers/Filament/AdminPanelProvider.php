<?php

namespace App\Providers\Filament;

use App\Filament\Pages\DischargePatient;
use App\Filament\Pages\RecordTreatedBy;
use App\Filament\Pages\TransferPatient;
use App\Filament\Resources\Admissions\AdmissionResource;
use Illuminate\Support\Facades\Auth;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Blue,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Care Actions')
                    ->collapsed(false),
                NavigationGroup::make()
                    ->label('Care Lists'),
            ])
            ->navigationItems([
                NavigationItem::make('New Admission')
                    ->group('Care Actions')
                    ->icon('heroicon-o-user-plus')
                    ->url(fn (): string => AdmissionResource::getUrl('create'))
                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.resources.admissions.create'))
                    ->visible(fn (): bool => Auth::user()?->can('patient.admit') ?? false)
                    ->sort(1),
                NavigationItem::make('Discharge Patient')
                    ->group('Care Actions')
                    ->icon('heroicon-o-user-minus')
                    ->url(fn (): string => DischargePatient::getUrl())
                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.pages.discharge'))
                    ->visible(fn (): bool => Auth::user()?->can('patient.discharge') ?? false)
                    ->sort(2),
                NavigationItem::make('Transfer Patient')
                    ->group('Care Actions')
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->url(fn (): string => TransferPatient::getUrl())
                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.pages.transfer'))
                    ->visible(fn (): bool => Auth::user()?->can('patient.transfer') ?? false)
                    ->sort(3),
                NavigationItem::make('Record Treated By')
                    ->group('Care Actions')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->url(fn (): string => RecordTreatedBy::getUrl())
                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.pages.treated-by'))
                    ->visible(fn (): bool => Auth::user()?->can('patient.record_treatment') ?? false)
                    ->sort(4),
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
            ]);
    }
}
