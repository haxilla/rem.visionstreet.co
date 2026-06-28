<?php

namespace App\Models\Sync;

use Illuminate\Database\Eloquent\Model;

class propphotoCurArc extends Model
{

   protected $table = 'remarchives.remailphotosmaster';
   protected $primaryKey = 'photoID';
   public $timestamps=false;

}