<?php
/**
 * IMDB PHP Parser
 *
 * This class can be used to retrieve data from IMDB.com with PHP.
 * This script will fail once in a while, after IMDB changes *anything* on their
 * HTML. The fact, that there is an API provided by IMDB but you're not allowed
 * to use it just sucks.
 *
 * If you want to thank me for this script and the support, you can do this
 * through PayPal (see email bellow) or just buy me something on Amazon
 * (http://www.amazon.de/wishlist/3IAUEEEY6GD20) - thank you! :)
 *
 * @link http://fabian-beiner.de
 * @copyright 2010 Fabian Beiner
 * @author Fabian Beiner (mail@fabian-beiner.de)
 * @license MIT License
 *
 * @version 5.0.3 (October 13th, 2010)
*/

class IMDB {
    // Debug?
    const IMDB_DEBUG = false;
    // cURL Timeout
    const IMDB_TIMEOUT = 15;

    // Regular expressions
    const IMDB_CAST         = '~<td class="name">\s+<a\s+href="/name/nm(\d+)/">(.*)</a>\s+</td~Ui';
    const IMDB_COUNTRY      = '~<a href="/country/(\w+)">(.*)</a>~Ui';
    const IMDB_DIRECTOR     = '~<h4 class="inline">\s+(Director|Directors):\s+</h4>(.*)</div><div~Ui';
    const IMDB_GENRE        = '~<a href="/genre/(.*)"~Ui';
    const IMDB_MPAA         = '~<h4>Motion Picture Rating \(<a href="/mpaa">MPAA</a>\)</h4>(.*) <span~Ui';
    const IMDB_PLOT         = '~<h2>Storyline</h2><p>(.*)<em class="nobr">~Ui';
    const IMDB_POSTER       = '~<a href="/media/(.*)"><img src="(.*)"~Ui';
    const IMDB_RATING       = '~<span class="rating-rating">(\d+\.\d+)<span>~Ui';
    const IMDB_RELEASE_DATE = '~Release Date:</h4>(.*)</div>~Ui';
    const IMDB_RUNTIME      = '~(\d+)\smin~Uis';
    const IMDB_SEARCH       = '~<b>Media from&nbsp;<a href="/title/tt(\d+)/"~i';
    const IMDB_TAGLINE      = '~<h4 class="inline">Taglines:</h4>(.*)(<[^>]+>)~Ui';
    const IMDB_TITLE        = '~<title>(.*) \((.*)\).*~Ui';
    const IMDB_TITLE_ORIG   = '~<span class="title-extra">(.*) <i>\(original title\)</i></span>~Ui';
    const IMDB_URL          = '~http://(.*\.|.*)imdb.com/(t|T)itle(\?|/)(..\d+)~i';
    const IMDB_VOTES        = '~>(\d+|\d+,\d+) votes</a>\)~Ui';
    const IMDB_WRITER       = '~<h4 class="inline">\s+(Writer|Writers):(.*)</div><div~Ui';
    const IMDB_REDIRECT     = '~Location:\s(.*)~';
    const IMDB_LANGUAGES    = '~<a href="/language/(\w+)">(.*)</a>~Ui';
    const IMDB_BUDGET       = '~Budget:</h4> (.*)\(estimated\)~Ui';
    const IMDB_NAME         = '~href="/name/nm(\d+)/">(.*)</a>~Ui';

    // cURL cookie file
    private $fCookie  = false;

    // IMDB url
    private $strUrl    = NULL;
    // IMDB source
    private $strSource = NULL;
    // IMDB id
    private $intId     = NULL;
    // IMDB cache
    private $intCache  = 0;
    // IMDB posters directory
    private $bolPoster = NULL;
    // IMDB cache directory
    private $bolCache  = NULL;

    // Movie found?
    public $isReady    = false;

