# Change Log
All notable changes to this project will be documented in this file.

## [6.1.7] - 2020-02-19
### Changed
- Improved `getPoster()` method. You can now get different image smaller sizes (`xxs, xs, s`). *Thanks, @phyozawtun!*

## [6.1.6] - 2018-11-03
### Changed
- Added getLocations() method. *Thanks, @defro!*

## [6.1.5] - 2018-10-15
### Changed
- Fixed a problem with German Umlauts. *Thanks, @DLX23!*

## [6.1.4] - 2018-08-22
### Changed
- Added `getGross()`. *Thanks, @bla0r!*

## [6.1.3] - 2018-05-15
### Changed
- Added `getReleaseDates()`
- Fixed `getCastImages()`

## [6.1.2] - 2018-05-10
### Changed
- Some smaller improvements, updates, etc.

## [6.1.1] - 2018-05-06
### Changed
- Added `getCastImages()`.

## [6.1.0] - 2018-04-25
### Changed
- Added `getDescription()`.
- Improved `getAll()`;
- Fixed some smaller stuff. *Gosh, it's time for a rewrite!*
- Updated `imdb.tests.php`, README, and license year.

## [6.0.8] - 2018-04-18
### Changed
- Switched everything to https.
- Updated `imdb.tests.php`.
- Added `getBudget()`. *Thanks, @bla0r!*

## [6.0.7] - 2017-12-25
### Changed
- Updated the title regex.
- Optimized cURL options.

## [6.0.6] - 2017-12-24 🎄
### Changed
- Now using the `/reference` page instead of the `/combined` one.
- Using `sha1` instead of `md5` for filenames of the caches. This should invalidate all old ones automatically. **I'd recommend cleaning `/cache` anyway!**
- Updated almost all regular expressions. *Thanks to @paulitap for the great work!*
- Closed #87 🙌
- Fixed a typo. *Why did nobody tell me before? 😞*
- Some smaller changes I can't remember.

## [6.0.5] - 2016-06-06
### Changed
- Disabled `CURLOPT_SSL_VERIFYPEER` and `CURLOPT_SSL_VERIFYHOST` in the `runCurl` method. Please check http://php.net/manual/en/function.curl-setopt.php for more details on these configuration settings. This should fix the problem of downloading posters without valid certificates and stuff like that.
- Reformatted some code and stuff. *(Yeah, the code quality of this project is bad, I know.)*
- Requires PHP 5.6+ in `composer.json`.

## [6.0.4] - 2016-06-06
### Changed
- Fixed `IMDB_SEASONS`.

## [6.0.3] - 2016-01-08
### Changed
- Fixed `getPoster`. It most likely always did a hot link to IMDB instead of the local image. The default is now to hot link to IMDb with the small resolution.
- Made the private `$iId` variable public. You can now access the ID of the movie without any problems (eg. `$oIMDB->iId`).
- Reformatted `imdb.example.php` and also fixed a small issue there.

## [6.0.2] - 2015-12-19
### Changed
- Added more test cases to `imdb.example.php`.
- Fixed `getYear` (#70 & #71).
- Reformatted `imdb.class.php` (again…) and fixed some third-party spelling issues.
- Fixed `cleanString` (#69).

### Removed
- `getMovieMeter`.

## [6.0.1] - 2015-09-12
### Changed
- `LICENSE` year updated.
- `imdb.example.php` and `imdb.tests.php` reformatted. Also added a new test.
- Fixed `getGenre` (#66). *Thanks, doodley2!*
- Reformatted `imdb.class.php`. Also changed some code.

### Added
- `.editorconfig` added. Please make sure that your editor uses these settings. See [editorconfig.org](http://editorconfig.org/).

## [6.0.0] - 2015-01-02
### Changed
- Since this script is still heavily used by you guys, I decided to clean up the codebase. It’s still far away from being a great example of good code – but remember: This all started on a Saturday, when I was feeling sick and didn’t have anything else to do.
- Switched back to MIT license. People never cared that they’re not allowed to use this commercially, so I give up. **This is still a proof of concept and you should not use this in any way.**
- Removed all the 3rd party stuff.
- Moved `imdb.example.php` to the new directory *examples*. Same goes with `imdb.tests.php`, since this file doesn’t contain real “tests”.
- As it doesn’t matter anymore, I removed the changelog of everything below v6.0.0.
- “To use [Yoda conditions](http://en.wikipedia.org/wiki/Yoda_conditions) as much as possible trying.”
- Switched to the “combined” page. This enables easier scrapping, but we lose some methods (like `getBudget()` or `getDescription()`). Then again, a few new ones where added. See README.
- Removed `getFullCast()`, because `getCast()` now returns the full cast.

[Unreleased]: https://github.com/FabianBeiner/PHP-IMDB-Grabber/compare/v6.1.2...HEAD
[6.0.0]: https://github.com/FabianBeiner/PHP-IMDB-Grabber/compare/5.5.21...v6.0.0
[6.0.1]: https://github.com/FabianBeiner/PHP-IMDB-Grabber/compare/v6.0.0...v6.0.1
[6.0.2]: https://github.com/FabianBeiner/PHP-IMDB-Grabber/compare/v6.0.1...v6.0.2
[6.0.3]: https://github.com/FabianBeiner/PHP-IMDB-Grabber/compare/v6.0.2...v6.0.3
[6.0.4]: https://github.com/FabianBeiner/PHP-IMDB-Grabber/compare/v6.0.3...v6.0.4
[6.0.5]: https://github.com/FabianBeiner/PHP-IMDB-Grabber/compare/v6.0.4...v6.0.5
[6.0.6]: https://github.com/FabianBeiner/PHP-IMDB-Grabber/compare/v6.0.5...v6.0.6
[6.0.7]: https://github.com/FabianBeiner/PHP-IMDB-Grabber/compare/v6.0.6...v6.0.7
[6.0.8]: https://github.com/FabianBeiner/PHP-IMDB-Grabber/compare/v6.0.7...v6.0.8
[6.1.0]: https://github.com/FabianBeiner/PHP-IMDB-Grabber/compare/v6.0.8...v6.1.0
[6.1.1]: https://github.com/FabianBeiner/PHP-IMDB-Grabber/compare/v6.1.0...v6.1.1
[6.1.2]: https://github.com/FabianBeiner/PHP-IMDB-Grabber/compare/v6.1.1...v6.1.2
[6.1.3]: https://github.com/FabianBeiner/PHP-IMDB-Grabber/compare/v6.1.2...v6.1.3
[6.1.4]: https://github.com/FabianBeiner/PHP-IMDB-Grabber/compare/v6.1.3...v6.1.4
[6.1.5]: https://github.com/FabianBeiner/PHP-IMDB-Grabber/compare/v6.1.4...v6.1.5
[6.1.6]: https://github.com/FabianBeiner/PHP-IMDB-Grabber/compare/v6.1.5...v6.1.6
[6.1.7]: https://github.com/FabianBeiner/PHP-IMDB-Grabber/compare/v6.1.6...v6.1.7
[Unreleased]: https://github.com/FabianBeiner/PHP-IMDB-Grabber/compare/v6.1.7...HEAD
