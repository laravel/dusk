<?php

namespace Laravel\Dusk\Console;

use Illuminate\Console\Command;
use Laravel\Dusk\OperatingSystem;
use Symfony\Component\Process\Process;
use ZipArchive;

/**
 * @copyright Originally created by Jonas Staudenmeir: https://github.com/staudenmeir/dusk-updater
 */
class ChromeDriverCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dusk:chrome-driver {version?}
                    {--all : Install a ChromeDriver binary for every OS}
                    {--detect : Detect the installed Chrome / Chromium version}
                    {--proxy= : The proxy to download the binary through (example: "tcp://127.0.0.1:9000")}
                    {--ssl-no-verify : Bypass SSL certificate verification when installing through a proxy}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the ChromeDriver binary';

    /**
     * URL to the latest stable release version.
     *
     * @var string
     */
    protected $latestVersionUrl = 'https://chromedriver.storage.googleapis.com/LATEST_RELEASE';

    /**
     * URL to the latest release version for a major Chrome version.
     *
     * @var string
     */
    protected $versionUrl = 'https://chromedriver.storage.googleapis.com/LATEST_RELEASE_%d';

    /**
     * URL to the ChromeDriver download.
     *
     * @var string
     */
    protected $downloadUrl = 'https://chromedriver.storage.googleapis.com/%s/chromedriver_%s.zip';

    /**
     * Download slugs for the available operating systems.
     *
     * @var array
     */
    protected $slugs = [
        'linux' => 'linux64',
        'mac' => 'mac64',
        'mac-intel' => 'mac64',
        'mac-arm' => 'mac_arm64',
        'win' => 'win32',
    ];

    /**
     * The legacy versions for the ChromeDriver.
     *
     * @var array
     */
    protected $legacyVersions = [
        43 => '2.20',
        44 => '2.20',
        45 => '2.20',
        46 => '2.21',
        47 => '2.21',
        48 => '2.21',
        49 => '2.22',
        50 => '2.22',
        51 => '2.23',
        52 => '2.24',
        53 => '2.26',
        54 => '2.27',
        55 => '2.28',
        56 => '2.29',
        57 => '2.29',
        58 => '2.31',
        59 => '2.32',
        60 => '2.33',
        61 => '2.34',
        62 => '2.35',
        63 => '2.36',
        64 => '2.37',
        65 => '2.38',
        66 => '2.40',
        67 => '2.41',
        68 => '2.42',
        69 => '2.44',
    ];

    /**
     * Path to the bin directory.
     *
     * @var string
     */
    protected $directory = __DIR__.'/../../bin/';

    /**
     * The default commands to detect the installed Chrome / Chromium version.
     *
     * @var array
     */
    protected $chromeVersionCommands = [
        'linux' => [
            '/usr/bin/google-chrome --version',
            '/usr/bin/chromium-browser --version',
            '/usr/bin/chromium --version',
            '/usr/bin/google-chrome-stable --version',
        ],
        'mac-intel' => [
            '/Applications/Google\ Chrome.app/Contents/MacOS/Google\ Chrome --version',
        ],
        'mac-arm' => [
            '/Applications/Google\ Chrome.app/Contents/MacOS/Google\ Chrome --version',
        ],
        'win' => [
            'reg query "HKEY_CURRENT_USER\Software\Google\Chrome\BLBeacon" /v version',
        ],
    ];

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $version = $this->version();

        $all = $this->option('all');

        $currentOS = OperatingSystem::id();

        foreach ($this->slugs as $os => $slug) {
            if ($all || ($os === $currentOS)) {
                $archive = $this->download($version, $slug);

                $binary = $this->extract($archive);

                $this->rename($binary, $os);
            }
        }

        $message = 'ChromeDriver %s successfully installed for version %s.';

        $this->info(sprintf($message, $all ? 'binaries' : 'binary', $version));
    }

    /**
     * Get the desired ChromeDriver version.
     *
     * @return string
     */
    protected function version()
    {
        $version = $this->argument('version');

        if ($this->option('detect')) {
            $version = $this->detectChromeVersion(OperatingSystem::id());
        }

        if (! $version) {
            return $this->latestVersion();
        }

        if (! ctype_digit($version)) {
            return $version;
        }

        $version = (int) $version;

        if ($version < 70) {
            return $this->legacyVersions[$version];
        }

        return trim($this->getUrl(
            sprintf($this->versionUrl, $version)
        ));
    }

    /**
     * Get the latest stable ChromeDriver version.
     *
     * @return string
     */
    protected function latestVersion()
    {
        $streamOptions = [];

        if ($this->option('ssl-no-verify')) {
            $streamOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ],
            ];
        }

        if ($this->option('proxy')) {
            $streamOptions['http'] = ['proxy' => $this->option('proxy'), 'request_fulluri' => true];
        }

        return trim(file_get_contents($this->latestVersionUrl, false, stream_context_create($streamOptions)));
    }

    /**
     * Detect the installed Chrome / Chromium major version.
     *
     * @param  string  $os
     * @return int|bool
     */
    protected function detectChromeVersion($os)
    {
        foreach ($this->chromeVersionCommands[$os] as $command) {
            $process = Process::fromShellCommandline($command);

            $process->run();

            preg_match('/(\d+)(\.\d+){3}/', $process->getOutput(), $matches);

            if (! isset($matches[1])) {
                continue;
            }

            return $matches[1];
        }

        $this->error('Chrome version could not be detected.');

        return false;
    }

    /**
     * Download the ChromeDriver archive.
     *
     * @param  string  $version
     * @param  string  $slug
     * @return string
     */
    protected function download($version, $slug)
    {
        $url = sprintf($this->downloadUrl, $version, $slug);

        file_put_contents(
            $archive = $this->directory.'chromedriver.zip',
            $this->getUrl($url)
        );

        return $archive;
    }

    /**
     * Extract the ChromeDriver binary from the archive and delete the archive.
     *
     * @param  string  $archive
     * @return string
     */
    protected function extract($archive)
    {
        $zip = new ZipArchive;

        $zip->open($archive);

        $zip->extractTo($this->directory);

        $binary = $zip->getNameIndex(0);

        $zip->close();

        unlink($archive);

        return $binary;
    }

    /**
     * Rename the ChromeDriver binary and make it executable.
     *
     * @param  string  $binary
     * @param  string  $os
     * @return void
     */
    protected function rename($binary, $os)
    {
        $newName = str_replace('chromedriver', 'chromedriver-'.$os, $binary);

        rename($this->directory.$binary, $this->directory.$newName);

        chmod($this->directory.$newName, 0755);
    }

    /**
     * Get the contents of a URL using the 'proxy' and 'ssl-no-verify' command options.
     *
     * @param  string  $url
     * @return string|bool
     */
    protected function getUrl(string $url)
    {
        $contextOptions = [];

        if ($this->option('proxy')) {
            $contextOptions['http'] = ['proxy' => $this->option('proxy'), 'request_fulluri' => true];
        }

        if ($this->option('ssl-no-verify')) {
            $contextOptions['ssl'] = ['verify_peer' => false];
        }

        $streamContext = stream_context_create($contextOptions);

        return file_get_contents($url, false, $streamContext);
    }
}
