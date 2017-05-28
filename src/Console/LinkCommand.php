<?php

namespace Laravel\Dusk\Console;

use Illuminate\Console\Command;

class LinkCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'dusk:link';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a symbolic link from "tests/Browser/screenshots" to "storage/app/public/screenshots"';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        if (file_exists(storage_path('app/public/screenshots'))) {
            return $this->error('The "app/public/screenshots" directory already exists.');
        }

        $this->laravel->make('files')->link(
            base_path('tests/Browser/screenshots'), storage_path('app/public/screenshots')
        );

        $this->info('The [app/public/screenshots] directory has been linked.');
    }
}
