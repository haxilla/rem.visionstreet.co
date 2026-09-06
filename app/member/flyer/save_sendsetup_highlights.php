<?php

use App\Models\Core\Propflyer;

$validatedData = $request->validate([

    'flyerId'           => 'required|integer',
    'openHouseDate1'    => 'nullable|date',
    'openHouseTime1'    => 'nullable|date_format:H:i',
    'openHouseDate2'    => 'nullable|date',
    'openHouseTime2'    => 'nullable|date_format:H:i',
    'agentBonusAmount'  => 'nullable|string|max:255',
    'agentBonusComment' => 'nullable|string|max:255',

]);

$flyer = Propflyer::where('id', $validatedData['flyerId'])
    ->where('propagent_id', auth()->id())
    ->first();

if (!$flyer) {
    dd("Error: Flyer not found or you don't have permission to edit it.");
}

$flyer->openHouseDate1    = $validatedData['openHouseDate1'] ?? null;
$flyer->openHouseTime1    = $validatedData['openHouseTime1'] ?? null;
$flyer->openHouseDate2    = $validatedData['openHouseDate2'] ?? null;
$flyer->openHouseTime2    = $validatedData['openHouseTime2'] ?? null;
$flyer->agentBonusAmount  = $validatedData['agentBonusAmount'] ?? null;
$flyer->agentBonusComment = $validatedData['agentBonusComment'] ?? null;

$flyer->save();

redirect('/member/flyer/sendsetup?flyerId=' . $flyer->id)->send();

exit();
