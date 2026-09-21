<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

// One row per postal city / locality, saying which RealtyEmails marketing territory it belongs to
// (region + sub-area) and its best-fit MLS. Managed by hand at Admin > Data > Cities & Areas.
// The table is created with SQL (postal_cities_arizona.sql); "region" and "subregion" are lower-case
// keys such as phoenix / west_valley, "mls_system" is free text such as ARMLS or MLSSAZ.
class PostalCity extends Model
{
    protected $table   = 'postal_cities';
    public $timestamps = false;
    protected $guarded = ['id'];
}
