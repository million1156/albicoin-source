<?php
/*
$seed = time();
srand($seed);

$size_h = isset($_GET['height']) ? intval($_GET['height']) : 250;
$size_w = isset($_GET['width']) ? intval($_GET['width']) : 250;

$gd = imagecreatetruecolor($size_w, $size_h);
$dots = imagecolorallocate($gd, 252, 186, 3);
$bg = imagecolorallocate($gd, 76, 66, 255);

imagefill($gd, 0, 0, $bg);

for ($i = 0; $i < 1000; $i++) {
  $x = rand(1, $size_w);
  $y = rand(1, $size_h);

  imagesetpixel($gd, round($x),round($y), $dots);
}

imagepng($gd, './imgs/' . 'img' . $seed . '.png');
echo "<img src=". './imgs/' .'img' . $seed . '.png'.">";
*/
?>