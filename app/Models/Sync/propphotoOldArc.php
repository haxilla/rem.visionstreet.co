<?php

namespace App\autosynch\models\propphoto;

class propphotoOldArc extends \App\Model
{

	protected $connection = 'oldsite';
	protected $table='remailphotosmaster';
	protected $primaryKey = 'photoID';
	public $timestamps = false;

}