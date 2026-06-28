<?php

// add dimension values of original - gets width/height
list($width, $height) = getimagesize($source);

// error if none
if(!$width||!$height||$width==0||$height==0){
	include(app_path().'/autosynch/resize/functions/fileCorrupt.php');}

// get new dimensions & add values
$ratio=$width/$height;
$ratio=round($ratio,4);

// set orient
if($width>$height){
	$orient='wide';
}else{
	$orient='tall';}