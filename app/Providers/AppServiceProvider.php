<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Disable mass assignment protection since Filament validations are used.
        Model::unguard();

        // Register Filament navigation groups in a specific order.
        Filament::registerNavigationGroups([
            NavigationGroup::make('Care Actions'),
            NavigationGroup::make('Care Lists'),
            NavigationGroup::make('Care Management'),
            NavigationGroup::make('System'),
        ]);
    }
}
