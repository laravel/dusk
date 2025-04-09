<?php

namespace Laravel\Dusk;

use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Contracts\Routing\UrlGenerator as UrlGeneratorContract;
use Illuminate\Foundation\Application;
use Illuminate\Routing\RouteCollectionInterface;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Dusk\Http\ProxyServer;
use Laravel\Dusk\Http\UrlGenerator;
use React\EventLoop\Loop;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;

class DuskServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(ProxyServer::class, function($app) {
            return new ProxyServer(
                kernel: $app->make(HttpKernel::class),
                loop: Loop::get(),
                factory: $app->make(HttpFoundationFactory::class),
                host: config('dusk.proxy.host', '127.0.0.1'),
                port: config('dusk.proxy.port', $this->findOpenPort(...)),
            );
        });

        $this->app->singleton(UrlGenerator::class, function (Application $app) {
            $proxy = $app->make(ProxyServer::class);

            return new UrlGenerator(
                proxyHostname: $proxy->host,
                proxyPort: $proxy->port,
                appHost: parse_url(config('app.url'), PHP_URL_HOST),
                url: $app->make(UrlGeneratorContract::class),
            );
        });
    }

    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        if (! $this->app->environment('production')) {
            Route::group(array_filter([
                'prefix' => config('dusk.path', '_dusk'),
                'domain' => config('dusk.domain', null),
                'middleware' => config('dusk.middleware', 'web'),
            ]), function () {
                Route::get('/login/{userId}/{guard?}', [
                    'uses' => 'Laravel\Dusk\Http\Controllers\UserController@login',
                    'as' => 'dusk.login',
                ]);

                Route::get('/logout/{guard?}', [
                    'uses' => 'Laravel\Dusk\Http\Controllers\UserController@logout',
                    'as' => 'dusk.logout',
                ]);

                Route::get('/user/{guard?}', [
                    'uses' => 'Laravel\Dusk\Http\Controllers\UserController@user',
                    'as' => 'dusk.user',
                ]);
            });
        }

        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\InstallCommand::class,
                Console\DuskCommand::class,
                Console\DuskFailsCommand::class,
                Console\MakeCommand::class,
                Console\PageCommand::class,
                Console\PurgeCommand::class,
                Console\ComponentCommand::class,
                Console\ChromeDriverCommand::class,
            ]);
        }
    }

    /**
     * Find an available port to listen on.
     *
     * @return int
     */
    protected function findOpenPort(): int
    {
        $sock = socket_create_listen(0);
        socket_getsockname($sock, $addr, $port);
        socket_close($sock);

        return $port;
    }
}
