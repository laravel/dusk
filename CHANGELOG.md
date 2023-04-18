# Release Notes

## [Unreleased](https://github.com/laravel/dusk/compare/v7.7.1...7.x)

## [v7.7.1](https://github.com/laravel/dusk/compare/v7.7.0...v7.7.1) - 2023-04-13

- Allow `dusk` attribute selectors to be chained by @JayBizzle in https://github.com/laravel/dusk/pull/1034

## [v7.7.0](https://github.com/laravel/dusk/compare/v7.6.1...v7.7.0) - 2023-02-21

- Use `--headless=new` by @SjorsO in https://github.com/laravel/dusk/pull/1027

## [v7.6.1](https://github.com/laravel/dusk/compare/v7.6.0...v7.6.1) - 2023-02-14

### Fixed

- Fix PHPUnit v10 stubs by @driesvints in https://github.com/laravel/dusk/pull/1024

## [v7.6.0](https://github.com/laravel/dusk/compare/v7.5.0...v7.6.0) - 2023-02-07

### Added

- Adds PHPUnit 10 support  by @crynobone in https://github.com/laravel/dusk/pull/1023

## [v7.5.0](https://github.com/laravel/dusk/compare/v7.4.0...v7.5.0) - 2023-01-22

### Added

- Allow custom dusk selector by @taylorotwell in https://github.com/laravel/dusk/commit/cf04717664f80204567ad3077ea7484a0be16497

## [v7.4.0](https://github.com/laravel/dusk/compare/v7.3.0...v7.4.0) - 2023-01-06

### Added

- Laravel v10 support by @driesvints in https://github.com/laravel/dusk/pull/1015

## [v7.3.0](https://github.com/laravel/dusk/compare/v7.2.1...v7.3.0) - 2023-01-03

### Changed

- Added .env file existence check by @Kravets1996 in https://github.com/laravel/dusk/pull/1014
- Uses PHP Native Type Declarations 🐘  by @nunomaduro in https://github.com/laravel/dusk/pull/1004

## [v7.2.1](https://github.com/laravel/dusk/compare/v7.2.0...v7.2.1) - 2022-12-16

### Fixed

- Revert "feat: add alternative tag for element selection" by @driesvints in https://github.com/laravel/dusk/pull/1013

## [v7.2.0](https://github.com/laravel/dusk/compare/v7.1.1...v7.2.0) - 2022-12-14

### Added

- Add alternative tag for element selection by @pataar in https://github.com/laravel/dusk/pull/1012

## [v7.1.1](https://github.com/laravel/dusk/compare/v7.1.0...v7.1.1) - 2022-09-29

### Fixed

- Fix updated Mac image name by @driesvints in https://github.com/laravel/dusk/pull/1001

## [v7.1.0](https://github.com/laravel/dusk/compare/v7.0.2...v7.1.0) - 2022-09-27

### Added

- Add `pauseIf()` / `pauseUnless()` by @u01jmg3 in https://github.com/laravel/dusk/pull/999

## [v7.0.2](https://github.com/laravel/dusk/compare/v7.0.1...v7.0.2) - 2022-09-15

### Changed

- Allow to click and wait [N] seconds for reload by @fabio-ivona in https://github.com/laravel/dusk/pull/998

## [v7.0.1](https://github.com/laravel/dusk/compare/v7.0.0...v7.0.1) - 2022-09-02

### Fixed

- Remove extra Directory Separator from ChromeProcess by @GeoSot in https://github.com/laravel/dusk/pull/995

## [v7.0.0](https://github.com/laravel/dusk/compare/v6.25.1...v7.0.0) - 2022-08-19

### Changed

- Uses Pest if available by @nunomaduro in https://github.com/laravel/dusk/pull/771
- Use selector to double click, and to click and hold by @rodrigopedra in https://github.com/laravel/dusk/pull/848
- Bump dependencies by @driesvints in https://github.com/laravel/dusk/pull/874

### Removed

