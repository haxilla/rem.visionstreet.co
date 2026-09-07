<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class Propmeta extends Model
{

   protected $primaryKey   = 'propflyer_id';
   protected $table        = 'remuserdb.propmetas';
   public $incrementing    = false;

   protected $fillable = [
    'propflyer_id',
    'propagent_id',
    'zipDir',
    'mlsDir',
    'xPropType',
    'xListingType',
   ];

}
