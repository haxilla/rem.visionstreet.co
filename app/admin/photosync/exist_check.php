<?php

use App\Models\sync\propphotos;
use App\Models\sync\propphotoOld;
use App\Models\sync\propphotoOldArc;
use App\Models\sync\propphotoCurArc;

//set existCheck
$existCheck=\Carbon\Carbon::now();

//update LOCAL
propphotos::where('photoName','=',"$photo->photoName")
->update([
    'existCheck'   => $existCheck,
]);
//update REMOTE
propphotoOld::where('locname','=',"$photo->photoName")
->update([
    'exist_check'  => $existCheck,
]);
//update REMOTE Archive
propphotoOldArc::where('locname','=',"$photo->photoName")
->update([
    'exist_check'  => $existCheck,
]);
//update LOCAL Archive
propphotoCurArc::where('locname','=',"$photo->photoName")
->update([
    'exist_check'  => $existCheck,
]);