    /**
     * IMDB constructor.
     *
     * @param string  $strSearch The movie name / IMDB url
     * @param integer $intCache  The maximum age (in minutes) of a cache
     */
    public function __construct($strSearch, $intCache = 1440) {
        // Cookie path.
        if (function_exists('sys_get_temp_dir')) {
            $this->fCookie = tempnam(sys_get_temp_dir(), 'imdb');
            if (IMDB::IMDB_DEBUG) echo '<b>- Path to cookie:</b> ' . $this->fCookie . '<br>';
        }
        // Posters and cache directory existant?
        if (!file_exists(getcwd() . '/posters/')) {
            if (mkdir(getcwd() . '/posters/', 0777)) {
                $this->bolPoster = true;
            }
        }
        elseif (is_writeable(getcwd() . '/posters/')) {
            $this->bolPoster = true;
        }
        else {
            return;
        }
        if (!file_exists(getcwd() . '/cache/')) {
            if (mkdir(getcwd() . '/cache/', 0777)) {
                $this->bolCache = true;
            }
        }
        elseif (is_writeable(getcwd() . '/cache/')) {
            $this->bolCache = true;
        }
        else {
            return;
        }
        // Debug only.
        if (IMDB::IMDB_DEBUG) {
          error_reporting(E_ALL);
          ini_set('display_errors', '1');
          echo '<b>- Running:</b> IMDB::fetchUrl<br>';
        }
        // Set global cache and fetch the data.
        $this->intCache = (int)$intCache;
        IMDB::fetchUrl($strSearch);
    }

    /**
     * Regular expressions helper function.
     *
     * @param string  $strContent The content to search in
     * @param string  $strRegex   The regular expression
     * @param integer $intIndex   The index to return
     * @return string The match found
     * @return array  The matches found
     */
    private function matchRegex($strContent, $strRegex, $intIndex = null) {
        $arrMatches = null;
        preg_match_all($strRegex, $strContent, $arrMatches);
        if ($arrMatches === FALSE) return;
        if ($intIndex != null && is_int($intIndex)) {
            if ($arrMatches[$intIndex]) {
              return $arrMatches[$intIndex][0];
            }
            return;
        }
        return $arrMatches;
    }

    /**
     * Returns a shortened text.
     *
     * @param string  $strText   The text to shorten
     * @param integer $intLength The new length of the text
     */
    public function makeShort($strText, $intLength = 100) {
        $strText = trim($strText) . ' ';
        $strText = substr($strText, 0, $intLength);
        $strText = substr($strText, 0, strrpos($strText, ' '));
        return $strText . '&hellip;';
    }

