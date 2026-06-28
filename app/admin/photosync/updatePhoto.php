<?php

//model
Use App\autosynch\models\propphoto\propphotos;
Use App\autosynch\models\propphoto\propphotoOld;
Use App\autosynch\models\propphoto\propphotoCurArc;
Use App\autosynch\models\propphoto\propphotoOldArc;

//update as resized=2 to mark as done but failed
propphotos::where('photoID','=',"$photo->photoID")
->update([
  'resized'     => 2,
  'ratio'       => $ratio,
  'existCheck'  => \Carbon\Carbon::now(),]);

//oldPhoto = realtyemails.com
propphotoOld::where('locname','=',"$photo->photoName")
->update([
  'resized'      => 2,
  'ratio'        => $ratio,
  'exist_check'  => \Carbon\Carbon::now(),]);

//oldPhoto = realtyemails.com
propphotoCurArc::where('locname','=',"$photo->photoName")
->update([
  'resized'      => 2,
  'ratio'        => $ratio,
  'exist_check'  => \Carbon\Carbon::now(),]);

//oldPhoto = realtyemails.com
propphotoOldArc::where('locname','=',"$photo->photoName")
->update([
  'resized'      => 2,
  'ratio'        => $ratio,
  'exist_check'  => \Carbon\Carbon::now(),]);
