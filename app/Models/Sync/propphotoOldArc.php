<?php

namespace App\Models\Sync;

class propphotoOldArc extends Model
{

	protected $connection = 'oldsite';
	protected $table='remailphotosmaster';
	protected $primaryKey = 'photoID';
	public $timestamps = false;

}