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
            $this->assertStringContainsString('chromedriver-win32.exe', $exception->getMessage());
        }
    }

    public function test_build_process_for_darwin_intel()
    {
        try {
            (new ChromeProcessDarwinIntel)->toProcess();
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString('chromedriver-mac-intel', $exception->getMessage());
        }
    }

    public function test_build_process_for_darwin_arm()
    {
        try {
            (new ChromeProcessDarwinArm)->toProcess();
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString('chromedriver-mac-arm', $exception->getMessage());
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
        $this->expectExceptionMessage('Invalid path to Chromedriver [/not/a/valid/path]. Make sure to install the Chromedriver first by running the dusk:chrome-driver command.');

        (new ChromeProcess('/not/a/valid/path'))->toProcess();
    }
}

class ChromeProcessWindows extends ChromeProcess
{
    protected function onMac()
    {
        return false;
    }

    protected function onWindows()
    {
        return true;
    }

    protected function operatingSystemId()
    {
        return 'win';
    }
}

class ChromeProcessDarwinIntel extends ChromeProcess
{
    protected function onMac()
    {
        return true;
    }

    protected function onWindows()
    {
        return false;
    }

    protected function operatingSystemId()
    {
        return 'mac-intel';
    }
}

class ChromeProcessDarwinArm extends ChromeProcess
{
    protected function onMac()
    {
        return true;
    }

    protected function onWindows()
    {
        return false;
    }

    protected function operatingSystemId()
    {
        return 'mac-arm';
    }
}

class ChromeProcessLinux extends ChromeProcess
{
    protected function onArmMac()
    {
        return false;
    }

    protected function onIntelMac()
    {
        return false;
    }

    protected function onWindows()
    {
        return false;
    }

    protected function operatingSystemId()
    {
        return 'linux';
    }
}
