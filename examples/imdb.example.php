<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>PHP-IMDB-Grabber by Fabian Beiner | Example</title>
  <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Open+Sans:400,700">
  <style>
    body {
      background-color: #E5E5E5;
      color: #222;
      font-family: "Open Sans", sans-serif;
      font-size: 15px;
      max-width: 1000px;
      margin: 20px auto;
      width: 100%;
    }

    p {
      margin: 0 0 10px;
      padding: 0;
    }

    hr {
      clear: both;
      margin: 25px 0;
      border: 1px #000 solid;
      height: 1px;
      background: #FFF;
    }

    a {
      color: #222;
    }

    a:hover, a:focus, a:active {
      text-decoration: none;
      color: #222;
    }

    h1 {
      font-size: 32px;
      text-align: center;
      font-weight: 700;
    }
  </style>
</head>
<body>
<?php
include_once '../imdb.class.php';
//
//$oIMDB = new IMDB('http://us.imdb.com/Title?0144117');
//if ($oIMDB->isReady) {
//    echo '<p><a href="' .
//         $oIMDB->getUrl() .
//         '">' .
//         $oIMDB->getTitle() .
//         '</a> got rated ' .
//         $oIMDB->getRating() .
//         '.</p>';
//} else {
//    echo '<p>Movie not found!</p>';
//}
//?>
<!---->
<!--<hr>-->
<!---->
<?php
//$oIMDB = new IMDB('New York, I Love You');
//if ($oIMDB->isReady) {
//    echo '<h1>' . $oIMDB->getTitle() . '</h1>';
//    foreach ($oIMDB->getAll() as $aItem) {
//        if ($oIMDB::$sNotFound !== $aItem['value']) {
//            echo '<p><b>' . $aItem['name'] . '</b>: ' . $aItem['value'] . '</p>';
//        }
//    }
//} else {
//    echo '<p>Movie not found!</p>';
//}
//?>
<!---->
<!--<hr>-->

<?php
$oIMDB = new IMDB('http://www.imdb.com/title/tt1022603/');
if ($oIMDB->isReady) {
  echo '<p><a href="' .
      $oIMDB->getUrl() .
      '">' .
      $oIMDB->getTitle() .
      '</a> got rated ' .
      $oIMDB->getRating() .
      '.</p>';
  echo '<p><img src="../' .
      $oIMDB->getPoster('small', true) .
      '" style="float:left;margin:4px 10px 10px 0;"> <b>About the movie:</b> ' .
      $oIMDB->getPlot() .
      '</p>';

  echo '<h2>Cast</h2>';

  // Get cast images with hashed filenames
  $castImages = $oIMDB->getCastImages(5, true, 'big', true);
  if (!is_array($castImages)) {
    $castImages = [];
  }

  // Display cast with images
  echo '<div style="clear:both;">';
  foreach ($castImages as $name => $image) {
    echo '<div style="display:inline-block;margin:10px;text-align:center;width:150px;">';

    // Check if image exists
    if (!empty($image) && $image !== 'cast/not-found.jpg') {
      echo '<img src="../' . htmlspecialchars($image) . '" alt="' . htmlspecialchars($name) . '" style="width:140px;height:140px;object-fit:cover;border-radius:50%;">';
    } else {
      // Fallback placeholder if image not found
      echo '<div style="width:140px;height:140px;background:#ddd;border-radius:50%;display:flex;align-items:center;justify-content:center;">No Image</div>';
    }

    echo '<p style="margin-top:5px;font-size:14px;"><strong>' . htmlspecialchars($name) . '</strong></p>';
    echo '</div>';
  }
  echo '</div>';

} else {
  echo '<p>Movie not found!</p>';
}
?>

<hr>

<?php
$oIMDB = new IMDB('Fabian Beiner never made a movie. Yet!');
if ($oIMDB->isReady) {
    echo '<p><b>' . $oIMDB->getTitle() . '</b></p>';
} else {
    echo '<p>Movie not found!</p>';
}
?>
</body>
</html>
