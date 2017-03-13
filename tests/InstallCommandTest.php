<?php

use Laravel\Dusk\Console\InstallCommand;

class InstallCommandTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        rename(base_path('tests'), base_path('tests-bkp'));
        Mockery::mock(Illuminate\Console\Command::class);
    }

    public function test_workflow()
    {
        $ic = Mockery::mock(InstallCommand::class)
            ->makePartial()
            ->shouldReceive('info')
            ->mock();
        $ic->handle();

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
    }

    public function tearDown()
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

