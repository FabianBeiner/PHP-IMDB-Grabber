<?php
/**
 * PHP-IMDB-Grabber -- a PHP IMDb.com scraper
 *
 * This class can be used to retrieve data from IMDb.com with PHP.
 *
 * The technique used is called “web scraping”
 * (see http://en.wikipedia.org/wiki/Web_scraping for details).
 * Which means: If IMDb changes *anything* on their HTML, the script is going to
 * fail (even a single space might be enough).
 *
 * You might not know, but there is an IMDb API available. The problem?
 * You will have to pay at least $15.000 to use it. Great, thank you.
 *
 *
 * If you want to thank me for my work and the support, feel free to do this
 * through PayPal (use mail@fabian-beiner.de as payment destination) or just
 * buy me a book at Amazon (http://www.amazon.de/wishlist/3IAUEEEY6GD20)
 * – thank you! :-)
 *
 *
 * @link http://fabian-beiner.de
 * @copyright 2011 Fabian Beiner
 * @author Fabian Beiner (mail@fabian-beiner.de)
 * @license MIT License
 *
 * @version 5.3.0 (March 26th, 2011)
*/

class IMDBException extends Exception {}

class IMDB {
    // Define what to return if something is not found.
    public $strNotFound = 'n/A';
    // Please set this to 'true' for debugging purposes only.
    const IMDB_DEBUG    = false;
    // Define a timeout for the request of the IMDb page.
    const IMDB_TIMEOUT  = 15;

    // Regular expressions, I would not touch them. :)
    const IMDB_BUDGET       = '~Budget:</h4> (.*)\(estimated\)~Ui';
    const IMDB_CAST         = '~<td class="name">\s+<a\s+href="/name/nm(\d+)/">(.*)</a>\s+</td~Ui';
    const IMDB_CHAR         = '~<td class="character">(.*)</td~Ui';
    const IMDB_COUNTRY      = '~<a href="/country/(\w+)">(.*)</a>~Ui';
    const IMDB_CREATOR      = '~<h4 class="inline">\s+(Creator|Creators):\s+</h4>(.*)</div><div~Ui';
    const IMDB_DIRECTOR     = '~<h4 class="inline">\s+(Director|Directors):\s+</h4>(.*)</div><div~Ui';
    const IMDB_GENRE        = '~<a href="/genre/(.*)"~Ui';
    const IMDB_LANGUAGES    = '~<a href="/language/(\w+)">(.*)</a>~Ui';
    const IMDB_LOCATION     = '~<h4 class="inline">Filming Locations:</h4> <a href="/search/title\?locations=(.*)">(.*)</a>~Ui';
    const IMDB_MPAA         = '~<h4>Motion Picture Rating \(<a href="/mpaa">MPAA</a>\)</h4>(.*) <span~Ui';
    const IMDB_NAME         = '~href="/name/nm(\d+)/">(.*)</a>~Ui';
    const IMDB_PLOT         = '~<h2>Storyline</h2><p>(.*)(<em class="nobr">|</p>)~Ui';
    const IMDB_POSTER       = '~href="/media/(.*)"\s+><img src="(.*)"~Ui';
    const IMDB_RATING       = '~<span class="rating-rating"><span class="value".*?>(\d+\.\d+)<span>~Ui';
    const IMDB_REDIRECT     = '~Location:\s(.*)~';
    const IMDB_RELEASE_DATE = '~Release Date:</h4>(.*)(<span|</div>)~Ui';
    const IMDB_RUNTIME      = '~(\d+)\smin~Uis';
    const IMDB_SEARCH       = '~<b>Media from&nbsp;<a href="/title/tt(\d+)/"~i';
    const IMDB_SEASONS      = '~<h4 class="inline">Season: </h4><span class="see-more inline">(.*)</div><div~Ui';
    const IMDB_TAGLINE      = '~<h4 class="inline">Taglines:</h4>(.*)(<[^>]+>)~Ui';
    const IMDB_TITLE        = '~<title>(.*) \((.*)\).*~Ui';
    const IMDB_TITLE_ORIG   = '~<span class="title-extra">(.*) <i>\(original title\)</i></span>~Ui';
    const IMDB_URL          = '~http://(.*\.|.*)imdb.com/(t|T)itle(\?|/)(..\d+)~i';
    const IMDB_VOTES        = '~>(\d+|\d+,\d+) votes</a>\)~Ui';
    const IMDB_WRITER       = '~<h4 class="inline">\s+(Writer|Writers):(.*)</div><div~Ui';

