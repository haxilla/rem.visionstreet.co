<?php

use App\autosynch\models\propphoto\propphotos;
use App\autosynch\models\propphoto\propphotoOld;
use App\autosynch\models\propphoto\propphotoOldArc;
use App\autosynch\models\propphoto\propphotoCurArc;

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