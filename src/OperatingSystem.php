<?php

namespace Laravel\Dusk;

use Illuminate\Support\Str;

class OperatingSystem
{
    /**
     * Returns the current OS identifier.
     *
     * @return string
     */
    public static function id()
    {
        if (static::onWindows()) {
            return static::windowsArchitectureId();
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
     * Windows platform architecture.
     *
     * @return string
     */
    public static function windowsArchitectureId()
    {
        switch (PHP_INT_SIZE) {
            case 8:
                return 'win64';
            default:
                return 'win32';
        }
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
     * Mac platform architecture.
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
