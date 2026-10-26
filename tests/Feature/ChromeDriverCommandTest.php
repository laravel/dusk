<?php

namespace Laravel\Dusk\Tests\Feature;

use Illuminate\Contracts\Console\Kernel;
use Laravel\Dusk\Console\ChromeDriverCommand;
use Orchestra\Testbench\TestCase;

class ChromeDriverCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        FakeChromeDriverCommand::$installed = [];

        $this->app[Kernel::class]->registerCommand(new FakeChromeDriverCommand);
    }

    public function test_all_includes_linux_arm_when_available()
    {
        $this->artisan('dusk:chrome-driver', ['version' => '153.0.7922.0', '--all' => true])->assertSuccessful();

        $this->assertContains('linux-arm', FakeChromeDriverCommand::$installed);
    }

    public function test_all_skips_linux_arm_before_it_is_available()
    {
        $this->artisan('dusk:chrome-driver', ['version' => '152.0.7880.0', '--all' => true])->assertSuccessful();

        $this->assertNotContains('linux-arm', FakeChromeDriverCommand::$installed);
        $this->assertContains('linux', FakeChromeDriverCommand::$installed);
    }
}

class FakeChromeDriverCommand extends ChromeDriverCommand
{
    public static $installed = [];

    protected function download($version, $os)
    {
        static::$installed[] = $os;

        return $os;
    }

    protected function extract($archive)
    {
        return $archive;
    }

    protected function rename($binary, $os)
    {
    }
}
