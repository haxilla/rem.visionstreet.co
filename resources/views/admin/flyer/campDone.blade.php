{{-- One COMPLETED campaign on the admin flyer page, as a slim row: campaign id, area,
     subject, who it came from (Agent / Admin) and the completed date. The button at the
     right opens the rest - contacts, the full requested / started / completed times and
     the Edit panel (subject + dates).
     Needs $cid, $area, $completed, $requested, $started. Optional: $adminAdded, $emails,
     $subject. The row's layout is the .done-* CSS at the bottom of admin/flyer/camps.blade.php. --}}
@php
    $when = function ($value) {
        if (!$value) {
            return null;
        }

        try {
            $date = \Carbon\Carbon::parse($value);
        } catch (\Throwable $e) {
            return null;   // legacy rows can hold odd values
        }

        return $date->year > 1970 ? $date : null;   // and zero-dates
    };

    $requestedAt = $when($requested);
    $startedAt   = $when($started);
    $completedAt = $when($completed);

    $stamp = fn ($d) => $d ? $d->format('M j, Y g:i A') : '—';
@endphp

<div data-row-card>

    <div class="done-row text-sm">

        <span class="done-id text-xs font-semibold tabular-nums text-slate-500">#{{ $cid }}</span>

        <span class="done-area truncate font-semibold text-slate-900" title="{{ $area }}">{{ $area }}</span>

        <span class="done-subject truncate text-slate-700" title="{{ $subject ?? '' }}">
            @if(!empty($subject)) {{ $subject }} @else <span class="text-slate-400">No subject</span> @endif
        </span>

        <span class="done-src">
            @if($adminAdded ?? false)
                <span class="whitespace-nowrap rounded-full bg-violet-100 px-2 py-0.5 text-[11px] font-semibold text-violet-700">Admin</span>
            @else
                <span class="whitespace-nowrap rounded-full bg-sky-100 px-2 py-0.5 text-[11px] font-semibold text-sky-700">Agent{{ !empty($slot) ? ' · Area ' . $slot : '' }}</span>
            @endif
        </span>

        <span class="done-date whitespace-nowrap font-semibold tabular-nums text-slate-900">
            {{ $completedAt ? $completedAt->format('M j, Y') : '—' }}
        </span>

        <button type="button" data-row-toggle aria-expanded="false" title="More details"
                class="done-toggle inline-flex h-7 w-7 items-center justify-center rounded-lg border border-slate-300 bg-white text-[#214e9b] hover:bg-slate-50">
            <svg class="h-3 w-3 transition-transform" data-row-chevron viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M2 4l4 4 4-4"/></svg>
        </button>

    </div>

    {{-- THE REST --}}
    <div class="border-t border-slate-100 bg-slate-50 px-4 py-3" data-row-panel hidden>

        <div class="grid gap-x-6 gap-y-2 text-sm sm:grid-cols-2">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Contacts</span>
                <span class="font-semibold text-slate-900">{{ isset($emails) && is_numeric($emails) ? number_format($emails) : '—' }}</span>
            </div>
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Source</span>
                <span class="font-semibold text-slate-900">{{ ($adminAdded ?? false) ? 'Admin added · Free' : 'Agent sent' . (!empty($slot) ? ' · Area ' . $slot : '') }}</span>
            </div>
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Requested</span>
                <span class="font-semibold text-slate-900">{{ $stamp($requestedAt) }}</span>
            </div>
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Started</span>
                <span class="font-semibold text-slate-900">{{ $stamp($startedAt) }}</span>
            </div>
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Completed</span>
                <span class="font-semibold text-slate-900">{{ $stamp($completedAt) }}</span>
            </div>
        </div>

        @include('admin.flyer.campEdit', ['cid' => $cid, 'requested' => $requested, 'started' => $started, 'completed' => $completed, 'subject' => $subject ?? null])

    </div>

</div>
