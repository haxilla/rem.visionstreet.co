<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class Propagent extends Model{

    protected $table='remuserdb.propagents';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $guarded=['id'];

    public function theAgentMeta(){
      return $this->hasOne('App\Models\Core\Propagentmeta','propagent_id','id');
    }

    public function theAgentCleanup(){
      return $this->hasOne('App\Models\Core\Propagentcleanup','propagent_id','id');
    }

    public function theStats(){
        return $this->hasMany('App\Models\Core\Propflyerstats','propagent_id','id');
    }

}
