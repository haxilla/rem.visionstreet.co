<?php
//model
use App\models\core\propflyer;
//set date frame
$theDate=\Carbon\Carbon::today()->subDays(30);
//query
$baseQuery=propflyer::select(
  'id','propflyers.propagent_id','propflyers.created_at','creationDate',
  'xFullStreet','xCity','xState','xZip','xxZip','xBeds','xBaths','officeID',
  'xSqft','xxBeds','xxBaths','xxSqft','xYrBuilt','xxYrBuilt','xListPrice',
  'xWebViews','url_slug','flyer_code')
->where(function($q)use($theDate){
  //either or created_at || creationDate
  $q->where('propflyers.created_at','>',"$theDate")
    ->orWhere('propflyers.creationDate','>',"$theDate");
})
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
->where('xAgtSent','=','1');
