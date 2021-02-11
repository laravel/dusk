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
            return 'win';
        } elseif (static::onIntelMac()) {
            return 'mac-intel';
        } elseif (static::onArmMac()) {
            return 'mac-arm';
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
     * Determine if the operating system is macOS x86_64.
     *
     * @return bool
     */
    public static function onIntelMac()
    {
        return PHP_OS === 'Darwin' && php_uname('m') === 'x86_64';
    }

    /**
     * Determine if the operating system is macOS arm64.
     *
     * @return bool
     */
    public static function onArmMac()
    {
        return PHP_OS === 'Darwin' && php_uname('m') === 'arm64';
    }
}
