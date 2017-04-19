<?php

use PHPUnit\Framework\TestCase;
use Laravel\Dusk\DuskServiceProvider;

class DuskServiceProviderTest extends TestCase {

    /**
     * @expectedException Exception
     */
    public function test_production_is_disallowed() {
        $app = Mockery::mock(StdClass::class);
        $app->shouldReceive('environment')->with(['production'])->andReturn(true);
        $dusk = new DuskServiceProvider($app);
        $dusk->register();
    }

    public function test_blacklist_override() {
        $app = Mockery::mock(StdClass::class);
        $app->shouldReceive('environment')->with([])->andReturn(false);
        $app->shouldReceive('runningInConsole')->andReturn(true);
        $dusk = new ExtendedDuskServiceProvider($app);
        $dusk->register();
    }

}

class ExtendedDuskServiceProvider extends DuskServiceProvider {
    protected $blacklist = [];

    public function commands($commands) {

    }
}