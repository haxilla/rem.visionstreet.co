<?php

namespace App\Models\Sync;

class propphotoOld extends Model
{

	protected $connection = 'oldsite';
	protected $table='remailphotos';
	protected $primaryKey = 'photoID';
	public $timestamps = false;

}