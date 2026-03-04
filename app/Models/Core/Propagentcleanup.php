<?php

namespace App\Models\Core;

Use Illuminate\Database\Eloquent\Model;

class Propagentcleanup extends Model
{
   protected $primaryKey   = 'propagent_id';
   protected $table        = 'propagentcleanup';
   protected $dates        = ['agtPhotoCheck','agtLogoCheck','eidxCheck',
                              'agtUnameCheck','LicNumberCheck',
                              'EmployerLicNumberCheck'];

}
