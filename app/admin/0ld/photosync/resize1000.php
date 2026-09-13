<?php

//get model
use App\Models\Sync\propphotos;
use App\Models\Sync\propphotoOld;
use App\Models\Sync\propphotoCurArc;
use App\Models\Sync\propphotoOldArc;

//if tall resize to 300height otherwise 300wide
if($orient=='tall'){
   $resize_h='600';
   $targetWidth=$resize_h*$ratio;
   $resize_w=round($targetWidth);
   $filePrefix='h600';
   //echo "<BR>ORIENT: $orient - H: $newHeight X W: $targetWidth<BR>";
}else{
   $resize_w='1000';
   $targetHeight=$resize_w/$ratio;
   $resize_h=round($targetHeight);
   $filePrefix='w1000';}
   //echo "<BR>ORIENT: $orient - W: $newWidth X H: $targetHeight<BR>";

if(!$photo->photoName){
   dd('error-line23-autosynch/resize/functions/resize1000');}

//set variables for photo resizing
$photoPath     = "hqphotos/$zipDir/$mlsDir/$photo->photoName";
$file          = $photoPath;
$resizedName   = "$filePrefix-$photo->photoName";
$resizedLoc    = "hqphotos/$zipDir/$mlsDir/$resizedName";

//call the function (when passing path to pic)
smart_resize_image($file ,null, $resize_w, $resize_h, false, $resizedLoc, false, false, 90 );

//re-assign the name and resized values
$newFileName=$resizedName;
$newFileSize=filesize($resizedLoc);
$resized=1000;

if(file_exists($resizedLoc)){
   $localFound=1;
}else{
   dd('error-line44-autosynch/functions/resize1000');}

//add dimension values of original - gets width/height
list($width, $height) = getimagesize($resizedLoc);

//get new dimensions & add values
$ratio=$width/$height;
$ratio=round($ratio,4);

//update original resized
propphotos::where('photoID','=',"$photo->photoID")
->update([
   'resized'     => 1,
   'existCheck'  => \Carbon\Carbon::now(),
   'localFound'  => $localFound,
]);

$matchLocname     = array('locname' =>$newFileName);
$matchPhotoname   = array('photoName' =>$newFileName);
//propphotos contains merged archives
//insert resized record into database
propphotos::updateOrCreate($matchPhotoname,[
   'resized'      => $resized,
   'resize_w'     => $resize_w,
   'resize_h'     => $resize_h,
   'width'        => $width,
   'height'       => $height,
   'ratio'        => $ratio,
   'photoName'    => $newFileName,
   'newFileSize'  => $newFileSize,
   'oldFileName'  => $photo->photoName,
   'oldFileSize'  => $photo->oldFileSize,
   'orient'       => $orient,
   'propflyer_id' => $photo->propflyer_id,
   'propagent_id' => $photo->propagent_id,
   'ord'          => $photo->ord,
   'def'          => $photo->def,
   'existCheck'   => \Carbon\Carbon::now(),
   'localFound'   => $localFound
]);

//remote
//remote originalFile
$remoteOriginal=propphotoOld::select('photoID')
->where('locname','=',"$photo->photoName")
->first();
//remote newFile
$remoteResize=propphotoOld::select('photoID')
->where('locname','=',"$newFileName")
->first();

// if original photo is found in remailphotos
// add resize to remailphotos
if($remoteOriginal && !$remoteResize){

   //update original remailphotos
   propphotoOld::where('locname','=',"$photo->photoName")
   ->update([
      'resized'      => 1,
      'ratio'        => $ratio,
      'exist_check'  => \Carbon\Carbon::now(),
   ]);

   //maker resize record on remailphotos
   propphotoOld::updateOrCreate($matchLocname,[
      'ufid'         => $photo->propflyer_id,
      'umid'         => $photo->propagent_id,
      'origname'     => $photo->photoName,
      'locname'      => $newFileName,
      'filesize'     => $newFileSize,
      'ord'          => $photo->ord,
      'def'          => $photo->def,
      'width'        => $width,
      'height'       => $height,
      'orient'       => $orient,
      'resized'      => $resized,
      'ratio'        => $ratio,
      'exist_check'  => \Carbon\Carbon::now(),
   ]);
}

//****************//
// Check Archive  //
//****************//
$localArchive=propphotoCurArc::select('photoID')
->where('locname','=',"$photo->photoName")
->first();
//if original is found in archive
//add the resize so re-added on new synch
if($localArchive){

   //update remote archive
   propphotoOldArc::where('locname','=',"$photo->photoName")
   ->update([
      'resized'      => 1,
      'localFound'   => 1,
      'exist_check'  => \Carbon\Carbon::now(),
   ]);

   //update local archive
   propphotoCurArc::where('locname','=',"$photo->photoName")
   ->update([
      'resized'      => 1,
      'localFound'   => 1,
      'exist_check'  => \Carbon\Carbon::now(),
   ]);

   //make archive
   $addLocalArchive=propphotoCurArc::updateOrCreate($matchLocname,[
      'ufid'         => $photo->propflyer_id,
      'umid'         => $photo->propagent_id,
      'origname'     => $photo->photoName,
      'locname'      => $newFileName,
      'filesize'     => $newFileSize,
      'ord'          => $photo->ord,
      'def'          => $photo->def,
      'width'        => $width,
      'height'       => $height,
      'orient'       => $orient,
      'resized'      => $resized,
      'ratio'        => $ratio,
      'exist_check'  => \Carbon\Carbon::now(),
      'localFound'   => $localFound
   ]);

   //use same ID to match
   $newPhotoID=$addLocalArchive['photoID'];

   //make archive
   $addRemoteArchive=propphotoOldArc::updateOrCreate($matchLocname,[
      'photoID'      => $newPhotoID,
      'ufid'         => $photo->propflyer_id,
      'umid'         => $photo->propagent_id,
      'origname'     => $photo->photoName,
      'locname'      => $newFileName,
      'filesize'     => $newFileSize,
      'ord'          => $photo->ord,
      'def'          => $photo->def,
      'width'        => $width,
      'height'       => $height,
      'orient'       => $orient,
      'resized'      => $resized,
      'ratio'        => $ratio,
      'exist_check'  => \Carbon\Carbon::now(),
      'localFound'   => $localFound
   ]);

}


