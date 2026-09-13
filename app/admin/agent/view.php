<?php

use App\Models\Core\Propagent;
use App\Models\Core\Propflyer;
use App\Models\Core\Propdeliv;
use Illuminate\Support\Facades\DB;

$agent = Propagent::with('theAgtOffice')->findOrFail($id);
$flyerCount = Propflyer::where('propagent_id', $id)->count();
$campaignCount = Propdeliv::where('propagent_id', $id)->count();
$orders = DB::table('allorders')->where('propagent_id', $id)->orderByDesc('payment_date')->get();
