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

$memberSince=