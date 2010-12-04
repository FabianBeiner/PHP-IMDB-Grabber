<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>PHP-IMDB-Grabber by Fabian Beiner | Examples</title>
  <style>
    body {
      background-color:#aaa;
      color:#111;
      font-family:Corbel, "Lucida Grande", "Lucida Sans Unicode",  "Lucida Sans", "DejaVu Sans", "Bitstream Vera Sans", "Liberation Sans", Verdana, sans-serif;
      font-size:14px;
      margin:20px auto;
      width:700px;
    }
    p {
      margin:0;
      padding:0;
      margin-bottom:5px;
    }
    hr {
      clear:both;
      margin:20px 0;
    }
  </style>
</head>
<body>
<?php
include_once 'imdb.class.php';

$oIMDB = new IMDB('New York, I Love You');
if ($oIMDB->isReady) {
    echo '<p>Budget: <b>' . $oIMDB->getBudget() . '</b></p>';
    echo '<p>Cast (limited to 5): <b>' . $oIMDB->getCast(5) . '</b></p>';
    echo '<p>Cast as URL (default limited to 20): <b>' . $oIMDB->getCastAsUrl() . '</b></p>';
    echo '<p>Cast and Character (limited to 10): <b>' . $oIMDB->getCastAndCharacter(10) . '</b></p>';
    echo '<p>Cast and Character as URL (limited to 10): <b>' . $oIMDB->getCastAndCharacterAsUrl(10) . '</b></p>';
    echo '<p>Countries as URL: <b>' . $oIMDB->getCountryAsUrl() . '</b></p>';
    echo '<p>Countries: <b>' . $oIMDB->getCountry() . '</b></p>';
    echo '<p>Creators as URL: <b>' . $oIMDB->getCreatorAsUrl() . '</b></p>';
    echo '<p>Creators: <b>' . $oIMDB->getCreator() . '</b></p>';
    echo '<p>Directors as URL: <b>' . $oIMDB->getDirectorAsUrl() . '</b></p>';
    echo '<p>Directors: <b>' . $oIMDB->getDirector() . '</b></p>';
    echo '<p>Genres as URL: <b>' . $oIMDB->getGenreAsUrl() . '</b></p>';
    echo '<p>Genres: <b>' . $oIMDB->getGenre() . '</b></p>';
    echo '<p>Languages as URL: <b>' . $oIMDB->getLanguagesAsUrl() . '</b></p>';
    echo '<p>Languages: <b>' . $oIMDB->getLanguages() . '</b></p>';
    echo '<p>Location as URL: <b>' . $oIMDB->getLocationAsUrl() . '</b></p>';
    echo '<p>Location: <b>' . $oIMDB->getLocation() . '</b></p>';
    echo '<p>MPAA: <b>' . $oIMDB->getMpaa() . '</b></p>';
    echo '<p>Plot (shortened to 150 chars): <b>' . $oIMDB->getPlot(150) . '</b></p>';
    echo '<p>Poster: <b>' . $oIMDB->getPoster() . '</b></p>';
    echo '<p>Rating: <b>' . $oIMDB->getRating() . '</b></p>';
    echo '<p>Release Date: <b>' . $oIMDB->getReleaseDate() . '</b></p>';
    echo '<p>Runtime: <b>' . $oIMDB->getRuntime() . '</b></p>';
    echo '<p>Seasons: <b>' . $oIMDB->getSeasons() . '</b></p>';
    echo '<p>Tagline: <b>' . $oIMDB->getTagline() . '</b></p>';
    echo '<p>Title: <b>' . $oIMDB->getTitle() . '</b></p>';
    echo '<p>Url: <b><a href="' . $oIMDB->getUrl() . '">' . $oIMDB->getUrl() . '</a></b></p>';
    echo '<p>Votes: <b>' . $oIMDB->getVotes() . '</b></p>';
    echo '<p>Writers as URL: <b>' . $oIMDB->getWriterAsUrl() . '</b></p>';
    echo '<p>Writers: <b>' . $oIMDB->getWriter() . '</b></p>';
    echo '<p>Year: <b>' . $oIMDB->getYear() . '</b></p>';
}
else {
    echo '<p>Movie not found!</p>';
}
?>

<hr>

<?php
$oIMDB = new IMDB('http://us.imdb.com/Title?0144117');
if ($oIMDB->isReady) {
    echo '<p><a href="' . $oIMDB->getUrl() . '">' . $oIMDB->getTitle() . '</a> got rated ' . $oIMDB->getRating() . '.</p>';
}
else {
    echo '<p>Movie not found!</p>';
}
?>

<hr>

<?php
$oIMDB = new IMDB('http://www.imdb.com/title/tt1022603/');
if ($oIMDB->isReady) {
    echo '<p><a href="' . $oIMDB->getUrl() . '">' . $oIMDB->getTitle() . '</a> got rated ' . $oIMDB->getRating() . '.</p>';
    echo '<p><img src="' . $oIMDB->getPoster() . '" style="float:left;margin:4px 10px 10px 0;"> <b>About the movie:</b> ' . $oIMDB->getPlot() . '</p>';
}
else {
    echo '<p>Movie not found!</p>';
}
?>

<hr>

<?php
$oIMDB = new IMDB('Fabian Beiner never made a movie. Yet!');
if ($oIMDB->isReady) {
    echo '<p><b>' . $oIMDB->getTitle() . '</b></p>';
}
else {
    echo '<p>Movie not found!</p>';
}
?>
</body>
</html>
