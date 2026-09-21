<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Propflyer extends Model{

    use SoftDeletes;

    protected $table='remuserdb.propflyers';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $guarded=['id'];

    // Every flyer gets its public URL slug (see App\Support\FlyerSlug) as soon as it has a
    // complete address - which a new flyer has from the first wizard step - so it happens
    // on whichever path saves the flyer, with nothing to remember at each of them. A flyer
    // that already has a slug is left alone. A model loaded with only some columns can't
    // say whether it has one, so that case asks the database.
    protected static function booted()
    {
        static::saved(function (Propflyer $flyer) {
            $attributes = $flyer->getAttributes();

            if (array_key_exists('url_slug', $attributes) && filled($attributes['url_slug'])) {
                return;
            }

            $slug = \App\Support\FlyerSlug::ensure($flyer->getKey());

            if ($slug) {
                $flyer->setAttribute('url_slug', $slug);
                $flyer->syncOriginalAttribute('url_slug');
            }
        });

        // A flyer's city + state has to be in the Areas list (postal_cities). When a flyer is created, or
        // its city / state changes, the pair is added there - city + state only, no region, which is what
        // marks it "needs review" - if it isn't already. Best effort: it can never stop a flyer saving.
        static::saved(function (Propflyer $flyer) {
            try {
                if ($flyer->wasRecentlyCreated || $flyer->wasChanged(['xCity', 'xState', 'state'])) {
                    \App\Support\PostalCityRegistrar::noteFlyer($flyer);
                }
            } catch (\Throwable $e) {
                // never let this interfere with saving the flyer
            }
        });
    }

    public function theAgent(){
        return $this->belongsTo('App\Models\Core\Propagent','propagent_id','id');
    }

    public function theOffice(){
        return $this->belongsTo('App\Models\Core\Agtoffice','propagent_id','propagent_id');
    }

    public function theStyle(){
        return $this->hasOne('App\Models\Core\Propstyle','propflyer_id','id');
    }

    public function theMeta(){
        return $this->hasOne('App\Models\Core\Propmeta','propflyer_id','id');
    }

    public function thePhotos(){
        return $this->hasMany('App\Models\Core\Propphoto','propflyer_id','id');
    }

    public function theMap(){
        return $this->hasOne('App\Models\Core\Propmapping','propflyer_id','id');
    }

    public function theStats(){
        return $this->hasOne('App\Models\Core\Propflyerstat','propflyer_id','id');
    }

    public function theRemarks(){
        return $this->hasOne('App\Models\Core\Propremark','propflyer_id','id');
    }



}
