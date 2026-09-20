<?php

namespace App\Support;

/**
 * Where an agent's photo and logo live on disk, and whether the file is there.
 *
 * Same locations the flyers and the member area use:
 *   photo: public/agentPhotos/{photoToken}/{agtPhoto}
 *          (older photos may still sit in public/agentPhotos/{newRemID}/ until
 *           app/code/migrate_agent_photos.php moves them - those are found too)
 *   logo:  public/officeLogos/{officeID}/{agtLogo}
 *          (needs the agent's office record - the flyers build the address from it)
 *
 * The stored value is a file name; only its basename is ever used, so a stray
 * "../" in the database can't point outside these folders.
 */
class AgentImages
{
    public static function photoDir($agent): string
    {
        return public_path('agentPhotos/' . $agent->photoToken());
    }

    /** null when the agent has no office record (a logo can't be used without one) */
    public static function logoDir($agent): ?string
    {
        $officeId = optional($agent->theAgtOffice)->officeID;

        return $officeId ? public_path('officeLogos/' . $officeId) : null;
    }

    /**
     * @return array{file:?string, url:?string, found:bool, legacy:bool}
     *   file   - the name stored in the database (null when none)
     *   url    - where to show it from, when the file is actually on this server
     *   found  - whether the file exists on this server
     *   legacy - found in the old folder rather than the current one
     */
    public static function photo($agent): array
    {
        $file = basename((string) $agent->agtPhoto);

        if ($file === '') {
            return ['file' => null, 'url' => null, 'found' => false, 'legacy' => false];
        }

        $token = $agent->photoToken();

        if (is_file(public_path("agentPhotos/{$token}/{$file}"))) {
            return ['file' => $file, 'url' => asset("agentPhotos/{$token}/{$file}"), 'found' => true, 'legacy' => false];
        }

        $oldFolder = optional($agent->theAgentCleanup)->newRemID;

        if ($oldFolder && is_file(public_path("agentPhotos/{$oldFolder}/{$file}"))) {
            return ['file' => $file, 'url' => asset("agentPhotos/{$oldFolder}/{$file}"), 'found' => true, 'legacy' => true];
        }

        return ['file' => $file, 'url' => null, 'found' => false, 'legacy' => false];
    }

    /** @return array{file:?string, url:?string, found:bool, legacy:bool} */
    public static function logo($agent): array
    {
        $file = basename((string) $agent->agtLogo);

        if ($file === '') {
            return ['file' => null, 'url' => null, 'found' => false, 'legacy' => false];
        }

        $officeId = optional($agent->theAgtOffice)->officeID;

        if ($officeId && is_file(public_path("officeLogos/{$officeId}/{$file}"))) {
            return ['file' => $file, 'url' => asset("officeLogos/{$officeId}/{$file}"), 'found' => true, 'legacy' => false];
        }

        return ['file' => $file, 'url' => null, 'found' => false, 'legacy' => false];
    }
}
