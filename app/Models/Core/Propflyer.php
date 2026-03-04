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

    public function theOffice(){
        return $this->belongsTo('App\Models\Core\Agtoffice','propagent_id','propagent_id');
    }

    public function theMeta(){
        return $this->hasOne('App\Models\Core\Propmeta','propflyer_id','id');
    }

    public function thePhotos(){
        return $this->hasMany('App\Models\Core\Propphoto','propflyer_id','id');
    }

}
