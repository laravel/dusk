# Upgrade Guide

## Upgrading To 5.0 From 4.0

### PHPUnit 8

Dusk now provides optional support for PHPUnit 8, which requires PHP >= 7.2 Please read through the entire list of changes in [the PHPUnit 8 release announcement](https://phpunit.de/announcements/phpunit-8.html). Using PHPUnit 8 will require Laravel 5.8, which will be released at the end of February 2019.

You may also continue using PHPUnit 7, which requires a minimum of PHP 7.1.

### Minimum Laravel version

Laravel 5.7 is now the minimum supported version of the framework and you should upgrade to continue using Dusk.

### `setUp` and `tearDown` changes

The `setUp` and `tearDown` methods now require the `void` return type. If you were overwriting these methods you should add it to the method signatures.
