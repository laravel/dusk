<?php

namespace Laravel\Dusk\Console;

use Dotenv\Dotenv;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use PHPUnit\Runner\Version;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Exception\ProcessSignaledException;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\Process;

class DuskCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dusk
                {--browse : Open a browser instead of using headless mode}
                {--without-tty : Disable output to TTY}
                {--pest : Run the tests using Pest}';

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

        $this->purgeSourceLogs();

        $options = collect($_SERVER['argv'])
            ->slice(2)
            ->diff(['--browse', '--without-tty'])
            ->values()
            ->all();

        return $this->withDuskEnvironment(function () use ($options) {
            $process = (new Process(array_merge(
                $this->binary(), $this->phpunitArguments($options)
            ), null, $this->env()))->setTimeout(null);

            try {
                $process->setTty(! $this->option('without-tty'));
            } catch (RuntimeException $e) {
                $this->output->writeln('Warning: '.$e->getMessage());
            }

            try {
                return $process->run(function ($type, $line) {
                    $this->output->write($line);
                });
            } catch (ProcessSignaledException $e) {
                if (extension_loaded('pcntl') && $e->getSignal() !== SIGINT) {
                    throw $e;
                }
            }
        });
    }

    /**
     * Get the PHP binary to execute.
     *
     * @return array
     */
    protected function binary()
    {
        $binaryPath = 'vendor/phpunit/phpunit/phpunit';

        if ($this->option('pest')) {
            $binaryPath = 'vendor/pestphp/pest/bin/pest';
        }

        if ('phpdbg' === PHP_SAPI) {
            return [PHP_BINARY, '-qrr', $binaryPath];
        }

        return [PHP_BINARY, $binaryPath];
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
            return ! Str::startsWith($option, ['--env=', '--pest']);
        }));

        if (! file_exists($file = base_path('phpunit.dusk.xml'))) {
            $file = base_path('phpunit.dusk.xml.dist');
        }

        return array_merge(['-c', $file], $options);
    }

    /**
     * Get the PHP binary environment variables.
     *
     * @return array|null
     */
    protected function env()
    {
        if ($this->option('browse') && ! isset($_ENV['CI']) && ! isset($_SERVER['CI'])) {
            return ['DUSK_HEADLESS_DISABLED' => true];
        }
    }

    /**
     * Purge the failure screenshots.
     *
     * @return void
     */
    protected function purgeScreenshots()
    {
        $this->purgeDebuggingFiles(
            base_path('tests/Browser/screenshots'), 'failure-*'
        );
    }

    /**
     * Purge the console logs.
     *
     * @return void
     */
    protected function purgeConsoleLogs()
    {
        $this->purgeDebuggingFiles(
            base_path('tests/Browser/console'), '*.log'
        );
    }

    /**
     * Purge the source logs.
     *
     * @return void
     */
    protected function purgeSourceLogs()
    {
        $this->purgeDebuggingFiles(
            base_path('tests/Browser/source'), '*.txt'
        );
    }

    /**
     * Purge debugging files based on path and patterns.
     *
     * @param  string  $path
     * @param  string  $patterns
     * @return void
     */
    protected function purgeDebuggingFiles($path, $patterns)
    {
        if (! is_dir($path)) {
            return;
        }

        $files = Finder::create()->files()
                       ->in($path)
                       ->name($patterns);

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
        $this->setupDuskEnvironment();

        try {
            return $callback();
        } finally {
            $this->teardownDuskEnviroment();
        }
    }

    /**
     * Setup the Dusk environment.
     *
     * @return void
     */
    protected function setupDuskEnvironment()
    {
        if (file_exists(base_path($this->duskFile()))) {
            if (file_exists(base_path('.env')) &&
                file_get_contents(base_path('.env')) !== file_get_contents(base_path($this->duskFile()))) {
                $this->backupEnvironment();
            }

            $this->refreshEnvironment();
        }

        $this->writeConfiguration();

        $this->setupSignalHandler();
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
     * Refresh the current environment variables.
     *
     * @return void
     */
    protected function refreshEnvironment()
    {
        // BC fix to support Dotenv ^2.2...
        if (! method_exists(Dotenv::class, 'create')) {
            (new Dotenv(base_path()))->overload(); // @phpstan-ignore-line

            return;
        }

        // BC fix to support Dotenv ^3.0...
        if (! method_exists(Dotenv::class, 'createMutable')) {
            Dotenv::create(base_path())->overload();

            return;
        }

        Dotenv::createMutable(base_path())->load();
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
            if (version_compare(Version::id(), '10.0', '>=')) {
                copy(realpath(__DIR__.'/../../stubs/phpunit.xml'), $file);
            } else {
                copy(realpath(__DIR__.'/../../stubs/phpunit9.xml'), $file);
            }

            return;
        }

        $this->hasPhpUnitConfiguration = true;
    }

    /**
     * Setup the SIGINT signal handler for CTRL+C exits.
     *
     * @return void
     */
    protected function setupSignalHandler()
    {
        if (extension_loaded('pcntl')) {
            pcntl_async_signals(true);

            pcntl_signal(SIGINT, function () {
                $this->teardownDuskEnviroment();
            });
        }
    }

    /**
     * Restore the original environment.
     *
     * @return void
     */
    protected function teardownDuskEnviroment()
    {
        $this->removeConfiguration();

        if (file_exists(base_path($this->duskFile())) && file_exists(base_path('.env.backup'))) {
            $this->restoreEnvironment();
        }
    }

    /**
     * Remove the Dusk PHPUnit configuration.
     *
     * @return void
     */
    protected function removeConfiguration()
    {
        if (! $this->hasPhpUnitConfiguration && file_exists($file = base_path('phpunit.dusk.xml'))) {
            unlink($file);
        }
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
