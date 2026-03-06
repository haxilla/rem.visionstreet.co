<?php

namespace App\Models\Core;

Use Illuminate\Database\Eloquent\Model;

class Propflyerstat extends Model
{
   protected $primaryKey = 'propflyer_id';

   protected $dates = [
      'created_at','updated_at','xLastDeliveryDate'
   ];

}