    // cURL cookie file.
    private $_fCookie   = false;
    // IMDb url.
    private $_strUrl    = NULL;
    // IMDb source.
    private $_strSource = NULL;
    // IMDb cache.
    private $_strCache  = 0;
    // IMDb posters directory.
    private $_bolPoster = false;
    // IMDb cache directory.
    private $_bolCache  = false;
    // IMDb movie id.
    private $_strId     = false;
    // Movie found?
    public $isReady     = false;

    /**
     * IMDB constructor.
     *
     * @param string  $strSearch The movie name / IMDb url
     * @param integer $intCache  The maximum age (in minutes) of the cache (default 1 day)
     */
    public function __construct($strSearch, $intCache = 1440) {
        // Posters and cache directory existant?
        if (is_writable(getcwd() . '/posters/') || mkdir(getcwd() . '/posters/')) {
            $this->_bolPoster = true;
        }
        else {
            throw new IMDBException(getcwd() . '/posters/ is not writable!');
        }
        if (is_writable(getcwd() . '/cache/') || mkdir(getcwd() . '/cache/')) {
            $this->_bolCache = true;
        }
        else {
            throw new IMDBException(getcwd() . '/cache/ is not writable!');
        }
        // cURL.
        if (!function_exists('curl_init')) {
            throw new IMDBException('You need PHP with cURL enabled to use this script!');
        }
        // Debug only.
        if (IMDB::IMDB_DEBUG) {
          error_reporting(-1);
          ini_set('display_errors', 1);
          echo '<b>- Running:</b> IMDB::fetchUrl<br>';
        }
        // Set global cache and fetch the data.
        $this->_intCache = (int)$intCache;
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
        if ($arrMatches === FALSE) return false;
        if ($intIndex != null && is_int($intIndex)) {
            if ($arrMatches[$intIndex]) {
              return $arrMatches[$intIndex][0];
            }
            return false;
        }
        return $arrMatches;
    }

    /**
     * Returns a shortened text.
     *
     * @param string  $strText   The text to shorten
     * @param integer $intLength The new length of the text
     */
    public function getShortText($strText, $intLength = 100) {
        $strText = trim($strText) . ' ';
        $strText = substr($strText, 0, $intLength);
        $strText = substr($strText, 0, strrpos($strText, ' '));
        return $strText . '&hellip;';
    }

