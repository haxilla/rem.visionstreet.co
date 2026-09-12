<?php

include('countBullets.php');

$fromURL='https://rem.visionstreet.co';
$fromURL2='https://rem.visionstreet.co';
$display="screen";
$enc=0;

$totalPhotos = $propInfo->thePhotos
->where('resized','=','500')
->count();

$template           = $propInfo->theStyle->template;
$graphic_words      = $propInfo->theStyle->graphic_words;
$graphic_textcolor  = $propInfo->theStyle->graphic_textcolor;
$graphic_style      = $propInfo->theStyle->graphic_style;
$hlGraphic          = $graphic_words.'_'.$graphic_textcolor.'_'.$graphic_style.'x.png';

//theHeadline
$theHeadline=$propInfo['xHeadline'];
if(!$theHeadline){
    $theHeadline=$propInfo['xxHeadline'];}

$agentInfo=$propInfo->theAgent;
$officeInfo=$propInfo->theOffice;

// The accent color is used directly as a TEXT color in several places
// (photo/MLS/virtual-tour links, bullet separators, etc.) whose own
// background is always light regardless of the flyer's overall
// background theme - a pale accent (near-white, light yellow) reads
// as invisible there. Fall back to a dark neutral for those specific
// colors instead of the raw accent hex, matching the same set
// colorswatch.js already treats as "too pale for text" on click.
$accentbars = $propInfo->theStyle->accentbars;
$paleAccents = ['ffffff', 'eeeeee', 'ffffcc', 'ffc60b'];
$accentTextColor = in_array(strtolower((string) $accentbars), $paleAccents, true)
    ? '333333'
    : $accentbars;

// General body text (remarks, address, MLS#, bullets, etc.) has always
// been hardcoded to a dark grey regardless of the chosen background -
// invisible against a dark one. Same dark-background list already used
// by colorswatch.js and the Design page's accent-swatch visibility.
$darkFlyerBackgrounds = ['996600', '990000', '000066', '000000'];
$isDarkFlyerBackground = in_array(strtolower((string) $propInfo->theStyle->flyer_background), $darkFlyerBackgrounds, true);
$bodyTextColor = $isDarkFlyerBackground ? 'ffffff' : '333333';

// headline_text sits directly on the outer flyer background (no white
// panel behind it, unlike the remarks/address area) - it's normally
// kept in sync with the background by colorswatch.js when someone
// clicks through the Design page, but that JS only ever runs on a
// live click, not on the initial page load - so a flyer whose
// headline_text was set before a later background change (or any
// legacy flyer that never went through that JS at all) can still end
// up dark-on-dark. Force it safe for the same known dark backgrounds
// rather than trusting whatever's stored.
$safeHeadlineText = $isDarkFlyerBackground ? 'ffffff' : ($propInfo->theStyle->headline_text ?: '333333');

