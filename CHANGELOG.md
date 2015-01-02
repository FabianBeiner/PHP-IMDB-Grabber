# Change Log
All notable changes to this project will be documented in this file.

## [6.0.0] - 2014-12-27
### Changed
- Since this script is still heavily used by you guys, I decided to clean up the codebase. It’s still far away from being a great example of good code – but remember: This all started on a Saturday, when I was feeling sick and didn’t have anything else to do.
- Switched back to MIT license. People never cared that they’re not allowed to use this commercially, so I give up. **This is still a proof of concept and you should not use this in any way.**
- Removed all the 3rd party stuff.
- Moved `imdb.example.php` to the new directory *examples*. Same goes with `imdb.tests.php`, since this file doesn’t contain real “tests”.
- As it doesn’t matter anymore, I removed the changelog of everything below v6.0.0.
- “To use [Yoda conditions](http://en.wikipedia.org/wiki/Yoda_conditions) as much as possible trying.”
- Removed `getBudget()` and `getDescription` *(use `getPlot()` instead!)*, since these values are not printed on the combined page.
- Removed `getFullCast()`, because `getCast()` now returns the full cast.
- tbc.

[Unreleased]: https://github.com/FabianBeiner/PHP-IMDB-Grabber/compare/v6.0.0...HEAD
[6.0.0]: https://github.com/FabianBeiner/PHP-IMDB-Grabber/compare/5.5.20...v6.0.0