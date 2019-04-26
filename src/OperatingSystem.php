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
        return static::onWindows() ? 'win' : (static::onMac() ? 'mac' : 'linux');
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
}
