<?php
/**
* IMDB PHP Parser.
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
* @version 3.4 (2009-10-04)
*/

class IMDB {
	private $_sSource = null;
	private $_sUrl    = null;
	private $_sId     = null;
	public  $_bFound  = false;

	// Latest update: 2009-10-04
	const IMDB_COUNTRY      = '#<h5>Country:</h5><a href="(.*)">(.*)</a></div>#Uis';
	const IMDB_DIRECTOR     = '#<a href="/name/(.*)/" onclick="\(new Image\(\)\).src=\'/rg/directorlist/position-1/images/b.gif\?link=name/(.*)/\';">(.*)</a><br/>#Uis';
	const IMDB_MPAA         = '#<h5><a href="/mpaa">MPAA</a>:</h5> (.*)</div>#Uis';
	const IMDB_PLOT         = '#<h5>Plot:</h5>(.*) <a#Uis';
	const IMDB_RATING       = '#<div class="meta"><b>(.*)</b>#Uis';
	const IMDB_RELEASE_DATE = '#<h5>Release Date:</h5> (.*) \((.*)\) <a#Uis';
	const IMDB_RUNTIME      = '#Runtime:</h5>(.*) (.*)</div>#Uis';
	const IMDB_POSTER       = '#<a name="poster" href="(.*)" title="(.*)"><img border="0" alt="(.*)" title="(.*)" src="(.*)" /></a>#Uis';
	const IMDB_TITLE        = '#<title>(.*) \((.*)\)</title>#Uis';
	const IMDB_VOTES        = '#&nbsp;&nbsp;<a href="ratings" class="tn15more">(.*) votes</a>#Uis';
	const IMDB_URL          = '#http://(.*\.|.*)imdb.com/(t|T)itle(\?|/)(..\d+)#i';
	const IMDB_SEARCH       = '#<b>Media from&nbsp;<a href="/title/tt(\d+)/"#i';

	/**
	 * Public constructor.
	 *
	 * @param string $sSearch
	 */
	public function __construct($sSearch) {
		$sUrl = $this->findUrl($sSearch);
		if ($sUrl) {
			$bFetch        = $this->fetchUrl($this->_sUrl);
			$this->_bFound = true;
		}
	}

	/**
	 * Little REGEX helper.
	 *
	 * @param string $sRegex
	 * @param string $sContent
	 * @param int    $iIndex;
	 */
	private function getMatch($sRegex, $sContent, $iIndex = 1) {
		preg_match($sRegex, $sContent, $aMatches);
		if ($iIndex > count($aMatches)) return;
		if ($iIndex == null) {
			return $aMatches;
		}
		return $aMatches[(int)$iIndex];
	}

	/**
	 * Find a valid Url out of the passed argument.
	 *
	 * @param string $sSearch
	 */
	private function findUrl($sSearch) {
		$sSearch = trim($sSearch);
		if ($aUrl = $this->getMatch(self::IMDB_URL, $sSearch, 4)) {
			$this->_sId  = 'tt' . ereg_replace('[^0-9]', '', $aUrl);
			$this->_sUrl = 'http://www.imdb.com/title/' . $this->_sId .'/';
			return true;
		} else {
			$sTemp    = 'http://www.imdb.com/find?s=all&q=' . str_replace(' ', '+', $sSearch) . '&x=0&y=0';
			$bFetch   = $this->fetchUrl($sTemp);
			if ($bFetch) {
				if ($strMatch = $this->getMatch(self::IMDB_SEARCH, $this->_sSource)) {
					$this->_sUrl = 'http://www.imdb.com/title/tt' . $strMatch . '/';
					unset($this->_sSource);
					return true;
				}
			}
		}
		return false;
	}

