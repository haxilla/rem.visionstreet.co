<?php

Use App\Models\Core\Propflyer;

// query
$details=Propflyer::select(
   'id','propagent_id','officeID','xFullStreet',
   'xListPrice','xCity','xState','xZip','xxZip','xHeadline',
   'xMlsNum','xBeds','xxBeds','xBaths','xxBaths',
   'xSqft','xxSqft','xYrBuilt','xVirtualTour',
   'xMlsLink','xxHeadline')
->where('url_slug','=',"$flyerslug")
->with(['theRemarks'=>function($q){
   $q->select('propflyer_id','xb1','xb2','xb3','xb4',
      'xb5','xb6','xb7','xb8','xPubRemarks');}])
->with(['thePhotos'=>function($q){
   $q->select('propflyer_id','photoName',
      'photoID','def','ord','orient','resized')
      ->where('resized','=','500');}])
->with(['theMeta'=>function($q){
   $q->select('propflyer_id','zipDir','mlsDir','sk1');}])
->with(['theAgent'=>function($q){
   $q->select('id','agtPhoto','agtLogo','officeID',
      'agtFullName','agtDesigs','agtMainPhone');}])
->with(['theOffice'=>function($q){
   $q->select('officeName','officeAddress1','propagent_id',
      'officeCity','officeState','officeZip','officeID');}])
->with(['theMap'=>function($q){
   $q->select('propflyer_id','xIntersection');
}])
->first();