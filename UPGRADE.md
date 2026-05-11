# Upgrade Guide

## Upgrading To 9.0 From 8.x

jQuery has been [removed as a dependency](https://github.com/laravel/dusk/pull/1166), improving performance. Sites using the jQuery library should find version conflicts are now avoided during Dusk tests.

Dusk no longer injects jQuery into the page, so calls to `jQuery(...)` or `$(...)` will only work if your application loads jQuery itself.

If your application does not load jQuery itself, these scripts can be rewritten to use browser-native DOM APIs:

```php
// Before...
$browser->script("return $('.alert').text();");

// After...
$browser->script("return document.querySelector('.alert')?.textContent;");
```

```php
// Before...
$browser->script("$('#name').val('Taylor').trigger('input');");

// After...
$browser->script(<<<'JS'
    const element = document.querySelector('#name');

    element.value = 'Taylor';
    element.dispatchEvent(new Event('input', { bubbles: true }));
JS);
```

jQuery-only selectors such as `:contains` and `:visible` are not supported by `querySelector`. These should be rewritten using native DOM APIs:

```php
// Before...
$browser->script("$('a:contains(\"Dashboard\"):visible').click();");

// After...
$browser->script(<<<'JS'
    Array.from(document.querySelectorAll('a'))
        .find((element) => element.textContent.includes('Dashboard') && element.offsetParent !== null)
        ?.click();
JS);
```

Common replacements include:

- `$('.selector').length` may be replaced with `document.querySelectorAll('.selector').length`.
- `$('.selector').text()` may be replaced with `document.querySelector('.selector')?.textContent`.
- `$('.selector').html()` may be replaced with `document.querySelector('.selector')?.innerHTML`.
- `$('.selector').val()` may be replaced with `document.querySelector('.selector')?.value`.
- `$('.selector').attr('href')` may be replaced with `document.querySelector('.selector')?.getAttribute('href')`.
- `$('.selector').addClass('active')` may be replaced with `document.querySelector('.selector')?.classList.add('active')`.
- `$('.selector').is(':visible')` may be replaced with a visibility check such as `element.offsetParent !== null` or `element.getClientRects().length > 0`.

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
