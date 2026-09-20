<?php

use App\Models\Core\Propagent;

$agent = Propagent::findOrFail(auth()->id());

$validatedData = $request->validate([
    // the name on the flyer is made from these two (48 + space + 48 fits the 100-character full name)
    'agtFirst'      => 'nullable|string|max:48',
    'agtLast'       => 'nullable|string|max:48',
    'agtDesigs'     => 'nullable|string|max:100',
    'agtMainPhone'  => 'nullable|string|max:30',
    'officeName'     => 'nullable|string|max:150',
    'officeAddress1' => 'nullable|string|max:150',
    'officeCity'     => 'nullable|string|max:100',
    'officeState'   => 'nullable|string|in:' . implode(',', array_keys(config('usstates'))),
    'officeZip'     => 'nullable|string|max:10',
    'agtPhotoFile'  => 'nullable|image|max:5120',
    'agtLogoFile'   => 'nullable|image|max:5120',
]);

$agent->agtFirst = trim((string) ($validatedData['agtFirst'] ?? ''));
$agent->agtLast  = trim((string) ($validatedData['agtLast'] ?? ''));

// The name on flyers and public pages is always made from first + last. With both boxes
// empty the existing name is kept, never wiped.
$agent->agtFullName = \App\Support\AgentNames::combine($agent->agtFirst, $agent->agtLast) ?? $agent->agtFullName;
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

    // Shrink and re-encode first: the flyer is emailed, so every recipient
    // downloads this image (see App\Support\ImageOptimizer).
    try {
        [$bytes, $extension] = \App\Support\ImageOptimizer::optimize($file->getRealPath(), \App\Support\ImageOptimizer::PHOTO_BOX, false);
    } catch (\RuntimeException $e) {
        throw \Illuminate\Validation\ValidationException::withMessages(['agtPhotoFile' => $e->getMessage()]);
    }

    $filename = $agent->id . 'agtphoto-' . strtoupper(bin2hex(random_bytes(16))) . '.' . $extension;
    file_put_contents("{$dir}/{$filename}", $bytes);
    @chmod("{$dir}/{$filename}", 0644);
    $agent->agtPhoto = $filename;
}

$office = $agent->theAgtOffice;

if ($office) {
    $office->officeName     = $validatedData['officeName'] ?? null;
    $office->officeAddress1 = $validatedData['officeAddress1'] ?? null;
    $office->officeCity     = $validatedData['officeCity'] ?? null;
    $office->officeState    = $validatedData['officeState'] ?? null;
    $office->officeZip      = $validatedData['officeZip'] ?? null;
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

    try {
        [$bytes, $extension] = \App\Support\ImageOptimizer::optimize($file->getRealPath(), \App\Support\ImageOptimizer::LOGO_BOX, true);
    } catch (\RuntimeException $e) {
        throw \Illuminate\Validation\ValidationException::withMessages(['agtLogoFile' => $e->getMessage()]);
    }

    $filename = $agent->id . 'agtlogo-' . strtoupper(bin2hex(random_bytes(16))) . '.' . $extension;
    file_put_contents("{$dir}/{$filename}", $bytes);
    @chmod("{$dir}/{$filename}", 0644);
    $agent->agtLogo = $filename;
}

$agent->save();

if ($office) {
    $office->save();
}

response('OK')->send();
exit();
