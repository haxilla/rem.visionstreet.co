<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class Propphoto extends Model
{
   protected $primaryKey = 'photoID';
   protected $dates = ['existCheck','photoDate'];

   public function theMeta(){
      return $this->hasOne('App\Models\Core\Propmeta','propflyer_id','propflyer_id');
   }

}