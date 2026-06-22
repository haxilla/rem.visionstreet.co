<?php

Use App\Models\Core\Propflyer;
Use App\Models\Core\Propmeta;
Use App\Models\Core\Propphoto;

//make sure it belongs to user or error
$flyer = Propflyer::where('id', request('flyerId'))
    ->where('propagent_id', auth()->id())
    ->first();

if (!$flyer) {
    dd("Error: Flyer not found or you don't have permission to delete it.");}

//get list of property photos
$photos = Propphoto::where('propflyer_id', $flyer->id)->get(); 

//get zipDir and mlsDir from propmeta
$meta = Propmeta::where('propflyer_id', $flyer->id)->first();
$zipDir = $meta->zipDir ?? null;    
$mlsDir = $meta->mlsDir ?? null;

//do not delete photos if no zipDir or mlsDir, 
// as we won't know where they are stored, 
// and we want to avoid accidentally deleting 
// unrelated photos if the directories 
// are not set correctly
if($zipDir && $mlsDir) {
    //delete the photos from the file system
    foreach ($photos as $photo) {
        //set photo path based on zipDir and mlsDir
        $photoPath=public_path('hqphotos/' . $zipDir . '/' . $mlsDir . '/' . $photo->photoName);
        //delete the photo file if it exists
        if (file_exists($photoPath)) {
            unlink($photoPath);
        }
    }
}

//delete from propphoto
Propphoto::where('propflyer_id', $flyer->id)->delete();
//delete from propmeta
Propmeta::where('propflyer_id', $flyer->id)->delete();
//delete from propflyer
$flyer->delete();

redirect('/member/dashboard')->send();
exit();