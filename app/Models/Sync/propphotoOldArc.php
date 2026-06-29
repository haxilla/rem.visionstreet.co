<?php

namespace App\Models\Sync;

use Illuminate\Database\Eloquent\Model;

class propphotoOldArc extends Model
{

	protected $connection = 'remote_realtyemails';
	protected $table='remailphotosmaster';
	protected $primaryKey = 'photoID';
	public $timestamps = false;

	protected $guarded = [];


}