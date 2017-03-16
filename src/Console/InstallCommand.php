<?php

namespace Laravel\Dusk\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    /**
     * The driver downloader.
     *
     * @var DriverDownloader
     */
    protected $driverDownloader;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dusk:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Dusk into the application';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(DriverDownloader $driverDownloader)
    {
        $this->driverDownloader = $driverDownloader;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Downloading the driver..');
        $this->driverDownloader->run();
        $this->createTestDirectories();
        $this->copyStubs();
    }

    /**
     * Create test directories.
     *
     * @return  void
     */
    protected function createTestDirectories()
    {
        $dirs = [
            'Pages' => false,
            'screenshots' => true,
            'console' => true,
        ];

        foreach ($dirs as $dir => $has_dot_gitignore) {
            if (!is_dir(base_path("tests/Browser/{$dir}"))) {
                mkdir(base_path("tests/Browser/{$dir}"), 0755, true);

                if ($has_dot_gitignore) {
                    file_put_contents(base_path("tests/Browser/{$dir}/.gitignore"), '*'.PHP_EOL.'!.gitignore'.PHP_EOL);
                }
            }
        }
    }

    /**
     * Copy stubs to test directories.
     *
     * @return  void
     */
    protected function copyStubs()
    {
        $subs = [
            'ExampleTest.stub' => base_path('tests/Browser/ExampleTest.php'),
            'HomePage.stub' => base_path('tests/Browser/Pages/HomePage.php'),
            'DuskTestCase.stub' => base_path('tests/DuskTestCase.php'),
            'Page.stub' => base_path('tests/Browser/Pages/Page.php'),
        ];

        foreach ($subs as $stub => $file) {
            if (! is_file($file)) {
                copy(__DIR__.'/../../stubs/'.$stub, $file);
            }
        }

        $this->info('Dusk scaffolding installed successfully.');
    }
}
