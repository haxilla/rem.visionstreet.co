<?php

use App\Models\Sync\propphotos;
use App\Models\Sync\propphotoOld;
use App\Models\Sync\propphotoOldArc;
use App\Models\Sync\propphotoCurArc;

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