<?php

require app_path('queries/base.php');

//clone & continue
$newAdds    = clone $base;
$mostViews  = clone $base;
//new Adds = ordered by created_at desc
$newAdds=$newAdds
->whereHas('thePhotos',function($q){
  $q->where('def','=','1')
    ->where('resized','=','1000')
    ->where('orient','=','wide');
})
->whereHas('thePhotos',function($q){
  $q->where('def','=','1')
  ->where('resized','=','1000');
})
->with(['thePhotos'=>function($q){
  $q->select('propflyer_id','photoName','def','resized','localFound',
    'width','height','orient','ratio','ord','notFound','photoID','remoteFound')
    ->where('resized','=','1000')
    ->where('def','=','1');
}])
->orderBy('propflyers.creationDate','desc')
->take(10)
->get();

//most Views = ordered by xWebViews desc
$mostViews=$mostViews
->with(['thePhotos'=>function($q){
  $q->select('propflyer_id','photoName','def','resized','localFound',
    'width','height','orient','ratio','ord','notFound','photoID','remoteFound')
    ->where('resized','=','500')
    ->where('def','=','1');
}])
->with(['theRemarks'=>function($q){
  $q->select('propflyer_id','xPubRemarks');
}])
->take(10)
->orderBy('xWebViews','desc')
->get();

// member since / agent wall
$theDate = \Carbon\Carbon::today()->subDays(45);

$memberSince = \App\models\core\propagent::select(
    'startDate',
    'agtFullName',
    'id',
    'officeID',
    'agtPhoto'
)
->whereHas('theStats', function ($q) use ($theDate) {
    $q->select('propagent_id', 'xLastDeliveryDate')
      ->where('xLastDeliveryDate', '>', $theDate);
})
->with(['theAgentMeta' => function ($q) {
    $q->select('newRemID', 'propagent_id');
}])
->with(['theAgentCleanup' => function ($q) {
    $q->select('newRemID', 'propagent_id');
}])
->with(['theAgtOffice' => function ($q) {
    $q->select('officeID', 'propagent_id');
}])
->whereNotNull('startDate')
->whereNotNull('agtPhoto')
->groupBy('id')
->orderBy('startDate')
->take(36)
->get();

dd($memberSince)