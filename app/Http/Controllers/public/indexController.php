<?php

namespace App\Http\Controllers\public;
use App\Http\Controllers\Controller;

class indexController extends Controller
{

  public function index(){

    require app_path('code/users_rebuild.php');
    require app_path('public/index.php');

    //return view
    return view('mdbxPublic.index',
      [
        'newAdds'     => $newAdds,
        'mostViews'   => $mostViews,
      ]);
  }

}