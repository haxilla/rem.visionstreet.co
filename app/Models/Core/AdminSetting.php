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
