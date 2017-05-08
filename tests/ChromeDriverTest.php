<?php

use RuntimeException;
use PHPUnit\Framework\TestCase;
use Laravel\Dusk\SupportsChrome;

class ChromeDriverTest extends TestCase
{
    public function test_default_file()
    {
        switch (PHP_OS) {
            case 'Darwin':
                $driverSuffix = 'mac';
                break;
            case 'WINNT':
                $driverSuffix = 'win.exe';
                break;
            default:
                $driverSuffix = 'linux';
        }

        $driverFile = SupportsChromeTestClass::driverFile();

        $this->assertEquals('chromedriver-'.$driverSuffix, $driverFile);
    }

    public function test_custom_path()
    {
        $customPath = '/path/to/the/driver/directory';

        SupportsChromeTestClass::useDriverPath($customPath);

        $this->assertEquals($customPath, SupportsChromeTestClass::driverPath());
    }

    public function test_custom_file()
    {
        $customFile = 'custom-driver-file';

        SupportsChromeTestClass::useDriverFile($customFile);

        $this->assertEquals($customFile, SupportsChromeTestClass::driverFile());
    }

    public function test_invalid_path()
    {
        $customFile = 'custom-driver-file';

        SupportsChromeTestClass::useDriverFile($customFile);

        $this->expectException(RuntimeException::class);

        SupportsChromeTestClass::buildChromeProcess();
    }
}


class SupportsChromeTestClass
{
    use SupportsChrome {
        buildChromeProcess as public;
    }
}
