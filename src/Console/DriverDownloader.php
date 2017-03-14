<?php

namespace Laravel\Dusk\Console;

class DriverDownloader
{
    /**
     * Run driver downloader.
     *
     * @return void
     */
    public function run()
    {
        $file = $this->getChromeDriverFileToDownload();
        $filepath = $this->downloadToBinFolder($file);
        $filepath = $this->unzip($filepath);
        $this->setPermissions($filepath); 
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
}
