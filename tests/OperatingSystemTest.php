<?php

namespace Laravel\Dusk\Tests;

use Laravel\Dusk\OperatingSystem;
use PHPUnit\Framework\TestCase;

class OperatingSystemTest extends TestCase
{
    public function test_it_matches_possible_os()
    {
        $this->assertTrue(\in_array(OperatingSystem::id(), OperatingSystem::all()));
    }

    public function test_it_has_correct_os()
    {
        $this->assertSame([
            'linux',
            'mac',
            'mac-intel',
            'mac-arm',
            'win',
        ], OperatingSystem::all());
    }

    public function test_it_can_resolve_chrome_version_commands()
    {
        foreach (OperatingSystem::all() as $os) {
            $commands = OperatingSystem::chromeVersionCommands($os);

            $this->assertTrue(is_array($commands), 'Commands should be an array');
            $this->assertFalse(empty($commands), 'Commands should not be empty');
        }
    }

    public function test_it_cant_resolve_invalid_chrome_version_commands()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Unable to find commands for Operating System [window_os]');

        OperatingSystem::chromeVersionCommands('window_os');
    }

    /**
     * @dataProvider resolveChromeDriverSlugDataProvider
     */
    public function test_it_can_resolve_chromedriver_slug($version, $os, $expected)
    {
        $this->assertSame($expected, OperatingSystem::chromeDriverSlug($os, $version));
    }

    public function test_it_cant_resolve_invalid_chromedriver_slug()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Unable to find ChromeDriver slug for Operating System [window_os]');

        OperatingSystem::chromeDriverSlug('window_os');
    }

    public static function resolveChromeDriverSlugDataProvider()
    {
        yield ['115.0', 'linux', 'linux64'];
        yield ['113.0', 'linux', 'linux64'];
        yield ['105.0', 'linux', 'linux64'];

        yield ['115.0', 'mac', 'mac-x64'];
        yield ['113.0', 'mac', 'mac64'];
        yield ['105.0', 'mac', 'mac64'];

        yield ['115.0', 'mac-intel', 'mac-x64'];
        yield ['113.0', 'mac-intel', 'mac64'];
        yield ['105.0', 'mac-intel', 'mac64'];

        yield ['115.0', 'mac-arm', 'mac-arm64'];
        yield ['113.0', 'mac-arm', 'mac_arm64'];
        yield ['105.0', 'mac-arm', 'mac64_m1'];

        yield ['115.0', 'win', 'win32'];
        yield ['113.0', 'win', 'win32'];
        yield ['105.0', 'win', 'win32'];
    }
}
