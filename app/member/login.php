<?php

require app_path('queries/base.php');

//clone & continue
$slides    = clone $base;

$slides=$slides
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
->orderBy('propflyers.creationDate','desc')
->take(10)
->get();

$data=[
    'slides'=>$slides,
];

dd($data);