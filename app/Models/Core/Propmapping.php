<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class Propmapping extends Model
{

   protected $primaryKey   = 'propflyer_id';
   public $incrementing    = false;

   protected $fillable = [
    'propflyer_id',
    'propagent_id',
    'xIntersection',
   ];

}
