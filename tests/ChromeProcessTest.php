<?php

namespace Laravel\Dusk\Tests;

use Laravel\Dusk\Chrome\ChromeProcess;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\Process\Process;

class ChromeProcessTest extends TestCase
{
    public function test_build_process_with_custom_driver()
    {
        $driver = __DIR__;

        $process = (new ChromeProcess($driver))->toProcess();

        $this->assertInstanceOf(Process::class, $process);
        $this->assertStringContainsString("$driver", $process->getCommandLine());
    }

    public function test_build_process_for_windows()
    {
        try {
            (new ChromeProcessWindows)->toProcess();
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString('chromedriver-win.exe', $exception->getMessage());
        }
    }

    public function test_build_process_for_darwin()
    {
        try {
            (new ChromeProcessDarwin)->toProcess();
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString('chromedriver-mac', $exception->getMessage());
        }
    }

    public function test_build_process_for_linux()
    {
        try {
            (new ChromeProcessLinux)->toProcess();
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString('chromedriver-linux', $exception->getMessage());
        }
    }

    public function test_invalid_path()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Invalid path to Chromedriver [/not/a/valid/path]. Make sure to install the Chromedriver first by running the dusk:chrome-driver command.");

        (new ChromeProcess('/not/a/valid/path'))->toProcess();
    }
}

class ChromeProcessWindows extends ChromeProcess
{
    protected function onWindows()
    {
        return true;
    }
}

class ChromeProcessDarwin extends ChromeProcess
{
    protected function onMac()
    {
        return true;
    }

    protected function onWindows()
    {
        return false;
    }
}

class ChromeProcessLinux extends ChromeProcess
{
    protected function onMac()
    {
        return false;
    }

    protected function onWindows()
    {
        return false;
    }
}
