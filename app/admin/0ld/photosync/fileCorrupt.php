<?php

use App\autosynch\models\propphoto\propphotos;
use App\autosynch\models\propphoto\propphotoOld;
use App\autosynch\models\propphoto\propphotoCurArc;
use App\autosynch\models\propphoto\propphotoOldArc;

//update as resized=3 to mark as done but failed
propphotos::where('photoName','=',"$photo->photoName")
->update([
  'resized'     => 5,
  'existCheck'  => \Carbon\Carbon::now(),]);

//update as resized=3 to mark as done but failed
propphotoOld::where('locname','=',"$photo->photoName")
->update([
  'resized'      => 5,
  'exist_check'  => \Carbon\Carbon::now(),]);

//update as resized=3 to mark as done but failed
propphotoCurArc::where('locname','=',"$photo->photoName")
->update([
  'resized'      => 5,
  'exist_check'  => \Carbon\Carbon::now(),]);

//update as resized=3 to mark as done but failed
propphotoOldArc::where('locname','=',"$photo->photoName")
->update([
  'resized'      => 5,
  'exist_check'  => \Carbon\Carbon::now(),]);