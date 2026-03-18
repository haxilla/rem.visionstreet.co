<?php
// get model
use App\Models\Core\Propflyer;

if(!$flyerId){
   dd('error-line7-queries/flyerdetails.php');}

// query
$propInfo=Propflyer::select(
   'id','propagent_id','officeID','xFullStreet',
   'xListPrice','xCity','xState','xZip','xHeadline',
   'xMlsNum','xBeds','xxBeds','xBaths','xxBaths',
   'xSqft','xxSqft','xYrBuilt','xVirtualTour',
   'xMlsLink','xxHeadline')
->where('id','=',"$flyerId")
->with(['theRemarks'=>function($q){
   $q->select('propflyer_id','xb1','xb2','xb3','xb4',
      'xb5','xb6','xb7','xb8','xPubRemarks');}])
->with(['thePhotos'=>function($q){
   $q->select('propflyer_id','photoName',
      'photoID','def','ord','orient','resized')
      ->where('resized','=','500');}])
->with(['theStyle'=>function($q){
   $q->select('propflyer_id','graphic_words',
      'graphic_textcolor','graphic_style','colors_chosen',
      'flyer_background','template','headline_text','headline_chosen',
      'headline_bar_bg','accentbars','headline_bar_text',
      'virtualTour_chosen','mlsLink_chosen');}])
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


dd($propInfo);
/*
$newRemID=propagentmeta::where('propagent_id','=',"$umid")
->pluck('newRemID')
->first();
*/

//error if none
if(!$propInfo){
   dd('error-line45-flyerdetails.php');}
