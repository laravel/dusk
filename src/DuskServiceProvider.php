<?php

namespace Laravel\Dusk;

use Exception;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class DuskServiceProvider extends ServiceProvider
{

    /**
     * Environments prohibited to register dusk
     *
     * @var array
     */
    protected $blacklist = ['production'];

    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
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

    /**
     * Register any package services.
     *
     * @return void
     * @throws Exception
     */
    public function register()
    {
        if ($this->app->environment($this->blacklist)) {
            throw new Exception('It is unsafe to run Dusk in production.');
        }

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
