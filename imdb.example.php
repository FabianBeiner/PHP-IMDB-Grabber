<?php
include_once 'imdb.class.php';

$oIMDB = new IMDB('Cruel Intentions');
if ($oIMDB->_bFound) {
	echo 'Title: ' . $oIMDB->getTitle() . '<br>';
	echo 'Country: ' . $oIMDB->getCountry() . '<br>';
	echo 'Country Url: ' . $oIMDB->getCountryUrl() . '<br>';
	echo 'Director: ' . $oIMDB->getDirector() . '<br>';
	echo 'Director Url: ' . $oIMDB->getDirectorUrl() . '<br>';
	echo 'MPAA: ' . $oIMDB->getMpaa() . '<br>';
	echo 'Plot: ' . $oIMDB->getPlot() . '<br>';
	echo 'Rating: ' . $oIMDB->getRating() . '<br>';
	echo 'Release Date: ' . $oIMDB->getReleaseDate() . '<br>';
	echo 'Runtime: ' . $oIMDB->getRuntime() . '<br>';
	echo 'Url: ' . $oIMDB->getUrl() . '<br>';
	echo 'Votes: ' . $oIMDB->getVotes() . '<br>';
	echo 'Poster Url: ' . $oIMDB->getPoster() . '<br>';
	echo 'Tagline: ' . $oIMDB->getTagline() . '<br>';
	echo 'Year: ' . $oIMDB->getYear() . '<br>';
} else {
	echo 'Movie not found!';
}

echo '<hr>';

$oIMDB = new IMDB('http://us.imdb.com/Title?0118883');
if ($oIMDB->_bFound) {
	echo '<a href="' . $oIMDB->getUrl() . '">' . $oIMDB->getTitle() . '</a> got rated ' . $oIMDB->getRating() . '.';
} else {
	echo 'Movie not found!';
}

echo '<hr>';

$oIMDB = new IMDB('http://www.imdb.com/title/tt0478087/');
if ($oIMDB->_bFound) {
	echo '<a href="' . $oIMDB->getUrl() . '">' . $oIMDB->getTitle() . '</a> got rated ' . $oIMDB->getRating() . '.<br>';
	echo 'About the movie: <b>' . $oIMDB->getPlot() . '"</b><br>';
	echo '<img src="' . $oIMDB->getPoster() . '">' . '<br>';
} else {
	echo 'Movie not found!';
}

echo '<hr>';

$oIMDB = new IMDB('Fabian Beiner never made a movie. Yet!');
if ($oIMDB->_bFound) {
	echo $oIMDB->getTitle() . '<br>';
} else {
	echo 'Movie not found!';
}