    /**
     * Fetch data from the given url.
     *
     * @param string  $strSearch The movie name / IMDB url
     * @param string  $strSave   The path to the file
     * @return boolean
     */
    private function fetchUrl($strSearch) {
        // Remove whitespaces.
        $strSearch = trim($strSearch);

        // Check for a valid IMDB URL and use it, if available.
        if ($strId = IMDB::matchRegex($strSearch, IMDB::IMDB_URL, 4)) {
            $this->strUrl = 'http://www.imdb.com/title/tt' . preg_replace('~[\D]~', '', $strId) . '/';
            $this->isReady = true;
        }
        // Otherwise try to find one.
        else {
            $this->strUrl = 'http://www.imdb.com/find?s=all&q=' . str_replace(' ', '+', $strSearch);
            // Check for cached redirects of this search.
            $fRedirect = getcwd() . '/cache/' . md5($this->strUrl);
            if (file_exists($fRedirect) && is_readable($fRedirect)) {
                $fRedirect = file_get_contents($fRedirect);
                if (IMDB::IMDB_DEBUG) echo '<b>- Found an old redirect:</b> ' . $fRedirect . '<br>';
                $this->strUrl = $fRedirect;
                $this->isReady = true;
            }
        }

        $fCache = getcwd() . '/cache/' . md5($this->strUrl) . '.cache';

        // Check if there is a cache we can use.
        $bolNewRequest = false;
        if (file_exists($fCache)) {
            $intChanged = filemtime($fCache);
            $intNow     = time();
            $intDiff    = $intNow - $intChanged;
            $intCache   = $this->intCache * 60;
            if ($intCache >= $intDiff) {
                $bolNewRequest = true;
            }
        }

        if ($bolNewRequest) {
            if (IMDB::IMDB_DEBUG) echo '<b>- Using cache for ' . $strSearch . ' from . ' . $fCache . '</b><br>';
            $this->strSource = file_get_contents($fCache);
            return true;
        }
        // Check if cURL is installed.
        elseif (function_exists('curl_init')) {
            // Initialize and run the request.
            if (IMDB::IMDB_DEBUG) echo '<b>- Run cURL on:</b> ' . $this->strUrl . '<br>';
            $oCurl = curl_init($this->strUrl);
            curl_setopt_array($oCurl, array (
                                            CURLOPT_VERBOSE => FALSE,
                                            CURLOPT_HEADER => TRUE,
                                            CURLOPT_FRESH_CONNECT => TRUE,
                                            CURLOPT_RETURNTRANSFER => TRUE,
                                            CURLOPT_TIMEOUT => IMDB::IMDB_TIMEOUT,
                                            CURLOPT_CONNECTTIMEOUT => 0,
                                            CURLOPT_REFERER => 'http://www.google.com',
                                            CURLOPT_USERAGENT, 'Googlebot/2.1 (+http://www.google.com/bot.html)',
                                            CURLOPT_FOLLOWLOCATION => FALSE,
                                            CURLOPT_COOKIEFILE => $this->fCookie
                                            ));
            $strOutput = curl_exec($oCurl);
            $this->strSource = $strOutput;

            // Check if the request actually worked.
            if ($strOutput === FALSE) {
                if (IMDB::IMDB_DEBUG) echo '<b>! cURL error:</b> ' . $strUrl . '<br>';
                if (file_exists($fCache)) {
                     $this->strSource = file_get_contents($fCache);
                    return true;
                }
                return;
            }

            // Get returned information.
            $arrInfo = curl_getinfo($oCurl);
            curl_close($oCurl);

            // Check if there is a redirect given (IMDB sometimes does not return 301 for this...).
            if ($strMatch = $this->matchRegex($strOutput, IMDB::IMDB_REDIRECT, 1)) {
                if (IMDB::IMDB_DEBUG) echo '<b>- Found a redirect:</b> ' . $strMatch . '<br>';
                // Try to save the redirect for later usage.
                $fRedirect = getcwd() . '/cache/' . md5($this->strUrl);
                if (IMDB::IMDB_DEBUG) echo '<b>- Saved a new redirect:</b> ' . $fRedirect . '<br>';
                file_put_contents($fRedirect, $strMatch);
                // Run the cURL request again with the new url.
                IMDB::fetchUrl($strMatch);
            }
            // Lets assume the first search result is what we want. :)
            elseif ($strMatch = $this->matchRegex($strOutput, IMDB::IMDB_SEARCH, 1)) {
                $strMatch = 'http://www.imdb.com/title/tt' . $strMatch . '/';
                if (IMDB::IMDB_DEBUG) echo '<b>- Using the first search result:</b> ' . $strMatch . '<br>';
                // Try to save the redirect for later usage.
                $fRedirect = getcwd() . '/cache/' . md5($this->strUrl);
                if (IMDB::IMDB_DEBUG) echo '<b>- Saved a new redirect:</b> ' . $fRedirect . '<br>';
                file_put_contents($fRedirect, $strMatch);
                // Run the cURL request again with the new url.
                IMDB::fetchUrl($strMatch);
            }
            // If it's not a redirect and the HTTP response is not 200 or 302, abort.
            elseif ($arrInfo['http_code'] != 200 && $arrInfo['http_code'] != 302) {
                if (IMDB::IMDB_DEBUG) echo '<b>- Wrong HTTP code received, aborting:</b> ' . $arrInfo['http_code'] . '<br>';
                return;
            }

            // Set the global source.
            $this->strSource = preg_replace('~(\r|\n|\r\n)~', '', $this->strSource);

            // Save cache.
            if (IMDB::IMDB_DEBUG) echo '<b>- Saved a new cache:</b> ' . $fCache . '<br>';
            file_put_contents($fCache, $this->strSource);

            return true;
        }
        return;
    }

