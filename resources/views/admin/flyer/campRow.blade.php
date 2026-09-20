{{-- One campaign on the admin flyer page, the same layout at every stage:
     area + source tag + subject on the left, a status pill on the right, then a
     Requested -> Started -> Completed timeline and the "Edit dates" control.
     Needs $stage ('awaiting' | 'approved' | 'progress' | 'complete'), $area, $cid,
     $requested, $started, $completed. Optional: $adminAdded, $emails, $subject. --}}
@php
    $stages = [
        'awaiting' => ['label' => 'Awaiting approval',         'pill' => 'bg-amber-100 text-amber-800',     'bar' => 'bg-amber-400'],
        'approved' => ['label' => 'Approved, waiting to send', 'pill' => 'bg-indigo-100 text-indigo-700',   'bar' => 'bg-indigo-500'],
        'progress' => ['label' => 'In progress',               'pill' => 'bg-blue-100 text-blue-700',       'bar' => 'bg-blue-500'],
        'complete' => ['label' => 'Completed',                 'pill' => 'bg-emerald-100 text-emerald-700', 'bar' => 'bg-emerald-500'],
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

<div class="px-5 py-4">

    <div class="flex flex-wrap items-start justify-between gap-x-4 gap-y-2">

        <div class="min-w-0 flex-1 basis-56">
            <div class="flex flex-wrap items-center gap-x-1 gap-y-1">
                <span class="break-words text-base font-semibold text-slate-900">{{ $area }}</span>
                @include('admin.flyer.campSource', ['adminAdded' => $adminAdded ?? false])
            </div>

            @if(!empty($subject))
                <div class="mt-0.5 break-words text-sm text-slate-500">{{ $subject }}</div>
            @endif
        </div>

        <div class="flex shrink-0 flex-col items-start gap-1 sm:items-end">
            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $st['pill'] }}">{{ $st['label'] }}</span>

            @if(isset($emails) && is_numeric($emails))
                <span class="text-xs font-medium text-slate-500">
                    {{ number_format($emails) }} {{ (int) $emails === 1 ? 'contact' : 'contacts' }}
                </span>
            @endif
        </div>

    </div>

    {{-- progress: a step fills in (in the stage colour) once it has happened --}}
    <div class="mt-4 grid grid-cols-3 gap-3">
        @foreach($steps as [$stepLabel, $stepDate])
            <div>
                <div class="h-1 rounded-full {{ $stepDate ? $st['bar'] : 'bg-slate-200' }}"></div>

                <div class="mt-2 text-[11px] font-semibold uppercase tracking-wide {{ $stepDate ? 'text-slate-500' : 'text-slate-300' }}">
                    {{ $stepLabel }}
                </div>

                <div class="text-sm font-medium {{ $stepDate ? 'text-slate-900' : 'text-slate-300' }}">
                    {{ $stepDate ? $stepDate->format('M j, Y') : '—' }}
                </div>

                @if($stepDate)
                    <div class="text-xs text-slate-400">{{ $stepDate->format('g:i A') }}</div>
                @endif
            </div>
        @endforeach
    </div>

    @include('admin.flyer.campDates', ['cid' => $cid, 'requested' => $requested, 'started' => $started, 'completed' => $completed])

</div>
