<?php

namespace App\Models\Core;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Propagent extends Authenticatable

{

    protected $table='remuserdb.propagents';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $guarded=['id'];

    // Every agent gets their web address slug (see App\Support\AgentSlug) as soon as they have
    // a name, on whichever path saves them. One who already has a slug is left alone; a model
    // loaded with only some columns can't say whether it has one, so that case asks the database.
    protected static function booted()
    {
        static::saved(function (Propagent $agent) {
            $attributes = $agent->getAttributes();

            $column = \App\Support\AgentSlug::COLUMN;

            if (array_key_exists($column, $attributes) && filled($attributes[$column])) {
                return;
            }

            $slug = \App\Support\AgentSlug::ensureOnSave($agent->getKey());

            if ($slug) {
                $agent->setAttribute($column, $slug);
                $agent->syncOriginalAttribute($column);
            }
        });
    }

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
