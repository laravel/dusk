<?php

namespace Laravel\Dusk\Console;

use Illuminate\Console\Command;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;

class DuskCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dusk';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the Dusk tests for the application';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->ignoreValidationErrors();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->purgeScreenshots();

        $options = implode(' ', array_slice($_SERVER['argv'], 2));

        $this->withDuskEnvironment(function () use ($options) {
            (new Process(trim('php vendor/bin/phpunit -c "'.base_path('phpunit.dusk.xml').'" '.$options), base_path(), []))
                    ->setTty(true)
                    ->run(function ($type, $line) {
                        $this->output->write($line);
                    });
        });
    }

    /**
     * Purge the failure screenshots
     *
     * @return void
     */
    protected function purgeScreenshots()
    {
        $files = Finder::create()->files()
                        ->in(base_path('tests/Browser/screenshots'))
                        ->name('failure-*');

        foreach ($files as $file) {
            @unlink($file->getRealPath());
        }
    }

    /**
     * Run the given callback with the Dusk configuration files.
     *
     * @param  \Closure  $callback
     * @return void
     */
    protected function withDuskEnvironment($callback)
    {
        if (file_exists(base_path($this->duskFile()))) {
            $this->backupEnvironment();
        }

        $this->writeConfiguration();

        $callback();

        $this->removeConfiguration();

        if (file_exists(base_path($this->duskFile()))) {
            $this->restoreEnvironment();
        }
    }

    /**
     * Backup the current environment file.
     *
     * @return void
     */
    protected function backupEnvironment()
    {
        copy(base_path('.env'), base_path('.env.backup'));

        copy(base_path($this->duskFile()), base_path('.env'));
    }

    /**
     * Restore the backed-up environment file.
     *
     * @return void
     */
    protected function restoreEnvironment()
    {
        copy(base_path('.env.backup'), base_path('.env'));

        unlink(base_path('.env.backup'));
    }

    /**
     * Write the Dusk PHPUnit configuration.
     *
     * @return void
     */
    protected function writeConfiguration()
    {
        copy(realpath(__DIR__.'/../../stubs/phpunit.xml'), base_path('phpunit.dusk.xml'));
    }

    /**
     * Remove the Dusk PHPUnit configuration.
     *
     * @return void
     */
    protected function removeConfiguration()
    {
        unlink(base_path('phpunit.dusk.xml'));
    }

    /**
     * Get the name of the Dusk file for the environment.
     *
     * @return string
     */
    protected function duskFile()
    {
        if (file_exists(base_path($file = '.env.dusk.'.$this->laravel->environment()))) {
            return $file;
        }
        
        return '.env.dusk';
    }
}