	/**
	* Fetch data from given Url.
	* Uses cURL if installed, otherwise falls back to file_get_contents.
	*
	* @param string $sUrl
	* @param int    $iTimeout;
	*/
	private function fetchUrl($sUrl, $iTimeout = 15) {
		$sUrl = trim($sUrl);
		if (function_exists('curl_init')) {
			$oCurl = curl_init($sUrl);
			curl_setopt_array($oCurl, array (
											CURLOPT_VERBOSE => 0,
											CURLOPT_HEADER => 0,
											CURLOPT_FRESH_CONNECT => true,
											CURLOPT_RETURNTRANSFER => 1,
											CURLOPT_TIMEOUT => (int)$iTimeout,
											CURLOPT_CONNECTTIMEOUT => (int)$iTimeout,
											CURLOPT_REFERER => $sUrl));
			$sOutput = curl_exec($oCurl);

			if ($sOutput === false) {
				return false;
			}

			$aInfo = curl_getinfo($oCurl);
			if ($aInfo['http_code'] != 200) {
				return false;
			}
			$this->_sSource = str_replace("\n", '', (string)$sOutput);
			curl_close($oCurl);
			return true;
		} else {
			$sOutput = @file_get_contents($sUrl, 0);
			if (strpos($http_response_header[0], '200') === false){
				return false;
			}
			$this->_sSource = str_replace("\n", '', (string)$sOutput);
			return true;
		}
	}

	/**
	 * Get the country of the current movie.
	 */
	public function getCountry() {
		if ($this->_sSource) {
			return $this->getMatch(self::IMDB_COUNTRY, $this->_sSource, 2);
		}
		return false;
	}

	/**
	 * Get the country url of the current movie.
	 */
	public function getCountryUrl() {
		if ($this->_sSource) {
			return 'http://www.imdb.com' . $this->getMatch(self::IMDB_COUNTRY, $this->_sSource);
		}
		return false;
	}

	/**
	 * Get the director of the current movie.
	 */
	public function getDirector() {
		if ($this->_sSource) {
			return $this->getMatch(self::IMDB_DIRECTOR, $this->_sSource, 3);
		}
		return false;
	}

	/**
	 * Get the director of the current movie.
	 */
	public function getDirectorUrl() {
		if ($this->_sSource) {
			return 'http://www.imdb.com/name/' . $this->getMatch(self::IMDB_DIRECTOR, $this->_sSource);
		}
		return false;
	}

	/**
	 * Get the mpaa of the current movie.
	 */
	public function getMpaa() {
		if ($this->_sSource) {
			return $this->getMatch(self::IMDB_MPAA, $this->_sSource);
		}
		return false;
	}

	/**
	 * Get the plot of the current movie.
	 */
	public function getPlot() {
		if ($this->_sSource) {
			return $this->getMatch(self::IMDB_PLOT, $this->_sSource);
		}
		return false;
	}

	/**
	 * Get the rating of the current movie.
	 */
	public function getRating() {
		if ($this->_sSource) {
			return $this->getMatch(self::IMDB_RATING, $this->_sSource);
		}
		return false;
	}


	/**
	 * Get the release date of the current movie.
	 */
	public function getReleaseDate() {
		if ($this->_sSource) {
			return $this->getMatch(self::IMDB_RELEASE_DATE, $this->_sSource);
		}
		return false;
	}

	/**
	 * Get the runtime of the current movie.
	 */
	public function getRuntime() {
		if ($this->_sSource) {
			return $this->getMatch(self::IMDB_RUNTIME, $this->_sSource);
		}
		return false;
	}

	/**
	 * Get the release date of the current movie.
	 */
	public function getTitle() {
		if ($this->_sSource) {
			return $this->getMatch(self::IMDB_TITLE, $this->_sSource);
		}
		return false;
	}

	/**
	 * Get the url of the current movie.
	 */
	public function getUrl() {
		return $this->_sUrl;
	}

	/**
	 * Get the votes of the current movie.
	 */
	public function getVotes() {
		if ($this->_sSource) {
			return $this->getMatch(self::IMDB_VOTES, $this->_sSource);
		}
		return false;
	}

	/**
	 * Download the poster, cache it and return the local path of the current movie.
	 */
	public function getPoster() {
		if ($this->_sSource) {
			return $this->getMatch(self::IMDB_POSTER, $this->_sSource, 5);
		}
		return false;
	}
}
