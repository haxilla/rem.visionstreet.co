{{-- One campaign on the admin flyer page, the same compact card at every stage: a
     stripe down the left edge in the stage's colour, the area + source tag + contact
     count with the status on the right, the subject, and ONE line of dates that opens
     the Edit panel (subject + dates).
     Only the dates that can exist for the stage are shown: a campaign waiting to be
     authorized / sent has only been requested, an in-progress one has also started,
     and only a completed one has all three.
     Needs $stage ('awaiting' | 'approved' | 'progress' | 'complete'), $area, $cid,
     $requested, $started, $completed. Optional: $adminAdded, $emails, $subject. --}}
@php
    // pill = the status badge, accent = the stripe
    $stages = [
        'awaiting' => ['label' => 'Not authorized',            'pill' => 'bg-amber-100 text-amber-800',     'accent' => 'border-l-amber-400'],
        'approved' => ['label' => 'Authorized',                'pill' => 'bg-indigo-100 text-indigo-700',   'accent' => 'border-l-indigo-500'],
        'progress' => ['label' => 'In progress',               'pill' => 'bg-blue-100 text-blue-700',       'accent' => 'border-l-blue-500'],
        'complete' => ['label' => 'Completed',                 'pill' => 'bg-emerald-100 text-emerald-700', 'accent' => 'border-l-emerald-500'],
    ];
    $st = $stages[$stage];

    // Real dates only: legacy rows can hold NULL, a zero date or odd values.
    $when = function ($value) {
        if (!$value) {
            return null;
        }

        try {
            $date = \Carbon\Carbon::parse($value);
        } catch (\Throwable $e) {
            return null;
        }

        return $date->year > 1970 ? $date : null;
    };

    // the dates to show for this stage
    $shown = [['Requested', $when($requested)]];

    if (in_array($stage, ['progress', 'complete'], true)) {
        $shown[] = ['Started', $when($started)];
    }

    if ($stage === 'complete') {
        $shown[] = ['Completed', $when($completed)];
    }
@endphp

<div class="rounded-xl border-l-[6px] bg-white px-4 py-3 shadow-sm ring-1 ring-slate-200 {{ $st['accent'] }}">

    <div class="flex flex-wrap items-center justify-between gap-x-4 gap-y-2">

        <div class="flex min-w-0 flex-1 basis-56 flex-wrap items-center gap-x-2 gap-y-1">
            <span class="break-words text-lg font-bold text-slate-900">{{ $area }}</span>

            @include('admin.flyer.campSource', ['adminAdded' => $adminAdded ?? false])

            @if(isset($emails) && is_numeric($emails))
                <span class="text-sm font-medium text-slate-600">
                    {{ number_format($emails) }} {{ (int) $emails === 1 ? 'contact' : 'contacts' }}
                </span>
            @endif
        </div>

        @if(in_array($stage, ['awaiting', 'approved'], true))
            {{-- waiting to start: the badge is a toggle that authorizes / unauthorizes just this campaign --}}
            @php $isOn = $stage === 'approved'; @endphp
            <form method="POST" action="{{ route('admin.campaignAuthorize', $cid) }}" class="shrink-0">
                @csrf
                <input type="hidden" name="authorized" value="{{ $isOn ? 0 : 1 }}">

                <button type="submit"
                        title="{{ $isOn ? 'Click to unauthorize this area' : 'Click to authorize this area' }}"
                        class="inline-flex items-center gap-2 rounded-full py-1 pl-3 pr-1.5 text-xs font-bold ring-1 ring-black/5 {{ $st['pill'] }}">
                    {{ $st['label'] }}
                    <span class="relative inline-block h-5 w-9 rounded-full transition-colors {{ $isOn ? 'bg-indigo-600' : 'bg-slate-400' }}">
                        <span class="absolute top-0.5 h-4 w-4 rounded-full bg-white shadow transition-all {{ $isOn ? 'left-[18px]' : 'left-0.5' }}"></span>
                    </span>
                </button>
            </form>
        @else
            <span class="shrink-0 rounded-full px-3 py-1 text-xs font-bold {{ $st['pill'] }}">{{ $st['label'] }}</span>
        @endif

    </div>

    @if(!empty($subject))
        <div class="mt-1 break-words text-sm text-slate-700">
            <span class="font-semibold text-slate-900">Subject:</span> {{ $subject }}
        </div>
    @endif

    {{-- the dates, on one line; the line itself opens the Edit panel --}}
    <details class="group mt-2 border-t border-slate-100 pt-2">
        <summary class="flex cursor-pointer list-none flex-wrap items-center justify-between gap-x-4 gap-y-1 [&::-webkit-details-marker]:hidden">

            <span class="flex flex-wrap gap-x-5 gap-y-1 text-sm">
                @foreach($shown as [$dateLabel, $date])
                    <span>
                        <span class="text-[11px] font-bold uppercase tracking-wide text-slate-500">{{ $dateLabel }}</span>
                        <span class="font-semibold {{ $date ? 'text-slate-900' : 'text-slate-400' }}">{{ $date ? $date->format('M j, Y g:i A') : '—' }}</span>
                    </span>
                @endforeach
            </span>

            <span class="inline-flex items-center gap-1 rounded-lg border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-[#214e9b] hover:bg-slate-50">
                Edit
                <svg class="h-3 w-3 transition-transform group-open:rotate-180" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M2 4l4 4 4-4"/></svg>
            </span>

        </summary>

        @include('admin.flyer.campEdit', ['cid' => $cid, 'requested' => $requested, 'started' => $started, 'completed' => $completed, 'subject' => $subject ?? null])
    </details>

</div>