- Drop PHPUnit 7 by @driesvints in https://github.com/laravel/dusk/pull/762
- Drop PHP 7.2 by @driesvints in https://github.com/laravel/dusk/pull/860
- Drop Laravel v6 & v7 support by @driesvints in https://github.com/laravel/dusk/pull/862
- Drop PHPUnit v8 by @driesvints in https://github.com/laravel/dusk/pull/861
- Remove chrome binaries by @driesvints in https://github.com/laravel/dusk/pull/873
- Drop old PHP and Laravel versions by @driesvints in https://github.com/laravel/dusk/pull/993

## [v6.25.1](https://github.com/laravel/dusk/compare/v6.25.0...v6.25.1) - 2022-07-25

### Fixed

- Try clicking all elements before throwing `ElementClickInterceptedException` by @SjorsO in https://github.com/laravel/dusk/pull/989

## [v6.25.0](https://github.com/laravel/dusk/compare/v6.24.0...v6.25.0) - 2022-07-11

### Added

- Added responsiveScreenShots method by @ps-sean in https://github.com/laravel/dusk/pull/984
- Add `assertIndeterminate` assertion for checkbox. by @crynobone in https://github.com/laravel/dusk/pull/986

## [v6.24.0](https://github.com/laravel/dusk/compare/v6.23.1...v6.24.0) - 2022-05-09

### Added

- Add `hasStartMaximizedEnabled` method by @roksprogar in https://github.com/laravel/dusk/pull/978

## [v6.23.1](https://github.com/laravel/dusk/compare/v6.23.0...v6.23.1) - 2022-05-02

### Fixed

- Fix storing page source to disk when source assertions fail. by @calmdev in https://github.com/laravel/dusk/pull/976

## [v6.23.0](https://github.com/laravel/dusk/compare/v6.22.3...v6.23.0) - 2022-04-11

### Added

- Add `waitForEvent()` method by @michaelhue in https://github.com/laravel/dusk/pull/972

## [v6.22.3](https://github.com/laravel/dusk/compare/v6.22.2...v6.22.3) - 2022-04-04

### Fixed

- Fixed vueAttribute for older Vue 2.x projects by @myMartek in https://github.com/laravel/dusk/pull/970

## [v6.22.2](https://github.com/laravel/dusk/compare/v6.22.1...v6.22.2) - 2022-03-24

### Changed

- `assertVue*()` methods support Vue 3 composition API by @derekmd in https://github.com/laravel/dusk/pull/969

## [v6.22.1](https://github.com/laravel/dusk/compare/v6.22.0...v6.22.1) - 2022-02-15

### Fixed