    /**
     * Save the image locally.
     *
     * @param string $strUrl The URL to the image on imdb
     * @param integer $intId The IMDB id of the movie
     * @return string The local path to the image
     */
    private function saveImage($strUrl) {
        $strUrl = trim($strUrl);

        if (preg_match('/imdb-share-logo.gif/', $strUrl)) {
            if (file_exists('posters/not-found.jpg')) {
                return 'posters/not-found.jpg';
            }
            return $strUrl;
        }

        $strFilename = getcwd() . '/posters/' . md5(IMDB::getTitle()) . '.jpg';
        if (file_exists($strFilename)) {
            return 'posters/' . md5(IMDB::getTitle()) . '.jpg';
        }
        if (function_exists('curl_init')) {
            $oCurl = curl_init($strUrl);
            curl_setopt_array($oCurl, array (
                                            CURLOPT_VERBOSE => FALSE,
                                            CURLOPT_HEADER => FALSE,
                                            CURLOPT_RETURNTRANSFER => TRUE,
                                            CURLOPT_TIMEOUT => IMDB::IMDB_TIMEOUT,
                                            CURLOPT_CONNECTTIMEOUT => IMDB::IMDB_TIMEOUT,
                                            CURLOPT_REFERER => $strUrl,
                                            CURLOPT_BINARYTRANSFER => TRUE));
            $sOutput = curl_exec($oCurl);
            curl_close($oCurl);
            $oFile = fopen($strFilename, 'x');
            fwrite($oFile, $sOutput);
            fclose($oFile);
            return 'posters/' . md5(IMDB::getTitle()) . '.jpg';
        } else {
            $oImg = imagecreatefromjpeg($strUrl);
            imagejpeg($oImg, $strFilename);
            return 'posters/' . md5(IMDB::getTitle()) . '.jpg';
        }
        return $strUrl;
    }

    /**
     * Returns the budget.
     *
     * @return string The movie budget.
     */
    public function getBudget() {
        if ($this->isReady) {
            if ($strReturn = $this->matchRegex($this->strSource, IMDB::IMDB_BUDGET, 1)) {
                return $strReturn;
            }
            return 'n/A';
        }
        return 'n/A';
    }

    /**
     * Returns the cast.
     *
     * @return array The movie cast (default limited to 20).
     */
    public function getCast($intLimit = 20, $bolMore = true) {
        if ($this->isReady) {
            $arrReturned = $this->matchRegex($this->strSource, IMDB::IMDB_CAST);
            if (count($arrReturned[2])) {
                foreach ($arrReturned[2] as $i => $strName) {
                    if ($i >= $intLimit) break;
                    $arrReturn[] = $strName;
                }
                return implode(' / ', $arrReturn) . ($bolMore && (count($arrReturned[2]) > $intLimit) ? '&hellip;' : '');
            }
            return 'n/A';
        }
        return 'n/A';
    }

    /**
     * Returns the cast as URL.
     *
     * @return array The movie cast as URL (default limited to 20).
     */
    public function getCastAsUrl($intLimit = 20, $bolMore = true) {
        if ($this->isReady) {
            $arrReturned = $this->matchRegex($this->strSource, IMDB::IMDB_CAST);
            if (count($arrReturned[2])) {
                foreach ($arrReturned[2] as $i => $strName) {
                    if ($i >= $intLimit) break;
                    $arrReturn[] = '<a href="http://www.imdb.com/name/' . $arrReturned[1][$i] . '/">' . $strName . '</a>';
                }
                return implode(' / ', $arrReturn) . ($bolMore && (count($arrReturned[2]) > $intLimit) ? '&hellip;' : '');
            }
            return 'n/A';
        }
        return 'n/A';
    }

    /**
     * Returns the countr(y|ies).
     *
     * @return array The movie countr(y|ies).
     */
    public function getCountry() {
        if ($this->isReady) {
            $arrReturned = $this->matchRegex($this->strSource, IMDB::IMDB_COUNTRY);
            if (count($arrReturned[2])) {
                foreach ($arrReturned[2] as $strName) {
                    $arrReturn[] = $strName;
                }
                return implode(' / ', $arrReturn);
            }
            return 'n/A';
        }
        return 'n/A';
    }

    /**
     * Returns the countr(y|ies) as URL
     *
     * @return array The movie countr(y|ies) as URL.
     */
    public function getCountryAsUrl() {
        if ($this->isReady) {
            $arrReturned = $this->matchRegex($this->strSource, IMDB::IMDB_COUNTRY);
            if (count($arrReturned[2])) {
                foreach ($arrReturned[2] as $i => $strName) {
                    $arrReturn[] = '<a href="http://www.imdb.com/country/' . $arrReturned[1][$i] . '/">' . $strName . '</a>';
                }
                return implode(' / ', $arrReturn);
            }
            return 'n/A';
        }
        return 'n/A';
    }

    /**
     * Returns the director(s).
     *
     * @return array The movie director(s).
     */
    public function getDirector() {
        if ($this->isReady) {
            $strContainer = $this->matchRegex($this->strSource, IMDB::IMDB_DIRECTOR, 2);
            $arrReturned  = $this->matchRegex($strContainer, IMDB::IMDB_NAME);
            if (count($arrReturned[2])) {
                foreach ($arrReturned[2] as $i => $strName) {
                    $arrReturn[] = $strName;
                }
                return implode(' / ', $arrReturn);
            }
            return 'n/A';
        }
        return 'n/A';
    }

