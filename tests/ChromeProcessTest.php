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
        $process = (new ChromeProcessWindows)->toProcess();

        $this->assertInstanceOf(Process::class, $process);
        $this->assertStringContainsString('chromedriver-win.exe', $process->getCommandLine());
    }

    public function test_build_process_for_darwin()
    {
        $process = (new ChromeProcessDarwin)->toProcess();

        $this->assertInstanceOf(Process::class, $process);
        $this->assertStringContainsString('chromedriver-mac', $process->getCommandLine());
    }

    public function test_build_process_for_linux()
    {
        $process = (new ChromeProcessLinux)->toProcess();

        $this->assertInstanceOf(Process::class, $process);
        $this->assertStringContainsString('chromedriver-linux', $process->getCommandLine());
    }

    public function test_invalid_path()
    {
        $this->expectException(RuntimeException::class);

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
