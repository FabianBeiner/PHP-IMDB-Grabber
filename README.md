# PHP IMDb.com Grabber

**This class enables you to retrieve data from IMDb.com with PHP.**

*The script is a proof of concept. It's working pretty well, but you shouldn't use it since IMDb does not allow this method of data grabbing! Personally, I do not use or promote this script. You’re responsible IF you’re using it.*

The technique used is called “[web scraping](http://en.wikipedia.org/wiki/Web_scraping "Web scraping")”. That means: If IMDb changes anything on their HTML, the script is going to fail.

---

Did you know about the available IMDb.com API? The price to use it is around $15.000. This might be fine for commercial projects, but it's impossible to afford for private/non-commercial ones.

---

**If you want to thank me for my work and the support, feel free to do this through PayPal (use mail@fabian-beiner.de as payment destination) or just buy me a book at [Amazon](http://www.amazon.de/registry/wishlist/8840JITISN9L) – thank you! :-)**

## License

Since version 5.5.0 the script is licensed under [CC BY-NC-SA 3.0](http://creativecommons.org/licenses/by-nc-sa/3.0/).

## Changes

5.5.17
- *Heavy* reformating.
- Merged with the changes by *emersonbroga*. (Thanks!)
- Added getFullCast().
- A few smaller fixes.
- Fixed MPAA.

5.5.16
- Typos.

5.5.15
- Fixed IMDB_MPAA. *(Thanks to bla0r)*
- Added possibility to search only for TV shows etc. via IMDB_SEARCHFOR. *(Thanks to bla0r)* (Note: This should be setable via public variable someday ...)

5.5.14
- Fixed IMDB_DIRECTOR.
- Added IMDB_LANG (so you can define which Accept-Language header will be used). *(Thanks to bonk-se.)*
- Fixed IMDB_TITLE_ORIG. *(Thanks to Tamás)*

5.5.13
- Fixed some regular expressions (cast, name, title, year)
- Changed MPAA rating regex - then again, I don't know if this is MPAA or something else. I don't care. :)

5.5.12

- Now checking if there is a valid IMDb id given as search param (this should fix https://github.com/FabianBeiner/PHP-IMDB-Grabber/issues/29)
- Instead of using the URL as cache, now using the ID, as this is always the same.

5.5.11

- `getPoster('big')` is back. *(Thanks to Robert again)*

5.5.10

- Fixed IMDB_RUNTIME.
- Added getSeasonsAsUrl() and reversed the return of getSeasons (starting with Season 1).

5.5.9

- Updated almost every used regex. Gosh, this took me hours …
- `getTitle()` now returns the local movie name if called via `getTitle(true)`. Falls back to global one.
- Tried to fix MPAA rating. Should work, but who knows? ;)
- Removed the `getPoster('big')` parameter as this fails on most of the pictures. So call getPoster() without any parameter.

*(As this is a really heavy update, there will probably be a bunch of mistakes. But give it a try.)*


5.5.8

- Added option to download bigger images: `$oIMDb->getPoster('big')`; *(Thanks to Robert)*

5.5.7

- Fixed rating & search (again). *(Thanks to Chiel)*

5.5.6

- Fixed search.

5.5.5

- Now looking for an exact match first, instead of popular one.

5.5.4

- Fixed getSeasons()
- Added getAll (which returns ALL information as an object) *(Thanks to Brett Brewer)*

5.5.3

- Fixed getTrailerAsUrl() *(Thanks to luizrafael)*
- Fixed imdb.tests.php

5.5.2

- Fixed getSeasons() & $_strRoot.

5.5.1

- Fixed a bug in the caching system.
- Added getAspectRatio(), getOpening(), getSoundMix() and getSitesAsUrl($strTarget = '').
- Every getSomethingAsUrl got a option to add a target now.

5.5.0

- Fixed almost every function, like Cast, Color, Company, Country, Language, Director and Writer.
- Added $strSeperator which lets you define the seperator for lists (default is /).
- Did some code cleaning.
- Optimized local caching system.
- Added a small remote debugging feature.
- Added a trim to every possible return.
- Added getDescription() which returns a small description given by IMDb.
- Changed License. Which means basically: You're not allowed to use this for your commercial work.
-

5.4.3

- Fixed getCompanyAsUrl()

5.4.2

- Fixed title and trailer

5.4.1

- Fixed title
- Added getTrailer() and getAka() *(Thanks to Seifer Almasy)*

5.4.0

- Fixed Writer, Writer as URL and Votes
- Added getColor(), getCompany() and getCompanyAsUrl()

5.3.2

- Fixed Director, MPAA, Country & Language

5.3.1

- Fixed IMDB_VOTES regular expression *(Thanks to hareevs)*
- Tiny cleanup.

5.3.0

- Added ".redir"-suffix to the redirect caches
- Naming local posters is using the movie id now (instead of a cryptic md5 hash)
- Added variable to specific a string to return if movie is not found ($strNotFound)
- Removing cookie after it's not used anymore
- Some code tweaks

5.2.4

- Fixed a few functions in imdb.class.php
- Added gallery script *(Thanks to xsabianus)*
- Cleaned the search script

5.2.3

- And another fix for getCastAndCharacter(AsUrl())

5.2.2

- Forgot a link in getCastAndCharacterAsUrl() function

5.2.1

- Added getCastAndCharacter() and getCastAndCharacterAsUrl() *(Thanks to Taha Demirhan for snippets)*
- Added imdb.search.php - a small example search form *(Thanks to xsabianus)*

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
- Added a simple caching mechanism. Default is set to one day (1440 minutes). Feel free to change this: **new IMDB('Movie', 60)** (for one hour). This speeds up everything dramatically.
- Removed /10 from rating return

5.0.0

- **Complete rewrite**
- Added caching for redirects
- Fixed ALL regular expressions according to new IMDb layout
- Added getBudget function
- Added debug option

## Bugs?
If you run into a problem, feel free to contact me. I will help you if my time allows it. However, support is not guaranteed.

I will only answer bug report if you provide me a detailed output of the failing script – please enable debug through setting "**const IMDB_DEBUG = true;**" in imdb.class.php.

## Wishes?

Well, normally I do not update or fix parts of this script anymore. I do update it, if one of the few givers asks me for an update. But it's very unlikely that I'll add new features for just "anyone". But feel free to add your wish to the project wiki.

## Usage

The usage of this script is simple. Just have a look at imdb.example.php – you will understand easily how it works.

## Example output (of imdb.example.php)

![Screenshot](http://img801.imageshack.us/img801/3749/imdbc.png "Screenshot of imdb.example.php output")
