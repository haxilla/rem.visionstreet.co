<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class Propremark extends Model
{
   protected $primaryKey   = 'propflyer_id';

   protected $fillable = [
    'propflyer_id',
    'propagent_id',
    'xPubRemarks',
    'xb1',
    'xb2',
    'xb3',
    'xb4',
    'xb5',
    'xb6',
    'xb7',
    'xb8',
   ];
}