    /**
     * Fetch data from the given url.
     *
     * @param string  $strSearch The movie name / IMDb url
     * @param string  $strSave   The path to the file
     * @return boolean
     */
    private function fetchUrl($strSearch) {
        // Remove whitespaces.
        $strSearch = trim($strSearch);

        // Check for a valid IMDb URL and use it, if available.
        if ($strId = IMDB::matchRegex($strSearch, IMDB::IMDB_URL, 4)) {
            $this->_strUrl = 'http://www.imdb.com/title/tt' . preg_replace('~[\D]~', '', $strId) . '/';
            $this->_strId  = preg_replace('~[\D]~', '', $strId);
            $this->isReady = true;
        }
        // Otherwise try to find one.
        else {
            $this->_strUrl = 'http://www.imdb.com/find?s=all&q=' . str_replace(' ', '+', $strSearch);
            // Check for cached redirects of this search.
            if ($fRedirect = @file_get_contents(getcwd() . '/cache/' . md5($this->_strUrl) . '.redir')) {
                if (IMDB::IMDB_DEBUG) echo '<b>- Found an old redirect:</b> ' . $fRedirect . '<br>';
                $this->_strUrl = trim($fRedirect);
                $this->_strId  = preg_replace('~[\D]~', '', IMDB::matchRegex($fRedirect, IMDB::IMDB_URL, 4));
                $this->isReady = true;
            }
        }

        // Check if there is a cache we can use.
        $fCache = getcwd() . '/cache/' . md5($this->_strUrl) . '.cache';
        $bolNewRequest = false;
        if (file_exists($fCache)) {
            $intChanged = filemtime($fCache);
            $intNow     = time();
            $intDiff    = $intNow - $intChanged;
            $intCache   = $this->_intCache * 60;
            if ($intCache >= $intDiff) {
                $bolNewRequest = true;
            }
        }

        if ($bolNewRequest) {
            if (IMDB::IMDB_DEBUG) echo '<b>- Using cache for ' . $strSearch . ' from ' . $fCache . '</b><br>';
            $this->_strSource = file_get_contents($fCache);
            return true;
        }
        else {
            // Cookie path.
            if (function_exists('sys_get_temp_dir')) {
                $this->_fCookie = tempnam(sys_get_temp_dir(), 'imdb');
                if (IMDB::IMDB_DEBUG) echo '<b>- Path to cookie:</b> ' . $this->_fCookie . '<br>';
            }
            // Initialize and run the request.
            if (IMDB::IMDB_DEBUG) echo '<b>- Run cURL on:</b> ' . $this->_strUrl . '<br>';
            $oCurl = curl_init($this->_strUrl);
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
                                            CURLOPT_COOKIEFILE => $this->_fCookie
                                            ));
            $strOutput = curl_exec($oCurl);
            $this->_strSource = $strOutput;

            // Remove cookie.
            if ($this->_fCookie) {
                unlink($this->_fCookie);
            }

            // Check if the request actually worked.
            if ($strOutput === FALSE) {
                if (IMDB::IMDB_DEBUG) echo '<b>! cURL error:</b> ' . $_strUrl . '<br>';
                if ($this->_strSource = @file_get_contents($fCache)) {
                    return true;
                }
                return false;
            }

            // Get returned information.
            $arrInfo = curl_getinfo($oCurl);
            curl_close($oCurl);

            // Check if there is a redirect given (IMDb sometimes does not return 301 for this...).
            $fRedirect = getcwd() . '/cache/' . md5($this->_strUrl) . '.redir';
            if ($strMatch = $this->matchRegex($strOutput, IMDB::IMDB_REDIRECT, 1)) {
                if (IMDB::IMDB_DEBUG) echo '<b>- Found a redirect:</b> ' . $strMatch . '<br>';
                // Try to save the redirect for later usage.
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
                if (IMDB::IMDB_DEBUG) echo '<b>- Saved a new redirect:</b> ' . $fRedirect . '<br>';
                file_put_contents($fRedirect, $strMatch);
                // Run the cURL request again with the new url.
                IMDB::fetchUrl($strMatch);
            }
            // If it's not a redirect and the HTTP response is not 200 or 302, abort.
            elseif ($arrInfo['http_code'] != 200 && $arrInfo['http_code'] != 302) {
                if (IMDB::IMDB_DEBUG) echo '<b>- Wrong HTTP code received, aborting:</b> ' . $arrInfo['http_code'] . '<br>';
                return false;
            }

            // Set the global source.
            $this->_strSource = preg_replace('~(\r|\n|\r\n)~', '', $this->_strSource);

            // Save cache.
            if (IMDB::IMDB_DEBUG) echo '<b>- Saved a new cache:</b> ' . $fCache . '<br>';
            file_put_contents($fCache, $this->_strSource);

