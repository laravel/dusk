<?php

namespace Innobird\Dusky\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dusky:install
                {--proxy= : The proxy to download the binary through (example: "tcp://127.0.0.1:9000")}
                {--ssl-no-verify : Bypass SSL certificate verification when installing through a proxy}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Dusk into the application';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (!is_dir(base_path('app/Browser/Pages'))) {
            mkdir(base_path('app/Browser/Pages'), 0755, true);
        }

        if (!is_dir(base_path('app/Browser/Components'))) {
            mkdir(base_path('app/Browser/Components'), 0755, true);
        }

        if (!is_dir(base_path('app/Browser/screenshots'))) {
            $this->createScreenshotsDirectory();
        }

        if (!is_dir(base_path('app/Browser/console'))) {
            $this->createConsoleDirectory();
        }

        $stubs = [
            'HomePage.stub' => base_path('app/Browser/Pages/HomePage.php'),
            'Page.stub' => base_path('app/Browser/Pages/Page.php'),
        ];

        foreach ($stubs as $stub => $file) {
            if (!is_file($file)) {
                copy(__DIR__ . '/../../stubs/' . $stub, $file);
            }
        }

        $this->info('Dusk scaffolding installed successfully.');

        $this->comment('Downloading ChromeDriver binaries...');

        $driverCommandArgs = ['--all' => true];

        if ($this->option('proxy')) {
            $driverCommandArgs['--proxy'] = $this->option('proxy');
        }

        if ($this->option('ssl-no-verify')) {
            $driverCommandArgs['--ssl-no-verify'] = true;
        }

        $this->call('dusky:chrome-driver', $driverCommandArgs);
    }

    /**
     * Create the screenshots directory.
     *
     * @return void
     */
    protected function createScreenshotsDirectory()
    {
        mkdir(base_path('tests/Browser/screenshots'), 0755, true);

        file_put_contents(base_path('tests/Browser/screenshots/.gitignore'), '*
!.gitignore
');
    }

    /**
     * Create the console directory.
     *
     * @return void
     */
    protected function createConsoleDirectory()
    {
        mkdir(base_path('tests/Browser/console'), 0755, true);

        file_put_contents(base_path('tests/Browser/console/.gitignore'), '*
!.gitignore
');
    }
}
