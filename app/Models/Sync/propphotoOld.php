<?php

namespace App\Models\Sync;

use Illuminate\Database\Eloquent\Model;

class propphotoOld extends Model
{

	protected $connection = 'remote_realtyemails';
	protected $table='remailphotos';
	protected $primaryKey = 'photoID';
	public $timestamps = false;

}