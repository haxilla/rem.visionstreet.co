@php 
    $fromURL='https://www.realtyrepublic.com';
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

    include(app_path().'/flyers/variables.php');
        
@endphp


<div>
    YOURE ON THE FLYERS INDEX PAGE
</div>

<div>
    @include('flyers.s1pc')
</div>
<div>
    @include('flyers.s2pb')
</div>
<div>
    @include('flyers.s3pt')
</div>
<div>
    @include('flyers.s4sp')
</div>
<div>
    @include('flyers.s5pt')
</div>