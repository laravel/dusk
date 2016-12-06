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
        if (! is_dir(base_path('tests/Browser/Pages'))) {
            mkdir(base_path('tests/Browser/Pages'), 0755, true);
        }

        if (! is_dir(base_path('tests/Browser/screenshots'))) {
            mkdir(base_path('tests/Browser/screenshots'), 0755, true);
        }

        copy(__DIR__.'/../../stubs/ExampleTest.php', base_path('tests/Browser/ExampleTest.php'));
        copy(__DIR__.'/../../stubs/HomePage.php', base_path('tests/Browser/Pages/HomePage.php'));
        copy(__DIR__.'/../../stubs/DuskTestCase.php', base_path('tests/DuskTestCase.php'));
        copy(__DIR__.'/../../stubs/Page.php', base_path('tests/Browser/Pages/Page.php'));

        file_put_contents(base_path('tests/Browser/screenshots/.gitignore'), '*
!.gitignore
');

        $this->info('Dusk scaffolding installed successfully.');
    }
}
