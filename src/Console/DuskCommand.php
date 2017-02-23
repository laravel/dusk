<?php

namespace Laravel\Dusk\Console;

use Dotenv\Dotenv;
use Illuminate\Console\Command;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

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
     * Whether the project has its own phpunit.dusk.xml file.
     *
     * @var boolean
     */
    protected $phpunitDuskXmlExists = false;

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

        $options = array_slice($_SERVER['argv'], 2);

        return $this->withDuskEnvironment(function () use ($options) {
            return (new ProcessBuilder())
                ->setTimeout(null)
                ->setPrefix($this->binary())
                ->setArguments($this->phpunitArguments($options))
                ->getProcess()
                ->setTty(PHP_OS !== 'WINNT')
                ->run(function ($type, $line) {
                    $this->output->write($line);
                });
        });
    }

    /**
     * Get the PHP binary to execute.
     *
     * @return string
     */
    protected function binary()
    {
        return PHP_OS === 'WINNT' ? base_path('vendor\bin\phpunit.bat') : 'vendor/bin/phpunit';
    }

    /**
     * Get the array of arguments for running PHPUnit.
     *
     * @return array
     */
    protected function phpunitArguments($options)
    {
        return array_merge(['-c', base_path('phpunit.dusk.xml')], $options);
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
     * @return mixed
     */
    protected function withDuskEnvironment($callback)
    {
        if (file_exists(base_path($this->duskFile()))) {
            if (file_exists(base_path('.env.backup'))) {
                $this->error('Cannot backup the current environment because there is already a .env.backup file in place!');
                $this->error('Please check the contents of .env and .env.backup, remove the latter one if redundant, and then start Dusk again.');
                $this->error('Aborting...');

                return 2; // non-zero exit code to indicate failure
            }

            $this->backupEnvironment();

            $this->refreshEnvironment();
        }

        $this->writeConfiguration();

        return tap($callback(), function () {
            $this->removeConfiguration();

            if (file_exists(base_path($this->duskFile()))) {
                $this->restoreEnvironment();
            }
        });
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
     * Refresh the current environment variables.
     *
     * @return void
     */
    protected function refreshEnvironment()
    {
        (new Dotenv(base_path()))->overload();
    }

    /**
     * Write the Dusk PHPUnit configuration.
     *
     * @return void
     */
    protected function writeConfiguration()
    {
        $file = base_path('phpunit.dusk.xml');
        if (file_exists($file)) {
            $this->phpunitDuskXmlExists = true;
        } else {
            copy(realpath(__DIR__.'/../../stubs/phpunit.xml'), $file);
        }
    }

    /**
     * Remove the Dusk PHPUnit configuration.
     *
     * @return void
     */
    protected function removeConfiguration()
    {
        if (! $this->phpunitDuskXmlExists) {
            unlink(base_path('phpunit.dusk.xml'));
        }
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
