# PHP IMDB Grabber

This PHP class enables you to fetch / parse the most important data from IMDB.com.

The script will fail as soon as IMDB changes their layout. Let me know if this happens.

If you want to thank me for this script and the support, you can do this through PayPal (see email bellow) or just buy me something on Amazon (http://www.amazon.de/wishlist/3IAUEEEY6GD20) - **thank you**! :)

## Changes

5.0.3

- Fixed regular expression for title

5.0.2

- Added regular expression for original title (which I prefer instead of the localized one)

5.0.1

- Renamed 'redirects' to 'cache'
- Added a simple caching mechanism. Defaults to one day (1440 minutes). Feel free to change this: **new IMDB('Movie', 60)** (for one hour). This speeds up everything dramatically.
- Removed /10 from rating return

5.0.0

- **Complete rewrite**
- Added caching for redirects
- Fixed ALL regular expressions according to new IMDB layout
- Added getBudget function
- Added debug option

## Bugs?
If you're sending me bugs, please enable debug by setting "const IMDB_DEBUG = true;" and provide me the output.

## Usage

See the imdb.example.php file.

## Example output (of imdb.example.php)

![Screenshot](http://img148.imageshack.us/img148/5420/imdbd.png "Example output")
