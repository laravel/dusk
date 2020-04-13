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
        if (! $this->app->environment('production')) {
            Route::get('/_dusk/login/{userId}/{guard?}', [
                'middleware' => 'web',
                'uses' => 'Laravel\Dusk\Http\Controllers\UserController@login',
            ]);

            Route::get('/_dusk/logout/{guard?}', [
                'middleware' => 'web',
                'uses' => 'Laravel\Dusk\Http\Controllers\UserController@logout',
            ]);

            Route::get('/_dusk/user/{guard?}', [
                'middleware' => 'web',
                'uses' => 'Laravel\Dusk\Http\Controllers\UserController@user',
            ]);
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     *
     * @throws \Exception
     */
    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\InstallCommand::class,
                Console\DuskCommand::class,
                Console\DuskFailsCommand::class,
                Console\MakeCommand::class,
                Console\PageCommand::class,
                Console\ComponentCommand::class,
                Console\ChromeDriverCommand::class,
            ]);
        }
    }
}
