<?php

namespace App\Models\core;

use Illuminate\Database\Eloquent\Model;

class Propagent extends Model{

    protected $table='remuserdb.propagents';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $guarded=['id'];

    public function theAgentMeta(){
      return $this->hasOne('App\Models\Core\Propagentmeta','propagent_id','id');
    }

}
