<?php

namespace Laravel\Dusk\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dusk:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Dusk into the application';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->downloadChromeDriver();
        $this->createTestDirectories();
        $this->copyStubs();
    }

    /**
     * Download chromedriver binary.
     *
     * @return void
     */
    protected function downloadChromeDriver()
    {
        $this->info('Downloading chrome driver..');
        $file = $this->getChromeDriverFileToDownload();
        $filepath = $this->downloadToBinFolder($file);
        $filepath = $this->unzip($filepath);
        $this->setPermissions($filepath); 
    }

    /**
     * Set the right permissions for the downloaded file.
     *
     * @param  string   $filepath
     * @return void
     */
    private function setPermissions($filepath)
    {
        if (PHP_OS != 'WINNT') {
            chmod($filepath, 0755);
        }

        return $filepath;
    }

    /**
     * Unzip the given archive.
     *
     * @param  string    $filepath
     * @throws \Exception
     * @return string
     */
    private function unzip($filepath)
    {
        $fileinfo = pathinfo($filepath);
        $zip = new \ZipArchive;

        if ($zip->open($filepath)) {
            $zip->extractTo($fileinfo['dirname']);
            $zip->close();
            unlink($filepath);
            $filename = preg_replace('/(32|64)/', '', $fileinfo['filename']);
            $filepath = $fileinfo['dirname'].DIRECTORY_SEPARATOR.str_replace('_', '-', $filename);
            rename($fileinfo['dirname'].DIRECTORY_SEPARATOR.'chromedriver', $filepath);

            return $filepath;
        }

        throw new \Exception("Could not extract {$filepath}");
    }

    /**
     * Download file to bin folder.
     *
     * @param  string    $file
     * @return string
     */
    private function downloadToBinFolder($file)
    {
        $pathinfo = pathinfo($file);
        $filename = $pathinfo['basename'];
        $filepath = realpath(__DIR__ . "/../../bin").DIRECTORY_SEPARATOR.$filename;

        file_put_contents($filepath, fopen($file, 'r'));

        return $filepath;
    }

    /**
     * Get chrome driver file to download.
     *
     * @return string
     */
    private function getChromeDriverFileToDownload()
    {
        $latest_release = trim(file_get_contents('https://chromedriver.storage.googleapis.com/LATEST_RELEASE'));
        $file = "https://chromedriver.storage.googleapis.com/{$latest_release}/chromedriver_%s.zip";
        $architect = $this->getArchitect();
        $file = sprintf($file, $architect);

        $this->getDownloadableChromeDriverFile($file);
        return $file;
    }

    /**
     * @TODO: maybe it should die and throw exception if chrome driver file was not found.
     *
     * Get downloadable chrome driver file.
     *
     * @param string    $file
     * @param bool      $rec
     *
     * @return string
     */
    private function getDownloadableChromeDriverFile($file, $rec = true)
    {
        $headers = @get_headers($file);

        // Chrome driver not found for current system:
        if (strpos($headers[0], '200 OK') === false) {
            if (strpos($file, '32.zip') !== false) {
                $file = str_replace('32.zip', '64.zip', $file);
            } else {
                $file = str_replace('64.zip', '32.zip', $file);
            }

            if ($rec) {
                return $this->getDownloadableChromeDriverFile($file, false);
            } else {
                throw new \Exception("chromedriver binaries not found: {$file} for your system: " . $this->getArchitect());
            }
        }

        return $file;
    }

    /**
     * Get system architecture.
     *
     * @return  string
     */
    private function getArchitect()
    {
        $bit = PHP_INT_SIZE * 8;

        if (PHP_OS == 'Darwin') {
            return "mac{$bit}";
        }

        if (PHP_OS == 'WINNT') {
            return "win{$bit}";
        }

        return "linux{$bit}";
    }

    /**
     * Create test directories.
     *
     * @return  void
     */
    protected function createTestDirectories()
    {
        $dirs = [
            'Pages' => false,
            'screenshots' => true,
            'console' => true,
        ];

        foreach ($dirs as $dir => $has_dot_gitignore) {
            if (!is_dir(base_path("tests/Browser/{$dir}"))) {
                mkdir(base_path("tests/Browser/{$dir}"), 0755, true);

                if ($has_dot_gitignore) {
                    file_put_contents(base_path("tests/Browser/{$dir}/.gitignore"), '*'.PHP_EOL.'!.gitignore'.PHP_EOL);
                }
            }
        }
    }

    /**
     * Copy stubs to test directories.
     *
     * @return  void
     */
    protected function copyStubs()
    {
        $subs = [
            'ExampleTest.stub' => base_path('tests/Browser/ExampleTest.php'),
            'HomePage.stub' => base_path('tests/Browser/Pages/HomePage.php'),
            'DuskTestCase.stub' => base_path('tests/DuskTestCase.php'),
            'Page.stub' => base_path('tests/Browser/Pages/Page.php'),
        ];

        foreach ($subs as $stub => $file) {
            if (! is_file($file)) {
                copy(__DIR__.'/../../stubs/'.$stub, $file);
            }
        }

        $this->info('Dusk scaffolding installed successfully.');
    }
}
