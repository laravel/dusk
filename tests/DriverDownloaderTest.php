<?php

use Laravel\Dusk\Console\DriverDownloader;

class DriverDownloaderTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->driverDownloader = new DriverDownloader;
    } 

    /**
     * Invoke inaccessible methods.
     *
     * @param  string    $method
     * @return mixed
     */
    private function invokeMethod($method, ...$args)
    {
        $m = new ReflectionMethod(DriverDownloader::class, $method);
        $m->setAccessible(true);
        return $m->invokeArgs($this->driverDownloader, $args);
    }

    public function test_unzip_setPermissions()
    {
        $file = $this->invokeMethod('getChromeDriverFileToDownload');
        $filepath = $this->invokeMethod('downloadToBinFolder', $file);
        $new_filepath = $this->invokeMethod('unzip', $filepath);
        $this->invokeMethod('setPermissions', $new_filepath);

        $this->assertFileNotExists($filepath);
        $this->assertFileExists($new_filepath);
        $this->assertEquals('0755', substr(sprintf('%o', fileperms($new_filepath)), -4));
        unlink($new_filepath);
    }

    public function test_downloadToBinFolder()
    {
        $file = $this->invokeMethod('getChromeDriverFileToDownload');
        $filepath = $this->invokeMethod('downloadToBinFolder', $file);
        
        $this->assertFileExists($filepath);
        $this->assertTrue(filesize($filepath) > 0);     // at least 1 byte :)
        unlink($filepath);
    }

    public function test_getDownloadableChromeDriverFile()
    {
        $file = $this->invokeMethod(
            'getDownloadableChromeDriverFile', 
            'https://chromedriver.storage.googleapis.com/2.28/chromedriver_mac32.zip'
        );

        $this->assertEquals($file, 'https://chromedriver.storage.googleapis.com/2.28/chromedriver_mac64.zip');

        try {
            $this->invokeMethod(
                'getDownloadableChromeDriverFile', 
                'https://chromedriver.storage.googleapis.com/2.28/chromedriver_macFAKE.zip'
            );

            $this->assertTrue(false);
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
    }

    public function test_getChromeDriverFileToDownload()
    {
        $file = $this->invokeMethod('getChromeDriverFileToDownload');
        $this->assertRegExp(
            '/https:\/\/chromedriver.storage.googleapis.com\/\d\.\d\d\/chromedriver_(linux|mac|win)(32|64).zip/',
            $file
        );
    }

    public function test_getArchitect()
    {
        $architect = $this->invokeMethod('getArchitect');
        $this->assertRegExp('/(linux|mac|win)(32|64)/', $architect);
    } 
}
