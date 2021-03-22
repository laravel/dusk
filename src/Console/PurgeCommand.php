<?php

namespace Laravel\Dusk\Console;

use Dotenv\Dotenv;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Exception\ProcessSignaledException;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\Process;

class PurgeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dusk:purge';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Purge dusk test debugging files';

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
    }

    /**
     * Purge the failure screenshots.
     *
     * @return void
     */
    protected function purgeScreenshots()
    {
        $this->purgeDebuggingFiles(
            'tests/Browser/screenshots', 'failure-*'
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
           'tests/Browser/console', '*.log'
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
            'tests/Browser/source', '*.txt'
        );
    }

    /**
     * Purge debugging files based on path and patterns.
     *
     * @param  string  $relativePath
     * @param  string  $patterns
     * @return void
     */
    protected function purgeDebuggingFiles($relativePath, $patterns)
    {
        $path = base_path($relativePath);

        if (! is_dir($path)) {
            $this->warn(
                "Ignored purging none existing [{$relativePath}] path.", OutputInterface::VERBOSITY_DEBUG
            );

            return;
        }

        $files = Finder::create()->files()
                       ->in($path)
                       ->name($patterns);

        foreach ($files as $file) {
            @unlink($file->getRealPath());
        }

        $this->info("Purged \"{$patterns}\" from [{$relativePath}] path.");
    }
}
