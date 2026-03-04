<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class Propflyer extends Model{

    protected $table='remuserdb.propflyers';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $guarded=['id'];

    public function theAgent(){
        return $this->belongsTo('App\Models\Core\Propagent','propagent_id','id');
    }

}
