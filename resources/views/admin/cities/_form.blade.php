{{--
    The fields for adding or editing a city. Needs: $city (a PostalCity, or null when adding),
    $lists (the region / sub-area / MLS values already in the table), $states.

    Region, sub-area and MLS are CHOSEN from what is already in the table, so they can't be
    mistyped; "Add a new..." at the end of each list opens a box for a value that doesn't exist yet
    (the server tidies it and adds it). Only the first form on the page includes the small script.
--}}
@php
    $val = fn (string $field, $default = '') => old($field, $city->{$field} ?? $default);

    // [column, label, values in use, is it required?, what "new" is called, example for the new box, display text per value]
    $choices = [
        ['region',     'Region',   $lists['regions'],    true,  'region',   'e.g. central',    []],
        ['subregion',  'Sub-area', $lists['subregions'], false, 'sub-area', 'e.g. east_valley', []],
        ['mls_system', 'MLS',      $lists['mls'],        false, 'MLS',      'e.g. ARMLS',      []],
    ];

    // The LOCAL distribution list: the mailing list that is "local" for this city (azphxwv, aznaz ...).
    // Only offered once the column has been added to the table.
    if (!empty($lists['hasLocal'])) {
        $choices[] = ['local_list', 'Local list', array_keys($lists['lists']), false, 'list', 'e.g. azphxwv', $lists['lists']];
    }
@endphp

<div class="ui-grid">
    <div class="ui-field">
        <label for="city">City</label>
        <input type="text" id="city" name="city" maxlength="100" required autocomplete="off" value="{{ $val('city') }}">
    </div>

    <div class="ui-field">
        <label for="state">State</label>
        <select id="state" name="state" required>
            @foreach($states as $abbr => $stateName)
                <option value="{{ $abbr }}" @selected($val('state', 'AZ') === $abbr)>{{ $abbr }} &mdash; {{ $stateName }}</option>
            @endforeach
        </select>
    </div>

    @foreach($choices as [$column, $label, $values, $required, $noun, $example, $texts])
        @php $current = $val($column); @endphp

        <div class="ui-field js-choice">
            <label for="{{ $column }}">{{ $label }}</label>

            <select id="{{ $column }}" name="{{ $column }}" @if($required) required @endif>
                <option value="">{{ $required ? 'Choose a ' . $noun . '…' : '— none —' }}</option>

                @foreach($values as $v)
                    <option value="{{ $v }}" @selected($current === $v)>{{ $texts[$v] ?? $v }}</option>
                @endforeach

                <option value="__new__" @selected($current === '__new__')>＋ Add a new {{ $noun }}…</option>
            </select>

            @if($column === 'local_list')
                <div class="ui-help">The mailing list that counts as local for this city. A brand-new list name also has to exist on the mailer.</div>
            @endif

            <input type="text" name="{{ $column }}_new" maxlength="100" placeholder="{{ $example }}" autocomplete="off"
                   value="{{ old($column . '_new') }}" class="js-new" style="margin-top:8px;{{ $current === '__new__' ? '' : 'display:none' }}">
        </div>
    @endforeach
</div>

@once
<script>
// Show the "new value" box only while "Add a new..." is chosen in that list.
(function () {
    function sync(wrapper) {
        var select = wrapper.querySelector('select');
        var box    = wrapper.querySelector('.js-new');
        var isNew  = select.value === '__new__';

        box.style.display = isNew ? '' : 'none';

        if (isNew) { box.focus(); }
    }

    document.querySelectorAll('.js-choice').forEach(function (wrapper) {
        wrapper.querySelector('select').addEventListener('change', function () { sync(wrapper); });
    });
})();
</script>
@endonce
