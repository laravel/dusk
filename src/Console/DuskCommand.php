<?php

namespace Laravel\Dusk\Console;

use Dotenv\Dotenv;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\RuntimeException;

class DuskCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dusk {--without-tty : Disable output to TTY}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the Dusk tests for the application';

    /**
     * Indicates if the project has its own PHPUnit configuration.
     *
     * @var bool
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

        $options = array_slice($_SERVER['argv'], $this->option('without-tty') ? 3 : 2);

        return $this->withDuskEnvironment(function () use ($options) {
            $process = (new Process(array_merge(
                $this->binary(), $this->phpunitArguments($options)
            )))->setTimeout(null);

            try {
                $process->setTty(! $this->option('without-tty'));
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
        if ('phpdbg' === \PHP_SAPI) {
            return [PHP_BINARY, '-qrr', 'vendor/phpunit/phpunit/phpunit'];
        }

        return [PHP_BINARY, 'vendor/phpunit/phpunit/phpunit'];
    }

    /**
     * Get the array of arguments for running PHPUnit.
     *
     * @param  array  $options
     * @return array
     */
    protected function phpunitArguments($options)
    {
        $options = array_values(array_filter($options, function ($option) {
            return ! Str::startsWith($option, '--env=');
        }));

        if (! file_exists($file = base_path('phpunit.dusk.xml'))) {
            $file = base_path('phpunit.dusk.xml.dist');
        }

        return array_merge(['-c', $file], $options);
    }

    /**
     * Purge the failure screenshots.
     *
     * @return void
     */
    protected function purgeScreenshots()
    {
        $path = base_path('tests/Browser/screenshots');

        if (! is_dir($path)) {
            return;
        }

        $files = Finder::create()->files()
                        ->in($path)
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
        $path = base_path('tests/Browser/console');

        if (! is_dir($path)) {
            return;
        }

        $files = Finder::create()->files()
            ->in($path)
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
        // BC fix to support Dotenv ^2.2
        if (! method_exists(Dotenv::class, 'create')) {
            (new Dotenv(base_path()))->overload();

            return;
        }

        Dotenv::create(base_path())->overload();
    }

    /**
     * Write the Dusk PHPUnit configuration.
     *
     * @return void
     */
    protected function writeConfiguration()
    {
        if (! file_exists($file = base_path('phpunit.dusk.xml')) &&
            ! file_exists(base_path('phpunit.dusk.xml.dist'))) {
            copy(realpath(__DIR__.'/../../stubs/phpunit.xml'), $file);

            return;
        }

        $this->hasPhpUnitConfiguration = true;
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
