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
        echo '<p>Also Known As: <b>' . $oIMDB->getAka() . '</b></p>' . PHP_EOL;
        echo '<p>Aspect Ratio: <b>' . $oIMDB->getAspectRatio() . '</b></p>' . PHP_EOL;
        echo '<p>Budget: <b>' . $oIMDB->getBudget() . '</b></p>' . PHP_EOL;
        echo '<p>Cast: <b>' . $oIMDB->getCast() . '</b></p>' . PHP_EOL;
        echo '<p>Full Cast: <b>' . $oIMDB->getFullCast() . '</b></p>' . PHP_EOL;
        echo '<p>Cast as URL: <b>' . $oIMDB->getCastAsUrl() . '</b></p>' . PHP_EOL;
        echo '<p>Cast and Character: <b>' . $oIMDB->getCastAndCharacter() . '</b></p>' . PHP_EOL;
        echo '<p>Cast and Character as URL: <b>' . $oIMDB->getCastAndCharacterAsUrl() . '</b></p>' . PHP_EOL;
        echo '<p>Color: <b>' . $oIMDB->getColor() . '</b></p>' . PHP_EOL;
        echo '<p>Company: <b>' . $oIMDB->getCompany() . '</b></p>' . PHP_EOL;
        echo '<p>Company as URL: <b>' . $oIMDB->getCompanyAsUrl() . '</b></p>' . PHP_EOL;
        echo '<p>Countries: <b>' . $oIMDB->getCountry() . '</b></p>' . PHP_EOL;
        echo '<p>Countries as URL: <b>' . $oIMDB->getCountryAsUrl() . '</b></p>' . PHP_EOL;
        echo '<p>Creators: <b>' . $oIMDB->getCreator() . '</b></p>' . PHP_EOL;
        echo '<p>Creators as URL: <b>' . $oIMDB->getCreatorAsUrl() . '</b></p>' . PHP_EOL;
        echo '<p>Description: <b>' . $oIMDB->getDescription() . '</b></p>' . PHP_EOL;
        echo '<p>Directors: <b>' . $oIMDB->getDirector() . '</b></p>' . PHP_EOL;
        echo '<p>Directors as URL: <b>' . $oIMDB->getDirectorAsUrl() . '</b></p>' . PHP_EOL;
        echo '<p>Genres: <b>' . $oIMDB->getGenre() . '</b></p>' . PHP_EOL;
        echo '<p>Genres as URL: <b>' . $oIMDB->getGenreAsUrl() . '</b></p>' . PHP_EOL;
        echo '<p>Languages: <b>' . $oIMDB->getLanguages() . '</b></p>' . PHP_EOL;
        echo '<p>Languages as URL: <b>' . $oIMDB->getLanguagesAsUrl() . '</b></p>' . PHP_EOL;
        echo '<p>Location: <b>' . $oIMDB->getLocation() . '</b></p>' . PHP_EOL;
        echo '<p>Location as URL: <b>' . $oIMDB->getLocationAsUrl() . '</b></p>' . PHP_EOL;
        echo '<p>MPAA: <b>' . $oIMDB->getMpaa() . '</b></p>' . PHP_EOL;
        echo '<p>Opening Weekend: <b>' . $oIMDB->getOpening() . '</b></p>' . PHP_EOL;
        echo '<p>Plot: <b>' . $oIMDB->getPlot() . '</b></p>' . PHP_EOL;
        echo '<p>Poster: <b>' . $oIMDB->getPoster() . '</b></p>' . PHP_EOL;
        echo '<p>Rating: <b>' . $oIMDB->getRating() . '</b></p>' . PHP_EOL;
        echo '<p>Release Date: <b>' . $oIMDB->getReleaseDate() . '</b></p>' . PHP_EOL;
        echo '<p>Runtime: <b>' . $oIMDB->getRuntime() . '</b></p>' . PHP_EOL;
        echo '<p>Seasons: <b>' . $oIMDB->getSeasons() . '</b></p>' . PHP_EOL;
        echo '<p>Sound Mix: <b>' . $oIMDB->getSoundMix() . '</b></p>' . PHP_EOL;
        echo '<p>Tagline: <b>' . $oIMDB->getTagline() . '</b></p>' . PHP_EOL;
        echo '<p>Title: <b>' . $oIMDB->getTitle() . '</b></p>' . PHP_EOL;
        echo '<p>Trailer: <b><a href="' . $oIMDB->getTrailerAsUrl() . '">' . $oIMDB->getTrailerAsUrl() . '</a></b></p>' . PHP_EOL;
        echo '<p>Url: <b><a href="' . $oIMDB->getUrl() . '">' . $oIMDB->getUrl() . '</a></b></p>' . PHP_EOL;
        echo '<p>Votes: <b>' . $oIMDB->getVotes() . '</b></p>' . PHP_EOL;
        echo '<p>Writers: <b>' . $oIMDB->getWriter() . '</b></p>' . PHP_EOL;
        echo '<p>Writers as URL: <b>' . $oIMDB->getWriterAsUrl() . '</b></p>' . PHP_EOL;
        echo '<p>Year: <b>' . $oIMDB->getYear() . '</b></p>' . PHP_EOL;
}
else {
    echo '<p>Movie not found!</p>' . PHP_EOL;
}
?>

<hr>

<?php
$oIMDB = new IMDB('http://us.imdb.com/Title?0144117');
if ($oIMDB->isReady) {
    echo '<p><a href="' . $oIMDB->getUrl() . '">' . $oIMDB->getTitle() . '</a> got rated ' . $oIMDB->getRating() . '.</p>' . PHP_EOL;
}
else {
    echo '<p>Movie not found!</p>' . PHP_EOL;
}
?>

<hr>

<?php
$oIMDB = new IMDB('http://www.imdb.com/title/tt1022603/');
if ($oIMDB->isReady) {
    echo '<p><a href="' . $oIMDB->getUrl() . '">' . $oIMDB->getTitle() . '</a> got rated ' . $oIMDB->getRating() . '.</p>' . PHP_EOL;
    echo '<p><img src="' . $oIMDB->getPoster() . '" style="float:left;margin:4px 10px 10px 0;"> <b>About the movie:</b> ' . $oIMDB->getPlot() . '</p>' . PHP_EOL;
}
else {
    echo '<p>Movie not found!</p>' . PHP_EOL;
}
?>

<hr>

<?php
$oIMDB = new IMDB('Fabian Beiner never made a movie. Yet!');
if ($oIMDB->isReady) {
    echo '<p><b>' . $oIMDB->getTitle() . '</b></p>' . PHP_EOL;
}
else {
    echo '<p>Movie not found!</p>' . PHP_EOL;
}
?>
</body>
</html>
