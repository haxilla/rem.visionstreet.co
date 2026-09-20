{{-- One campaign on the admin flyer page, the same layout at every stage. It is its
     own white card with a stripe down the left edge in the stage's colour, so each
     campaign stands apart from the next: area + source tag on the left, a status pill
     on the right, the subject, then a Requested -> Started -> Completed timeline
     and the "Edit dates" control.
     Needs $stage ('awaiting' | 'approved' | 'progress' | 'complete'), $area, $cid,
     $requested, $started, $completed. Optional: $adminAdded, $emails, $subject. --}}
@php
    // pill = the status badge, accent = the stripe, bar = the timeline fill
    $stages = [
        'awaiting' => ['label' => 'Awaiting approval',         'pill' => 'bg-amber-100 text-amber-800',     'accent' => 'border-l-amber-400',   'bar' => 'bg-amber-400'],
        'approved' => ['label' => 'Approved, waiting to send', 'pill' => 'bg-indigo-100 text-indigo-700',   'accent' => 'border-l-indigo-500',  'bar' => 'bg-indigo-500'],
        'progress' => ['label' => 'In progress',               'pill' => 'bg-blue-100 text-blue-700',       'accent' => 'border-l-blue-500',    'bar' => 'bg-blue-500'],
        'complete' => ['label' => 'Completed',                 'pill' => 'bg-emerald-100 text-emerald-700', 'accent' => 'border-l-emerald-500', 'bar' => 'bg-emerald-500'],
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

    $steps = [
        ['Requested', $when($requested)],
        ['Started',   $when($started)],
        ['Completed', $when($completed)],
    ];
@endphp

<div class="rounded-xl border-l-[6px] bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-5 {{ $st['accent'] }}">

    <div class="flex flex-wrap items-start justify-between gap-x-4 gap-y-2">

        <div class="min-w-0 flex-1 basis-56">
            <div class="flex flex-wrap items-center gap-x-1 gap-y-1">
                <span class="break-words text-lg font-bold text-slate-900">{{ $area }}</span>
                @include('admin.flyer.campSource', ['adminAdded' => $adminAdded ?? false])
            </div>

            @if(isset($emails) && is_numeric($emails))
                <div class="mt-0.5 text-sm font-medium text-slate-600">
                    {{ number_format($emails) }} {{ (int) $emails === 1 ? 'contact' : 'contacts' }}
                </div>
            @endif
        </div>

        <span class="shrink-0 rounded-full px-3 py-1 text-xs font-bold {{ $st['pill'] }}">{{ $st['label'] }}</span>

    </div>

    @if(!empty($subject))
        <div class="mt-3 break-words text-sm text-slate-700">
            <span class="font-semibold text-slate-900">Subject:</span> {{ $subject }}
        </div>
    @endif

    {{-- progress: a step fills in (in the stage colour) once it has happened --}}
    <div class="mt-4 grid grid-cols-3 gap-3 rounded-lg bg-slate-50 p-3 ring-1 ring-slate-200">
        @foreach($steps as [$stepLabel, $stepDate])
            <div>
                <div class="h-1.5 rounded-full {{ $stepDate ? $st['bar'] : 'bg-slate-200' }}"></div>

                <div class="mt-2 text-[11px] font-bold uppercase tracking-wide {{ $stepDate ? 'text-slate-600' : 'text-slate-400' }}">
                    {{ $stepLabel }}
                </div>

                <div class="text-sm font-semibold {{ $stepDate ? 'text-slate-900' : 'text-slate-400' }}">
                    {{ $stepDate ? $stepDate->format('M j, Y') : '—' }}
                </div>

                @if($stepDate)
                    <div class="text-xs text-slate-500">{{ $stepDate->format('g:i A') }}</div>
                @endif
            </div>
        @endforeach
    </div>

    @include('admin.flyer.campDates', ['cid' => $cid, 'requested' => $requested, 'started' => $started, 'completed' => $completed])

</div>
