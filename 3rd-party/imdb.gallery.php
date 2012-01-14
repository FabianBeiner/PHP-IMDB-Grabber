<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>PHP-IMDB-Grabber by Fabian Beiner | Gallery by xsabianus</title>
  <!-- Small modifications by Fabian Beiner. Anyhow, no support for this script. -->
<style>

img{
border:5px  solid #ECEBEB;
margin:4px 8px 5px 0;
-moz-border-radius:5px 5px 5px 5px;
-khtml-border-radius:5px 5px 5px 5px;
-webkit-border-radius:5px 5px 5px 5px;
border-radius:5px 5px 5px 5px;
  }

a  img {
display:inline;
margin-bottom:10px;
margin-right:5px;
margin-left:5px;
background:#f5f5f5;
border:1px solid #ccc;
padding:2px;
}

a:hover img {
background:#4680C2;
border:1px  solid #4680C2;
}
img:hover {
opacity: .75;
-moz-opacity: .75;
filter: alpha(opacity=75);

}

 </style>

</head>
<body>
<table width="760" bgcolor="#ffffff" border="0" cellspacing="8" cellpadding="8" align="center">
<table width="760" bgcolor="#ffffff" border="0" cellspacing="8" cellpadding="8" align="center">
<td>
  <center>
<?php
$root = "../posters";
$take = opendir($root);
while($picturefile = readdir($take)){
if(is_file($root."/".$picturefile) && $picturefile != 'not-found.jpg')
$picture[] = $picturefile;
}
closedir($take);


$limit = 24; //Number of images to display on a page
$page = $_GET["page"];
if($page < 1) $page = 1;
$total = count($picture);


$startpage = ($page-1) * $limit;
$endpage = ($startpage+$limit);
if($endpage > $total) $endpage = $total;


for($i=$startpage; $i < $endpage; $i++){
echo "

<a href='http://www.imdb.com/title/tt".substr($picture[$i], 0,strrpos($picture[$i],'.'))."/' target='_blank'>
<img  onContextMenu='return false' src='".$root."/".$picture[$i]."' width='107'  height='158' border='0'></a>";
}
echo"<br>";


for($i=1; $i < $total / $limit; $i++){
if($page == $i)

echo "$in"; else
echo "<a href='imdb.gallery.php?page=$i'>[$i]</a>";

}

?>
</center>
<tr></td></tr></table>
<tr></td></tr></table>
</body>
</html>
