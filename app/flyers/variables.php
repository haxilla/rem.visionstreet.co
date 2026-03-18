<?php

include('countBullets.php');

$fromURL='https://www.realtyrepublic.com';
$fromURL2='https://www.realtyrepublic.com';
$display="screen";
$enc=0;

$totalPhotos = $propInfo->thePhotos
->where('resized','=','500')
->count();

$graphic_words      = $propInfo->theStyle->graphic_words;
$graphic_textcolor  = $propInfo->theStyle->graphic_textcolor;
$graphic_style      = $propInfo->theStyle->graphic_style;
$hlGraphic          = $graphic_words.'_'.$graphic_textcolor.'_'.$graphic_style.'x.png';

//theHeadline
$theHeadline=$propInfo['xHeadline'];
if(!$theHeadline){
    $theHeadline=$propInfo['xxHeadline'];}

$agentInfo=$propInfo->theAgent;

