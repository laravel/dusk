<?php

namespace Laravel\Dusk\Console;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Utils;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Laravel\Dusk\OperatingSystem;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Process\Process;
use ZipArchive;

/**
 * @copyright Originally created by Jonas Staudenmeir: https://github.com/staudenmeir/dusk-updater
 */
#[AsCommand(name: 'dusk:chrome-driver')]
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
     * The legacy versions for ChromeDriver.
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
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $version = $this->version();

        $all = $this->option('all');

        $currentOS = OperatingSystem::id();

        foreach (OperatingSystem::all() as $os) {
            if ($all || ($os === $currentOS)) {
                $archive = $this->download($version, $os);

                $binary = $this->extract($version, $archive);

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
        } elseif ($version < 115) {
            return $this->fetchChromeVersionFromUrl($version);
        }

        $milestones = $this->resolveChromeVersionsPerMilestone();

        return $milestones['milestones'][$version]['version']
            ?? throw new Exception('Could not determine the ChromeDriver version.');
    }

    /**
     * Get the latest stable ChromeDriver version.
     *
     * @return string
     */
    protected function latestVersion()
    {
        $versions = json_decode($this->getUrl('https://googlechromelabs.github.io/chrome-for-testing/last-known-good-versions-with-downloads.json'), true);

        return $versions['channels']['Stable']['version']
            ?? throw new Exception('Could not get the latest ChromeDriver version.');
    }

    /**
     * Detect the installed Chrome / Chromium major version.
     *
     * @param  string  $os
     * @return int|bool
     */
    protected function detectChromeVersion($os)
    {
        foreach (OperatingSystem::chromeVersionCommands($os) as $command) {
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
     * @param  string  $os
     * @return string
     */
    protected function download($version, $os)
    {
        $url = $this->resolveChromeDriverDownloadUrl($version, $os);

        $resource = Utils::tryFopen($archive = $this->directory.'chromedriver.zip', 'w');

        $client = new Client();

        $response = $client->get($url, array_merge([
            'sink' => $resource,
            'verify' => $this->option('ssl-no-verify') === false,
        ], array_filter([
            'proxy' => $this->option('proxy'),
        ])));

        if ($response->getStatusCode() < 200 || $response->getStatusCode() > 299) {
            throw new Exception("Unable to download ChromeDriver from [{$url}].");
        }

        return $archive;
    }

    /**
     * Extract the ChromeDriver binary from the archive and delete the archive.
     *
     * @param  string  $version
     * @param  string  $archive
     * @return string
     */
    protected function extract($version, $archive)
    {
        $zip = new ZipArchive;

        $zip->open($archive);

        $zip->extractTo($this->directory);

        $binary = $zip->getNameIndex(version_compare($version, '115.0', '<') ? 0 : 1);

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
        $binary = str_replace(DIRECTORY_SEPARATOR, '/', $binary);

        $newName = Str::contains($binary, '/')
            ? Str::after(str_replace('chromedriver', 'chromedriver-'.$os, $binary), '/')
            : str_replace('chromedriver', 'chromedriver-'.$os, $binary);

        rename($this->directory.$binary, $this->directory.$newName);

        chmod($this->directory.$newName, 0755);
    }

    /**
     * Get the Chrome version from URL.
     *
     * @return string
     */
    protected function fetchChromeVersionFromUrl(int $version)
    {
        return trim((string) $this->getUrl(
            sprintf('https://chromedriver.storage.googleapis.com/LATEST_RELEASE_%d', $version)
        ));
    }

    /**
     * Get the Chrome versions per milestone.
     *
     * @return array
     */
    protected function resolveChromeVersionsPerMilestone()
    {
        return json_decode(
            $this->getUrl('https://googlechromelabs.github.io/chrome-for-testing/latest-versions-per-milestone-with-downloads.json'), true
        );
    }

    /**
     * Resolve the download URL.
     *
     * @return string
     *
     * @throws \Exception
     */
    protected function resolveChromeDriverDownloadUrl(string $version, string $os)
    {
        $slug = OperatingSystem::chromeDriverSlug($os, $version);

        if (version_compare($version, '115.0', '<')) {
            return sprintf('https://chromedriver.storage.googleapis.com/%s/chromedriver_%s.zip', $version, $slug);
        }

        $milestone = (int) $version;

        $versions = $this->resolveChromeVersionsPerMilestone();

        /** @var array<string, mixed> $chromedrivers */
        $chromedrivers = $versions['milestones'][$milestone]['downloads']['chromedriver']
            ?? throw new Exception('Could not get the ChromeDriver version.');

        return collect($chromedrivers)->firstWhere('platform', $slug)['url']
            ?? throw new Exception('Could not get the ChromeDriver version.');
    }

    /**
     * Get the contents of a URL using the 'proxy' and 'ssl-no-verify' command options.
     *
     * @return string
     *
     * @throws Exception
     */
    protected function getUrl(string $url)
    {
        $client = new Client();

        $response = $client->get($url, array_merge([
            'verify' => $this->option('ssl-no-verify') === false,
        ], array_filter([
            'proxy' => $this->option('proxy'),
        ])));

        if ($response->getStatusCode() < 200 || $response->getStatusCode() > 299) {
            throw new Exception("Unable to fetch contents from [{$url}].");
        }

        return (string) $response->getBody();
    }
}
