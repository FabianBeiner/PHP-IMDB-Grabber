<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>PHP-IMDB-Grabber by Fabian Beiner | Gallery by xsabianus</title>
  <!-- Small modifications by Fabian Beiner. Anyhow, no support for this script. -->
</head>
<body>
<table width="655" bgcolor="#ffffff" border="0" cellspacing="8" cellpadding="8" align="center">
<table width="655" bgcolor="#ffffff" border="0" cellspacing="8" cellpadding="8" align="center">
<td>
  <center>
<?php
$root = "posters";
$take = opendir($root);
while($picturefile = readdir($take)){
if(is_file($root."/".$picturefile) && $picturefile != 'not-found.jpg')
$picture[] = $picturefile;
}
closedir($take);


$limit = 30; //Number of images to display on a page
$page = $_GET["page"];
if($page < 1) $page = 1;
$total = count($picture);


$startpage = ($page-1) * $limit;
$endpage = ($startpage+$limit);
if($endpage > $total) $endpage = $total;


for($i=$startpage; $i < $endpage; $i++){
echo "

<a href='".$root."/".$picture[$i]."' target='_blank'>
<img onContextMenu='return false' src='".$root."/".$picture[$i]."' width='100'  height='100' border='0'></a>";
}
echo"<br>";


for($i=1; $i < $total / $limit; $i++){
if($page == $i)

echo "$in"; else
echo "<a href='gallery.php?page=$i'>[$i]</a> ";

}

?>
</center>
<tr></td></tr></table>
<tr></td></tr></table>
</body>
</html>
