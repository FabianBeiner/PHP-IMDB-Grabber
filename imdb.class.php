<?php
/**
 * IMDB PHP Parser
 *
 * This class can be used to retrieve data from IMDB.com with PHP. This script will fail once in
 * a while, when IMDB changes *anything* on their HTML. Guys, it's time to provide an API!
 *
 * Original idea by David Walsh (http://davidwalsh.name).
 *
 *
 * @link http://fabian-beiner.de
 * @copyright 2009 Fabian Beiner
 * @author Fabian Beiner (mail [AT] fabian-beiner [DOT] de)
 * @license MIT License
 *
 * @version 3.1 (2009-09-30)
 */

class IMDB {
    private $strSource;
    private $strURL;

    public function __construct($strSearch) {
        $this->strSource = $this->imdbHandler($strSearch);
    }

    private function imdbHandler($strSearch) {
        $strSearch = trim($strSearch);
        if (($arrURL = $this->getMatch('#http://(.*\.|.*)imdb.com/title/tt(\d+)/#i', $strSearch, null)) OR ($arrURL = $this->getMatch('#http://(.*\.|.*)imdb.com/Title\?(\d+)#i', $strSearch, null))) {
            if (empty($arrURL[1])) {
                $strSuffix = 'www.';
            } else {
                $strSuffix = $arrURL[1];
            }
            $this->strURL = 'http://' . $strSuffix . 'imdb.com/title/tt' . $arrURL[2] .'/';
        } else {
            $strTmpURL = 'http://www.imdb.com/find?s=all&q=' . str_replace(' ', '+', $strSearch) . '&x=0&y=0';
            $strSource = $this->fetchUrl($strTmpURL);
            $strMatch  = $this->getMatch('|<b>Media from&nbsp;<a href="/title/tt(\d+)/"|i', $strSource);
            if ($strMatch) {
                $this->strURL = 'http://www.imdb.com/title/tt' . $strMatch . '/';
            } else {
                return false;
            }
        }
        unset($strSource);
        $strSource = $this->fetchUrl($this->strURL);
        if ($strSource) {
            return str_replace("\n",'',(string)$strSource);
        }
        return false;
    }

    private function fetchUrl($strURL, $intTimeout = 5) {
        $strURL = trim($strURL);
        if ($this->getMatch('|^http://(.*)$|i', $strURL)) {
            if (extension_loaded('curl') AND function_exists('curl_init')) {
                $objCurl = curl_init($strURL);
                curl_setopt_array($objCurl, array(CURLOPT_VERBOSE => 0,
                                                  CURLOPT_NOBODY => 0,
                                                  CURLOPT_HEADER => 0,
                                                  CURLOPT_FRESH_CONNECT => true,
                                                  CURLOPT_RETURNTRANSFER => 1,
                                                  CURLOPT_TIMEOUT => (int)$intTimeout,
                                                  CURLOPT_CONNECTTIMEOUT => (int)$intTimeout
                                                 )
                                 );
                $strSource     = curl_exec($objCurl);
                $strResponse   = curl_getinfo($objCurl);
                curl_close($objCurl);
                if(intval($strResponse['http_code']) == 200) {
                    return str_replace("\n",'',(string)$strSource);
                }
                return false;
            } else {
                if(false == ($strSource = file_get_contents($strUrl))) {
                    return false;
                }
                return str_replace("\n",'',(string)$strSource);
            }
        }
        return false;
    }

    private function getMatch($strRegex, $strContent, $intIndex = 1) {
        preg_match($strRegex, $strContent, $arrMatches);
        if ($intIndex == null) {
            return $arrMatches;
        }
        return $arrMatches[(int)$intIndex];
    }

    public function getFullInfo() {
        return $this->getInfo('full');
    }

