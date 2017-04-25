<?php

namespace Laravel\Dusk\Console;

use Laravel\Dusk\Console\DuskCommand as BaseCommand;
use Symfony\Component\Process\ProcessBuilder;

class ServeCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dusk:serve';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Serve the application and run Dusk tests';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->withDuskEnvironment(function () {
            $exec = PHP_OS != 'WINNT' ? ['exec'] : [];
            $arguments = array_merge($exec, [PHP_BINARY, 'artisan', 'serve']);

            $serve = (new ProcessBuilder($arguments))
                ->setTimeout(null)
                ->getProcess();

            $serve->start();

            return tap(parent::runPhpunit(), function () use ($serve) {
                $serve->stop();
            });
        });
    }
}