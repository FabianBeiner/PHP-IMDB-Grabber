# PHP IMDb.com Grabber

**This PHP library enables you to scrape data from IMDB.com.**

*This script is a proof of concept. It’s working, but you shouldn’t use it. IMDb doesn’t allow this method of data fetching. I do not use or promote this script. You’re responsible for using it.*

The technique used is called “[web scraping](http://en.wikipedia.org/wiki/Web_scraping "Web scraping at Wikipedia").”
 Which means, if IMDb changes any of their HTML, the script is going to fail. I won’t update this on a regular basis, so don’t count on it to be working all the time.

## License

[The MIT License (MIT)](https://fabianbeiner.mit-license.org/ "The MIT License")

## Example Usage

```php
<?php
include_once 'imdb.class.php';
$IMDB = new IMDB('Movie Title or IMDB URL');
if ($IMDB->isReady) {
    print_r($IMDB->getAll());
} else {
    echo 'Movie not found. 😞';
}
```

## Available Methods

**Get all available data**

`getAll()`

**Also Known As**

`getAka()`

**All local names**

`getAkas()`

**Aspect Ratio**

`getAspectRatio()`

**Awards**

`getAwards()`

**Budget**

`getBudget()`

**Cast**

`getCast($iLimit = 0, $bMore = true)` - `$iLimit` defines the maximum amount of people returned, `$bMore` if "…" should be added to the string if needed

**Cast Images**

`getCastImages($iLimit = 0, $bMore = true, $sSize = 'small', $bDownload = false)` - `$iLimit` defines the maximum amount of people returned, `$bMore` if "…" should be added to the string if needed, `$sSize` defines the size of the cast image "small, mid and big", `$bDownload` if the cast image should be downloaded or not

**Cast (with links)**

`getCastAsUrl($iLimit = 0, $bMore = true, $sTarget = '')` - `$iLimit` defines the maximum amount of people returned, `$bMore` if "…" should be added to the string if needed, `$sTarget` defines a target

**Cast and Character**

`getCastAndCharacter($iLimit = 0, $bMore = true)` - `$iLimit` defines the maximum amount of people returned, `$bMore` if "…" should be added to the string if needed

**Cast and Character (with links)**

`getCastAndCharacterAsUrl($iLimit = 0, $bMore = true, $sTarget = '')` - `$iLimit` defines the maximum amount of people returned, `$bMore` if "…" should be added to the string if needed, `$sTarget` defines a target

**Certification**

`getCertification()`

**Color**

`getColor()`

**Company**

`getCompany()`

**Company (with links)**

`getCompanyAsUrl($sTarget = '')` - `$sTarget` defines a target

**Country**

`getCountry()`

**Country (with links)**

`getCountryAsUrl($sTarget = '')` - `$sTarget` defines a target

**Creator**

`getCreator()`

**Creator (with links)**

`getCreatorAsUrl($sTarget = '')` - `$sTarget` defines a target

**Description**

`getDescription()`

**Director**

`getDirector()`

**Director (with links)**

`getDirectorAsUrl($sTarget = '')` - `$sTarget` defines a target

**Genre**

`getGenre()`

**Genre (with links)**

`getGenreAsUrl($sTarget = '')` - `$sTarget` defines a target

**Gross**

`getGross()` to get cumulative worldwide gross

**Language**

`getLanguage()`

**Language (with links)**

`getLanguageAsUrl($sTarget = '')` - `$sTarget` defines a target

**Location**

`getLocation()`

**Location (with links)**

`getLocationAsUrl($sTarget = '')` - `$sTarget` defines a target

**MPAA**

`getMpaa()`

**Plot**

`getPlot($iLimit = 0)` - `$iLimit` defines the maximum characters returned

**Plot Keywords**

`getPlotKeywords()`

**Poster**

`getPoster($sSize = 'small', $bDownload = true)` - `$sSize` defines small or big poster size, `$bDownload` if the poster should be downloaded or not

**Rating**

`getRating()`

**Release Date**

`getReleaseDate()`

**Release Dates**

`getReleaseDates()` returning all release dates for each country

**Runtime**

`getRuntime()`

**Seasons**

`getSeasons()`

**Seasons (with links)**

`getSeasonsAsUrl($sTarget = '')` - `$sTarget` defines a target

**Sound Mix**

`getSoundMix()`

**Tagline**

`getTagline()`

**Title**

`getTitle($bForceLocal = false)` - `$bForceLocal` tries to return the original name of the movie

**Trailer**

`getTrailerAsUrl($bEmbed = false)` - `$bEmbed` defines if you want to link to player directly or not.

**Url**

`getUrl()`

**User Review**

`getUserReview()`

**Votes**

`getVotes()`

**Writer**

`getWriter()`

**Writer (with links)**

`getWriterAsUrl($sTarget = '')` - `$sTarget` defines a target

**Year**

`getYear()`

## Bugs?

If you run into any malfunctions, feel free to submit an issue. Make sure to enable debugging: `const IMDB_DEBUG = true;` in `imdb.class.php`.
