<?php

function generateCode($characters) {
	$possible = '123456789abcdfghjkmnpqrstvwxyz';
	$code = '';
	$i = 0;
	while ($i < $characters) { 
		$code .= substr($possible, mt_rand(0, strlen($possible)-1), 1);
		$i++;
	}
	return $code;
}

$width = 70;
$height = 25;

$im = imagecreate($width, $height);
$bg = imagecolorallocate($im, 255, 255, 255);
$border = imagecolorallocate($im, 207, 199, 199);
imagerectangle($im, 0, 0, $width - 1, $height - 1, $border);
$text = generateCode(5);
$textcolor = imagecolorallocate($im, 59, 127, 202);
$font = 5;
$font_width = imagefontwidth($font);
$font_height = imagefontheight($font);
$text_width = $font_width * strlen($text);
$position_center = ceil(($width - $text_width) / 2);
$text_height = $font_height;
$position_middle = ceil(($height - $text_height) / 2);
$image_string = imagestring($im, $font, $position_center, $position_middle, $text, $textcolor);
header("Content-type: image/png");
imagepng($im);
?>