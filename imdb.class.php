<?php
/**
 * PHP-IMDb-Grabber -- a PHP IMDb.com scraper
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
 * buy me a book at Amazon (http://www.amazon.de/registry/wishlist/8840JITISN9L)
 * – thank you! :-)
 *
 *
 * @author  Fabian Beiner (mail@fabian-beiner.de)
 * @link    http://fabian-beiner.de
 * @license Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported
 *
 * @version 5.5.19 (October 29th, 2013)
*/

class IMDBException extends Exception {}

class IMDB {
    // Define what to return if something is not found.
    public $strNotFound  = 'n/A';
    // Define the seperator (eg. for cast).
    public $strSeperator = ' / ';
    // Please set this to 'TRUE' for debugging purposes only.
    const IMDB_DEBUG     = FALSE;
    // Define a timeout for the request of the IMDb page.
    const IMDB_TIMEOUT   = 15;
    // Define the "Accept-Language" header language (so IMDb replies with decent localization settings).
    const IMDB_LANG      = 'en-US, en';
    // Define the default search type (all/tvtitle/tvepisode/movie).
    const IMDB_SEARCHFOR = 'all';

    // Regular expressions, I would not touch them. :)
    const IMDB_AKA          = '~Also Known As:</h4>(.*)<span~Ui';
    const IMDB_ASPECT_RATIO = '~Aspect Ratio:</h4>(.*)</div>~Ui';
    const IMDB_BUDGET       = '~Budget:</h4>(.*)<span~Ui';
    const IMDB_CAST         = '~itemprop="actor"(?:.*)><a href="/name/nm(\d+)/(?:.*)" itemprop=\'url\'> <span class="itemprop" itemprop="name">(.*)</span>~Ui';
    const IMDB_FULL_CAST    = '~<span class="itemprop" itemprop="name">(.*?)</span>~Ui';
    const IMDB_CHAR         = '~<td class="character">\s+<div>(.*)</div>\s+</td~Ui';
    const IMDB_COLOR        = '~href="/search/title\?colors=(?:.*)" itemprop=\'url\'>(.*)</a>~Ui';
    const IMDB_COMPANY      = '~Production Co:</h4>(.*)</div>~Ui';
    const IMDB_COMPANY_NAME = '~href="/company/co(\d+)(?:\?.*)" itemprop=\'url\'>(.*)</a>~Ui';
    const IMDB_COUNTRY      = '~href="/country/(\w+)\?(?:.*)" itemprop=\'url\'>(.*)</a>~Ui';
    const IMDB_CREATOR      = '~(?:Creator|Creators):</h4>(.*)</div>~Ui';
    const IMDB_DESCRIPTION  = '~<p itemprop="description">(.*)(?:<a|<\/p>)~Ui';
    const IMDB_DIRECTOR     = '~(?:Director|Directors):</h4>(.*)</div>~Ui';
    const IMDB_GENRE        = '~href="/genre/(.*)(?:\?.*)"(?:\s+|)>(.*)</a>~Ui';
    const IMDB_ID           = '~(tt\d{6,})~';
    const IMDB_LANGUAGES    = '~href="/language/(.*)(?:\?.*)" itemprop=\'url\'>(.*)</a>~Ui';
    const IMDB_LOCATION     = '~href="/search/title\?locations=(.*)(?:&.*)" itemprop=\'url\'>(.*)</a>~Ui';
    const IMDB_MPAA         = '~span itemprop="contentRating"(?:.*)>(.*)</span~Ui';
    const IMDB_NAME         = '~href="/name/nm(\d+)/(?:.*)" itemprop=\'(?:\w+)\'><span class="itemprop" itemprop="name">(.*)</span>~Ui';
    const IMDB_OPENING      = '~Opening Weekend:</h4>(.*)\(~Ui';
    const IMDB_PLOT         = '~Storyline</h2>\s+<div class="inline canwrap" itemprop="description">\s+<p>(.*)(?:<em|<\/p>|<\/div>)~Ui';
    const IMDB_POSTER       = '~"src="(.*)"itemprop="image" \/>~Ui';
    const IMDB_RATING       = '~<span itemprop="ratingValue">(.*)</span>~Ui';
    const IMDB_REDIRECT     = '~Location:\s(.*)~';
    const IMDB_RELEASE_DATE = '~Release Date:</h4>(.*)(?:<span|<\/div>)~Ui';
    const IMDB_RUNTIME      = '~Runtime:</h4>\s+<time itemprop="duration" datetime="(?:.*)">(.*)</time>~Uis';
    const IMDB_SEARCH       = '~<td class="result_text"> <a href="\/title\/tt(\d+)\/(?:.*)"(?:\s+|)>(.*)<\/a>~Uis';
    const IMDB_SEASONS      = '~Season:</h4>\s+<span class="see-more inline">(.*)</span>\s+</div>~Ui';
    const IMDB_SITES        = '~Official Sites:</h4>(.*)(?:<a href="officialsites|</div>)~Ui';
    const IMDB_SITES_A      = '~href="(.*)" itemprop=\'url\'>(.*)</a>~Ui';
    const IMDB_SOUND_MIX    = '~Sound Mix:</h4>(.*)</div>~Ui';
    const IMDB_SOUND_MIX_A  = '~href="/search/title\?sound_mixes=(?:.*)" itemprop=\'url\'>(.*)</a>~Ui';
    const IMDB_TAGLINE      = '~Taglines:</h4>(.*)(?:<span|<\/span>|</div>)~Ui';
    const IMDB_TITLE        = '~property=\'og:title\' content="(.*) \((?:.*)\)"~Ui';
    const IMDB_TITLE_ORIG   = '~<span class="title-extra" itemprop="name">(.*)<i>\(original title\)<\/i>\s+</span>~Ui';
    const IMDB_TRAILER      = '~href="/video/(.*)/(?:\?.*)"(?:.*)itemprop="trailer">~Ui';
    const IMDB_URL          = '~http://(?:.*\.|.*)imdb.com/(?:t|T)itle(?:\?|/)(..\d+)~i';
    const IMDB_VOTES        = '~<span itemprop="ratingCount">(.*)</span>~Ui';
    const IMDB_YEAR         = '~<h1 class="header">(?:\s+)<span class="itemprop" itemprop="name">(?:.*)</span>(?:\s+)<span class="nobr">\((.*)\)</span>~Ui';
    const IMDB_WRITER       = '~(?:Writer|Writers):</h4>(.*)</div>~Ui';

