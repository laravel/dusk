<?php

namespace Laravel\Dusk;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class DuskServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        Route::get('/_dusk/login/{userId}', [
            'middleware' => 'web',
            'uses' => 'Laravel\Dusk\Http\Controllers\LoginController@login'
        ]);

        $this->app->booted(function () {
            $this->makeLogoutAccessibleViaGet();
        });
    }

    /**
     * Make the "logout" named route accessible over the GET verb.
     *
     * @return void
     */
    protected function makeLogoutAccessibleViaGet()
    {
        Route::getRoutes()->refreshNameLookups();

        if ($route = Route::getRoutes()->getByName('logout')) {
            Route::get($route->uri, $route->action);
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\InstallCommand::class,
                Console\DuskCommand::class,
                Console\MakeCommand::class,
                Console\PageCommand::class,
            ]);
        }
    }
}
