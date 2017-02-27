<?php

namespace Laravel\Dusk\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
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
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (! is_dir($this->generateTestsPath('Browser/Pages'))) {
            mkdir($this->generateTestsPath('Browser/Pages'), 0755, true);
        }

        if (! is_dir($this->generateTestsPath('Browser/screenshots'))) {
            mkdir($this->generateTestsPath('Browser/screenshots'), 0755, true);
        }

        copy(__DIR__.'/../../stubs/ExampleTest.stub', $this->generateTestsPath('Browser/ExampleTest.php'));
        copy(__DIR__.'/../../stubs/HomePage.stub', $this->generateTestsPath('Browser/Pages/HomePage.php'));
        copy(__DIR__.'/../../stubs/DuskTestCase.stub', $this->generateTestsPath('DuskTestCase.php'));
        copy(__DIR__.'/../../stubs/Page.stub', $this->generateTestsPath('Browser/Pages/Page.php'));

        file_put_contents($this->generateTestsPath('Browser/screenshots/.gitignore'), '*
!.gitignore
');

        $this->info('Dusk scaffolding installed successfully.');
    }

    protected function generateTestsPath($path)
    {
        return (config('dusk.tests_path') ?: 'tests').DIRECTORY_SEPARATOR.$path;
    }
}
