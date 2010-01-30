<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
    "http://www.w3.org/TR/html4/strict.dtd"
    >
<html lang="en">
<head>
    <title>IMDB PHP Parser | Examples</title>
    <style type="text/css">
        body {background-color:#fff;color:#000;font-family:Verdana,Arial,sans-serif;font-size:13px;}
    </style>
</head>
<body>
<?php
include_once 'imdb.class.php';

$oIMDB = new IMDB('New York, I Love You');
if ($oIMDB->_bFound) {
	echo 'Cast: <b>' . $oIMDB->getCast(5) . '</b><br>';
	echo 'Cast as URL: <b>' . $oIMDB->getCastAsUrl(15) . '</b><br>';
	echo 'Countries: <b>' . $oIMDB->getCountry() . '</b><br>';
	echo 'Countries as URL: <b>' . $oIMDB->getCountryAsUrl() . '</b><br>';
	echo 'Directors: <b>' . $oIMDB->getDirector() . '</b><br>';
	echo 'Directors as URL: <b>' . $oIMDB->getDirectorAsUrl() . '</b><br>';
	echo 'Genres: <b>' . $oIMDB->getGenre() . '</b><br>';
	echo 'Genres as URL: <b>' . $oIMDB->getGenreAsUrl() . '</b><br>';
	echo 'MPAA: <b>' . $oIMDB->getMpaa() . '</b><br>';
	echo 'Plot: <b>' . $oIMDB->getPlot() . '</b><br>';
	echo 'Poster: <b>' . $oIMDB->getPoster() . '</b><br>';
	echo 'Rating: <b>' . $oIMDB->getRating() . '</b><br>';
	echo 'Release Date: <b>' . $oIMDB->getReleaseDate() . '</b><br>';
	echo 'Runtime: <b>' . $oIMDB->getRuntime() . '</b><br>';
	echo 'Tagline: <b>' . $oIMDB->getTagline() . '</b><br>';
	echo 'Title: <b>' . $oIMDB->getTitle() . '</b><br>';
	echo 'Url: <b>' . $oIMDB->getUrl() . '</b><br>';
	echo 'Votes: <b>' . $oIMDB->getVotes() . '</b><br>';
	echo 'Year: <b>' . $oIMDB->getYear() . '</b><br>';
	echo 'Writers: <b>' . $oIMDB->getWriter() . '</b><br>';
	echo 'Writers as URL: <b>' . $oIMDB->getWriterAsUrl() . '</b><br>';
} else {
	echo 'Movie not found!';
}

echo '<hr>';

$oIMDB = new IMDB('http://us.imdb.com/Title?0144117');
if ($oIMDB->_bFound) {
	echo '<a href="' . $oIMDB->getUrl() . '">' . $oIMDB->getTitle() . '</a> got rated ' . $oIMDB->getRating() . '.';
} else {
	echo 'Movie not found!';
}

echo '<hr>';

$oIMDB = new IMDB('http://www.imdb.com/title/tt1022603/');
if ($oIMDB->_bFound) {
	echo '<a href="' . $oIMDB->getUrl() . '">' . $oIMDB->getTitle() . '</a> got rated ' . $oIMDB->getRating() . '.<br>';
	echo 'About the movie: <b>' . $oIMDB->getPlot() . '</b><br>';
	echo '<img src="' . $oIMDB->getPoster() . '">' . '</b><br>';
} else {
	echo 'Movie not found!';
}

echo '<hr>';

$oIMDB = new IMDB('Fabian Beiner never made a movie. Yet!');
if ($oIMDB->_bFound) {
	echo $oIMDB->getTitle() . '</b><br>';
} else {
	echo 'Movie not found!';
}
?>
</body>
</html>