    // cURL cookie file.
    private $_fCookie   = FALSE;
    // IMDb url.
    private $_strUrl    = NULL;
    // IMDb source.
    private $_strSource = NULL;
    // IMDb cache.
    private $_strCache  = 0;
    // IMDb movie id.
    private $_strId     = FALSE;
    // Movie found?
    public $isReady     = FALSE;
    // Define root of this script.
    private $_strRoot   = '';
    // Current version.
    const IMDB_VERSION  = '5.5.19';

    /**
     * IMDB constructor.
     *
     * @param string  $strSearch The movie name / IMDb url
     * @param integer $intCache  The maximum age (in minutes) of the cache (default 1 day)
     */
    public function __construct($strSearch, $intCache = 1440) {
        if (!$this->_strRoot) {
            $this->_strRoot = dirname(__FILE__);
        }
        // Posters and cache directory existant?
        if (!is_writable($this->_strRoot . '/posters/') && !mkdir($this->_strRoot . '/posters/')) {
            throw new IMDBException($this->_strRoot . '/posters/ is not writable!');
        }
        if (!is_writable($this->_strRoot . '/cache/') && !mkdir($this->_strRoot . '/cache/')) {
            throw new IMDBException($this->_strRoot . '/cache/ is not writable!');
        }
        // cURL.
        if (!function_exists('curl_init')) {
            throw new IMDBException('You need PHP with cURL enabled to use this script!');
        }
        // Debug only.
        if (IMDB::IMDB_DEBUG) {
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(-1);
            echo '<b>- Running:</b> IMDB::fetchUrl<br>';
        }
        // Set global cache and fetch the data.
        $this->_intCache = (int) $intCache;
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
    private function matchRegex($strContent, $strRegex, $intIndex = NULL) {
        $arrMatches = FALSE;
        preg_match_all($strRegex, $strContent, $arrMatches);
        if ($arrMatches === FALSE)
            return FALSE;
        if ($intIndex != NULL && is_int($intIndex)) {
            if ($arrMatches[$intIndex]) {
                return $arrMatches[$intIndex][0];
            }
            return FALSE;
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
        return $strText . '…';
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

          // "Remote Debug" - so I can see which version you're running.
          // To due people complaing about broken functions while they're
          // using old versions. Feel free to remove this.
          if ($strSearch == '##REMOTEDEBUG##') {
              $strSearch = 'http://www.imdb.com/title/tt1022603/';
              echo '<pre>Running PHP-IMDB-Grabber v' . IMDB::IMDB_VERSION . '.</pre>';
          }

          // Get the ID of the movie.
          $strId = IMDB::matchRegex($strSearch, IMDB::IMDB_URL, 1);
          if (!$strId) {
              $strId = IMDB::matchRegex($strSearch, IMDB::IMDB_ID, 1);
          }

          // Check if we found an ID ...
          if ($strId) {
              $this->_strId  = preg_replace('~[\D]~', '', $strId);
              $this->_strUrl = 'http://www.imdb.com/title/tt' . $this->_strId . '/';
              $bolFound      = FALSE;
              $this->isReady = TRUE;
          }

          // ... otherwise try to find one.
          else {
              $strSearchFor = 'all';
              if (strtolower(IMDB::IMDB_SEARCHFOR) == 'movie') {
                  $strSearchFor = 'tt&ttype=ft&ref_=fn_ft';
              } elseif (strtolower(IMDB::IMDB_SEARCHFOR) == 'tvtitle') {
                  $strSearchFor = 'tt&ttype=tv&ref_=fn_tv';
              } elseif (strtolower(IMDB::IMDB_SEARCHFOR) == 'tvepisode') {
                  $strSearchFor = 'tt&ttype=ep&ref_=fn_ep';
              }

              $this->_strUrl = 'http://www.imdb.com/find?s=' . $strSearchFor . '&q=' . str_replace(' ', '+', $strSearch);
              $bolFound      = TRUE;

              // Check for cached redirects of this search.
              $fRedirect = @file_get_contents($this->_strRoot . '/cache/' . md5($this->_strUrl) . '.redir');
              if ($fRedirect) {
                  if (IMDB::IMDB_DEBUG) {
                      echo '<b>- Found an old redirect:</b> ' . $fRedirect . '<br>';
                  }
                  $this->_strUrl = trim($fRedirect);
                  $this->_strId  = preg_replace('~[\D]~', '', IMDB::matchRegex($fRedirect, IMDB::IMDB_URL, 1));
                  $this->isReady = TRUE;
                  $bolFound      = FALSE;
              }
          }

          // Check if there is a cache we can use.
          $fCache = $this->_strRoot . '/cache/' . md5($this->_strId) . '.cache';
          if (file_exists($fCache)) {
              $bolUseCache = TRUE;
              $intChanged  = filemtime($fCache);
              $intNow      = time();
              $intDiff     = round(abs($intNow - $intChanged) / 60);
              if ($intDiff > $this->_intCache) {
                  $bolUseCache = FALSE;
              }
          } else {
              $bolUseCache = FALSE;
          }

          if ($bolUseCache) {
              if (IMDB::IMDB_DEBUG) {
                  echo '<b>- Using cache for ' . $strSearch . ' from ' . $fCache . '</b><br>';
              }
              $this->_strSource = file_get_contents($fCache);
              return TRUE;
          } else {
              // Cookie path.
              if (function_exists('sys_get_temp_dir')) {
                  $this->_fCookie = tempnam(sys_get_temp_dir(), 'imdb');
                  if (IMDB::IMDB_DEBUG) {
                      echo '<b>- Path to cookie:</b> ' . $this->_fCookie . '<br>';
                  }
              }
              // Initialize and run the request.
              if (IMDB::IMDB_DEBUG) {
                  echo '<b>- Run cURL on:</b> ' . $this->_strUrl . '<br>';
              }

              $arrInfo   = $this->doCurl($this->_strUrl);
              $strOutput = $arrInfo['contents'];

              // Check if the request actually worked.
              if ($strOutput === FALSE) {
                  if (IMDB::IMDB_DEBUG) {
                      echo '<b>! cURL error:</b> ' . $this->_strUrl . '<br>';
                  }
                  $this->_strSource = file_get_contents($fCache);
                  if ($this->_strSource) {
                      return TRUE;
                  }
                  return FALSE;
              }

              // Check if there is a redirect given (IMDb sometimes does not return 301 for this...).
              $fRedirect = $this->_strRoot . '/cache/' . md5($this->_strUrl) . '.redir';
              if ($strMatch = $this->matchRegex($strOutput, IMDB::IMDB_REDIRECT, 1)) {
                  $arrExplode = explode('?fr=', $strMatch);
                  $strMatch   = ($arrExplode[0] ? $arrExplode[0] : $strMatch);
                  if (IMDB::IMDB_DEBUG) {
                      echo '<b>- Saved a new redirect:</b> ' . $fRedirect . '<br>';
                  }
                  file_put_contents($fRedirect, $strMatch);
                  $this->isReady    = FALSE;
                  // Run the cURL request again with the new url.
                  IMDB::fetchUrl($strMatch);
                  return TRUE;
              }
              // Check if any of the search regexes is matching.
              elseif ($strMatch = $this->matchRegex($strOutput, IMDB::IMDB_SEARCH, 1)) {
                  $strMatch = 'http://www.imdb.com/title/tt' . $strMatch . '/';
                  if (IMDB::IMDB_DEBUG) {
                      echo '<b>- Using the first search result:</b> ' . $strMatch . '<br>';
                      echo '<b>- Saved a new redirect:</b> ' . $fRedirect . '<br>';
                  }
                  file_put_contents($fRedirect, $strMatch);
                  // Run the cURL request again with the new url.
                  $this->_strSource = NULL;
                  $this->isReady    = FALSE;
                  IMDB::fetchUrl($strMatch);
                  return TRUE;
              }
              // If it's not a redirect and the HTTP response is not 200 or 302, abort.
              elseif ($arrInfo['http_code'] != 200 && $arrInfo['http_code'] != 302) {
                  if (IMDB::IMDB_DEBUG) {
                      echo '<b>- Wrong HTTP code received, aborting:</b> ' . $arrInfo['http_code'] . '<br>';
                  }
                  return FALSE;
              }

              $this->_strSource = $strOutput;

              // Set the global source.
              $this->_strSource = preg_replace('~(\r|\n|\r\n)~', '', $this->_strSource);

              // Save cache.
              if (!$bolFound) {
                  if (IMDB::IMDB_DEBUG) {
                      echo '<b>- Saved a new cache:</b> ' . $fCache . '<br>';
                  }
                  file_put_contents($fCache, $this->_strSource);
              }

              return TRUE;
          }
          return FALSE;
      }

    /**
     * Run a cURL request.
     *
     * @param str $strUrl             URL to run curl on.
     * @param bol $bolOverWriteSource Overwrite $this->_strSource?
     *
     * @return arr Array with cURL informations.
     */
      private function doCurl($strUrl, $bolOverWriteSource = TRUE) {
          $oCurl = curl_init($strUrl);
          curl_setopt_array($oCurl, array(
              CURLOPT_VERBOSE => FALSE,
              CURLOPT_HEADER => TRUE,
              CURLOPT_HTTPHEADER => array(
                  'Accept-Language:' . IMDB::IMDB_LANG . ';q=0.5'
              ),
              CURLOPT_FRESH_CONNECT => TRUE,
              CURLOPT_RETURNTRANSFER => TRUE,
              CURLOPT_TIMEOUT => IMDB::IMDB_TIMEOUT,
              CURLOPT_CONNECTTIMEOUT => 0,
              CURLOPT_REFERER => 'http://www.google.com',
              CURLOPT_USERAGENT => 'Googlebot/2.1 (+http://www.google.com/bot.html)',
              CURLOPT_FOLLOWLOCATION => FALSE,
              CURLOPT_COOKIEFILE => $this->_fCookie
          ));
          $strOutput = curl_exec($oCurl);

          // Remove cookie.
          if ($this->_fCookie) {
              @unlink($this->_fCookie);
          }

          // Get returned information.
          $arrInfo = curl_getinfo($oCurl);
          curl_close($oCurl);

          $arrInfo['contents'] = $strOutput;

          if ($bolOverWriteSource) {
              $this->_strSource = $strOutput;
          }

          // If it's not a redirect and the HTTP response is not 200 or 302, abort.
          if ($arrInfo['http_code'] != 200 && $arrInfo['http_code'] != 302) {
              if (IMDB::IMDB_DEBUG) {
                  echo '<b>- Wrong HTTP code received, aborting:</b> ' . $arrInfo['http_code'] . '<br>';
              }
              return FALSE;
          }

          return $arrInfo;
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

        $strFilename = $this->_strRoot . '/posters/' . $this->_strId . '.jpg';
        if (file_exists($strFilename)) {
            return 'posters/' . $this->_strId . '.jpg';
        }
        $oCurl = curl_init($_strUrl);
        curl_setopt_array($oCurl, array(
            CURLOPT_VERBOSE => FALSE,
            CURLOPT_HEADER => FALSE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_TIMEOUT => IMDB::IMDB_TIMEOUT,
            CURLOPT_CONNECTTIMEOUT => 0,
            CURLOPT_REFERER => $_strUrl,
            CURLOPT_BINARYTRANSFER => TRUE
        ));
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
     * Returns the "also known as" name.
     *
     * @return string The aka name.
     */
    public function getAka() {
        if ($this->isReady) {
            if ($strReturn = $this->matchRegex($this->_strSource, IMDB::IMDB_AKA, 1)) {
                return trim($strReturn);
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns the aspect ratio of the movie.
     *
     * @return string The aspect ratio.
     */
    public function getAspectRatio() {
        if ($this->isReady) {
            if ($strReturn = $this->matchRegex($this->_strSource, IMDB::IMDB_ASPECT_RATIO, 1)) {
                return trim($strReturn);
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns the budget.
     *
     * @return string The movie budget.
     */
    public function getBudget() {
        if ($this->isReady) {
            if ($strReturn = $this->matchRegex($this->_strSource, IMDB::IMDB_BUDGET, 1)) {
                return trim($strReturn);
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns the cast.
     *
     * @return array The movie cast (default limited to 20).
     */
    public function getCast($intLimit = 20, $bolMore = TRUE) {
        if ($this->isReady) {
            $arrReturned = $this->matchRegex($this->_strSource, IMDB::IMDB_CAST);
            if (count($arrReturned[2])) {
                foreach ($arrReturned[2] as $i => $strName) {
                    if ($i >= $intLimit) {
                        break;
                    }
                    $arrReturn[] = trim($strName);
                }
                return implode($this->strSeperator, $arrReturn) . ($bolMore && (count($arrReturned[2]) > $intLimit) ? '…' : '');
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns the full cast.
     *
     * @return string full cast.
     */
    public function getFullCast($intLimit = FALSE) {
        if ($this->isReady) {
            $fCache = $this->_strRoot . '/cache/' . md5($this->_strId) . '.fullcast';
            if (file_exists($fCache)) {
                $bolUseCache = TRUE;
                $intChanged  = filemtime($fCache);
                $intNow      = time();
                $intDiff     = round(abs($intNow - $intChanged) / 60);
                if ($intDiff > $this->_intCache) {
                    $bolUseCache = FALSE;
                }
            } else {
                $bolUseCache = FALSE;
            }

            if ($bolUseCache) {
                if (IMDB::IMDB_DEBUG) {
                    echo '<b>- Using cache for fullCast from ' . $fCache . '</b><br>';
                }
                $arrReturn = @file_get_contents($fCache);
                return implode($this->strSeperator, unserialize($arrReturn));
            } else {
                $fullCastUrl = sprintf('http://www.imdb.com/title/tt%s/fullcredits', $this->_strId);
                $arrInfo     = $this->doCurl($fullCastUrl, FALSE);
                if (!$arrInfo) {
                    return $this->strNotFound;
                }
                $arrReturned = $this->matchRegex($arrInfo['contents'], IMDB::IMDB_FULL_CAST);

                if (count($arrReturned[1])) {
                    foreach ($arrReturned[1] as $i => $strName) {
                        if ($intLimit !== FALSE && $i >= $intLimit) {
                            break;
                        }
                        $arrReturn[] = trim($strName);
                    }
                    @file_put_contents($fCache, serialize($arrReturn));
                    return implode($this->strSeperator, $arrReturn);
                }
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns the cast as URL.
     *
     * @return array The movie cast as URL (default limited to 20).
     */
    public function getCastAsUrl($intLimit = 20, $bolMore = TRUE, $strTarget = '') {
        if ($this->isReady) {
            $arrReturned = $this->matchRegex($this->_strSource, IMDB::IMDB_CAST);
            if (count($arrReturned[2])) {
                foreach ($arrReturned[2] as $i => $strName) {
                    if ($i >= $intLimit) {
                        break;
                    }
                    $arrReturn[] = '<a href="http://www.imdb.com/name/nm' . trim($arrReturned[1][$i]) . '/"' . ($strTarget ? ' target="' . $strTarget . '"' : '') . '>' . trim($strName) . '</a>';
                }
                return implode($this->strSeperator, $arrReturn) . ($bolMore && (count($arrReturned[2]) > $intLimit) ? '…' : '');
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns the cast and character.
     *
     * @return array The movie cast and character (default limited to 20).
     */
    public function getCastAndCharacter($intLimit = 20, $bolMore = TRUE) {
        if ($this->isReady) {
            $arrReturned = $this->matchRegex($this->_strSource, IMDB::IMDB_CAST);
            $arrChar     = $this->matchRegex($this->_strSource, IMDB::IMDB_CHAR);
            if (count($arrReturned[2])) {
                foreach ($arrReturned[2] as $i => $strName) {
                    if ($i >= $intLimit) {
                        break;
                    }
                    $arrChar[1][$i] = trim(preg_replace('~\((.*)\)~Ui', '', strip_tags($arrChar[1][$i])));
                    if ($arrChar[1][$i]) {
                        $arrReturn[] = trim($strName) . ' as ' . trim($arrChar[1][$i]);
                    } else {
                        $arrReturn[] = trim($strName);
                    }
                }
                return implode($this->strSeperator, $arrReturn) . ($bolMore && (count($arrReturned[2]) > $intLimit) ? '…' : '');
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns the cast and character as URL .
     *
     * @return array The movie cast and character as URL (default limited to 20).
     */
    public function getCastAndCharacterAsUrl($intLimit = 20, $bolMore = TRUE, $strTarget = '') {
        if ($this->isReady) {
            $arrReturned = $this->matchRegex($this->_strSource, IMDB::IMDB_CAST);
            $arrChar     = $this->matchRegex($this->_strSource, IMDB::IMDB_CHAR);
            if (count($arrReturned[2])) {
                foreach ($arrReturned[2] as $i => $strName) {
                    if ($i >= $intLimit) {
                        break;
                    }
                    $arrChar[1][$i] = trim(preg_replace('~\((.*)\)~Ui', '', $arrChar[1][$i]));
                    preg_match_all('~<a href="/character/ch(\d+)/">(.*)</a>~Ui', $arrChar[1][$i], $arrMatches);
                    if (isset($arrMatches[1][0]) && isset($arrMatches[2][0])) {
                        $arrReturn[] = '<a href="http://www.imdb.com/name/nm' . trim($arrReturned[1][$i]) . '/"' . ($strTarget ? ' target="' . $strTarget . '"' : '') . '>' . trim($strName) . '</a> as <a href="http://www.imdb.com/character/ch' . trim($arrMatches[1][0]) . '/">' . trim($arrMatches[2][0]) . '</a>';
                    } else {
                        if ($arrChar[1][$i]) {
                            $arrReturn[] = '<a href="http://www.imdb.com/name/nm' . trim($arrReturned[1][$i]) . '/"' . ($strTarget ? ' target="' . $strTarget . '"' : '') . '>' . trim($strName) . '</a> as ' . strip_tags(trim($arrChar[1][$i]));
                        } else {
                            $arrReturn[] = '<a href="http://www.imdb.com/name/nm' . trim($arrReturned[1][$i]) . '/"' . ($strTarget ? ' target="' . $strTarget . '"' : '') . '>' . trim($strName) . '</a>';
                        }
                    }
                }
                return implode($this->strSeperator, $arrReturn) . ($bolMore && (count($arrReturned[2]) > $intLimit) ? '…' : '');
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns the color.
     *
     * @return string The movie color.
     */
    public function getColor() {
        if ($this->isReady) {
            if ($strReturn = $this->matchRegex($this->_strSource, IMDB::IMDB_COLOR, 1)) {
                return $strReturn;
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns the companies.
     *
     * @return array The movie companies.
     */
    public function getCompany() {
        if ($this->isReady) {
            $strContainer = $this->matchRegex($this->_strSource, IMDB::IMDB_COMPANY, 1);
            $arrReturned  = $this->matchRegex($strContainer, IMDB::IMDB_COMPANY_NAME);
            if (count($arrReturned[2])) {
                foreach ($arrReturned[2] as $i => $strName) {
                    $arrReturn[] = trim($strName);
                }
                return implode($this->strSeperator, $arrReturn);
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns the companies as URL.
     *
     * @return array The movie companies as URL.
     */
    public function getCompanyAsUrl($strTarget = '') {
        if ($this->isReady) {
            $strContainer = $this->matchRegex($this->_strSource, IMDB::IMDB_COMPANY, 1);
            $arrReturned  = $this->matchRegex($strContainer, IMDB::IMDB_COMPANY_NAME);
            if (count($arrReturned[2])) {
                foreach ($arrReturned[2] as $i => $strName) {
                    $arrReturn[] = '<a href="http://www.imdb.com/company/co' . trim($arrReturned[1][$i]) . '/"' . ($strTarget ? ' target="' . $strTarget . '"' : '') . '>' . trim($strName) . '</a>';
                }
                return implode($this->strSeperator, $arrReturn);
            }
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
                    $arrReturn[] = trim($strName);
                }
                return implode($this->strSeperator, $arrReturn);
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns the countr(y|ies) as URL
     *
     * @return array The movie countr(y|ies) as URL.
     */
    public function getCountryAsUrl($strTarget = '') {
        if ($this->isReady) {
            $arrReturned = $this->matchRegex($this->_strSource, IMDB::IMDB_COUNTRY);
            if (count($arrReturned[2])) {
                foreach ($arrReturned[2] as $i => $strName) {
                    $arrReturn[] = '<a href="http://www.imdb.com/country/' . trim($arrReturned[1][$i]) . '/"' . ($strTarget ? ' target="' . $strTarget . '"' : '') . '>' . trim($strName) . '</a>';
                }
                return implode($this->strSeperator, $arrReturn);
            }
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
            $strContainer = $this->matchRegex($this->_strSource, IMDB::IMDB_CREATOR, 1);
            $arrReturned  = $this->matchRegex($strContainer, IMDB::IMDB_NAME);
            if (count($arrReturned[2])) {
                foreach ($arrReturned[2] as $i => $strName) {
                    $arrReturn[] = trim($strName);
                }
                return implode($this->strSeperator, $arrReturn);
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns the creator(s) as URL.
     *
     * @return array The movie creator(s) as URL.
     */
    public function getCreatorAsUrl($strTarget = '') {
        if ($this->isReady) {
            $strContainer = $this->matchRegex($this->_strSource, IMDB::IMDB_CREATOR, 1);
            $arrReturned  = $this->matchRegex($strContainer, IMDB::IMDB_NAME);
            if (count($arrReturned[2])) {
                foreach ($arrReturned[2] as $i => $strName) {
                    $arrReturn[] = '<a href="http://www.imdb.com/name/nm' . trim($arrReturned[1][$i]) . '/"' . ($strTarget ? ' target="' . $strTarget . '"' : '') . '>' . trim($strName) . '</a>';
                }
                return implode($this->strSeperator, $arrReturn);
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns the description.
     *
     * @return string The movie description.
     */
    public function getDescription() {
        if ($this->isReady) {
            if ($strReturn = $this->matchRegex($this->_strSource, IMDB::IMDB_DESCRIPTION, 1)) {
                return trim($strReturn);
            }
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
            $strContainer = $this->matchRegex($this->_strSource, IMDB::IMDB_DIRECTOR, 1);
            $arrReturned  = $this->matchRegex($strContainer, IMDB::IMDB_NAME);
            if (count($arrReturned[2])) {
                foreach ($arrReturned[2] as $i => $strName) {
                    $arrReturn[] = trim($strName);
                }
                return implode($this->strSeperator, $arrReturn);
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns the director(s) as URL.
     *
     * @return array The movie director(s) as URL.
     */
    public function getDirectorAsUrl($strTarget = '') {
        if ($this->isReady) {
            $strContainer = $this->matchRegex($this->_strSource, IMDB::IMDB_DIRECTOR, 1);
            $arrReturned  = $this->matchRegex($strContainer, IMDB::IMDB_NAME);
            if (count($arrReturned[2])) {
                foreach ($arrReturned[2] as $i => $strName) {
                    $arrReturn[] = '<a href="http://www.imdb.com/name/nm' . trim($arrReturned[1][$i]) . '/"' . ($strTarget ? ' target="' . $strTarget . '"' : '') . '>' . trim($strName) . '</a>';
                }
                return implode($this->strSeperator, $arrReturn);
            }
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
                    $arrReturn[] = trim($strName);
                }
                return implode($this->strSeperator, array_unique($arrReturn));
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns the genres as URL.
     *
     * @return array The movie genre as URL.
     */
    public function getGenreAsUrl($strTarget = '') {
        if ($this->isReady) {
            $arrReturned = $this->matchRegex($this->_strSource, IMDB::IMDB_GENRE);
            if (count($arrReturned[1])) {
                foreach ($arrReturned[1] as $i => $strName) {
                    $arrReturn[] = '<a href="http://www.imdb.com/genre/' . trim($strName) . '/"' . ($strTarget ? ' target="' . $strTarget . '"' : '') . '>' . trim($strName) . '</a>';
                }
                return implode($this->strSeperator, array_unique($arrReturn));
            }
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
                    $arrReturn[] = trim($strName);
                }
                return implode($this->strSeperator, $arrReturn);
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns the language(s) as URL.
     *
     * @return string The movie language(s) as URL.
     */
    public function getLanguagesAsUrl($strTarget = '') {
        if ($this->isReady) {
            $arrReturned = $this->matchRegex($this->_strSource, IMDB::IMDB_LANGUAGES);
            if (count($arrReturned[2])) {
                foreach ($arrReturned[2] as $i => $strName) {
                    $arrReturn[] = '<a href="http://www.imdb.com/language/' . trim($arrReturned[1][$i]) . '"' . ($strTarget ? ' target="' . $strTarget . '"' : '') . '>' . trim($strName) . '</a>';
                }
                return implode($this->strSeperator, $arrReturn);
            }
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
                return trim($strReturn);
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns the movie location as URL.
     *
     * @return string The location of the movie as URL.
     */
    public function getLocationAsUrl($strTarget = '') {
        if ($this->isReady) {
            if ($strReturn = $this->matchRegex($this->_strSource, IMDB::IMDB_LOCATION, 2)) {
                return '<a href="http://www.imdb.com/search/title?locations=' . urlencode(trim($strReturn)) . '"' . ($strTarget ? ' target="' . $strTarget . '"' : '') . '>' . trim($strReturn) . '</a>';
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns the MPAA.
     *
     * @return string The movie MPAA.
     */
    public function getOpening() {
        if ($this->isReady) {
            if ($strReturn = $this->matchRegex($this->_strSource, IMDB::IMDB_OPENING, 1)) {
                return trim($strReturn);
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns the opening weekend revenue.
     *
     * @return string The opening weekend revenue.
     */
    public function getMpaa() {
        if ($this->isReady) {
            $strReturn = $this->matchRegex($this->_strSource, IMDB::IMDB_MPAA);
            if ($strReturn && isset($strReturn[1]) && isset($strReturn[1][0])) {
                return trim($strReturn[1][0]);
            }
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
                    return strip_tags($this->getShortText($strReturn, $intLimit));
                }
                return trim(strip_tags($strReturn));
            }
        }
        return $this->strNotFound;
    }

    /**
     * Download the poster, cache it and return the local path to the image.
     *
     * @return string The path to the poster (either local or online).
     */
    public function getPoster($sSize = 'small') {
        if ($this->isReady) {

            // Check if matches a poster url
            $strReturn = $this->matchRegex($this->_strSource, IMDB::IMDB_POSTER, 1);
            if (!$strReturn) {
                return $this->strNotFound;
            }
            // check if its big
            if (strtolower($sSize) == 'big') {
                $strReturn = substr($strReturn, 0, strpos($strReturn, '_'));
            }

            $strLocal = $this->saveImage($strReturn);
            if (!$strLocal) {
                return $this->strNotFound;
            }
            return $strLocal;
        }
    }

    /**
     * Returns the rating.
     *
     * @return string The movie rating.
     */
    public function getRating() {
        if ($this->isReady) {
            if ($strReturn = $this->matchRegex($this->_strSource, IMDB::IMDB_RATING, 1)) {
                return trim($strReturn);
            }
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
                return str_replace('(', ' (', trim($strReturn));
            }
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
                return trim($strReturn);
            }
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
                $strFind   = array(
                    '&raquo;',
                    '&nbsp;',
                    'Full episode list',
                    ' '
                );
                $strReturn = str_replace($strFind, '', $strReturn);
                $arrReturn = explode('|', $strReturn);
                if ($arrReturn[0]) {
                    return implode($this->strSeperator, array_reverse($arrReturn));
                }
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns the seasons as URL.
     *
     * @return string The movie seasons.
     */
    public function getSeasonsAsUrl() {
        if ($this->isReady) {
            if ($strReturn = $this->matchRegex($this->_strSource, IMDB::IMDB_SEASONS)) {
                $strReturn  = strip_tags(implode($strReturn[1]));
                $strFind    = array(
                    '&raquo;',
                    '&nbsp;',
                    'Full episode list',
                    ' '
                );
                $strReturn  = str_replace($strFind, '', $strReturn);
                $arrSeasons = explode('|', $strReturn);
                if ($arrSeasons[0]) {
                    foreach (array_reverse($arrSeasons) as $sSeasons) {
                        $arrReturn[] = '<a href="http://www.imdb.com/title/tt' . $this->_strId . '/episodes?season=' . $sSeasons . '">' . $sSeasons . '</a>';
                    }
                    return implode($this->strSeperator, $arrReturn);
                }
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns the official sites as URL.
     *
     * @return string The official sites as URL.
     */
    public function getSitesAsUrl($strTarget = '') {
        if ($this->isReady) {
            $strContainer = $this->matchRegex($this->_strSource, IMDB::IMDB_SITES, 1);
            $arrReturned  = $this->matchRegex($strContainer, IMDB::IMDB_SITES_A);
            if (count($arrReturned[2])) {
                foreach ($arrReturned[2] as $i => $strName) {
                    if (strtolower(substr($arrReturned[1][$i], 0, 9)) == '/offsite/') {
                        $arrReturn[] = '<a href="http://www.imdb.com' . $arrReturned[1][$i] . '"' . ($strTarget ? ' target="' . $strTarget . '"' : '') . '>' . trim($strName) . '</a>';
                    }
                }
                return $arrReturn != null ? implode($this->strSeperator, $arrReturn) : $this->strNotFound;
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns the sound mix(es).
     *
     * @return string The sound mix(es).
     */
    public function getSoundMix() {
        if ($this->isReady) {
            $strContainer = $this->matchRegex($this->_strSource, IMDB::IMDB_SOUND_MIX, 1);
            $arrReturned  = $this->matchRegex($strContainer, IMDB::IMDB_SOUND_MIX_A);
            if (count($arrReturned[1])) {
                foreach ($arrReturned[1] as $i => $strName) {
                    $arrReturn[] = trim($strName);
                }
                return implode($this->strSeperator, $arrReturn);
            }
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
                return trim($strReturn);
            }
        }
        return $this->strNotFound;
    }

    /**
     * Return the title.
     *
     * @param  bool   Try to get the local naming of the movie first.
     * @return string The movie title.
     */
    public function getTitle($bForceLocal = FALSE) {
        if ($this->isReady) {
            if ($strReturn = $this->matchRegex($this->_strSource, ($bForceLocal ? IMDB::IMDB_TITLE : IMDB::IMDB_TITLE_ORIG), 1)) {
                return ltrim(rtrim(trim($strReturn), '"'), '"');
            }
            if ($strReturn = $this->matchRegex($this->_strSource, ($bForceLocal ? IMDB::IMDB_TITLE_ORIG : IMDB::IMDB_TITLE), 1)) {
                return trim($strReturn);
            }
        }
        return $this->strNotFound;
    }

    /** Return the first video found (should be the trailer). Thanks to Seifer Almasy.
     *
     * @return string The url to the trailer.
     */
    public function getTrailerAsUrl() {
        if ($this->isReady) {
            if ($strReturn = $this->matchRegex($this->_strSource, IMDB::IMDB_TRAILER, 1)) {
                return 'http://www.imdb.com/video/' . $strReturn . '/player';
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns the URL.
     *
     * @return string The movie URL.
     */
    public function getUrl() {
        if ($this->isReady) {
            return $this->_strUrl;
        }
        return $this->strNotFound;
    }

    /**
     * Returns the votes.
     *
     * @return string The votes of the movie.
     */
    public function getVotes() {
        if ($this->isReady) {
            if ($strReturn = $this->matchRegex($this->_strSource, IMDB::IMDB_VOTES, 1)) {
                return trim($strReturn);
            }
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
            $strContainer = $this->matchRegex($this->_strSource, IMDB::IMDB_WRITER, 1);
            $arrReturned  = $this->matchRegex($strContainer, IMDB::IMDB_NAME);
            if (count($arrReturned[2])) {
                foreach ($arrReturned[2] as $i => $strName) {
                    $arrReturn[] = trim($strName);
                }
                return implode($this->strSeperator, $arrReturn);
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns the writer(s) as URL.
     *
     * @return array The movie writer(s) as URL.
     */
    public function getWriterAsUrl($strTarget = '') {
        if ($this->isReady) {
            $strContainer = $this->matchRegex($this->_strSource, IMDB::IMDB_WRITER, 1);
            $arrReturned  = $this->matchRegex($strContainer, IMDB::IMDB_NAME);
            if (count($arrReturned[2])) {
                foreach ($arrReturned[2] as $i => $strName) {
                    $arrReturn[] = '<a href="http://www.imdb.com/name/nm' . trim($arrReturned[1][$i]) . '/"' . ($strTarget ? ' target="' . $strTarget . '"' : '') . '>' . trim($strName) . '</a>';
                }
                return implode($this->strSeperator, $arrReturn);
            }
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
            if ($strReturn = $this->matchRegex($this->_strSource, IMDB::IMDB_YEAR, 1)) {
                return substr(preg_replace('~[\D]~', '', $strReturn), 0, 4);
            }
        }
        return $this->strNotFound;
    }

    /**
     * Returns all the information found.
     *
     * @return Object An object with all the information found.
     */
    public function getAll() {
        $oData                        = new stdClass;
        $oData->aka                   = $this->getAka();
        $oData->aspectRatio           = $this->getAspectRatio();
        $oData->budget                = $this->getBudget();
        $oData->cast                  = $this->getCast();
        $oData->fullCast              = $this->getFullCast();
        $oData->castAsUrl             = $this->getCastAsUrl();
        $oData->castAndCharacter      = $this->getCastAndCharacter();
        $oData->castAndCharacterAsUrl = $this->getCastAndCharacterAsUrl();
        $oData->color                 = $this->getColor();
        $oData->company               = $this->getCompany();
        $oData->companyAsUrl          = $this->getCompanyAsUrl();
        $oData->country               = $this->getCountry();
        $oData->countryAsUrl          = $this->getCountryAsUrl();
        $oData->creator               = $this->getCreator();
        $oData->creatorAsUrl          = $this->getCreatorAsUrl();
        $oData->description           = $this->getDescription();
        $oData->director              = $this->getDirector();
        $oData->directorAsUrl         = $this->getDirectorAsUrl();
        $oData->genre                 = $this->getGenre();
        $oData->genreAsUrl            = $this->getGenreAsUrl();
        $oData->languages             = $this->getLanguages();
        $oData->languagesAsUrl        = $this->getLanguagesAsUrl();
        $oData->location              = $this->getLocation();
        $oData->locationAsUrl         = $this->getLocationAsUrl();
        $oData->mpaa                  = $this->getMpaa();
        $oData->opening               = $this->getOpening();
        $oData->plot                  = $this->getPlot();
        $oData->poster                = $this->getPoster();
        $oData->rating                = $this->getRating();
        $oData->releaseDate           = $this->getReleaseDate();
        $oData->runtime               = $this->getRuntime();
        $oData->seasons               = $this->getSeasons();
        $oData->seasonsAsUrl          = $this->getSeasonsAsUrl();
        $oData->soundMix              = $this->getSoundMix();
        $oData->sitesAsUrl            = $this->getSitesAsUrl();
        $oData->tagline               = $this->getTagline();
        $oData->title                 = $this->getTitle();
        $oData->trailerAsUrl          = $this->getTrailerAsUrl();
        $oData->url                   = $this->getUrl();
        $oData->votes                 = $this->getVotes();
        $oData->writers               = $this->getWriter();
        $oData->writersAsUrl          = $this->getWriterAsUrl();
        $oData->year                  = $this->getYear();
        return $oData;
    }
}
