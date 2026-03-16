<?php

namespace App\Models\Core;

class Propdeliv extends \App\Model
{
   protected $primaryKey   = 'cid';
   protected $dates = ['emRequest','emStart','emComplete',
   'created_at','updated_at'];

   public function theAgent(){
      return $this->belongsTo('App\Models\Core\Propagent','propagent_id','id')
      ->select(array('id','agtFullName'));
   }
   public function theFlyer(){
      return $this->belongsTo('App\Models\Core\Propflyer','propflyer_id','id')
      ->select(array('id','xFullStreet'));
   }
   public function theMeta(){
      return $this->hasOne('App\Models\Core\Propmeta','propflyer_id','propflyer_id')
      ->select(array('propflyer_id','sk1'));
   }

};




/*
public static function recentComplete(){
   $recentComplete=static::select(
      'cid','propflyer_id','propagent_id','emComplete'
   )
   ->whereNotNull('emComplete')
   ->with('agentInfo')
   ->orderBy('emComplete','desc')
   ->with('propInfo')
   ->with('agentInfo')
   ->take(10)
   ->get();

   return $recentComplete;
}
*/

/*
public static function complete5(){

   $umid=auth()->guard('web')->user()->id;
   //dd($umid);

   $completed5=static::selectRaw('ANY_VALUE(emComplete),xFullStreet,xWebViews,propflyers.id')
   ->leftJoin('propflyers','propflyers.id','=','propdelivs.propflyer_id')
   ->leftJoin('propflyerstats','propflyers.id','=','propflyerstats.propflyer_id')
   ->where('propflyers.propagent_id','=',"$umid")
   ->groupBy('propflyers.id')
   ->orderByRaw('min(emComplete)desc')
   ->get()
   ->take(5);

   return $completed5;
}
   */
