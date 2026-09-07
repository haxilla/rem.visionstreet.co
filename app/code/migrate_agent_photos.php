<?php

use App\Models\Core\Propagent;

/*
|--------------------------------------------------------------------------
| One-time migration: copy agent photos to a consistent path
|--------------------------------------------------------------------------
| Old path: public/agentPhotos/{newRemID}/{agtPhoto}  (only exists for
| agents that have a propagentcleanup record)
| New path: public/agentPhotos/{photoToken}/{agtPhoto}  (works for
| everyone, and - like the old newRemID - isn't a guessable sequential
| id, see Propagent::photoToken())
|
| This script only COPIES files into the new location. It never deletes
| or modifies anything at the old location, and nothing in the app reads
| from the new location yet, so running it changes nothing about how the
| site currently looks or behaves. Safe to run more than once.
|
| Set $dryRun = false below to actually copy files. Leave it true to just
| see what it would do first.
*/

$dryRun = true;

$copied = 0;
$skippedNoPhoto = 0;
$skippedNoCleanup = 0;
$skippedNoSource = 0;
$skippedExists = 0;
$errors = 0;

$agents = Propagent::with('theAgentCleanup')->get();

foreach ($agents as $agent) {

    if (empty($agent->agtPhoto)) {
        $skippedNoPhoto++;
        continue;
    }

    $newRemID = $agent->theAgentCleanup->newRemID ?? null;

    if (empty($newRemID)) {
        $skippedNoCleanup++;
        echo "SKIP (no cleanup record): agent {$agent->id}\n";
        continue;
    }

    $sourcePath = public_path("agentPhotos/{$newRemID}/{$agent->agtPhoto}");
    $destDir    = public_path("agentPhotos/{$agent->photoToken()}");
    $destPath   = "{$destDir}/{$agent->agtPhoto}";

    if (!file_exists($sourcePath)) {
        $skippedNoSource++;
        echo "SKIP (source missing): agent {$agent->id}, expected {$sourcePath}\n";
        continue;
    }

    if (file_exists($destPath)) {
        $skippedExists++;
        continue;
    }

    echo ($dryRun ? "[DRY RUN] " : "") . "COPY: agent {$agent->id} - {$sourcePath} -> {$destPath}\n";

    if ($dryRun) {
        $copied++;
        continue;
    }

    if (!is_dir($destDir)) {
        mkdir($destDir, 0755, true);
    }

    if (copy($sourcePath, $destPath)) {
        $copied++;
    } else {
        $errors++;
        echo "ERROR: failed to copy for agent {$agent->id}\n";
    }
}

echo "\n---- Summary ----\n";
echo "Dry run: " . ($dryRun ? 'yes' : 'no') . "\n";
echo "Copied: {$copied}\n";
echo "Skipped (no photo set): {$skippedNoPhoto}\n";
echo "Skipped (no cleanup record / no newRemID): {$skippedNoCleanup}\n";
echo "Skipped (source file missing): {$skippedNoSource}\n";
echo "Skipped (already exists at destination): {$skippedExists}\n";
echo "Errors: {$errors}\n";
