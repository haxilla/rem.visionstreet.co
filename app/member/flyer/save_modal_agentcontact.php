<?php

use App\Models\Core\Propagent;

$agent = Propagent::findOrFail(auth()->id());

$validatedData = $request->validate([
    'agtFullName'   => 'nullable|string|max:100',
    'agtDesigs'     => 'nullable|string|max:100',
    'agtMainPhone'  => 'nullable|string|max:30',
    'officeName'    => 'nullable|string|max:150',
    'officeAddress' => 'nullable|string|max:150',
    'officeCity'    => 'nullable|string|max:100',
    'officeState'   => 'nullable|string|in:' . implode(',', array_keys(config('usstates'))),
    'officeZip'     => 'nullable|string|max:10',
    'agtPhotoFile'  => 'nullable|image|max:5120',
    'agtLogoFile'   => 'nullable|image|max:5120',
]);

$agent->agtFullName  = $validatedData['agtFullName'] ?? null;
$agent->agtDesigs    = $validatedData['agtDesigs'] ?? null;
$agent->agtMainPhone = $validatedData['agtMainPhone'] ?? null;

if ($request->hasFile('agtPhotoFile')) {
    $file = $request->file('agtPhotoFile');
    $dir  = public_path("agentPhotos/{$agent->photoToken()}");

    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    if (!empty($agent->agtPhoto) && file_exists("{$dir}/{$agent->agtPhoto}")) {
        @unlink("{$dir}/{$agent->agtPhoto}");
    }

    $filename = $agent->id . 'agtphoto-' . strtoupper(bin2hex(random_bytes(16))) . '.' . $file->extension();
    $file->move($dir, $filename);
    $agent->agtPhoto = $filename;
}

$office = $agent->theAgtOffice;

if ($office) {
    $office->officeName    = $validatedData['officeName'] ?? null;
    $office->officeAddress = $validatedData['officeAddress'] ?? null;
    $office->officeCity    = $validatedData['officeCity'] ?? null;
    $office->officeState   = $validatedData['officeState'] ?? null;
    $office->officeZip     = $validatedData['officeZip'] ?? null;
}

if ($office && $request->hasFile('agtLogoFile')) {
    $file       = $request->file('agtLogoFile');
    $officeID   = $office->officeID ?? $agent->id;
    $dir        = public_path("officeLogos/{$officeID}");

    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    if (!empty($agent->agtLogo) && file_exists("{$dir}/{$agent->agtLogo}")) {
        @unlink("{$dir}/{$agent->agtLogo}");
    }

    $filename = $agent->id . 'agtlogo-' . strtoupper(bin2hex(random_bytes(16))) . '.' . $file->extension();
    $file->move($dir, $filename);
    $agent->agtLogo = $filename;
}

$agent->save();

if ($office) {
    $office->save();
}

response('OK')->send();
exit();
