<?php

use App\Models\Core\Propflyer;

// "What's new on RealtyEmails" on the login page: flyers that have actually been sent,
// the most recently sent first (propflyerstats.xLastDeliveryDate is a flyer's last
// delivery date - the same value the agent dashboard and admin flyer list show as
// "Last Sent"). Only flyers with a wide cover photo, since the panel shows a picture.

$slides = Propflyer::select(
  'propflyers.id','propflyers.propagent_id','propflyers.created_at','creationDate',
  'xFullStreet','xCity','xState','state','xZip','xxZip','officeID','url_slug',
  'propflyerstats.xLastDeliveryDate')
->with(['theAgent'=>function($q){
   $q->select(
    'id','agtFullName','agtPhoto','agtMainPhone','agtLogo')
      ->with(['theAgentMeta'=>function($q){
         $q->select('propagent_id','newRemID');
      }]);
   }])
->with(['theMeta'=>function($q){
  $q->select('propflyer_id','zipDir','mlsDir','sk1');
}])
->with(['theOffice'=>function($q){
  $q->select('officeName','propagent_id','officeID');
}])
->leftJoin('propflyerstats',
  'propflyers.id', '=', 'propflyerstats.propflyer_id')
->where('xAgtSent','=','1')
->whereNotNull('propflyerstats.xLastDeliveryDate')
->whereHas('thePhotos',function($q){
  $q->where('def','=','1')
    ->where('resized','=','1000')
    ->where('orient','=','wide');
})
->with(['thePhotos'=>function($q){
  $q->select('propflyer_id','photoName','def','resized','localFound',
    'width','height','orient','ratio','ord','notFound','photoID','remoteFound')
    ->where('resized','=','1000')
    ->where('def','=','1');
}])
->orderBy('propflyerstats.xLastDeliveryDate','desc')
->take(8)
->get();

$data=[
    'slides'=>$slides,
];
