<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class Propmeta extends Model
{

   protected $primaryKey   = 'propflyer_id';
   protected $table        = 'remuserdb.propmetas';

   protected $fillable = [
    'propflyer_id',
    'zipDir',
    'mlsDir',
   ];

}
