<?php

namespace Laravel\Dusk\Console;

use Dotenv\Dotenv;
use Illuminate\Console\Command;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Process\Exception\RuntimeException;

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
     * Indicates if the project has its own PHPUnit configuration.
     *
     * @var boolean
     */
    protected $hasPhpUnitConfiguration = false;

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

        $this->purgeConsoleLogs();

        $options = array_slice($_SERVER['argv'], 2);

        return $this->withDuskEnvironment(function () use ($options) {
            $process = (new ProcessBuilder())
                ->setTimeout(null)
                ->setPrefix($this->binary())
                ->setArguments($this->phpunitArguments($options))
                ->getProcess();

            try {
                $process->setTty(true);
            } catch (RuntimeException $e) {
                $this->output->writeln('Warning: '.$e->getMessage());
            }

            return $process->run(function ($type, $line) {
                $this->output->write($line);
            });
        });
    }

    /**
     * Get the PHP binary to execute.
     *
     * @return array
     */
    protected function binary()
    {
        return [PHP_BINARY, 'vendor/phpunit/phpunit/phpunit'];
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
     * Purge the console logs.
     *
     * @return void
     */
    protected function purgeConsoleLogs()
    {
        $files = Finder::create()->files()
            ->in(base_path('tests/Browser/console'))
            ->name('*.log');

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
            if (file_get_contents(base_path('.env')) !== file_get_contents(base_path($this->duskFile()))) {
                $this->backupEnvironment();
            }

            $this->refreshEnvironment();
        }

        $this->writeConfiguration();

        return tap($callback(), function () {
            $this->removeConfiguration();

            if (file_exists(base_path($this->duskFile())) && file_exists(base_path('.env.backup'))) {
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
        if (! file_exists($file = base_path('phpunit.dusk.xml'))) {
            copy(realpath(__DIR__.'/../../stubs/phpunit.xml'), $file);
        } else {
            $this->hasPhpUnitConfiguration = true;
        }
    }

    /**
     * Remove the Dusk PHPUnit configuration.
     *
     * @return void
     */
    protected function removeConfiguration()
    {
        if (! $this->hasPhpUnitConfiguration) {
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
