<?php

namespace App\Support;

use App\Models\Core\Agtoffice;
use App\Models\Core\Propagent;
use Illuminate\Support\Facades\DB;

/**
 * An agent's editable profile - contact details, address / office and licence details -
 * shared by the admin's agent page and the agent's own "Agent Info" page, so both
 * validate and save EXACTLY the same way.
 *
 * Not editable here: the login email (it decides who can sign in - only the admin's
 * lost-mailbox tool changes it), credits, start date and password.
 *
 * Rules that hold everywhere:
 *  - the name shown on flyers (agtFullName) is never typed: it is made from first + last
 *    (AgentNames). With both boxes empty the existing name is left alone;
 *  - a blank box is stored as an empty string (safe for nullable and NOT NULL legacy columns);
 *  - a website without a scheme gets https:// so it works as a link.
 */
class AgentProfile
{
    /** Contact fields: column => [label, input type, max length]. */
    public const CONTACT = [
        'agtFirst'     => ['First name',      'text',  48],
        'agtLast'      => ['Last name',       'text',  48],
        'agtEmail'     => ['Contact email',   'email', 100],
        'agtMainPhone' => ['Main phone',      'tel',   30],
        'agtMobile'    => ['Mobile',          'tel',   30],
        'agtHomePhone' => ['Home phone',      'tel',   30],
        'agtPhone2'    => ['Other phone',     'tel',   30],
        'agtWebsite'   => ['Website',         'text',  255],
    ];

    /** Licence details: column => label (all up to 100 characters). */
    public const LICENSE = [
        'agtMlsID'  => 'MLS ID',
        'agtBoard'  => 'Board',
        'agtDesigs' => 'Designations',
        'agtCounty' => 'County',
    ];

    /** Office record columns. */
    public const OFFICE = ['officeName', 'officeAddress1', 'officeCity', 'officeState', 'officeZip'];

    public static function contactRules(): array
    {
        $rules = [];

        foreach (self::CONTACT as $column => [, $type, $max]) {
            $rules[$column] = ['nullable', $type === 'email' ? 'email' : 'string', 'max:' . $max];
        }

        return $rules;
    }

    public static function licenseRules(): array
    {
        return array_map(fn () => ['nullable', 'string', 'max:100'], self::LICENSE);
    }

    /** @param Agtoffice|null $office the agent's current office record (its saved state is always accepted) */
    public static function officeRules(?Agtoffice $office = null): array
    {
        $states = array_keys(config('usstates'));

        return [
            'officeName'     => ['nullable', 'string', 'max:150'],
            'officeAddress1' => ['nullable', 'string', 'max:150'],
            'officeCity'     => ['nullable', 'string', 'max:100'],
            'officeZip'      => ['nullable', 'string', 'max:10'],
            // a state the old system saved in some other form is kept as-is rather than rejected
            'officeState'    => ['nullable', 'string', 'max:30', function ($attribute, $value, $fail) use ($states, $office) {
                if ($value !== null && $value !== '' && !in_array($value, $states, true) && $value !== ($office->officeState ?? null)) {
                    $fail('Choose a state from the list.');
                }
            }],
        ];
    }

    /** Save the contact fields (validated input in $data). Throws on a database error. */
    public static function saveContact(Propagent $agent, array $data): void
    {
        $data = array_map(fn ($value) => trim((string) $value), array_intersect_key($data, self::CONTACT));

        if (($data['agtWebsite'] ?? '') !== ''
            && !preg_match('#^https?://#i', $data['agtWebsite'])
            && !preg_match('/\s/', $data['agtWebsite'])
            && str_contains($data['agtWebsite'], '.')) {
            $data['agtWebsite'] = 'https://' . $data['agtWebsite'];
        }

        $fullName = AgentNames::combine($data['agtFirst'] ?? null, $data['agtLast'] ?? null);

        if ($fullName !== null) {
            $data['agtFullName'] = $fullName;
        }

        // the save stamps updated_at - in the agent's timezone
        AgentTime::apply($agent);

        $agent->forceFill($data)->save();
    }

    /**
     * Save the office record and the licence details. An agent with no office record gets
     * one when a brokerage or address is entered; its id is the agent's own id, the same
     * fallback the logo folder already uses. Throws on a database error.
     */
    public static function saveOffice(Propagent $agent, array $data): void
    {
        $data = array_map(fn ($value) => trim((string) $value), $data);

        $officeData  = array_intersect_key($data, array_flip(self::OFFICE));
        $licenseData = array_intersect_key($data, self::LICENSE);

        AgentTime::apply($agent);

        $office = $agent->theAgtOffice;

        DB::transaction(function () use ($agent, $office, $officeData, $licenseData) {
            $agent->forceFill($licenseData)->save();

            if ($office) {
                $office->forceFill($officeData)->save();
            } elseif (array_filter($officeData, fn ($value) => $value !== '') !== []) {
                $new = new Agtoffice();
                $new->forceFill($officeData + [
                    'propagent_id' => $agent->id,
                    'officeID'     => $agent->id,
                ])->save();
            }
        });
    }
}
