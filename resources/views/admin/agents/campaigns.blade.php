{{-- Only reachable through adminController::agentCampaigns, which supplies $agent,
     $groups and $campaigns (this file is also reachable by URL through the
     /admin/{segments} convention, where none of them exist). --}}
@php abort_unless(isset($agent, $groups, $campaigns), 404); @endphp

@include('public.layout.head')

<body data-section="admin" class="relative bg-white min-h-screen font-sans text-gray-800">

@include('admin.layout.nav')

@php
    $displayName = trim(($agent->agtFirst ?? '') . ' ' . ($agent->agtLast ?? ''));
    if ($displayName === '') {
        $displayName = $agent->agtFullName ?: $agent->agtUname ?: $agent->agtEmail ?: 'No Name';
    }

    $sentCount  = $campaigns->where('status', 'completed')->count();
    $queueCount = $campaigns->count() - $sentCount;

    // pill = the status badge, accent = the stripe down the card's left edge,
    // bar = the colour the progress bar fills with
    $statuses = [
        'completed'  => ['label' => 'Completed',                 'pill' => 'bg-emerald-100 text-emerald-700', 'accent' => 'border-l-emerald-500', 'bar' => 'bg-emerald-500'],
        'delivering' => ['label' => 'In Progress',               'pill' => 'bg-blue-100 text-blue-700',       'accent' => 'border-l-blue-500',    'bar' => 'bg-blue-500'],
        'approved'   => ['label' => 'Approved, waiting to send', 'pill' => 'bg-indigo-100 text-indigo-700',   'accent' => 'border-l-indigo-500',  'bar' => 'bg-indigo-500'],
        'pending'    => ['label' => 'Awaiting approval',         'pill' => 'bg-amber-100 text-amber-700',     'accent' => 'border-l-amber-400',   'bar' => 'bg-amber-400'],
    ];

    $day  = fn ($d) => $d ? $d->format('M j, Y') : null;
    $at   = fn ($d) => $d ? $d->format('g:i A') : null;
    $num  = fn ($n) => is_numeric($n) ? number_format($n) : '—';

    $card = 'rounded-[24px] bg-white shadow-[0_12px_35px_rgba(15,23,42,0.06)]';
@endphp

