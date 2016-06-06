# Change Log
All notable changes to this project will be documented in this file.

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

[Unreleased]: https://github.com/FabianBeiner/PHP-IMDB-Grabber/compare/v6.0.4...HEAD
[6.0.0]: https://github.com/FabianBeiner/PHP-IMDB-Grabber/compare/5.5.21...v6.0.0
[6.0.1]: https://github.com/FabianBeiner/PHP-IMDB-Grabber/compare/v6.0.0...v6.0.1
[6.0.2]: https://github.com/FabianBeiner/PHP-IMDB-Grabber/compare/v6.0.1...v6.0.2
[6.0.3]: https://github.com/FabianBeiner/PHP-IMDB-Grabber/compare/v6.0.2...v6.0.3
[6.0.4]: https://github.com/FabianBeiner/PHP-IMDB-Grabber/compare/v6.0.3...v6.0.4