    public function getInfo($strWhat) {
        switch (strtolower(trim($strWhat))) {
            case 'country':
                return ($strOutput = trim($this->getMatch('|<h5>Country:</h5><a href="(.*)">(.*)</a></div>|Uis', $this->strSource, 2))) ? $strOutput : false;
                break;
            case 'country_url':
                return ($strOutput = 'http://www.imdb.com' . trim($this->getMatch('|<h5>Country:</h5><a href="(.*)">(.*)</a></div>|Uis', $this->strSource))) ? $strOutput : false;
                break;
            case 'director':
                return ($strOutput = trim($this->getMatch('|<a href="/name/(.*)/" onclick="\(new Image\(\)\).src=\'/rg/directorlist/position-1/images/b.gif\?link=name/(.*)/\';">(.*)</a><br/>|Uis', $this->strSource, 3))) ? $strOutput : false;
                break;
            case 'director_url':
                return ($strOutput = 'http://www.imdb.com/name/' . trim($this->getMatch('|<a href="/name/(.*)/" onclick="\(new Image\(\)\).src=\'/rg/directorlist/position-1/images/b.gif\?link=name/(.*)/\';">(.*)</a><br/>|Uis', $this->strSource, 1)) . '/') ? $strOutput : false;
                break;
            case 'mpaa':
                return ($strOutput = trim($this->getMatch('|<h5><a href="/mpaa">MPAA</a>:</h5> (.*)</div>|Uis', $this->strSource))) ? $strOutput : false;
                break;
            case 'plot':
                return ($strOutput = trim($this->getMatch('|<h5>Plot:</h5>(.*) <a|Uis', $this->strSource))) ? $strOutput : false;
                break;
            case 'rating':
                return ($strOutput = trim($this->getMatch('|<div class="meta"><b>(.*)</b>|Uis', $this->strSource))) ? $strOutput : false;
                break;
            case 'release_date':
                return ($strOutput = trim($this->getMatch('|<h5>Release Date:</h5> (.*) \((.*)\) <a|Uis', $this->strSource))) ? $strOutput : false;
                break;
            case 'runtime':
                return ($strOutput = trim($this->getMatch('|Runtime:</h5>(.*) (.*)</div>|Uis', $this->strSource))) ? $strOutput : false;
                break;
            case 'thumb':
                return ($strOutput = trim($this->getMatch('|<a name="poster" href="(.*)" title="(.*)"><img border="0" alt="(.*)" title="(.*)" src="(.*)" /></a>|Uis', $this->strSource, 5))) ? $strOutput : false;
                break;
            case 'title':
                return ($strOutput = trim($this->getMatch('|<title>(.*) \((.*)\)</title>|Uis', $this->strSource))) ? $strOutput : false;
                break;
            case 'url':
                return ($strOutput = $this->strURL) ? $strOutput : 'Sorry, nothing found';
                break;
            case 'votes':
                return ($strOutput = trim($this->getMatch('|&nbsp;&nbsp;<a href="ratings" class="tn15more">(.*) votes</a>|Uis', $this->strSource))) ? $strOutput : false;
                break;
            case 'full':
                return array(
                    'country'      => ($strOutput = trim($this->getMatch('|<h5>Country:</h5><a href="(.*)">(.*)</a></div>|Uis', $this->strSource, 2))) ? $strOutput : '',
                    'country_url'  => ($strOutput = 'http://www.imdb.com' . trim($this->getMatch('|<h5>Country:</h5><a href="(.*)">(.*)</a></div>|Uis', $this->strSource))) ? $strOutput : '',
                    'director'     => ($strOutput = trim($this->getMatch('|<a href="/name/(.*)/" onclick="\(new Image\(\)\).src=\'/rg/directorlist/position-1/images/b.gif\?link=name/(.*)/\';">(.*)</a><br/>|Uis', $this->strSource, 3))) ? $strOutput : '',
                    'director_url' => ($strOutput = 'http://www.imdb.com/name/' . trim($this->getMatch('|<a href="/name/(.*)/" onclick="\(new Image\(\)\).src=\'/rg/directorlist/position-1/images/b.gif\?link=name/(.*)/\';">(.*)</a><br/>|Uis', $this->strSource, 1)) . '/') ? $strOutput : '',
                    'mpaa'         => ($strOutput = trim($this->getMatch('|<h5><a href="/mpaa">MPAA</a>:</h5> (.*)</div>|Uis', $this->strSource))) ? $strOutput : '',
                    'plot'         => ($strOutput = trim($this->getMatch('|<h5>Plot:</h5>(.*) <a|Uis', $this->strSource))) ? $strOutput : '',
                    'rating'       => ($strOutput = trim($this->getMatch('|<div class="meta"><b>(.*)</b>|Uis', $this->strSource))) ? $strOutput : '',
                    'release_date' => ($strOutput = trim($this->getMatch('|<h5>Release Date:</h5> (.*) \((.*)\) <a|Uis', $this->strSource))) ? $strOutput : '',
                    'runtime'      => ($strOutput = trim($this->getMatch('|Runtime:</h5>(.*) (.*)</div>|Uis', $this->strSource))) ? $strOutput : '',
                    'thumb'        => ($strOutput = trim($this->getMatch('|<a name="poster" href="(.*)" title="(.*)"><img border="0" alt="(.*)" title="(.*)" src="(.*)" /></a>|Uis', $this->strSource, 5))) ? $strOutput : '',
                    'title'        => ($strOutput = trim($this->getMatch('|<title>(.*) \((.*)\)</title>|Uis', $this->strSource))) ? $strOutput : '',
                    'url'          => ($strOutput = $this->strURL) ? $strOutput : '',
                    'votes'        => ($strOutput = trim($this->getMatch('|&nbsp;&nbsp;<a href="ratings" class="tn15more">(.*) votes</a>|Uis', $this->strSource))) ? $strOutput : ''
                );
                break;
            default:
                return false;
        }
        return false;
    }
}

// Case #1: With NAME:
$objIMDB = new IMDB('Cruel Intentions');
$arrIMDB = $objIMDB->getFullInfo();
if ($arrIMDB['title']) {
    echo '<pre>';
    print_r($arrIMDB);
    echo '</pre>';
} else {
    echo 'Sorry, nothing found!';
}


echo '<hr>';

// Case #2: With URL:
$objIMDB = new IMDB('http://us.imdb.com/Title?0118883');
if ($objIMDB->getInfo('title')) {
    echo $objIMDB->getInfo('title') . ' (' . $objIMDB->getInfo('url') . ') got ' . $objIMDB->getInfo('votes') . ' votes!';
} else {
    echo 'Sorry, nothing found!';
}


echo '<hr>';

// Case #3: Movie doesn't exists:
$objIMDB = new IMDB('I guess this movie name doesnt exists 123');
if ($objIMDB->getInfo('title')) {
    echo $objIMDB->getInfo('title') . ' (' . $objIMDB->getInfo('url') . ') got ' . $objIMDB->getInfo('votes') . ' votes!';
} else {
    echo 'Sorry, nothing found!';
}
