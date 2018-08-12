# Release Notes for 4.0.x

## v4.0.0 (2018-08-11)

Dusk 4.0.0 disables cookie serialization and is intended for use with Laravel 5.6.30 or later. If you are using a previous version of Laravel, please continue using Dusk 3.0.0.

# Release Notes for 1.0.x

## v1.0.13 (2017-04-20)

### Added
- Added `illuminate/console` as dependency ([#232](https://github.com/laravel/dusk/pull/232))
- Added security measurement against registering Dusk on production ([#229](https://github.com/laravel/dusk/pull/229))
- Added `PHP_BINARY` constant to the list of PHP's executable binaries ([#240](https://github.com/laravel/dusk/pull/240))

### Changed
- Changed `propagateScaffoldingToBrowser()` to `setUp()` for compatibility with PHPUnit ~6.0 ([#227](https://github.com/laravel/dusk/pull/227))
- Changed `selected()` comparison to always cast the value to string ([#239](https://github.com/laravel/dusk/pull/239))

### Fixed
- No longer throws exception when Tty is not available ([#226](https://github.com/laravel/dusk/pull/226))
- Use `getAttribute('value')` instead of `getText()` for `textarea` elements ([#237](https://github.com/laravel/dusk/pull/237))
- Fixed bug when giving strings with apostrophe to `clickLink()` ([#228](https://github.com/laravel/dusk/pull/228))

## v1.0.12 (2017-04-07)

### Added
- Added automated tests for HTML elements identified by strings with a colon ([#214](https://github.com/laravel/dusk/pull/214))

### Fixed
- Support for colon on HTML id tag ([#214](https://github.com/laravel/dusk/pull/214))


## v1.0.11 (2017-03-20)

### Added
- Added `assertSelectHasOptions()`, `assertSelectMissingOptions()`, `assertSelectHasOption()` and `and assertSelectMissingOption()` ([#195](https://github.com/laravel/dusk/pull/195))
- Added purge console logs before starting tests ([#193](https://github.com/laravel/dusk/pull/193))
- Added `assertPathIsNot()` ([#183](https://github.com/laravel/dusk/pull/183))
- Added support for back button ([#187](https://github.com/laravel/dusk/pull/187))
- Added `waitForLocation()` to allow waiting on `window.location` to be changed ([#176](https://github.com/laravel/dusk/pull/176))

### Changed
- Updated ChromeDriver to v2.28 so that it works with Chrome 57 ([#199](https://github.com/laravel/dusk/pull/199))
- Comparison to `option` inside `select` will no longer be strict ([#178](https://github.com/laravel/dusk/pull/178))
- Type-hint Browser for auto-complete support ([#174](https://github.com/laravel/dusk/pull/174))


## v1.0.10 (2017-03-03)
