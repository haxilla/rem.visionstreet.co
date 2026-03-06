<?php

Use Illuminate\Database\Eloquent\Model;

namespace App\models\core;

class Propflyerstat extends Model
{
   protected $primaryKey = 'propflyer_id';

   protected $dates = [
      'created_at','updated_at','xLastDeliveryDate'
   ];

}