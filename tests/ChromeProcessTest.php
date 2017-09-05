<?php

use PHPUnit\Framework\TestCase;

class ChromeProcessTest extends TestCase
{
    public function test_build_process_with_custom_driver()
    {
        $driver = __DIR__;

        $process = (new \Laravel\Dusk\Chrome\ChromeProcess($driver))->build();

        $this->assertInstanceOf(Symfony\Component\Process\Process::class, $process);
        $this->assertEquals("'{$driver}'", $process->getCommandLine());
    }

    public function test_build_process_for_windows()
    {
        $process = (new ChromeProcessWindows)->build();

        $this->assertInstanceOf(Symfony\Component\Process\Process::class, $process);
        $this->assertContains('chromedriver-win.exe', $process->getCommandLine());
    }

    public function test_build_process_for_darwin()
    {
        $process = (new ChromeProcessDarwin)->build();

        $this->assertInstanceOf(Symfony\Component\Process\Process::class, $process);
        $this->assertContains('chromedriver-mac', $process->getCommandLine());
    }

    public function test_build_process_for_linux()
    {
        $process = (new ChromeProcessLinux)->build();

        $this->assertInstanceOf(Symfony\Component\Process\Process::class, $process);
        $this->assertContains('chromedriver-linux', $process->getCommandLine());
    }
}

class ChromeProcessWindows extends \Laravel\Dusk\Chrome\ChromeProcess
{
    protected function isWindows()
    {
        return true;
    }
}


class ChromeProcessDarwin extends \Laravel\Dusk\Chrome\ChromeProcess
{
    protected function isDarwin()
    {
        return true;
    }
}

class ChromeProcessLinux extends \Laravel\Dusk\Chrome\ChromeProcess
{
    protected function isDarwin()
    {
        return false;
    }
}