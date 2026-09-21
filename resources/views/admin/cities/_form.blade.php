{{-- The fields for adding or editing a city. Needs: $city (a PostalCity, or null when adding), $lists, $states. --}}
@php
    $val = fn (string $field, $default = '') => old($field, $city->{$field} ?? $default);
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

    <div class="ui-field">
        <label for="region">Region</label>
        <input type="text" id="region" name="region" maxlength="50" required list="dl-regions" autocomplete="off" value="{{ $val('region') }}">
        <datalist id="dl-regions">@foreach($lists['regions'] as $v)<option value="{{ $v }}">@endforeach</datalist>
        <div class="ui-help">phoenix, northern, southern, western&hellip;</div>
    </div>

    <div class="ui-field">
        <label for="subregion">Sub-area</label>
        <input type="text" id="subregion" name="subregion" maxlength="50" list="dl-subregions" autocomplete="off" value="{{ $val('subregion') }}">
        <datalist id="dl-subregions">@foreach($lists['subregions'] as $v)<option value="{{ $v }}">@endforeach</datalist>
        <div class="ui-help">Optional. metro, northeast, southeast, west_valley&hellip;</div>
    </div>

    <div class="ui-field">
        <label for="mls_system">MLS</label>
        <input type="text" id="mls_system" name="mls_system" maxlength="100" list="dl-mls" autocomplete="off" value="{{ $val('mls_system') }}">
        <datalist id="dl-mls">@foreach($lists['mls'] as $v)<option value="{{ $v }}">@endforeach</datalist>
        <div class="ui-help">Optional. The best-fit local MLS, e.g. ARMLS, MLSSAZ.</div>
    </div>
</div>
