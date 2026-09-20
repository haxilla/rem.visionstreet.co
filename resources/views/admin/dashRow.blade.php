{{-- One flyer's campaign request on the admin dashboard, as a card that opens to show
     the areas requested. Only reachable through admin/dashboard (needs $group and
     $tone; this file is also reachable by URL through the /admin/{segments} convention,
     where neither exists).
     $group: one entry built in app/admin/dashboard.php.
     $stage: 'waiting' | 'progress' | 'completed' (what the areas' right-hand status shows).
     $tone:  'amber' | 'indigo' | 'blue' | 'emerald' (the stripe down the card's left edge). --}}
@php abort_unless(isset($group, $stage, $tone), 404); @endphp

@php
    $accent = [
        'amber'   => 'border-l-amber-400',
        'indigo'  => 'border-l-indigo-500',
        'blue'    => 'border-l-blue-500',
        'emerald' => 'border-l-emerald-500',
    ][$tone];

    $day = fn ($d) => $d ? $d->format('M j, Y') : null;
    $at  = fn ($d) => $d ? $d->format('g:i A') : null;

    // "3 hours ago"; a time that is (just) ahead of the server clock reads "just now"
    $ago = fn ($d) => $d ? ($d->isFuture() ? 'just now' : $d->diffForHumans()) : null;

    $areaCount = $group['areas']->count();
    $chip      = 'rounded-full px-2 py-0.5 text-[11px] font-semibold';
@endphp

<div class="rounded-xl border-l-[6px] bg-white shadow-sm ring-1 ring-slate-200 {{ $accent }}">
    <details class="group">

        <summary class="flex cursor-pointer list-none flex-wrap items-start gap-x-4 gap-y-3 p-3 sm:p-4 [&::-webkit-details-marker]:hidden">

            {{-- THUMBNAIL --}}
            <div class="flex h-16 w-20 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-slate-100 ring-1 ring-slate-200 sm:h-[72px] sm:w-24">
                @if($group['thumb'])
                    <img src="{{ $group['thumb'] }}" alt="" loading="lazy" class="h-full w-full object-cover">
                @else
                    <svg class="h-7 w-7 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 9.75L12 3l9 6.75V21a.75.75 0 01-.75.75H3.75A.75.75 0 013 21V9.75z"/>
                    </svg>
                @endif
            </div>

            {{-- ADDRESS, AGENT, SUBJECT, CHIPS --}}
            <div class="min-w-0 flex-1 basis-64">

                <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                    @if($group['deleted'])
                        <span class="break-words text-base font-bold text-slate-900">{{ $group['address'] }}</span>
                        <span class="{{ $chip }} bg-red-100 text-red-700">Flyer deleted</span>
                    @else
                        <a href="/admin/flyerCamps/{{ $group['flyer_id'] }}"
                           class="break-words text-base font-bold text-[#214e9b] hover:underline">{{ $group['address'] }}</a>
                    @endif
                </div>

                <div class="mt-0.5 text-xs text-slate-600">
                    @if($group['place'] !== '')
                        {{ $group['place'] }}<span class="mx-1.5 text-slate-300">&middot;</span>
                    @endif

                    @if($group['agent_id'])
                        <a href="/admin/agentView/{{ $group['agent_id'] }}" class="font-semibold text-slate-800 hover:underline">{{ $group['agent_name'] }}</a>
                    @else
                        <span class="font-semibold text-slate-800">{{ $group['agent_name'] }}</span>
                    @endif

                    @if($group['credits'] !== null)
                        <span class="mx-1.5 text-slate-300">&middot;</span>{{ number_format($group['credits']) }} {{ $group['credits'] === 1 ? 'credit' : 'credits' }}
                    @endif
                </div>

                <div class="mt-1 break-words text-sm text-slate-700">
                    <span class="font-semibold text-slate-900">Subject:</span>
                    @if($group['subject'])
                        {{ $group['subject'] }}
                        @if($group['subjects_vary'])
                            <span class="text-xs text-slate-500">(varies by area)</span>
                        @endif
                    @else
                        <span class="text-slate-400">none</span>
                    @endif
                </div>

                <div class="mt-2 flex flex-wrap items-center gap-1.5">
                    @if($group['admin_added'])
                        <span class="{{ $chip }} bg-violet-100 text-violet-700">Admin added &middot; Free</span>
                    @endif

                    @if($group['open_house'])
                        <span class="{{ $chip }} bg-teal-100 text-teal-700">Open house {{ $group['open_house']->format('M j') }}</span>
                    @endif

                    @if($group['bonus'])
                        <span class="{{ $chip }} bg-yellow-100 text-yellow-800">Agent bonus</span>
                    @endif

                    @if($group['reduced'])
                        <span class="{{ $chip }} bg-orange-100 text-orange-700">Price reduced</span>
                    @endif

                    <span class="text-[11px] text-slate-500">
                        {{ $group['last_sent'] ? 'Last sent ' . $day($group['last_sent']) : 'Never sent' }}
                    </span>
                </div>

            </div>

            {{-- DATES + THE AREAS DROPDOWN --}}
            <div class="flex w-full shrink-0 flex-col gap-2 sm:w-auto sm:items-end sm:text-right">

                <div class="text-sm">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Requested</span>
                        <span class="font-semibold text-slate-900">{{ $group['requested'] ? $day($group['requested']) . ' ' . $at($group['requested']) : '—' }}</span>
                    </div>

                    @if($stage === 'waiting')
                        <div class="text-xs text-slate-500">{{ $ago($group['requested']) }}</div>
                    @endif

                    @if($stage === 'progress' || $stage === 'completed')
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Started</span>
                            <span class="font-semibold text-slate-900">{{ $group['started'] ? $day($group['started']) . ' ' . $at($group['started']) : '—' }}</span>
                        </div>
                    @endif

                    @if($stage === 'progress')
                        <div class="text-xs text-slate-500">{{ $ago($group['started']) }}</div>
                    @endif

                    @if($stage === 'completed')
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Completed</span>
                            <span class="font-semibold text-slate-900">{{ $group['completed'] ? $day($group['completed']) . ' ' . $at($group['completed']) : '—' }}</span>
                        </div>
                    @endif
                </div>

                <span class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-[#214e9b] hover:bg-slate-50">
                    {{ $areaCount }} {{ $areaCount === 1 ? 'area' : 'areas' }}
                    @if($group['contacts'] > 0)
                        <span class="font-medium text-slate-500">&middot; {{ number_format($group['contacts']) }} contacts</span>
                    @endif
                    <svg class="h-3 w-3 transition-transform group-open:rotate-180" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M2 4l4 4 4-4"/></svg>
                </span>

            </div>

        </summary>

        {{-- THE AREAS REQUESTED --}}
        <div class="border-t border-slate-200 bg-slate-50 px-3 py-3 sm:px-4">

            <div class="divide-y divide-slate-200 rounded-lg bg-white ring-1 ring-slate-200">
                @foreach($group['areas'] as $area)
                    <div class="flex flex-wrap items-center justify-between gap-x-4 gap-y-1 px-3 py-2 text-sm">

                        <div class="flex min-w-0 flex-wrap items-center gap-x-2 gap-y-1">
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
                                <span class="font-semibold text-slate-800">{{ $day($area['started']) }} {{ $at($area['started']) }}</span>
                            @else
                                <span class="text-slate-500">Completed</span>
                                <span class="font-semibold text-slate-800">{{ $day($area['completed']) }} {{ $at($area['completed']) }}</span>
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

    </details>
</div>
