<?php

namespace Laravel\Dusk\Tests;

use Laravel\Dusk\Chrome\SupportsChrome;
use PHPUnit\Framework\TestCase;

class SupportsChromeTest extends TestCase
{
    use SupportsChrome;

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
}
