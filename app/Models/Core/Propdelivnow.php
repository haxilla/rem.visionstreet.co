<?php

//this model is for campaigns that have not been completed
//during delivery it uses this table, but upon completion
//its inserted into regular propdeliv table and deleted
//from this table
namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class Propdelivnow extends Model
{
   protected $table        = 'propdelivnow';
   protected $dates        = ['emRequest','emStart','emComplete','created_at',
                              'updated_at','campCreated'];
   protected $primaryKey   = 'cid';

   public function theAgent(){
      return $this->belongsTo('App\Models\Core\Propagent','propagent_id','id')
      ->select(array('id','agtFullName'));
   }
   public function theFlyer(){
      return $this->belongsTo('App\Models\Core\Propflyer','propflyer_id','id')
      ->select(array('id','propagent_id','xFullStreet'));
   }
   public function theMeta(){
      return $this->hasOne('App\Models\Core\Propmeta','propflyer_id','propflyer_id')
      ->select(array('propflyer_id','sk1'));
   }
}

/* can use below to map and show each area per flyer
/* was originally right below currentCampsQuery
/* inside currentCamps Function
/* currentCampsQuery had get() not count()
*************************************************
//mapped fields -- original query
$currentCampsMap = $currentCampsQuery->map(function ($item) {
    return [
      'authorized'   => $item->authorized,
      'campLabel'    => $item->campLabel,
      'propflyer_id' => $item->propflyer_id,
      'emSubject'    => $item->emSubject,
      'emArea'       => $item->emArea,
      'emStart'      => $item->emStart,
      'emRequest'    => $item->emRequest,
      'cid'          => $item->cid,
    ];
});
//groupBy propflyer_id
$grouped=$currentCampsMap->groupBy('propflyer_id');

return $grouped;
*/

/***   EXAMPLE OF OUTPUTTING MAPPED QUERY
*****************************************
foreach($grouped as $the){
   echo $the[0]['propflyer_id'].'<br>';
   foreach($the as $t){
      echo $t['emArea'].'<br>';
   }
   echo '<BR>';
}
***/

