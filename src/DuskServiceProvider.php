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
                Console\DuskCommand::class,
                Console\InstallCommand::class,
            ]);
        }
    }
}