    /**
     * Returns the director(s) as URL.
     *
     * @return array The movie director(s) as URL.
     */
    public function getDirectorAsUrl() {
        if ($this->isReady) {
            $strContainer = $this->matchRegex($this->strSource, IMDB::IMDB_DIRECTOR, 2);
            $arrReturned = $this->matchRegex($strContainer, IMDB::IMDB_NAME);
            if (count($arrReturned[2])) {
                foreach ($arrReturned[2] as $i => $strName) {
                    $arrReturn[] = '<a href="http://www.imdb.com/name/nm' . $arrReturned[1][$i] . '/">' . $strName . '</a>';
                }
                return implode(' / ', $arrReturn);
            }
            return 'n/A';
        }
        return 'n/A';
    }

    /**
     * Returns the genre(s).
     *
     * @return array The movie genre(s).
     */
    public function getGenre() {
        if ($this->isReady) {
            $arrReturned = $this->matchRegex($this->strSource, IMDB::IMDB_GENRE);
            if (count($arrReturned[1])) {
               foreach ($arrReturned[1] as $strName) {
                    $arrReturn[] = $strName;
                }
                return implode(' / ', $arrReturn);
            }
            return 'n/A';
        }
        return 'n/A';
    }

    /**
     * Returns the genres as URL.
     *
     * @return array The movie genre as URL.
     */
    public function getGenreAsUrl() {
        if ($this->isReady) {
            $arrReturned = $this->matchRegex($this->strSource, IMDB::IMDB_GENRE);
            if (count($arrReturned[1])) {
               foreach ($arrReturned[1] as $i => $strName) {
                    $arrReturn[] = '<a href="http://www.imdb.com/genre/' . $strName . '/">' . $strName . '</a>';
                }
                return implode(' / ', $arrReturn);
            }
            return 'n/A';
        }
        return 'n/A';
    }

    /**
     * Returns the language(s).
     *
     * @return string The movie language(s).
     */
    public function getLanguages() {
        if ($this->isReady) {
            $arrReturned = $this->matchRegex($this->strSource, IMDB::IMDB_LANGUAGES);
            if (count($arrReturned[2])) {
                foreach ($arrReturned[2] as $strName) {
                    $arrReturn[] = $strName;
                }
                return implode(' / ', $arrReturn);
            }
            return 'n/A';
        }
        return 'n/A';
    }

    /**
     * Returns the language(s) as URL.
     *
     * @return string The movie language(s) as URL.
     */
    public function getLanguagesAsUrl() {
        if ($this->isReady) {
            $arrReturned = $this->matchRegex($this->strSource, IMDB::IMDB_LANGUAGES);
            if (count($arrReturned[2])) {
                foreach ($arrReturned[2] as $i => $strName) {
                    $arrReturn[] = '<a href="http://www.imdb.com/language/' . $arrReturned[1][$i] . '">' . $strName . '</a>';
                }
                return implode(' / ', $arrReturn);
            }
            return 'n/A';
        }
        return 'n/A';
    }

    /**
     * Returns the MPAA.
     *
     * @return string The movie MPAA.
     */
    public function getMpaa() {
        if ($this->isReady) {
            if ($strReturn = $this->matchRegex($this->strSource, IMDB::IMDB_MPAA, 1)) {
                return $strReturn;
            }
            return 'n/A';
        }
        return 'n/A';
    }

    /**
     * Returns the plot.
     *
     * @return string The movie plot.
     */
    public function getPlot($intLimit = 0) {
        if ($this->isReady) {
            if ($strReturn = $this->matchRegex($this->strSource, IMDB::IMDB_PLOT, 1)) {
                if ($intLimit) {
                    return $this->makeShort($strReturn, $intLimit);
                }
                return $strReturn;
            }
            return 'n/A';
        }
        return 'n/A';
    }

    /**
     * Download the poster, cache it and return the local path to the image.
     *
     * @return string The path to the poster (either local or online).
     */
    public function getPoster() {
       if ($this->isReady) {
            if ($strReturn = $this->matchRegex($this->strSource, IMDB::IMDB_POSTER, 2)) {
                if ($strLocal = $this->saveImage($strReturn)) {
                    return $strLocal;
                }
                return $strReturn;
            }
           return 'n/A';
        }
        return 'n/A';
    }

