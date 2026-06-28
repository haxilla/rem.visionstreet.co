<?php

namespace App\Models\Sync;

use Illuminate\Database\Eloquent\Model;

class propphotoOld extends Model
{

	protected $connection = 'oldsite';
	protected $table='remailphotos';
	protected $primaryKey = 'photoID';
	public $timestamps = false;

}