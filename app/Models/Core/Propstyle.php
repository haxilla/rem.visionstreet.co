<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class Propstyle extends Model
{
   protected $primaryKey   = 'propflyer_id';

   protected $fillable = [
    'propflyer_id',
    'propagent_id',
    'flyer_background',
    'headline_bar_bg',
    'headline_bar_text',
    'headline_text',
    'graphic_words',
    'graphic_textcolor',
    'graphic_style',
    'roundedtop',
    'accentbars',
   ];

}
