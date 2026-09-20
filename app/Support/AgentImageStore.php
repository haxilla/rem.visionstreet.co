<?php

namespace App\Support;

use App\Models\Core\Propagent;
use Illuminate\Http\UploadedFile;

/**
 * Add / change / clear an agent's photo or logo, for the agent's own Agent Info page.
 * (The admin's agent page does the same in adminController::agentImageUpload / Clear;
 * both follow the same storage rules - see AgentImages for where the files live.)
 *
 *  - the image is shrunk and re-encoded first (ImageOptimizer): flyers are EMAILED, so
 *    every recipient downloads these;
 *  - the file is named {agentId}agtphoto-/agtlogo-{random}.{ext}, in the folders the flyers
 *    read from, and the previous file is deleted from that same folder;
 *  - a logo needs the agent's office record (flyers build the address from it).
 */
class AgentImageStore
{
    /** @return array{0:string, 1:string, 2:string} [database column, file-name prefix, label] */
    private static function spec(string $kind): array
    {
        return $kind === 'photo'
            ? ['agtPhoto', 'agtphoto', 'photo']
            : ['agtLogo',  'agtlogo',  'logo'];
    }

    /** @return array{ok:bool, message:string} */
    public static function store(Propagent $agent, string $kind, UploadedFile $file): array
    {
        [$column, $prefix, $label] = self::spec($kind);

        $dir = $kind === 'photo' ? AgentImages::photoDir($agent) : AgentImages::logoDir($agent);
        $box = $kind === 'photo' ? ImageOptimizer::PHOTO_BOX : ImageOptimizer::LOGO_BOX;

        if ($dir === null) {
            return ['ok' => false, 'message' => 'Add your brokerage or office address and save it first - then you can add a logo.'];
        }

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $previous = basename((string) $agent->{$column});

        try {
            [$bytes, $extension] = ImageOptimizer::optimize($file->getRealPath(), $box, $kind === 'logo');
        } catch (\RuntimeException $e) {
            return ['ok' => false, 'message' => $e->getMessage()];
        }

        $filename = $agent->id . $prefix . '-' . strtoupper(bin2hex(random_bytes(16))) . '.' . $extension;

        if (@file_put_contents("{$dir}/{$filename}", $bytes) === false) {
            return ['ok' => false, 'message' => "Your {$label} couldn't be saved. Please try again, or contact support."];
        }

        @chmod("{$dir}/{$filename}", 0644);

        // remove the old file - only from this same folder (an older copy in a legacy folder is left alone)
        if ($previous !== '' && is_file("{$dir}/{$previous}")) {
            @unlink("{$dir}/{$previous}");
        }

        AgentTime::apply($agent);

        $agent->{$column} = $filename;
        $agent->save();

        return ['ok' => true, 'message' => 'Your ' . $label . ($previous !== '' ? ' was updated.' : ' was added.')];
    }

    /** Forget the file name and delete the file. */
    public static function clear(Propagent $agent, string $kind): string
    {
        [$column, , $label] = self::spec($kind);

        $dir      = $kind === 'photo' ? AgentImages::photoDir($agent) : AgentImages::logoDir($agent);
        $previous = basename((string) $agent->{$column});

        if ($dir !== null && $previous !== '' && is_file("{$dir}/{$previous}")) {
            @unlink("{$dir}/{$previous}");
        }

        AgentTime::apply($agent);

        // NULL, the same "none" the admin's No Photo / No Logo lists look for
        $agent->{$column} = null;
        $agent->save();

        return 'Your ' . $label . ' was removed.';
    }
}
