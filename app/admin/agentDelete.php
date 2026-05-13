<?php

use App\Models\Core\Propagent;
use Illuminate\Support\Facades\DB;

public function agentDelete($id)
{
    $agent = Propagent::findOrFail($id);

    // Safety check
    if ($agent->startDate) {
        return redirect()->back();
    }

    /*
    |--------------------------------------------------------------------------
    | Delete from remote server/database
    |--------------------------------------------------------------------------
    */

    DB::connection('remote_realtyemails')
        ->table('remailagents')
        ->where('umid', $agent->id)
        ->delete();

    /*
    |--------------------------------------------------------------------------
    | Delete local record
    |--------------------------------------------------------------------------
    */

    $agent->delete();

}