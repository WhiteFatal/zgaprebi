<?php

function text($type,$text,$maxfsize,$minfsize,$maxwidth,$recreate=true){
	$font='img/bpg_glaho_arial_sp_v5.ttf';
	$image = new Imagick();
	$draw = new ImagickDraw();
	$color = new ImagickPixel('#FFFFFF');
	$background = new ImagickPixel('none'); // Transparent
	$draw->setFont($font);
	$draw->setFontSize($maxfsize);
	$draw->setFillColor($color);
	$draw->setStrokeAntialias(true);
	$draw->setTextAntialias(true);
	$metrics = $image->queryFontMetrics($draw, $text);
	$draw->setTextAlignment(2);
	$draw->annotation(round($metrics['textWidth']/2), $metrics['ascender'], $text);
	$image->newImage($metrics['textWidth']+5, $metrics['textHeight'], $background);
	$image->setImageFormat('png');
	$image->drawImage($draw);
	$titlesize = $image->getImageGeometry();
	if(($titlesize['width']>$maxwidth) && ($recreate===true)) {
		if(($maxfsize-2)>=$minfsize){
			$image = text($type,$text,$maxfsize-2,$minfsize,$maxwidth);
		} else {
			$words = explode(" ",$text);
			foreach($words as $k => $v) $words[$k] = array('w'=>(utf8_strlen($v)*($metrics['characterWidth']+$metrics['descender'])),'t'=>$v);
			$n = 0;
			foreach($words as $k => $v){
				if((($n + $words[$k]['w']+@$words[$k+1]['w'])>$maxwidth)){
					array_splice($words, $k+1, 0, array(array('w'=>'1','t'=>"\n")));
					$n = 0;
				} else {
					$n += $words[$k]['w'];
				}
			}
			$newWords = array();
			foreach($words as $v) $newWords[] = $v['t'];
			$text = implode(" ",$newWords);
			$text = str_replace(' \n ',"\n",$text);
			$image = text($type,$text,$minfsize,$minfsize,$maxwidth,false);
		}
	}
	if(!isset($_SESSION['tmp'][$type.'_ftsize']) || @$_SESSION['tmp'][$type.'_ftsize']>$maxfsize){
		$_SESSION['tmp'][$type.'_ftsize'] = $maxfsize;
	}
	return $image;
}


function signature_text($text){
	$font='img/arbat-bo.ttf';
	$image = new Imagick();
	$draw = new ImagickDraw();
	$color = new ImagickPixel('#FFFFFF');
	$background = new ImagickPixel('none'); // Transparent
	$draw->setFont($font);
	$draw->setFontSize(14);
	$draw->setFillColor($color);
	$draw->setStrokeAntialias(true);
	$draw->setTextAntialias(true);
	$metrics = $image->queryFontMetrics($draw, $text);
	$draw->setTextAlignment(2);
	$draw->annotation(round($metrics['textWidth']/2), $metrics['ascender'], $text);
	$image->newImage($metrics['textWidth']+5, $metrics['textHeight'], $background);
	$image->setImageFormat('png');
	$image->drawImage($draw);
	return $image;
}

function user_signature_text($text){
	$font='img/bpg_glaho_arial_sp_v5.ttf';
	$image = new Imagick();
	$draw = new ImagickDraw();
	$color = new ImagickPixel('#FFFFFF');
	$background = new ImagickPixel('none'); // Transparent
	$draw->setFont($font);
	$draw->setFontSize(12);
	$draw->setFillColor($color);
	$draw->setStrokeAntialias(true);
	$draw->setTextAntialias(true);
	$metrics = $image->queryFontMetrics($draw, $text);
	$draw->setTextAlignment(2);
	$draw->annotation(round($metrics['textWidth']/2), $metrics['ascender'], $text);
	$image->newImage($metrics['textWidth']+5, $metrics['textHeight'], $background);
	$image->setImageFormat('png');
	$image->drawImage($draw);
	return $image;
}


function rectange($w,$h){
	$image = new Imagick();
	$image->newImage($w,$h, new ImagickPixel('black'));
	$image->setImageFormat('png');
	return $image;
}

?>