{{-- The Edit panel of one campaign on the admin flyer page (opened from campRow):
     its own email subject, and its requested / started / completed dates.
     Needs $cid, and the campaign's $requested / $started / $completed (Carbon, string
     or null) and $subject. Clearing Started / Completed moves the campaign back a
     stage, so all three dates stay editable even though a waiting campaign only
     shows its requested date. --}}
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

    $inputClass = 'mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm';
    $labelClass = 'text-xs font-bold uppercase tracking-wide text-slate-600';
@endphp

<div class="mt-3 space-y-4 rounded-xl bg-slate-50 p-3 ring-1 ring-slate-200">

    {{-- SUBJECT: this campaign only --}}
    <form method="POST" action="{{ route('admin.campaignSubject', $cid) }}" class="flex flex-col gap-2 sm:flex-row sm:items-end">
        @csrf

        <label class="{{ $labelClass }} flex-1">
            Subject (this campaign only)
            <input type="text" name="subject" maxlength="255" value="{{ $subject ?? '' }}"
                   class="{{ $inputClass }} font-normal normal-case tracking-normal">
        </label>

        <button type="submit"
                class="rounded-lg bg-[#214e9b] px-4 py-2 text-sm font-semibold text-white hover:bg-[#1b3f80]">
            Save Subject
        </button>
    </form>

    {{-- DATES --}}
    <form method="POST" action="{{ route('admin.campaignDates', $cid) }}" class="border-t border-slate-200 pt-4">
        @csrf

        <div class="grid gap-3 sm:grid-cols-3">
            <label class="{{ $labelClass }}">
                Requested
                <input type="datetime-local" step="1" name="emRequest" required
                       value="{{ $dateValue($requested) }}" class="{{ $inputClass }} font-normal normal-case tracking-normal">
            </label>

            <label class="{{ $labelClass }}">
                Started
                <input type="datetime-local" step="1" name="emStart"
                       value="{{ $dateValue($started) }}" class="{{ $inputClass }} font-normal normal-case tracking-normal">
            </label>

            <label class="{{ $labelClass }}">
                Completed
                <input type="datetime-local" step="1" name="emComplete"
                       value="{{ $dateValue($completed) }}" class="{{ $inputClass }} font-normal normal-case tracking-normal">
            </label>
        </div>

        <div class="mt-3 flex flex-wrap items-center justify-between gap-3">
            <p class="text-xs text-slate-500">
                Times are the agent's local time. The dates decide which list the campaign appears in:
                leave Started and Completed empty for a waiting campaign.
            </p>

            <button type="submit"
                    class="rounded-lg bg-[#214e9b] px-4 py-2 text-sm font-semibold text-white hover:bg-[#1b3f80]">
                Save Dates
            </button>
        </div>
    </form>

</div>
