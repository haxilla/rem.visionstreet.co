{{-- "Edit dates" control for one campaign on the admin flyer page. Needs $cid and
     the campaign's $requested / $started / $completed (Carbon, string or null).
     Clearing Started / Completed moves the campaign back a stage. --}}
@php
    $dateValue = function ($value) {
        if (!$value) {
            return '';
        }

        try {
            $date = \Carbon\Carbon::parse($value);
        } catch (\Throwable $e) {
            return '';   // legacy rows can hold odd values
        }

        return $date->year > 1970 ? $date->format('Y-m-d\TH:i:s') : '';   // and zero-dates
    };

    $inputClass = 'mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm';
@endphp

<details class="mt-3">
    <summary class="inline-block cursor-pointer rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-[#214e9b] hover:bg-slate-50">Edit dates</summary>

    <form method="POST" action="{{ route('admin.campaignDates', $cid) }}"
          class="mt-2 grid gap-3 rounded-xl bg-slate-50 p-3 sm:grid-cols-3">
        @csrf

        <label class="text-xs font-semibold text-slate-600">
            Requested
            <input type="datetime-local" step="1" name="emRequest" required
                   value="{{ $dateValue($requested) }}" class="{{ $inputClass }}">
        </label>

        <label class="text-xs font-semibold text-slate-600">
            Started
            <input type="datetime-local" step="1" name="emStart"
                   value="{{ $dateValue($started) }}" class="{{ $inputClass }}">
        </label>

        <label class="text-xs font-semibold text-slate-600">
            Completed
            <input type="datetime-local" step="1" name="emComplete"
                   value="{{ $dateValue($completed) }}" class="{{ $inputClass }}">
        </label>

        <div class="flex flex-wrap items-center justify-between gap-3 sm:col-span-3">
            <p class="text-xs text-slate-500">
                Times are the agent's local time. Leave Started and Completed empty for a waiting
                campaign; the dates decide which list it appears in.
            </p>

            <button type="submit"
                    class="rounded-lg bg-[#214e9b] px-4 py-2 text-sm font-semibold text-white hover:bg-[#1b3f80]">
                Save Dates
            </button>
        </div>
    </form>
</details>
