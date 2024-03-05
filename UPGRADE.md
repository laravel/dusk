# Upgrade Guide

## Upgrading To 8.0 From 7.x

### Minimum Versions

The following required dependency versions have been updated:

- The minimum PHP version is now v8.1
- The minimum Laravel version is now v10.0
- The minimum PHPUnit version is now v10.0

### Removed `--pest` Option

The `--pest` option has been removed from the `dusk` Artisan command. If you have Pest installed, the `dusk` command will automatically use Pest instead of PHPUnit.

## Upgrading To 7.0 From 6.x

### Minimum Versions

The following required dependency versions have been updated:

- The minimum PHP version is now v8.0
- The minimum Laravel version is now v9.0
- The minimum PHPUnit version is now v9.0

### Removed Chrome Binaries

PR: https://github.com/laravel/dusk/pull/873

Going forward, Dusk will not ship with pre-installed Chrome binaries. Instead, you should install the required Chrome driver for your operating system using the following command:

```zsh
php artisan dusk:chrome-driver
```

## Upgrading To 5.0 From 4.x

### PHPUnit 8

Dusk now provides optional support for PHPUnit 8, which requires PHP >= 7.2 Please read through the entire list of changes in [the PHPUnit 8 release announcement](https://phpunit.de/announcements/phpunit-8.html). Using PHPUnit 8 will require Laravel 5.8, which will be released at the end of February 2019.

You may also continue using PHPUnit 7, which requires a minimum of PHP 7.1.

### Minimum Laravel version

Laravel 5.7 is now the minimum supported version of the framework and you should upgrade to continue using Dusk.

### `setUp` and `tearDown` changes

The `setUp` and `tearDown` methods now require the `void` return type. If you were overwriting these methods you should add it to the method signatures.
