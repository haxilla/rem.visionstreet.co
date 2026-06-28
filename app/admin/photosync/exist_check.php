<?php

use App\Models\Ssync\propphotos;
use App\Models\Ssync\propphotoOld;
use App\Models\Ssync\propphotoOldArc;
use App\Models\Ssync\propphotoCurArc;

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