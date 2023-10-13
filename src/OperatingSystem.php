<?php

namespace Laravel\Dusk;

use Illuminate\Support\Str;
use InvalidArgumentException;

class OperatingSystem
{
    /**
     * List of available operating system platforms.
     *
     * @var array<string, array{slug: string, commands: array<int, string>}>
     */
    protected static $platforms = [
        'linux' => [
            'slug' => 'linux64',
            'commands' => [
                '/usr/bin/google-chrome --version',
                '/usr/bin/chromium-browser --version',
                '/usr/bin/chromium --version',
                '/usr/bin/google-chrome-stable --version',
            ],
        ],
        'mac' => [
            'slug' => 'mac-x64',
            'commands' => [
                '/Applications/Google\ Chrome\ for\ Testing.app/Contents/MacOS/Google\ Chrome\ for\ Testing --version',
                '/Applications/Google\ Chrome.app/Contents/MacOS/Google\ Chrome --version',
            ],
        ],
        'mac-intel' => [
            'slug' => 'mac-x64',
            'commands' => [
                '/Applications/Google\ Chrome\ for\ Testing.app/Contents/MacOS/Google\ Chrome\ for\ Testing --version',
                '/Applications/Google\ Chrome.app/Contents/MacOS/Google\ Chrome --version',
            ],
        ],
        'mac-arm' => [
            'slug' => 'mac-arm64',
            'commands' => [
                '/Applications/Google\ Chrome\ for\ Testing.app/Contents/MacOS/Google\ Chrome\ for\ Testing --version',
                '/Applications/Google\ Chrome.app/Contents/MacOS/Google\ Chrome --version',
            ],
        ],
        'win' => [
            'slug' => 'win32',
            'commands' => [
                'reg query "HKEY_CURRENT_USER\Software\Google\Chrome\BLBeacon" /v version',
            ],
        ],
    ];

    /**
     * Resolve the Chrome version commands for the given operating system.
     *
     * @param  string  $operatingSystem
     * @return array<int, string>
     */
    public static function chromeVersionCommands($operatingSystem)
    {
        $commands = static::$platforms[$operatingSystem]['commands'] ?? null;

        if (is_null($commands)) {
            throw new InvalidArgumentException("Unable to find commands for Operating System [{$operatingSystem}]");
        }

        return $commands;
    }

    /**
     * Resolve the ChromeDriver slug for the given operating system.
     *
     * @param  string  $operatingSystem
     * @param  string|null  $version
     * @return string
     */
    public static function chromeDriverSlug($operatingSystem, $version = null)
    {
        $slug = static::$platforms[$operatingSystem]['slug'] ?? null;

        if (is_null($slug)) {
            throw new InvalidArgumentException("Unable to find ChromeDriver slug for Operating System [{$operatingSystem}]");
        }

        if (! is_null($version) && version_compare($version, '115.0', '<')) {
            if ($slug === 'mac-arm64') {
                return version_compare($version, '106.0.5249', '<') ? 'mac64_m1' : 'mac_arm64';
            } elseif ($slug === 'mac-x64') {
                return 'mac64';
            }
        }

        return $slug;
    }

    /**
     * Get all supported operating systems.
     *
     * @return array<int, string>
     */
    public static function all()
    {
        return array_keys(static::$platforms);
    }

    /**
     * Get the current operating system identifier.
     *
     * @return string
     */
    public static function id()
    {
        if (static::onWindows()) {
            return 'win';
        } elseif (static::onMac()) {
            return static::macArchitectureId();
        }

        return 'linux';
    }

    /**
     * Determine if the operating system is Windows or Windows Subsystem for Linux.
     *
     * @return bool
     */
    public static function onWindows()
    {
        return PHP_OS === 'WINNT' || Str::contains(php_uname(), 'Microsoft');
    }

    /**
     * Determine if the operating system is macOS.
     *
     * @return bool
     */
    public static function onMac()
    {
        return PHP_OS === 'Darwin';
    }

    /**
     * Get the current macOS platform architecture.
     *
     * @return string
     */
    public static function macArchitectureId()
    {
        switch (php_uname('m')) {
            case 'arm64':
                return 'mac-arm';
            case 'x86_64':
                return 'mac-intel';
            default:
                return 'mac';
        }
    }
}
