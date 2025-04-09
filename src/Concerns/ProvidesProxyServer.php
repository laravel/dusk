<?php

namespace Laravel\Dusk\Concerns;

use Illuminate\Contracts\Routing\UrlGenerator as UrlGeneratorContract;
use Illuminate\Routing\UrlGenerator as BaseUrlGenerator;
use Laravel\Dusk\Http\ProxyServer;
use Laravel\Dusk\Http\UrlGenerator;
use PHPUnit\Framework\Attributes\Before;

trait ProvidesProxyServer
{
    #[Before]
    public function setUpProvidesProxyServer(): void
    {
        $this->afterApplicationCreated(function () {
            $this->app->make(ProxyServer::class)->listen();
            $this->app->instance('url', $this->app->make(UrlGenerator::class));
            $this->app->instance(UrlGeneratorContract::class, $this->app->make(UrlGenerator::class));
            $this->app->instance(BaseUrlGenerator::class, $this->app->make(UrlGenerator::class));
        });

        $this->beforeApplicationDestroyed(function () {
            $this->app->make(ProxyServer::class)->flush();
        });
    }
}
