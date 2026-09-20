<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

// System-wide admin settings, one row per setting (key/value).
// The external mail system reads this table directly (e.g. trial_mode),
// so nothing about a setting is ever stamped onto campaign rows.
//
// To add a setting: add it to definitions() below - the admin Settings
// page and the save handler both work from that list.
class AdminSetting extends Model
{
    protected $table      = 'admin_settings';
    protected $primaryKey = 'setting_key';
    public $incrementing  = false;
    protected $keyType    = 'string';
    protected $guarded    = [];

    // Table only has updated_at (no created_at).
    const CREATED_AT = null;

    /**
     * Every setting the admin Settings page manages.
     *   type 'toggle' stores '1' (on) or '0' (off).
     *   type 'email'  stores an email address ('' when unset).
     *   'required_when' (optional) names a toggle setting; the value is
     *   then required while that toggle is on.
     */
    public static function definitions(): array
    {
        return [
            'trial_mode' => [
                'label'       => 'Trial mode',
                'description' => 'While on, sends are tests: no credits are used, and the agent does not receive their copy - it goes to the test email below instead. Turn this on before testing and off again for real sends.',
                'type'        => 'toggle',
                'default'     => '0',
            ],
            'confirm_agent_deletion' => [
                'label'       => 'Confirm agent deletion',
                'description' => 'When on, "Delete selected" on the "No Start Date" agents list asks you to confirm first. Turn it off to delete the ticked agents immediately with no prompt (handy while clearing out the easy ones), and back on when you are done.',
                'type'        => 'toggle',
                'default'     => '1',
            ],
            'agent_slugs_auto' => [
                'label'       => 'Give agents their web address automatically',
                'description' => 'When on, an agent gets their own web address (like /DebraLee) the first time their record is saved with a name. Keep this OFF while duplicate accounts are being merged: the account saved first would get the plain name (DebraLee) and the other a number (DebraLee2), whichever is the right one. It only affects automatic assignment - the "php artisan agents:backfill-slugs" command works whatever this is set to.',
                'type'        => 'toggle',
                'default'     => '0',
            ],
            'trial_email' => [
                'label'         => 'Test email address',
                'description'   => 'Where the agent\'s copy of a send goes while trial mode is on.',
                'type'          => 'email',
                'default'       => '',
                'required_when' => 'trial_mode',
            ],
        ];
    }

    public static function read(string $key, ?string $default = null): ?string
    {
        $value = static::whereKey($key)->value('setting_value');

        return $value ?? $default;
    }

    public static function write(string $key, string $value): void
    {
        static::updateOrCreate(
            ['setting_key' => $key],
            ['setting_value' => $value]
        );
    }

    public static function trialMode(): bool
    {
        return static::read('trial_mode', '0') === '1';
    }

    /**
     * Whether deleting an agent from the "No Start Date" list asks for
     * confirmation. On unless explicitly switched off - a missing row means on,
     * so the safe behaviour is the default.
     */
    public static function confirmAgentDeletion(): bool
    {
        return static::read('confirm_agent_deletion', '1') !== '0';
    }

    /**
     * Whether an agent is given their web address slug automatically when their record is saved
     * (App\Models\Core\Propagent's saved hook). OFF unless explicitly switched on, so nothing gets one
     * as a side effect of an edit - a missing row means off.
     */
    public static function agentSlugsAuto(): bool
    {
        return static::read('agent_slugs_auto', '0') === '1';
    }

    /** The address trial-mode agent copies go to ('' when not set). */
    public static function trialEmail(): string
    {
        return trim((string) static::read('trial_email', ''));
    }

    /** Current value of every defined setting (falls back to its default). */
    public static function currentValues(): array
    {
        $definitions = static::definitions();

        $stored = static::whereIn('setting_key', array_keys($definitions))
            ->pluck('setting_value', 'setting_key');

        $values = [];

        foreach ($definitions as $key => $definition) {
            $values[$key] = $stored[$key] ?? $definition['default'];
        }

        return $values;
    }
}
