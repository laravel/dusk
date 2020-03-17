# Release Notes

## [Unreleased](https://github.com/laravel/dusk/compare/v5.10.0...5.0)


## [v5.10.0 (2020-02-18)](https://github.com/laravel/dusk/compare/v5.9.2...v5.10.0)

### Added
- Adds `typeSlowly` & `appendSlowly` ([#748](https://github.com/laravel/dusk/pull/748))


## [v5.9.2 (2020-02-18)](https://github.com/laravel/dusk/compare/v5.9.1...v5.9.2)

### Fixed
- Bugfix quoting for `InteractsWithElements::value` ([#735](https://github.com/laravel/dusk/pull/735))
- Remove php-webdriver constraints ([#737](https://github.com/laravel/dusk/pull/737))


## [v5.9.1 (2020-02-12)](https://github.com/laravel/dusk/compare/v5.9.0...v5.9.1)

### Fixed
- Adds the missing import for `InteractsWithMouse@clickAtXPath` ([#728](https://github.com/laravel/dusk/pull/728))
- Size sanity check at fitContent ([#730](https://github.com/laravel/dusk/pull/730))
- Lock php-webdriver constraints for now ([#733](https://github.com/laravel/dusk/pull/733))


## [v5.9.0 (2020-01-28)](https://github.com/laravel/dusk/compare/v5.8.2...v5.9.0)

### Added
- Add `clickAtXPath` ([#723](https://github.com/laravel/dusk/pull/723), [effe73d](https://github.com/laravel/dusk/commit/effe73d6eb61b4bd77f88814bcd679e4fceb6f25))
- Add `ProvidesBrowser::getCallerName()` ([#725](https://github.com/laravel/dusk/pull/725))

### Fixed
- Fit content to `<html>` instead of `<body>` ([#726](https://github.com/laravel/dusk/pull/726))


## [v5.8.2 (2020-01-21)](https://github.com/laravel/dusk/compare/v5.8.1...v5.8.2)

### Changed
- Rename php-webdriver package ([#720](https://github.com/laravel/dusk/pull/720))
- Update jQuery file ([#721](https://github.com/laravel/dusk/pull/721))

### Fixed
- Update `fitContent()` ([#717](https://github.com/laravel/dusk/pull/717))


## [v5.8.1 (2020-01-07)](https://github.com/laravel/dusk/compare/v5.8.0...v5.8.1)

### Fixed
- Cast boolean values to appropriate string ([#713](https://github.com/laravel/dusk/pull/713))


## [v5.8.0 (2019-12-30)](https://github.com/laravel/dusk/compare/v5.7.0...v5.8.0)

### Added
- Add "waitUntilMissingText" ([#706](https://github.com/laravel/dusk/pull/706))
- Add ability to store source from browser ([#707](https://github.com/laravel/dusk/pull/707), [9c90e2a](https://github.com/laravel/dusk/commit/9c90e2a716030c9a36e6306c3f67d606a254bbb7), [1d5bc20](https://github.com/laravel/dusk/commit/1d5bc203b67ffc5a17eb1b89f3e22547e3ea174b))


## [v5.7.0 (2019-12-17)](https://github.com/laravel/dusk/compare/v5.6.3...v5.7.0)

### Added
- Automatically fit content on failures ([#704](https://github.com/laravel/dusk/pull/704))


## [v5.6.3 (2019-12-03)](https://github.com/laravel/dusk/compare/v5.6.2...v5.6.3)

### Added
- Support phpdotenv v4 ([#699](https://github.com/laravel/dusk/pull/699))

### Fixed
- scrollTo: add support for selectors with quotes ([#697](https://github.com/laravel/dusk/pull/697))


## [v5.6.2 (2019-11-26)](https://github.com/laravel/dusk/compare/v5.6.1...v5.6.2)

### Changed
- Allow for Symfony 5 ([#696](https://github.com/laravel/dusk/pull/696))


## [v5.6.1 (2019-11-12)](https://github.com/laravel/dusk/compare/v5.6.0...v5.6.1)

### Fixed
- Ensure jQuery for scrollTo ([#686](https://github.com/laravel/dusk/pull/686))
- Added missing return statement in withDuskEnvironment ([#691](https://github.com/laravel/dusk/pull/691))
- Prevent using pcntl when not installed ([#692](https://github.com/laravel/dusk/pull/692))


## [v5.6.0 (2019-10-29)](https://github.com/laravel/dusk/compare/v5.5.2...v5.6.0)

### Added
- Add scrollTo method ([#684](https://github.com/laravel/dusk/pull/684))

### Fixed
- Add graceful handler for `SIGINT` for .env restoration ([#682](https://github.com/laravel/dusk/pull/682), [f843b8a](https://github.com/laravel/dusk/commit/f843b8a51ae96933cefcc74dec515377d3135611))


## [v5.5.2 (2019-09-24)](https://github.com/laravel/dusk/compare/v5.5.1...v5.5.2)

### Fixed
- Improve detection of latest stable ChromeDriver release ([#677](https://github.com/laravel/dusk/pull/677))


## [v5.5.1 (2019-09-12)](https://github.com/laravel/dusk/compare/v5.5.0...v5.5.1)

### Fixed
- Update regular expression base on website changes ([#674](https://github.com/laravel/dusk/pull/674))


## [v5.5.0 (2019-08-06)](https://github.com/laravel/dusk/compare/v5.4.0...v5.5.0)

### Added
- Allow saving screenshots in a subdirectory ([#662](https://github.com/laravel/dusk/pull/662))


## [v5.4.0 (2019-07-30)](https://github.com/laravel/dusk/compare/v5.3.0...v5.4.0)

### Added
- Add assertion checks if a button is disabled or enabled ([#661](https://github.com/laravel/dusk/pull/661))

### Fixed
- Update constraints for Laravel 6.0 ([e4b4d63](https://github.com/laravel/dusk/commit/e4b4d63c179bb1f228db22852bd776db750d1ec6))


## [v5.3.0 (2019-07-11)](https://github.com/laravel/dusk/compare/v5.2.0...v5.3.0)

### Added
- Add proxy support to the `dusk:install` command ([#659](https://github.com/laravel/dusk/pull/659))


## [v5.2.0 (2019-06-25)](https://github.com/laravel/dusk/compare/v5.1.1...v5.2.0)

### Added
- Add option to fullsize the browser ([#655](https://github.com/laravel/dusk/pull/655))


## [v5.1.1 (2019-06-14)](https://github.com/laravel/dusk/compare/v5.1.0...v5.1.1)

### Fixed
- Add latest version of Facebook Webdriver ([#654](https://github.com/laravel/dusk/pull/654))


## [v5.1.0 (2019-05-02)](https://github.com/laravel/dusk/compare/v5.0.3...v5.1.0)

### Added
- Implement ChromeDriverCommand ([#643](https://github.com/laravel/dusk/pull/643), [60339a5](https://github.com/laravel/dusk/commit/60339a521a1b05e55af7c90b3151557100a0db4d), [#644](https://github.com/laravel/dusk/pull/644)) 


## [v5.0.3 (2019-04-02)](https://github.com/laravel/dusk/compare/v5.0.2...v5.0.3)

### Fixed
- Fix `assertVueContains` and `assertVueDoesNotContain` ([#638](https://github.com/laravel/dusk/pull/638))


## [v5.0.2 (2019-03-12)](https://github.com/laravel/dusk/compare/v5.0.1...v5.0.2)

### Fixed
- Fix cookies with falsey values ([#617](https://github.com/laravel/dusk/pull/617))
- Fix `with()` and page assertions ([#625](https://github.com/laravel/dusk/pull/625))
- Avoid deprecation messages on PHPUnit 8 ([#620](https://github.com/laravel/dusk/pull/620))


## [v5.0.1 (2019-02-27)](https://github.com/laravel/dusk/compare/v5.0.0...v5.0.1)

### Added
- Added support for `phpunit.dusk.xml.dist` ([#610](https://github.com/laravel/dusk/pull/610))
- Use custom DISPLAY variable when set on Linux ([#613](https://github.com/laravel/dusk/pull/613), [2480495](https://github.com/laravel/dusk/commit/24804953c5b521415a717afbf82ff4b938c10535))

### Fixed
- Added missing dependencies ([98eccfd](https://github.com/laravel/dusk/commit/98eccfd56e9b2b23b093b801f62c766aaf67589f))
- Fix installation of Dotenv on Laravel 5.8 ([1f67bf2](https://github.com/laravel/dusk/commit/1f67bf204fab65a212975683b5391c2f55dd3bcf))


## [v5.0.0 (2019-02-12)](https://github.com/laravel/dusk/compare/v4.0.5...v5.0.0)

### Added
- Support PHPUnit 8 ([788e79c](https://github.com/laravel/dusk/commit/788e79c4713a5706eeafaf7270986a71a4ed43be))
- Support Carbon 2 ([85cfc50](https://github.com/laravel/dusk/commit/85cfc50e126a3835428577052cc0660d1c3b639e))
- Support Laravel 5.8 ([5b2e9aa](https://github.com/laravel/dusk/commit/5b2e9aa9e4a124f4c4878f65fb644101de1644e7))

### Changed
- Update minimum Laravel version ([fcb2226](https://github.com/laravel/dusk/commit/fcb2226a524f47b51b9b49316d87bc51738733d7))
- Update Symfony dependencies to latest version ([788e79c](https://github.com/laravel/dusk/commit/788e79c4713a5706eeafaf7270986a71a4ed43be))
- Prefer stable dependencies ([fdb2fd4](https://github.com/laravel/dusk/commit/fdb2fd4b2a2e787b08cf44649c4eef84837324ca))


## [v4.0.0 (2018-08-11)](https://github.com/laravel/dusk/compare/v3.0.10...v4.0.0)

Dusk 4.0.0 disables cookie serialization and is intended for use with Laravel 5.6.30 or later. If you are using a previous version of Laravel, please continue using Dusk 3.0.0.


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
