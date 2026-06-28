<?php

namespace App\autosynch\models\propphoto;

class propphotoOld extends Model
{

	protected $connection = 'oldsite';
	protected $table='remailphotos';
	protected $primaryKey = 'photoID';
	public $timestamps = false;

}