- Fixes link assertion with single or double quote ([#965](https://github.com/laravel/dusk/pull/965))

## [v6.22.0](https://github.com/laravel/dusk/compare/v6.21.0...v6.22.0) - 2022-02-01

### Changed

- Add `waitForInput` by @SjorsO in https://github.com/laravel/dusk/pull/960

## [v6.21.0 (2022-01-12)](https://github.com/laravel/dusk/compare/v6.20.0...v6.21.0)

### Changed

- Laravel 9 Support ([#956](https://github.com/laravel/dusk/pull/956))

## [v6.20.0 (2022-01-04)](https://github.com/laravel/dusk/compare/v6.19.2...v6.20.0)

### Changed

- Add `clickAndWaitForReload` ([#953](https://github.com/laravel/dusk/pull/953))

## [v6.19.2 (2021-10-20)](https://github.com/laravel/dusk/compare/v6.19.1...v6.19.2)

### Fixed

- Fix assertValue for select elements ([#942](https://github.com/laravel/dusk/pull/942))

## [v6.19.1 (2021-10-19)](https://github.com/laravel/dusk/compare/v6.19.0...v6.19.1)

### Changed

- throw an error if assertValue() is used with an element that does not support the value attribute ([#936](https://github.com/laravel/dusk/pull/936), [334c49f](https://github.com/laravel/dusk/commit/334c49faa2e8ec4ddb759aadebdce67d654c305b))

### Fixed

- Fix logout() when using AuthenticateSession and default guard ([#939](https://github.com/laravel/dusk/pull/939))

## [v6.19.0 (2021-10-12)](https://github.com/laravel/dusk/compare/v6.18.1...v6.19.0)

### Added

- Add new assertion `assertValueIsNot()` ([#929](https://github.com/laravel/dusk/pull/929))
- Add new assertion `assertAttributeContains()` ([#931](https://github.com/laravel/dusk/pull/931), [ab47680](https://github.com/laravel/dusk/commit/ab476806c8bef81d8e3014bd3be4c142c0355e8e))

## [v6.18.1 (2021-09-07)](https://github.com/laravel/dusk/compare/v6.18.0...v6.18.1)

### Updated

- Using `loginAs` with id ([#922](https://github.com/laravel/dusk/pull/922)

### Fixed

- Fix "pest" option does not exist on `dusk:fails` command ([#921](https://github.com/laravel/dusk/pull/921))

## [v6.18.0 (2021-08-31)](https://github.com/laravel/dusk/compare/v6.17.1...v6.18.0)

### Added

- Add waitUntilEnabled and waitUntilDisabled ([#918](https://github.com/laravel/dusk/pull/918))

## [v6.17.1 (2021-08-17)](https://github.com/laravel/dusk/compare/v6.17.0...v6.17.1)

### Changed

- Support waiting for URLs with `waitForLocation` ([#916](https://github.com/laravel/dusk/pull/916))

## [v6.17.0 (2021-08-10)](https://github.com/laravel/dusk/compare/v6.16.0...v6.17.0)

### Added

- Add `assertInputPresent` and `assertInputMissing` assertions ([#914](https://github.com/laravel/dusk/pull/914))

### Changed

- Add source directory creation on install ([#915](https://github.com/laravel/dusk/pull/915))

## [v6.16.0 (2021-08-03)](https://github.com/laravel/dusk/compare/v6.15.1...v6.16.0)

### Added

- Make DuskCommand compatible with Pest ([#913](https://github.com/laravel/dusk/pull/913))

### Fixed

- Fix unicode support in appendSlowly method ([#907](https://github.com/laravel/dusk/pull/907))
- Redirect page to previous URL after asserting authentication ([#912](https://github.com/laravel/dusk/pull/912))

## [v6.15.1 (2021-07-06)](https://github.com/laravel/dusk/compare/v6.15.0...v6.15.1)

### Changed

- Allow to pass array for select multiple ([#904](https://github.com/laravel/dusk/pull/904))

### Fixed

- Fix for class imports ([#905](https://github.com/laravel/dusk/pull/905))

## [v6.15.0 (2021-04-06)](https://github.com/laravel/dusk/compare/v6.14.0...v6.15.0)

### Added

- Added middleware configuration ([#888](https://github.com/laravel/dusk/pull/888))

## [v6.14.0 (2021-03-23)](https://github.com/laravel/dusk/compare/v6.13.0...v6.14.0)

### Added

- Add purge console command ([#887](https://github.com/laravel/dusk/pull/887))

### Changed

- Move commands to boot method ([#884](https://github.com/laravel/dusk/pull/884))
- Refactor redundant purging methods ([#886](https://github.com/laravel/dusk/pull/886))

## [v6.13.0 (2021-02-23)](https://github.com/laravel/dusk/compare/v6.12.0...v6.13.0)

### Added

- Add `assertNotPresent()` assertion ([#879](https://github.com/laravel/dusk/pull/879))

## [v6.12.0 (2021-02-16)](https://github.com/laravel/dusk/compare/v6.11.3...v6.12.0)

### Added

- Add Dusk command argument --browse ([#870](https://github.com/laravel/dusk/pull/870))
- Add support for Mac ARM64 architecture ([#876](https://github.com/laravel/dusk/pull/876))

## [v6.11.3 (2021-02-09)](https://github.com/laravel/dusk/compare/v6.11.2...v6.11.3)

### Fixed

- Add `$seconds` options to `Browser::elseWhenAvailable()` ([#865](https://github.com/laravel/dusk/pull/865))

## [v6.11.2 (2021-01-26)](https://github.com/laravel/dusk/compare/v6.11.1...v6.11.2)

### Changed

- Add Chromium path for Debian 10 ([#855](https://github.com/laravel/dusk/pull/855))

### Fixed

- Do not resize to zero ([#858](https://github.com/laravel/dusk/pull/858))
- Fix the value generated when setting encrypted cookies ([#857](https://github.com/laravel/dusk/pull/857))

## [v6.11.1 (2021-01-19)](https://github.com/laravel/dusk/compare/v6.11.0...v6.11.1)

### Changed

- Allow passing mixed to assertVue and assertVueIsNot ([#853](https://github.com/laravel/dusk/pull/853))

## [v6.11.0 (2020-12-15)](https://github.com/laravel/dusk/compare/v6.10.0...v6.11.0)

### Added

- Add `Browser::elsewhereWhenAvailable()` ([#846](https://github.com/laravel/dusk/pull/846))

## [v6.10.0 (2020-12-15)](https://github.com/laravel/dusk/compare/v6.9.1...v6.10.0)

### Added

- Add new assertions `assertSeeAnythingIn()` and `assertSeeNothingIn()` ([#843](https://github.com/laravel/dusk/pull/843), [dc683ee](https://github.com/laravel/dusk/commit/dc683eeb551456d69b3207ef13daf03d3f1f2dea))

## [v6.9.1 (2020-11-24)](https://github.com/laravel/dusk/compare/v6.9.0...v6.9.1)

### Fixed

- Add Vue 3 support to the `assertVue*()` methods ([#834](https://github.com/laravel/dusk/pull/834))

## [v6.9.0 (2020-11-19)](https://github.com/laravel/dusk/compare/v6.8.1...v6.9.0)

### Added

- PHP 8 Support ([#833](https://github.com/laravel/dusk/pull/833))

## [v6.8.1 (2020-11-17)](https://github.com/laravel/dusk/compare/v6.8.0...v6.8.1)

### Changed

- Changes in preparation for Laravel Sail

## [v6.8.0 (2020-10-06)](https://github.com/laravel/dusk/compare/v6.7.0...v6.8.0)

### Added

- Capture source code on failure ([#819](https://github.com/laravel/dusk/pull/819), [3c59a5c](https://github.com/laravel/dusk/commit/3c59a5c698a6c4a3f06a3da174a1f0f3a01df8f5))
- Add `assertScript()` ([#821](https://github.com/laravel/dusk/pull/821))
- Added `waitForTextIn` Function ([#823](https://github.com/laravel/dusk/pull/823))
- Allow to utilise browser "about:blank" page  ([#824](https://github.com/laravel/dusk/pull/824))

### Fixed

- Fix choosing random elements on `<select>` ([#822](https://github.com/laravel/dusk/pull/822))
- Fix `logout()` when using AuthenticateSession middleware ([#826](https://github.com/laravel/dusk/pull/826))

## [v6.7.0 (2020-09-29)](https://github.com/laravel/dusk/compare/v6.6.1...v6.7.0)

### Added

- Add Chrome driver auto detection ([#816](https://github.com/laravel/dusk/pull/816), [3ca17f1](https://github.com/laravel/dusk/commit/3ca17f124342b90a3e1b2f04932a76fdfa89d6ef))

## [v6.6.1 (2020-09-22)](https://github.com/laravel/dusk/compare/v6.6.0...v6.6.1)

### Fixed

- Fix the `fitContent()` method ([#815](https://github.com/laravel/dusk/pull/815))

## [v6.6.0 (2020-09-08)](https://github.com/laravel/dusk/compare/v6.5.1...v6.6.0)

### Added

- Allow proxy for getting latest version ([#805](https://github.com/laravel/dusk/pull/805))

## [v6.5.1 (2020-08-28)](https://github.com/laravel/dusk/compare/v6.5.0...v6.5.1)

### Fixed

- Add CookieValuePrefix detection for encrypted cookies ([#804](https://github.com/laravel/dusk/pull/804))
- Allow dotenv 5.0 to be installed ([#803](https://github.com/laravel/dusk/pull/803))

## [v6.5.0 (2020-08-25)](https://github.com/laravel/dusk/compare/v6.4.1...v6.5.0)

### Added

- Support Laravel 8 ([#800](https://github.com/laravel/dusk/pull/800))

## [v6.4.1 (2020-07-14)](https://github.com/laravel/dusk/compare/v6.4.0...v6.4.1)

### Fixed

- Pass ssl-no-verify option into latestVersion of ChromeDriver install ([#794](https://github.com/laravel/dusk/pull/794))

## [v6.4.0 (2020-06-30)](https://github.com/laravel/dusk/compare/v6.3.0...v6.4.0)

### Added

- Support clicking the topmost element at a given pair of coordinates ([#788](https://github.com/laravel/dusk/pull/788))

## [v6.3.0 (2020-06-16)](https://github.com/laravel/dusk/compare/v6.2.0...v6.3.0)

### Added

- Support executing closures outside of the current selector scope ([#784](https://github.com/laravel/dusk/pull/784))

## [v6.2.0 (2020-05-26)](https://github.com/laravel/dusk/compare/v6.1.0...v6.2.0)

### Changed

- Allow Dusk route to be configurable ([#774](https://github.com/laravel/dusk/pull/774), [668289a](https://github.com/laravel/dusk/commit/668289a3a323f79eb42b19a391ed33768aa82791))

### Fixed

- Domain routing should only be optional ([#776](https://github.com/laravel/dusk/pull/776))
- Fix for cookie assertions ([#778](https://github.com/laravel/dusk/pull/778))

## [v6.1.0 (2020-04-28)](https://github.com/laravel/dusk/compare/v6.0.1...v6.1.0)

### Added

- Support scrolling an element into view ([#766](https://github.com/laravel/dusk/pull/766))
- Support navigating forward in the browser ([#767](https://github.com/laravel/dusk/pull/767))

## [v6.0.1 (2020-04-21)](https://github.com/laravel/dusk/compare/v6.0.0...v6.0.1)

### Fixed

- Trim trailing slash ([#764](https://github.com/laravel/dusk/pull/764))

## [v6.0.0 (2020-04-14)](https://github.com/laravel/dusk/compare/v5.11.0...v6.0.0)

### Added

- PHPUnit 9 support ([2770f25](https://github.com/laravel/dusk/commit/2770f256bd0ccd9b4c8a892fb9fb5b134e6f7c3a), [45ae210](https://github.com/laravel/dusk/commit/45ae210a3edd7d2ed3f712cd1aab59037266c21c))

### Changed

- Dropped support for Laravel 5.7 & 5.8 ([98af698](https://github.com/laravel/dusk/commit/98af6989532ad6ecb420cae8a6e2864335c2dd9a), [e0c173a](https://github.com/laravel/dusk/commit/e0c173af6deaeda6170e017eb3ce225d8d4c5964))
- Dropped support for PHP 7.1 ([210e03e](https://github.com/laravel/dusk/commit/210e03ec2c121517b99bd6163859bbdc5cce564a))
- Dropped support for Carbon v1 ([0b880e9](https://github.com/laravel/dusk/commit/0b880e9300257dd08aa25deef3e831a3deb3df44))
- Bumped minimum Symfony dependencies to 4.3 ([1ee28e1](https://github.com/laravel/dusk/commit/1ee28e1bfcce1de4a3ad83253394d964690602c4))

## [v5.11.0 (2020-03-24)](https://github.com/laravel/dusk/compare/v5.10.0...v5.11.0)

### Added

- Add assert attribute methods ([#751](https://github.com/laravel/dusk/pull/751))

### Fixed

- Fix Call to undefined method ([#750](https://github.com/laravel/dusk/pull/750))
- Avoid throwing exception on production ([#755](https://github.com/laravel/dusk/pull/755))

## [v5.10.0 (2020-03-17)](https://github.com/laravel/dusk/compare/v5.9.2...v5.10.0)

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
