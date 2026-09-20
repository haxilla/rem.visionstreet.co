<?php

// A flyer can be missing some of its side records - an older / imported flyer with no style
// record, or a brand-new one whose Details haven't been saved yet (no remarks or map). The
// templates read these unconditionally ("Attempt to read property on null"), so any that
// are missing get an empty, UNSAVED stand-in with the same defaults a new flyer is created
// with (see app/member/flyer/save.php). Nothing here is ever written to the database.
if (!$propInfo->theRemarks) {
    $propInfo->setRelation('theRemarks', new \App\Models\Core\Propremark());
}

if (!$propInfo->theMap) {
    $propInfo->setRelation('theMap', new \App\Models\Core\Propmapping());
}

if (!$propInfo->theStyle) {
    $standInStyle = new \App\Models\Core\Propstyle();
    $standInStyle->forceFill([
        'template'          => '1pc',
        'flyer_background'  => 'cccccc',
        'headline_bar_bg'   => '333333',
        'headline_bar_text' => 'ffffff',
        'headline_text'     => '333333',
        'graphic_words'     => 'greatbuy',
        'graphic_textcolor' => 'ffffff',
        'graphic_style'     => 'ul',
        'roundedtop'        => 'roundedtop-600px_cccccc.gif',
        'accentbars'        => '333333',
    ]);
    $propInfo->setRelation('theStyle', $standInStyle);
}

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

// Style 1's photo-count links bar (style1FlyerLinks.blade.php) uses
// accentbars as its OWN background, but its text was borrowing
// headline_bar_text - a color tuned for a completely different
// element's background (headline_bar_bg). The two fields are
// unrelated and can easily land on the same dark shade. Compute real
// contrast from accentbars itself (perceived-luminance formula, so
// it works for any accent color, not just an enumerated list).
$safeAccentBarTextColor = (function ($hex) {
    $hex = ltrim((string) $hex, '#');
    if (strlen($hex) !== 6 || !ctype_xdigit($hex)) {
        return '333333';
    }
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
    return $luminance > 0.6 ? '333333' : 'ffffff';
})($propInfo->theStyle->accentbars);

