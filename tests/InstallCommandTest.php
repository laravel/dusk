<?php

use Laravel\Dusk\Console\InstallCommand;

class InstallCommandTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Mockery::mock(Illuminate\Console\Command::class);

        $this->ic = Mockery::mock(InstallCommand::class)
            ->makePartial()
            ->shouldReceive('info')
            ->mock();
    }

    public function test_createTestDirectories_copyStubs()
    {
        $this->backupTestDir();

        $this->invokeMethod('createTestDirectories');
        $this->invokeMethod('copyStubs');

        $common_dir = base_path('tests/Browser');
        $should_exists = [
            'Pages',
            'screenshots', 
            'console',

            'ExampleTest.php',
            'Pages/HomePage.php',
            'Pages/Page.php',
        ];

        foreach ($should_exists as $file) {
            $this->assertFileExists($common_dir.DIRECTORY_SEPARATOR.$file);
        }

        // and the hunging fruit
        $this->assertFileExists(base_path('tests/DuskTestCase.php'));

        $this->restoreTestDir();
    }

    /**
     * Invoke inaccessible methods.
     *
     * @param  string    $method
     * @return mixed
     */
    private function invokeMethod($method, ...$args)
    {
        $m = new ReflectionMethod(InstallCommand::class, $method);
        $m->setAccessible(true);
        return $m->invokeArgs($this->ic, $args);
    }

    /**
     * Backup test directory.
     */
    private function backupTestDir()
    {
        rename(base_path('tests'), base_path('tests-bkp'));
    }

    /**
     * Restore test directory.
     */
    private function restoreTestDir()
    {
        $this->rmdir(base_path('tests'));
        rename(base_path('tests-bkp'), base_path('tests'));
    }

    /**
     * Recursivly delete generated tests directory and it's content.
     *
     * @param  string  $path
     */
    private function rmdir($path)
    {
        if (strpos($path, base_path('tests')) !== 0) {
            throw new \Exception('Be Carfule: you may delete your host!');
        }

        $files = array_diff(scandir($path), ['.','..']); 

        foreach ($files as $file) { 
            $file = $path . DIRECTORY_SEPARATOR . $file;

            if (is_dir($file)) {
                $this->rmdir($file);
            } else {
                unlink($file);
            }
        } 

        rmdir($path); 
    }
}
