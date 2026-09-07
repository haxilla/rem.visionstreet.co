<?php

namespace App\Models\Core;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Propagent extends Authenticatable

{

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
        return $this->hasMany('App\Models\Core\Propflyerstat','propagent_id','id');
    }

    public function theAgtOffice(){
      return $this->hasOne('App\Models\Core\Agtoffice','propagent_id','id');
    }

    // Stable, non-sequential folder name for this agent's uploaded files
    // (e.g. agentPhotos/{photoToken()}/...). Derived from the app secret
    // so it can't be guessed/enumerated from the agent's numeric id.
    public function photoToken(){
      $hash  = hash_hmac('sha256', (string) $this->id, config('app.key'), true);
      $token = preg_replace('/[^A-Za-z0-9]/', '', base64_encode($hash));
      return substr($token, 0, 10);
    }

}