    /**
     * Returns the rating.
     *
     * @return string The movie rating.
     */
    public function getRating() {
        if ($this->isReady) {
            if ($strReturn = $this->matchRegex($this->strSource, IMDB::IMDB_RATING, 1)) {
                return $strReturn;
            }
            return 'n/A';
        }
        return 'n/A';
    }

    /**
     * Returns the release date.
     *
     * @return string The movie release date.
     */
    public function getReleaseDate() {
        if ($this->isReady) {
            if ($strReturn = $this->matchRegex($this->strSource, IMDB::IMDB_RELEASE_DATE, 1)) {
                return str_replace('(', ' (', $strReturn);
            }
            return 'n/A';
        }
        return 'n/A';
    }

    /**
     * Returns the runtime.
     *
     * @return string The movie runtime.
     */
    public function getRuntime() {
        if ($this->isReady) {
            if ($strReturn = $this->matchRegex($this->strSource, IMDB::IMDB_RUNTIME, 1)) {
                return $strReturn;
            }
            return 'n/A';
        }
        return 'n/A';
    }

    /**
     * Returns the tagline.
     *
     * @return string The movie tagline.
     */
    public function getTagline() {
        if ($this->isReady) {
            if ($strReturn = $this->matchRegex($this->strSource, IMDB::IMDB_TAGLINE, 1)) {
                return $strReturn;
            }
            return 'n/A';
        }
        return 'n/A';
    }

    /**
     * Return the title.
     *
     * @return string The movie title.
     */
    public function getTitle() {
        if ($this->isReady) {
            if ($strReturn = $this->matchRegex($this->strSource, IMDB::IMDB_TITLE_ORIG, 1)) {
                return $strReturn;
            }
            if ($strReturn = $this->matchRegex($this->strSource, IMDB::IMDB_TITLE, 1)) {
                return $strReturn;
            }
            return 'n/A';
        }
        return 'n/A';
    }

    /**
     * Returns the URL.
     *
     * @return string The movie URL.
     */
    public function getUrl() {
        return $this->strUrl;
    }

    /**
     * Returns the votes.
     *
     * @return string The votes of the movie.
     */
    public function getVotes() {
        if ($this->isReady) {
            if ($strReturn = $this->matchRegex($this->strSource, IMDB::IMDB_VOTES, 1)) {
                return $strReturn;
            }
            return 'n/A';
        }
        return 'n/A';
    }

    /**
     * Returns the writer(s).
     *
     * @return array The movie writer(s).
     */
    public function getWriter() {
        if ($this->isReady) {
            $strContainer = $this->matchRegex($this->strSource, IMDB::IMDB_WRITER, 2);
            $arrReturned  = $this->matchRegex($strContainer, IMDB::IMDB_NAME);
            if (count($arrReturned[2])) {
                foreach ($arrReturned[2] as $i => $strName) {
                    $arrReturn[] = $strName;
                }
                return implode(' / ', $arrReturn);
            }
            return 'n/A';
        }
        return 'n/A';
    }

    /**
     * Returns the writer(s) as URL.
     *
     * @return array The movie writer(s) as URL.
     */
    public function getWriterAsUrl() {
        if ($this->isReady) {
            $strContainer = $this->matchRegex($this->strSource, IMDB::IMDB_WRITER, 2);
            $arrReturned  = $this->matchRegex($strContainer, IMDB::IMDB_NAME);
            if (count($arrReturned[2])) {
                foreach ($arrReturned[2] as $i => $strName) {
                    $arrReturn[] = '<a href="http://www.imdb.com/name/nm' . $arrReturned[1][$i] . '/">' . $strName . '</a>';
                }
                return implode(' / ', $arrReturn);
            }
            return 'n/A';
        }
        return 'n/A';
    }

    /**
     * Returns the movie year.
     *
     * @return string The year of the movie.
     */
    public function getYear() {
        if ($this->isReady) {
            if ($strReturn = $this->matchRegex($this->strSource, IMDB::IMDB_TITLE, 2)) {
                return substr(preg_replace('~[\D]~', '', $strReturn), 0, 4);
            }
            return 'n/A';
        }
        return 'n/A';
    }
}
