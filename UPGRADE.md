# Upgrade Guide

## Upgrading To 5.0 From 4.0

### PHPUnit 8

Dusk now provides support for PHPUnit 8. If you wish to make use of PHPUnit 8 the minimum requirement is PHP 7.2. Please read through the entire list of changes in [the PHPUnit 8 release announcement](https://phpunit.de/announcements/phpunit-8.html).

You may also choose to continue to use PHPUnit 7 which requires a minimum of PHP 7.1.

### `setUp` and `tearDown` changes

The `setUp` and `tearDown` methods now require the `void` return type. If you were overwriting these methods you should add it to the method signatures.
