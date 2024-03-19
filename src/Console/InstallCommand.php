<?php

namespace Laravel\Dusk\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'dusk:install')]
class InstallCommand extends Command
{
    use Concerns\InteractsWithTestingFrameworks;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dusk:install
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
        if (! is_dir(base_path('tests/Browser/Pages'))) {
            mkdir(base_path('tests/Browser/Pages'), 0755, true);
        }

        if (! is_dir(base_path('tests/Browser/Components'))) {
            mkdir(base_path('tests/Browser/Components'), 0755, true);
        }

        if (! is_dir(base_path('tests/Browser/screenshots'))) {
            $this->createScreenshotsDirectory();
        }

        if (! is_dir(base_path('tests/Browser/console'))) {
            $this->createConsoleDirectory();
        }

        if (! is_dir(base_path('tests/Browser/source'))) {
            $this->createSourceDirectory();
        }

        $stubs = [
            'HomePage.stub' => base_path('tests/Browser/Pages/HomePage.php'),
            'DuskTestCase.stub' => base_path('tests/DuskTestCase.php'),
            'Page.stub' => base_path('tests/Browser/Pages/Page.php'),
        ];

        if ($this->usingPest()) {
            $stubs['ExampleTest.pest.stub'] = base_path('tests/Browser/ExampleTest.php');

            $contents = file_get_contents(base_path('tests/Pest.php'));

            $contents = str_replace('<?php', <<<EOT
            <?php

            uses(
                Tests\DuskTestCase::class,
                // Illuminate\Foundation\Testing\DatabaseMigrations::class,
            )->in('Browser');
            EOT, $contents);

            file_put_contents(base_path('tests/Pest.php'), $contents);
        } else {
            $stubs['ExampleTest.stub'] = base_path('tests/Browser/ExampleTest.php');
        }

        foreach ($stubs as $stub => $file) {
            if (! is_file($file)) {
                copy(__DIR__.'/../../stubs/'.$stub, $file);
            }
        }

        $baseTestCase = file_get_contents(base_path('tests/DuskTestCase.php'));

        if (! trait_exists(\Tests\CreatesApplication::class)) {
            file_put_contents(base_path('tests/DuskTestCase.php'), str_replace(<<<'EOT'
                {
                    use CreatesApplication;

                EOT, <<<'EOT'
                {
                EOT,
                $baseTestCase,
            ));
        }

        $this->info('Dusk scaffolding installed successfully.');

        $this->comment('Downloading ChromeDriver binaries...');

        $driverCommandArgs = [];

        if ($this->option('proxy')) {
            $driverCommandArgs['--proxy'] = $this->option('proxy');
        }

        if ($this->option('ssl-no-verify')) {
            $driverCommandArgs['--ssl-no-verify'] = true;
        }

        $this->call('dusk:chrome-driver', $driverCommandArgs);
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

    /**
     * Create the source directory.
     *
     * @return void
     */
    protected function createSourceDirectory()
    {
        mkdir(base_path('tests/Browser/source'), 0755, true);

        file_put_contents(base_path('tests/Browser/source/.gitignore'), '*
!.gitignore
');
    }
}
