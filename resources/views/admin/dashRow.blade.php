{{-- One flyer's campaign request on the admin dashboard, as a compact card that opens
     to show the areas requested. Only reachable through admin/dashboard (needs $group,
     $stage and $tone; this file is also reachable by URL through the /admin/{segments}
     convention, where none of them exist).
     $group: one entry built in app/admin/dashboard.php.
     $stage: 'waiting' | 'progress' | 'completed' (which dates show, and what each area's status is).
     $tone:  'amber' | 'indigo' | 'blue' | 'emerald' (the stripe down the card's left edge).
     The card's layout is the .dash-card CSS at the top of admin/dashboard.blade.php. --}}
@php abort_unless(isset($group, $stage, $tone), 404); @endphp

@php
    $accent = [
        'amber'   => 'border-l-amber-400',
        'indigo'  => 'border-l-indigo-500',
        'blue'    => 'border-l-blue-500',
        'emerald' => 'border-l-emerald-500',
    ][$tone];

    $stamp = fn ($d) => $d ? $d->format('M j, Y g:i A') : '—';

    // "3 hours ago"; a time that is (just) ahead of the server clock reads "just now"
    $ago = fn ($d) => $d ? ($d->isFuture() ? 'just now' : $d->diffForHumans()) : null;

    $areaCount = $group['areas']->count();
    $chip      = 'whitespace-nowrap rounded-full px-2 py-0.5 text-[11px] font-semibold';
    $quiet     = $chip . ' bg-slate-100 text-slate-700';
@endphp

<div class="rounded-xl border-l-[6px] bg-white shadow-sm ring-1 ring-slate-200 {{ $accent }}" data-dash-card>
    <div>

        <div>
            <div class="dash-card">

                {{-- THUMBNAIL: opens the campaign review (the flyer's campaign page) --}}
                <a @unless($group['deleted']) href="/admin/flyerCamps/{{ $group['flyer_id'] }}" title="Open campaign review" @endunless
                   class="dc-thumb flex items-center justify-center overflow-hidden rounded-lg bg-slate-100 ring-1 ring-slate-200 hover:ring-2 hover:ring-[#214e9b]">
                    @if($group['thumb'])
                        <img src="{{ $group['thumb'] }}" alt="" loading="lazy" class="h-full w-full object-cover">
                    @else
                        <svg class="h-7 w-7 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 9.75L12 3l9 6.75V21a.75.75 0 01-.75.75H3.75A.75.75 0 013 21V9.75z"/>
                        </svg>
                    @endif
                </a>

                {{-- ADDRESS / AGENT + FACTS / SUBJECT --}}
                <div class="dc-main">

                    <div class="dc-line">
                        @if($group['deleted'])
                            <span class="text-base font-bold text-slate-900">{{ $group['address'] }}</span>
                            <span class="{{ $chip }} bg-red-100 text-red-700">Flyer deleted</span>
                        @else
                            <a href="/admin/flyerCamps/{{ $group['flyer_id'] }}"
                               class="text-base font-bold text-[#214e9b] hover:underline">{{ $group['address'] }}</a>
                        @endif

                        @if($group['place'] !== '')
                            <span class="text-xs text-slate-500">{{ $group['place'] }}</span>
                        @endif
                    </div>

                    <div class="dc-line mt-0.5 text-xs text-slate-600">
                        @if($group['agent_id'])
                            <a href="/admin/agentView/{{ $group['agent_id'] }}" class="font-semibold text-slate-800 hover:underline">{{ $group['agent_name'] }}</a>
                        @else
                            <span class="font-semibold text-slate-800">{{ $group['agent_name'] }}</span>
                        @endif

                        @if($group['credits'] !== null)
                            <span class="text-slate-300">&middot;</span>
                            <span>{{ number_format($group['credits']) }} {{ $group['credits'] === 1 ? 'credit' : 'credits' }}</span>
                        @endif

                        <span class="text-slate-300">&middot;</span>
                        <span>{{ $group['last_sent'] ? 'Last sent ' . $group['last_sent']->format('M j, Y') : 'Never sent' }}</span>

                        @if($group['open_house'])
                            <span class="{{ $quiet }}">Open house {{ $group['open_house']->format('M j') }}</span>
                        @endif
                        @if($group['reduced'])
                            <span class="{{ $quiet }}">Reduced ${{ number_format($group['reduced']) }}</span>
                        @endif
                    </div>

                    <div class="dc-subject mt-1 text-sm text-slate-700" title="{{ $group['subject'] }}">
                        <span class="font-semibold text-slate-900">Subject:</span>
                        @if($group['subject'])
                            {{ $group['subject'] }}@if($group['subjects_vary']) <span class="text-xs text-slate-500">(varies by area)</span>@endif
                        @else
                            <span class="text-slate-400">none</span>
                        @endif
                    </div>

                </div>

                {{-- DATES: label + value pairs, so they line up whatever the stage --}}
                <div class="dc-dates text-xs">
                    <div class="dc-pair">
                        <span class="dc-label">Requested</span>
                        <span class="font-semibold text-slate-900">{{ $stamp($group['requested']) }}</span>
                    </div>

                    @if($stage === 'progress' || $stage === 'completed')
                        <div class="dc-pair">
                            <span class="dc-label">Started</span>
                            <span class="font-semibold text-slate-900">{{ $stamp($group['started']) }}</span>
                        </div>
                    @endif

                    @if($stage === 'completed')
                        <div class="dc-pair">
                            <span class="dc-label">Completed</span>
                            <span class="font-semibold text-slate-900">{{ $stamp($group['completed']) }}</span>
                        </div>
                    @endif

                    @if($stage === 'waiting' && $group['requested'])
                        <div class="text-slate-500">{{ $ago($group['requested']) }}</div>
                    @elseif($stage === 'progress' && $group['started'])
                        <div class="text-slate-500">started {{ $ago($group['started']) }}</div>
                    @endif
                </div>

                {{-- THE AREAS DROPDOWN BUTTON: the only thing on the card that opens the areas
                     (script at the bottom of admin/dashboard.blade.php) --}}
                <div class="dc-toggle">
                    <button type="button" data-areas-toggle aria-expanded="false"
                            class="inline-flex cursor-pointer items-center gap-1.5 whitespace-nowrap rounded-lg border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-[#214e9b] hover:bg-slate-50">
                        {{ $areaCount }} {{ $areaCount === 1 ? 'area' : 'areas' }}
                        @if($group['contacts'] > 0)
                            <span class="font-medium text-slate-500">&middot; {{ number_format($group['contacts']) }} contacts</span>
                        @endif
                        <svg class="h-3 w-3 transition-transform" data-areas-chevron viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M2 4l4 4 4-4"/></svg>
                    </button>
                </div>

            </div>
        </div>

        {{-- THE AREAS REQUESTED --}}
        <div class="border-t border-slate-200 bg-slate-50 px-3 py-3 sm:px-4" data-areas-panel hidden>

            <div class="divide-y divide-slate-200 rounded-lg bg-white ring-1 ring-slate-200">
                @foreach($group['areas'] as $area)
                    <div class="flex flex-wrap items-center justify-between gap-x-4 gap-y-1 px-3 py-2 text-sm">

                        <div class="flex min-w-0 flex-wrap items-center gap-x-2 gap-y-1">
                            <span class="text-xs font-semibold tabular-nums text-slate-500" title="Campaign ID">#{{ $area['cid'] }}</span>
                            <span class="font-semibold text-slate-900">{{ $area['area'] }}</span>
                            @include('admin.flyer.campSource', ['adminAdded' => $area['admin_added']])
                            @if($area['contacts'] !== null)
                                <span class="text-xs text-slate-500">{{ number_format($area['contacts']) }} contacts</span>
                            @endif
                        </div>

                        <div class="text-xs">
                            @if($stage === 'waiting')
                                @if($area['authorized'])
                                    <span class="rounded-full bg-indigo-100 px-2.5 py-0.5 font-semibold text-indigo-700">Authorized</span>
                                @else
                                    <span class="rounded-full bg-amber-100 px-2.5 py-0.5 font-semibold text-amber-800">Not authorized</span>
                                @endif
                            @elseif($stage === 'progress')
                                <span class="text-slate-500">Started</span>
                                <span class="font-semibold text-slate-800">{{ $stamp($area['started']) }}</span>
                            @else
                                <span class="text-slate-500">Completed</span>
                                <span class="font-semibold text-slate-800">{{ $stamp($area['completed']) }}</span>
                            @endif
                        </div>

                    </div>
                @endforeach
            </div>

            @unless($group['deleted'])
                <div class="mt-2 text-right">
                    <a href="/admin/flyerCamps/{{ $group['flyer_id'] }}" class="text-xs font-semibold text-[#214e9b] hover:underline">
                        Open flyer &amp; manage campaigns →
                    </a>
                </div>
            @endunless

        </div>

    </div>
</div>
