<?php

namespace App\autosynch\models\propphoto;

class propphotoCurArc extends \App\Model
{

   protected $table = 'remarchives.remailphotosmaster';
   protected $primaryKey = 'photoID';
   public $timestamps=false;

}