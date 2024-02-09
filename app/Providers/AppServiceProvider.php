<?php

namespace App\Providers;

use App\Models\PersonalAccessToken;
use Filament\Facades\Filament;
use Filament\Forms\Components\Field;
use Filament\Navigation\NavigationGroup;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength( 191 );

        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);

        Filament::serving(function () {
            Filament::registerNavigationGroups([
                NavigationGroup::make()
                    ->label('Administracion')
                    ->icon('heroicon-s-pencil'),
                NavigationGroup::make()
                    ->label('Orquestador API')
                    ->icon('heroicon-s-cog'),
                NavigationGroup::make()
                    ->label('Integracion Roma')
                    ->icon('heroicon-s-cog')
                    ->collapsed(),
            ]);
        });

        Field::macro("hintHelp", function(string $tooltip) {
            return $this->hintAction(
                Action::make('help')
                    ->icon('heroicon-o-question-mark-circle')
                    ->extraAttributes(["class" => "text-gray-500"])
                    ->label("")
                    ->tooltip($tooltip)
            );
        });
    }
}
