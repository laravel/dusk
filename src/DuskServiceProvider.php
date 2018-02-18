<?php

namespace Laravel\Dusk;

use Exception;
use Illuminate\Support\Facades\Route;
use Laravel\Dusk\Faking\FakingManager;
use Illuminate\Support\ServiceProvider;
use Laravel\Dusk\Http\Middleware\SaveFaking;
use Laravel\Dusk\Http\Middleware\StartFaking;
use Laravel\Dusk\Http\Controllers\FakingController;
use Illuminate\Contracts\Http\Kernel as HttpKernel;

class DuskServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->bootAuthRoutes();
        $this->bootFakingRoutes();
    }

    /**
     * Register any package services.
     *
     * @return void
     * @throws Exception
     */
    public function register()
    {
        if ($this->app->environment('production')) {
            throw new Exception('It is unsafe to run Dusk in production.');
        }

        $this->app->singleton('faking', function ($app) {
            return new FakingManager($app);
        });

        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\InstallCommand::class,
                Console\DuskCommand::class,
                Console\MakeCommand::class,
                Console\PageCommand::class,
                Console\ComponentCommand::class,
            ]);
        } else {
            $kernel = $this->app->make(HttpKernel::class);
            $kernel->pushMiddleware(StartFaking::class);
            $kernel->pushMiddleware(SaveFaking::class);
        }

    }

    /**
     * Boot auth routes.
     *
     * @return void
     */
    protected function bootAuthRoutes()
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
     * Boot faking routes.
     *
     * @return void
     */
    protected function bootFakingRoutes()
    {
        Route::get('/_dusk/fake/{facade}', [
            'middleware' => 'web',
            'uses' => 'Laravel\Dusk\Http\Controllers\FakingController@fake',
        ]);

        Route::get('/_dusk/get-fake/{facade}', [
            'middleware' => 'web',
            'uses' => 'Laravel\Dusk\Http\Controllers\FakingController@getFake',
        ]);
    }
}
