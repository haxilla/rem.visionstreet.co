<?php

namespace App\autosynch\models\propphoto;

class propphotos extends \App\Model
{

	protected $table = 'propphotos';
	protected $primaryKey = 'photoID';

	public function theMeta(){
		return $this
		->hasOne(
			'App\autosynch\models\propmeta\propmetas',
			'propflyer_id','propflyer_id'
		);
	}

}