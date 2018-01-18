<?php

use PHPUnit\Framework\TestCase;

class ChromeProcessTest extends TestCase
{
    public function test_build_process_with_custom_driver()
    {
        $driver = __DIR__;

        $process = (new \Laravel\Dusk\Chrome\ChromeProcess($driver))->toProcess();

        $this->assertInstanceOf(Symfony\Component\Process\Process::class, $process);
        $this->assertContains("$driver", $process->getCommandLine());
    }

    public function test_build_process_for_windows()
    {
        $process = (new ChromeProcessWindows)->toProcess();

        $this->assertInstanceOf(Symfony\Component\Process\Process::class, $process);
        $this->assertContains('chromedriver-win.exe', $process->getCommandLine());
    }

    public function test_build_process_for_darwin()
    {
        $process = (new ChromeProcessDarwin)->toProcess();

        $this->assertInstanceOf(Symfony\Component\Process\Process::class, $process);
        $this->assertContains('chromedriver-mac', $process->getCommandLine());
    }

    public function test_build_process_for_linux()
    {
        $process = (new ChromeProcessLinux)->toProcess();

        $this->assertInstanceOf(Symfony\Component\Process\Process::class, $process);
        $this->assertContains('chromedriver-linux', $process->getCommandLine());
    }

    public function test_invalid_path()
    {
        $this->expectException(RuntimeException::class);

        (new \Laravel\Dusk\Chrome\ChromeProcess('/not/a/valid/path'))->toProcess();
    }
}

class ChromeProcessWindows extends \Laravel\Dusk\Chrome\ChromeProcess
{
    protected function onWindows()
    {
        return true;
    }
}


class ChromeProcessDarwin extends \Laravel\Dusk\Chrome\ChromeProcess
{
    protected function onMac()
    {
        return true;
    }
}

class ChromeProcessLinux extends \Laravel\Dusk\Chrome\ChromeProcess
{
    protected function onMac()
    {
        return false;
    }
}
