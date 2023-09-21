<?php

namespace Laravel\Dusk\Tests\Feature;

use Laravel\Dusk\Chrome\SupportsChrome;
use Laravel\Dusk\DuskServiceProvider;
use Orchestra\Testbench\TestCase;

class SupportsChromeTest extends TestCase
{
    use SupportsChrome;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('dusk:chrome-driver');
    }

    public function test_it_can_run_chrome_process()
    {
        $process = static::buildChromeProcess();

        $process->start();

        // Wait for the process to start up, and output any issues
        sleep(2);

        $process->stop();

        $this->assertStringContainsString('Starting ChromeDriver', $process->getOutput());
        $this->assertSame('', $process->getErrorOutput());
    }

    public function getPackageProviders($app)
    {
        return [DuskServiceProvider::class];
    }
}
