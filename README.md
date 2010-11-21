# PHP IMDB.com SCRAPER

**This class can be used to retrieve data from IMDB.com with PHP.**
It's a proof of concept, don't use it, since IMDB rules don't allow it!

The technique used is called “web scraping” (see [Wikipedia](http://en.wikipedia.org/wiki/Web_scraping "Web scraping") for details).
Which means: If IMDB changes *anything* on their HTML, the script is going to fail (even a single space might be enough)

You might not know, but there is an IMDB API available. The problem? You will have to pay at least $15.000 to use it. Great, thank you.


**If you want to thank me for my work and the support, feel free to do this through PayPal (use mail@fabian-beiner.de as payment destination) or just buy me a book at Amazon (http://www.amazon.de/wishlist/3IAUEEEY6GD20) – thank you! :-)**

## Changes

5.2.0

- Added series functions getSeasons() and getCreator()/getCreatorAsUrl() *(Coded by mali11011)*

5.1.1

- Fixed getCastAsUrl(); *(Reported by od3n)*

5.1.0

- Throws an exception if there is no posters/cache directory or cURL available
- Some code cleanup
- Added IMDB_LOCATION

5.0.4

- Removed/cleaned some variable names
- Fixed IMDB_POSTER regular expression
- Changed IMDB_PLOT regular expression

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
If you run into a problem, feel free to contact me. I will help you if my time allows it. However, support is not guaranteed.

I will only answer bug report if you provide me a detailed output of the failing script – please enable debug through setting "const IMDB_DEBUG = true;" in imdb.class.php.

## Usage

The usage of this script is simple. Just have a look at imdb.example.php – you will understand easily how it works.

## Example output (of imdb.example.php)

![Screenshot](http://img801.imageshack.us/img801/3749/imdbc.png "Screenshot of imdb.example.php output")