            return true;
        }
        return false;
    }

    /**
     * Save the image locally.
     *
     * @param string $_strUrl The URL to the image on imdb
     * @return string The local path to the image
     */
    private function saveImage($_strUrl) {
        $_strUrl = trim($_strUrl);

        if (preg_match('/imdb-share-logo.gif/', $_strUrl) && file_exists('posters/not-found.jpg')) {
            return 'posters/not-found.jpg';
        }

        $strFilename = getcwd() . '/posters/' . $this->_strId . '.jpg';
        if (file_exists($strFilename)) {
            return 'posters/' . $this->_strId . '.jpg';
        }
        $oCurl = curl_init($_strUrl);
        curl_setopt_array($oCurl, array (
                                        CURLOPT_VERBOSE => FALSE,
                                        CURLOPT_HEADER => FALSE,
                                        CURLOPT_RETURNTRANSFER => TRUE,
                                        CURLOPT_TIMEOUT => IMDB::IMDB_TIMEOUT,
                                        CURLOPT_CONNECTTIMEOUT => 0,
                                        CURLOPT_REFERER => $_strUrl,
                                        CURLOPT_BINARYTRANSFER => TRUE));
        $sOutput = curl_exec($oCurl);
        $arrInfo = curl_getinfo($oCurl);
        curl_close($oCurl);
        if ($arrInfo['http_code'] != 200 && $arrInfo['http_code'] != 302) {
            return $_strUrl;
        }
        $oFile = fopen($strFilename, 'x');
        fwrite($oFile, $sOutput);
        fclose($oFile);
        return 'posters/' . $this->_strId . '.jpg';
    }

    /**
     * Returns the budget.
     *
     * @return string The movie budget.
     */
    public function getBudget() {
        if ($this->isReady) {
            if ($strReturn = $this->matchRegex($this->_strSource, IMDB::IMDB_BUDGET, 1)) {
                return $strReturn;
            }
            return $this->strNotFound;
        }
        return $this->strNotFound;
    }

    /**
     * Returns the cast.
     *
     * @return array The movie cast (default limited to 20).
     */
    public function getCast($intLimit = 20, $bolMore = true) {
        if ($this->isReady) {
            $arrReturned = $this->matchRegex($this->_strSource, IMDB::IMDB_CAST);
            if (count($arrReturned[2])) {
                foreach ($arrReturned[2] as $i => $strName) {
                    if ($i >= $intLimit) break;
                    $arrReturn[] = $strName;
                }
                return implode(' / ', $arrReturn) . ($bolMore && (count($arrReturned[2]) > $intLimit) ? '&hellip;' : '');
            }
            return $this->strNotFound;
        }
        return $this->strNotFound;
    }

    /**
     * Returns the cast as URL.
     *
     * @return array The movie cast as URL (default limited to 20).
     */
    public function getCastAsUrl($intLimit = 20, $bolMore = true) {
        if ($this->isReady) {
            $arrReturned = $this->matchRegex($this->_strSource, IMDB::IMDB_CAST);
            if (count($arrReturned[2])) {
                foreach ($arrReturned[2] as $i => $strName) {
                    if ($i >= $intLimit) break;
                    $arrReturn[] = '<a href="http://www.imdb.com/name/nm' . $arrReturned[1][$i] . '/">' . $strName . '</a>';
                }
                return implode(' / ', $arrReturn) . ($bolMore && (count($arrReturned[2]) > $intLimit) ? '&hellip;' : '');
            }
            return $this->strNotFound;
        }
        return $this->strNotFound;
    }

    /**
     * Returns the cast and character.
     *
     * @return array The movie cast and character (default limited to 20).
     */
    public function getCastAndCharacter($intLimit = 20, $bolMore = true) {
        if ($this->isReady) {
            $arrReturned = $this->matchRegex($this->_strSource, IMDB::IMDB_CAST);
            $arrChar     = $this->matchRegex($this->_strSource, IMDB::IMDB_CHAR);
            if (count($arrReturned[2])) {
                foreach ($arrReturned[2] as $i => $strName) {
                    if ($i >= $intLimit) break;
                    $arrChar[1][$i] = trim(preg_replace('~\((.*)\)~Ui', '', strip_tags($arrChar[1][$i])));
                    if ($arrChar[1][$i]) {
                        $arrReturn[] = $strName . ' as ' . $arrChar[1][$i];
                    }
                    else {
                        $arrReturn[] = $strName;
                    }
                }
                return implode(' / ', $arrReturn) . ($bolMore && (count($arrReturned[2]) > $intLimit) ? '&hellip;' : '');
            }
            return $this->strNotFound;
        }
        return $this->strNotFound;
    }

    /**
     * Returns the cast and character as URL .
     *
     * @return array The movie cast and character as URL (default limited to 20).
     */
    public function getCastAndCharacterAsUrl($intLimit = 20, $bolMore = true) {
        if ($this->isReady) {
            $arrReturned = $this->matchRegex($this->_strSource, IMDB::IMDB_CAST);
            $arrChar     = $this->matchRegex($this->_strSource, IMDB::IMDB_CHAR);
            if (count($arrReturned[2])) {
                foreach ($arrReturned[2] as $i => $strName) {
                    if ($i >= $intLimit) break;
                    $arrChar[1][$i] = trim(preg_replace('~\((.*)\)~Ui', '', $arrChar[1][$i]));
                    //print_r($arrChar[1][$i]);
                    preg_match_all('~<a href="/character/ch(\d+)/">(.*)</a>~Ui', $arrChar[1][$i], $arrMatches);
                    if (isset($arrMatches[1][0]) && isset($arrMatches[2][0])) {
                        $arrReturn[] = '<a href="http://www.imdb.com/name/nm' . $arrReturned[1][$i] . '/">' . $strName . '</a> as <a href="http://www.imdb.com/character/ch' . $arrMatches[1][0] . '/">' . $arrMatches[2][0] . '</a>';
                    }
                    else {
                        if ($arrChar[1][$i]) {
                            $arrReturn[] = '<a href="http://www.imdb.com/name/nm' . $arrReturned[1][$i] . '/">' . $strName . '</a> as ' . strip_tags($arrChar[1][$i]);
                        }
                        else {
                            $arrReturn[] = '<a href="http://www.imdb.com/name/nm' . $arrReturned[1][$i] . '/">' . $strName . '</a>';
                        }
                    }
                }
                return implode(' / ', $arrReturn) . ($bolMore && (count($arrReturned[2]) > $intLimit) ? '&hellip;' : '');
            }
            return $this->strNotFound;
        }
        return $this->strNotFound;
    }


    /**
     * Returns the countr(y|ies).
     *
     * @return array The movie countr(y|ies).
     */
    public function getCountry() {
        if ($this->isReady) {
            $arrReturned = $this->matchRegex($this->_strSource, IMDB::IMDB_COUNTRY);
            if (count($arrReturned[2])) {
                foreach ($arrReturned[2] as $strName) {
                    $arrReturn[] = $strName;
                }
                return implode(' / ', $arrReturn);
            }
            return $this->strNotFound;
        }
        return $this->strNotFound;
    }

    /**
     * Returns the countr(y|ies) as URL
     *
     * @return array The movie countr(y|ies) as URL.
     */
    public function getCountryAsUrl() {
        if ($this->isReady) {
            $arrReturned = $this->matchRegex($this->_strSource, IMDB::IMDB_COUNTRY);
            if (count($arrReturned[2])) {
                foreach ($arrReturned[2] as $i => $strName) {
                    $arrReturn[] = '<a href="http://www.imdb.com/country/' . $arrReturned[1][$i] . '/">' . $strName . '</a>';
                }
                return implode(' / ', $arrReturn);
            }
            return $this->strNotFound;
        }
        return $this->strNotFound;
    }

    /**
     * Returns the director(s).
     *
     * @return array The movie director(s).
     */
    public function getDirector() {
        if ($this->isReady) {
            $strContainer = $this->matchRegex($this->_strSource, IMDB::IMDB_DIRECTOR, 2);
            $arrReturned  = $this->matchRegex($strContainer, IMDB::IMDB_NAME);
            if (count($arrReturned[2])) {
                foreach ($arrReturned[2] as $i => $strName) {
                    $arrReturn[] = $strName;
                }
                return implode(' / ', $arrReturn);
            }
            return $this->strNotFound;
        }
        return $this->strNotFound;
    }

    /**
     * Returns the director(s) as URL.
     *
     * @return array The movie director(s) as URL.
     */
    public function getDirectorAsUrl() {
        if ($this->isReady) {
            $strContainer = $this->matchRegex($this->_strSource, IMDB::IMDB_DIRECTOR, 2);
            $arrReturned = $this->matchRegex($strContainer, IMDB::IMDB_NAME);
            if (count($arrReturned[2])) {
                foreach ($arrReturned[2] as $i => $strName) {
                    $arrReturn[] = '<a href="http://www.imdb.com/name/nm' . $arrReturned[1][$i] . '/">' . $strName . '</a>';
                }
                return implode(' / ', $arrReturn);
            }
            return $this->strNotFound;
        }
        return $this->strNotFound;
    }


    /**
     * Returns the creator(s).
     *
     * @return array The movie creator(s).
     */
    public function getCreator() {
        if ($this->isReady) {
            $strContainer = $this->matchRegex($this->_strSource, IMDB::IMDB_CREATOR, 2);
            $arrReturned  = $this->matchRegex($strContainer, IMDB::IMDB_NAME);
            if (count($arrReturned[2])) {
                foreach ($arrReturned[2] as $i => $strName) {
                    $arrReturn[] = $strName;
                }
                return implode(' / ', $arrReturn);
            }
            return $this->strNotFound;
        }
        return $this->strNotFound;
    }

    /**
     * Returns the creator(s) as URL.
     *
     * @return array The movie creator(s) as URL.
     */
    public function getCreatorAsUrl() {
        if ($this->isReady) {
            $strContainer = $this->matchRegex($this->_strSource, IMDB::IMDB_CREATOR, 2);
            $arrReturned = $this->matchRegex($strContainer, IMDB::IMDB_NAME);
            if (count($arrReturned[2])) {
                foreach ($arrReturned[2] as $i => $strName) {
                    $arrReturn[] = '<a href="http://www.imdb.com/name/nm' . $arrReturned[1][$i] . '/">' . $strName . '</a>';
                }
                return implode(' / ', $arrReturn);
            }
            return $this->strNotFound;
        }
        return $this->strNotFound;
    }

    /**
     * Returns the genre(s).
     *
     * @return array The movie genre(s).
     */
    public function getGenre() {
        if ($this->isReady) {
            $arrReturned = $this->matchRegex($this->_strSource, IMDB::IMDB_GENRE);
            if (count($arrReturned[1])) {
               foreach ($arrReturned[1] as $strName) {
                    $arrReturn[] = $strName;
                }
                return implode(' / ', $arrReturn);
            }
            return $this->strNotFound;
        }
        return $this->strNotFound;
    }

    /**
     * Returns the genres as URL.
     *
     * @return array The movie genre as URL.
     */
    public function getGenreAsUrl() {
        if ($this->isReady) {
            $arrReturned = $this->matchRegex($this->_strSource, IMDB::IMDB_GENRE);
            if (count($arrReturned[1])) {
               foreach ($arrReturned[1] as $i => $strName) {
                    $arrReturn[] = '<a href="http://www.imdb.com/genre/' . $strName . '/">' . $strName . '</a>';
                }
                return implode(' / ', $arrReturn);
            }
            return $this->strNotFound;
        }
        return $this->strNotFound;
    }

    /**
     * Returns the language(s).
     *
     * @return string The movie language(s).
     */
    public function getLanguages() {
        if ($this->isReady) {
            $arrReturned = $this->matchRegex($this->_strSource, IMDB::IMDB_LANGUAGES);
            if (count($arrReturned[2])) {
                foreach ($arrReturned[2] as $strName) {
                    $arrReturn[] = $strName;
                }
                return implode(' / ', $arrReturn);
            }
            return $this->strNotFound;
        }
        return $this->strNotFound;
    }

    /**
     * Returns the language(s) as URL.
     *
     * @return string The movie language(s) as URL.
     */
    public function getLanguagesAsUrl() {
        if ($this->isReady) {
            $arrReturned = $this->matchRegex($this->_strSource, IMDB::IMDB_LANGUAGES);
            if (count($arrReturned[2])) {
                foreach ($arrReturned[2] as $i => $strName) {
                    $arrReturn[] = '<a href="http://www.imdb.com/language/' . $arrReturned[1][$i] . '">' . $strName . '</a>';
                }
                return implode(' / ', $arrReturn);
            }
            return $this->strNotFound;
        }
        return $this->strNotFound;
    }

    /**
     * Returns the movie location.
     *
     * @return string The location of the movie.
     */
    public function getLocation() {
        if ($this->isReady) {
            if ($strReturn = $this->matchRegex($this->_strSource, IMDB::IMDB_LOCATION, 2)) {
                return $strReturn;
            }
            return $this->strNotFound;
        }
        return $this->strNotFound;
    }

    /**
     * Returns the movie location as URL.
     *
     * @return string The location of the movie as URL.
     */
    public function getLocationAsUrl() {
        if ($this->isReady) {
            $strReturn    = $this->matchRegex($this->_strSource, IMDB::IMDB_LOCATION, 2);
            if ($strReturn) {
                return '<a href="http://www.imdb.com/search/title?locations=' . urlencode($strReturn) . '">' . $strReturn . '</a>';
            }
            return $this->strNotFound;
        }
        return $this->strNotFound;
    }


    /**
     * Returns the MPAA.
     *
     * @return string The movie MPAA.
     */
    public function getMpaa() {
        if ($this->isReady) {
            if ($strReturn = $this->matchRegex($this->_strSource, IMDB::IMDB_MPAA, 1)) {
                return $strReturn;
            }
            return $this->strNotFound;
        }
        return $this->strNotFound;
    }

    /**
     * Returns the plot.
     *
     * @return string The movie plot.
     */
    public function getPlot($intLimit = 0) {
        if ($this->isReady) {
            if ($strReturn = $this->matchRegex($this->_strSource, IMDB::IMDB_PLOT, 1)) {
                if ($intLimit) {
                    return $this->getShortText($strReturn, $intLimit);
                }
                return $strReturn;
            }
            return $this->strNotFound;
        }
        return $this->strNotFound;
    }

    /**
     * Download the poster, cache it and return the local path to the image.
     *
     * @return string The path to the poster (either local or online).
     */
    public function getPoster() {
       if ($this->isReady) {
            if ($strReturn = $this->matchRegex($this->_strSource, IMDB::IMDB_POSTER, 2)) {
                if ($strLocal = $this->saveImage($strReturn)) {
                    return $strLocal;
                }
                return $strReturn;
            }
           return $this->strNotFound;
        }
        return $this->strNotFound;
    }

    /**
     * Returns the rating.
     *
     * @return string The movie rating.
     */
    public function getRating() {
        if ($this->isReady) {
            if ($strReturn = $this->matchRegex($this->_strSource, IMDB::IMDB_RATING, 1)) {
                return $strReturn;
            }
            return $this->strNotFound;
        }
        return $this->strNotFound;
    }

    /**
     * Returns the release date.
     *
     * @return string The movie release date.
     */
    public function getReleaseDate() {
        if ($this->isReady) {
            if ($strReturn = $this->matchRegex($this->_strSource, IMDB::IMDB_RELEASE_DATE, 1)) {
                return str_replace('(', ' (', $strReturn);
            }
            return $this->strNotFound;
        }
        return $this->strNotFound;
    }

    /**
     * Returns the runtime.
     *
     * @return string The movie runtime.
     */
    public function getRuntime() {
        if ($this->isReady) {
            if ($strReturn = $this->matchRegex($this->_strSource, IMDB::IMDB_RUNTIME, 1)) {
                return $strReturn;
            }
            return $this->strNotFound;
        }
        return $this->strNotFound;
    }

    /**
     * Returns the tagline.
     *
     * @return string The movie tagline.
     */
    public function getTagline() {
        if ($this->isReady) {
            if ($strReturn = $this->matchRegex($this->_strSource, IMDB::IMDB_TAGLINE, 1)) {
                return $strReturn;
            }
            return $this->strNotFound;
        }
        return $this->strNotFound;
    }

    /**
     * Return the title.
     *
     * @return string The movie title.
     */
    public function getTitle() {
        if ($this->isReady) {
            if ($strReturn = $this->matchRegex($this->_strSource, IMDB::IMDB_TITLE_ORIG, 1)) {
                return $strReturn;
            }
            if ($strReturn = $this->matchRegex($this->_strSource, IMDB::IMDB_TITLE, 1)) {
                return $strReturn;
            }
            return $this->strNotFound;
        }
        return $this->strNotFound;
    }

    /**
     * Returns the URL.
     *
     * @return string The movie URL.
     */
    public function getUrl() {
        return $this->_strUrl;
    }

    /**
     * Returns the votes.
     *
     * @return string The votes of the movie.
     */
    public function getVotes() {
        if ($this->isReady) {
            if ($strReturn = $this->matchRegex($this->_strSource, IMDB::IMDB_VOTES, 1)) {
                return $strReturn;
            }
            return $this->strNotFound;
        }
        return $this->strNotFound;
    }

    /**
     * Returns the writer(s).
     *
     * @return array The movie writer(s).
     */
    public function getWriter() {
        if ($this->isReady) {
            $strContainer = $this->matchRegex($this->_strSource, IMDB::IMDB_WRITER, 2);
            $arrReturned  = $this->matchRegex($strContainer, IMDB::IMDB_NAME);
            if (count($arrReturned[2])) {
                foreach ($arrReturned[2] as $i => $strName) {
                    $arrReturn[] = $strName;
                }
                return implode(' / ', $arrReturn);
            }
            return $this->strNotFound;
        }
        return $this->strNotFound;
    }

    /**
     * Returns the writer(s) as URL.
     *
     * @return array The movie writer(s) as URL.
     */
    public function getWriterAsUrl() {
        if ($this->isReady) {
            $strContainer = $this->matchRegex($this->_strSource, IMDB::IMDB_WRITER, 2);
            $arrReturned  = $this->matchRegex($strContainer, IMDB::IMDB_NAME);
            if (count($arrReturned[2])) {
                foreach ($arrReturned[2] as $i => $strName) {
                    $arrReturn[] = '<a href="http://www.imdb.com/name/nm' . $arrReturned[1][$i] . '/">' . $strName . '</a>';
                }
                return implode(' / ', $arrReturn);
            }
            return $this->strNotFound;
        }
        return $this->strNotFound;
    }

    /**
     * Returns the movie year.
     *
     * @return string The year of the movie.
     */
    public function getYear() {
        if ($this->isReady) {
            if ($strReturn = $this->matchRegex($this->_strSource, IMDB::IMDB_TITLE, 2)) {
                return substr(preg_replace('~[\D]~', '', $strReturn), 0, 4);
            }
            return $this->strNotFound;
        }
        return $this->strNotFound;
    }

     /**
     * Returns the seasons.
     *
     * @return string The movie seasons.
     */
    public function getSeasons() {
        if ($this->isReady) {
            if ($strReturn = $this->matchRegex($this->_strSource, IMDB::IMDB_SEASONS)) {
                $strReturn = strip_tags(implode($strReturn[1]));
                $strFind   = array('&raquo;', '&nbsp;', 'Full episode list', ' ');
                $strReturn = str_replace($strFind, '', $strReturn);
                $arrReturn = explode('|', $strReturn);
                unset($arrReturn[(count($arrReturn)-1)]);
                if ($arrReturn) {
                    return implode(' / ', $arrReturn);
                }
            }
            return $this->strNotFound;
        }
        return $this->strNotFound;
    }
}