<main class="min-h-screen bg-[#f4f7fb] pt-24">
<div class="mx-auto max-w-[1100px] px-4 py-6 sm:px-6 lg:px-10 lg:py-8">

    {{-- ===================== PAGE HEADER ===================== --}}
    <div class="{{ $card }} px-5 py-6 sm:px-8 sm:py-7">

        <div class="text-[12px] font-semibold uppercase tracking-[0.22em] text-[#214e9b]/70">
            Admin / Agents / Campaigns
        </div>

        <div class="mt-3 flex flex-wrap items-start justify-between gap-4">
            <div class="min-w-0">
                <h1 class="break-words text-2xl font-semibold leading-tight text-slate-900 sm:text-[30px]">
                    {{ $displayName }}'s Campaigns
                </h1>

                <p class="mt-1 text-sm text-slate-600">
                    Agent ID {{ $agent->id }}
                    <span class="mx-1.5 text-slate-300">&middot;</span>
                    {{ number_format($campaigns->count()) }} {{ $campaigns->count() === 1 ? 'campaign' : 'campaigns' }}
                    across {{ number_format($groups->count()) }} {{ $groups->count() === 1 ? 'flyer' : 'flyers' }}
                </p>

                <div class="mt-3 flex flex-wrap gap-2 text-xs font-semibold">
                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-emerald-700 ring-1 ring-emerald-200">
                        {{ number_format($sentCount) }} sent
                    </span>
                    @if($queueCount > 0)
                        <span class="rounded-full bg-amber-50 px-3 py-1 text-amber-700 ring-1 ring-amber-200">
                            {{ number_format($queueCount) }} in queue / in progress
                        </span>
                    @endif
                </div>
            </div>

            <a href="/admin/agentView/{{ $agent->id }}"
               class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                ← Back to agent
            </a>
        </div>
    </div>

    {{-- ===================== ONE CARD PER FLYER ===================== --}}
    <div class="mt-6 space-y-8">

        @forelse($groups as $group)

            <section class="overflow-hidden rounded-[24px] bg-white shadow-[0_12px_35px_rgba(15,23,42,0.10)] ring-1 ring-slate-200">

                {{-- THE PROPERTY: a dark band (the site's navy), so it stands well clear of
                     the campaigns listed below it --}}
                <div class="bg-gradient-to-r from-[#1b2f63] via-[#223a75] to-[#2a4486] px-5 py-5 text-white sm:px-6">
                    <div class="flex items-start gap-4">

                        {{-- default photo, at a fixed size --}}
                        <div class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-white/10 ring-2 ring-white/60 sm:h-20 sm:w-20">
                            @if($group['thumb'])
                                <img src="{{ $group['thumb'] }}" alt="{{ $group['title'] }}" loading="lazy"
                                     class="h-full w-full object-cover">
                            @else
                                <svg class="h-7 w-7 text-white/60" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 9.75L12 3l9 6.75V21a.75.75 0 01-.75.75H3.75A.75.75 0 013 21V9.75z"/>
                                </svg>
                            @endif
                        </div>

                        <div class="min-w-0 flex-1">

                            <div class="flex flex-wrap items-start justify-between gap-x-4 gap-y-2">
                                <div class="min-w-0">
                                    <h2 class="break-words text-lg font-bold leading-snug text-white sm:text-xl">
                                        {{ $group['title'] }}
                                    </h2>

                                    @if($group['place'] !== '')
                                        <p class="text-sm text-blue-100">{{ $group['place'] }}</p>
                                    @endif
                                </div>

                                @if($group['deleted'])
                                    <span class="rounded-full bg-red-500 px-3 py-1 text-xs font-semibold text-white">Flyer deleted</span>
                                @else
                                    <a href="/admin/flyerCamps/{{ $group['flyerId'] }}"
                                       class="rounded-lg bg-white/15 px-3 py-1.5 text-xs font-semibold text-white ring-1 ring-white/30 hover:bg-white/25">
                                        Open flyer →
                                    </a>
                                @endif
                            </div>

                            {{-- totals for this flyer --}}
                            <div class="mt-3 flex flex-wrap gap-2 text-xs">
                                <span class="rounded-full bg-white/15 px-3 py-1 font-medium text-blue-50 ring-1 ring-white/20">
                                    <strong class="font-bold text-white">{{ $group['campaigns']->count() }}</strong>
                                    {{ $group['campaigns']->count() === 1 ? 'campaign' : 'campaigns' }}
                                </span>

                                <span class="rounded-full bg-white/15 px-3 py-1 font-medium text-blue-50 ring-1 ring-white/20">
                                    <strong class="font-bold text-white">{{ number_format($group['emailsSent']) }}</strong>
                                    emails sent
                                </span>

                                <span class="rounded-full bg-white/15 px-3 py-1 font-medium text-blue-50 ring-1 ring-white/20">
                                    <strong class="font-bold text-white">{{ $group['hits'] === null ? '—' : number_format($group['hits']) }}</strong>
                                    flyer hits
                                </span>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- ITS CAMPAIGNS: each one its own card on a soft grey ground, with a
                     status-coloured stripe and a progress bar --}}
                <div class="space-y-3 bg-slate-100 p-3 sm:p-4">

                    @foreach($group['campaigns'] as $c)

                        @php
                            $st    = $statuses[$c['status']];
                            $steps = [
                                ['Requested', $c['requested']],
                                ['Started',   $c['started']],
                                ['Completed', $c['completed']],
                            ];
                        @endphp

                        <div class="rounded-xl border-l-4 bg-white p-4 shadow-sm ring-1 ring-slate-200 {{ $st['accent'] }}">

                            <div class="flex flex-wrap items-start justify-between gap-x-4 gap-y-2">

                                <div class="min-w-0 flex-1 basis-56">
                                    <div class="flex flex-wrap items-center gap-x-1 gap-y-1">
                                        <span class="break-words text-base font-bold text-slate-900">{{ $c['area'] }}</span>
                                        @include('admin.flyer.campSource', ['adminAdded' => $c['admin_added']])
                                    </div>

                                    <div class="mt-1 break-words text-sm text-slate-600">
                                        {{ $c['subject'] ?: 'No subject' }}
                                    </div>
                                </div>

                                <div class="flex shrink-0 flex-col items-start gap-1.5 sm:items-end">
                                    <span class="rounded-full px-3 py-1 text-xs font-bold {{ $st['pill'] }}">
                                        {{ $st['label'] }}
                                    </span>
                                    <span class="text-xs font-semibold text-slate-500">
                                        {{ $num($c['emails']) }} {{ $c['emails'] == 1 ? 'email' : 'emails' }}
                                    </span>
                                </div>

                            </div>

                            {{-- progress: Requested -> Started -> Completed. A step fills in (in the
                                 status colour) once it has happened; the rest stay grey. --}}
                            <div class="mt-4 grid grid-cols-3 gap-2 sm:gap-3">
                                @foreach($steps as [$stepLabel, $stepDate])
                                    <div>
                                        <div class="h-1.5 rounded-full {{ $stepDate ? $st['bar'] : 'bg-slate-200' }}"></div>

                                        <div class="mt-1.5 text-[11px] font-semibold uppercase tracking-wide {{ $stepDate ? 'text-slate-500' : 'text-slate-300' }}">
                                            {{ $stepLabel }}
                                        </div>

                                        <div class="text-xs font-semibold {{ $stepDate ? 'text-slate-800' : 'text-slate-300' }}">
                                            {{ $day($stepDate) ?? '—' }}
                                        </div>

                                        @if($stepDate)
                                            <div class="text-[11px] text-slate-400">{{ $at($stepDate) }}</div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                        </div>

                    @endforeach

                </div>

            </section>

        @empty

            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center text-sm text-slate-500">
                This agent has no campaigns.
            </div>

        @endforelse

    </div>

</div>
</main>

@include('public.layout.footer')

</body>
</html>
