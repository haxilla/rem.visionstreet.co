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

    $statuses = [
        'completed'  => ['label' => 'Completed',                 'class' => 'bg-emerald-100 text-emerald-700'],
        'delivering' => ['label' => 'In Progress',               'class' => 'bg-blue-100 text-blue-700'],
        'approved'   => ['label' => 'Approved, waiting to send', 'class' => 'bg-indigo-100 text-indigo-700'],
        'pending'    => ['label' => 'Awaiting approval',         'class' => 'bg-amber-100 text-amber-700'],
    ];

    $time = fn ($d) => $d ? $d->format('M j, Y g:i A') : '—';
    $num  = fn ($n) => is_numeric($n) ? number_format($n) : '—';

    $card = 'rounded-[24px] bg-white shadow-[0_12px_35px_rgba(15,23,42,0.06)]';
@endphp

<main class="min-h-screen bg-[#f4f7fb] pt-24">
<div class="mx-auto max-w-[1100px] px-4 py-6 sm:px-6 lg:px-10 lg:py-8">

    {{-- HEADER --}}
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

    {{-- FLYERS, EACH WITH ITS CAMPAIGNS UNDERNEATH --}}
    <div class="mt-6 space-y-6">

        @forelse($groups as $group)

            <section class="{{ $card }} overflow-hidden">

                {{-- flyer address --}}
                <div class="flex flex-wrap items-start justify-between gap-3 border-b border-slate-100 bg-slate-50 px-5 py-4 sm:px-6">
                    <div class="min-w-0">
                        <h2 class="break-words text-base font-semibold text-slate-900 sm:text-lg">
                            {{ $group['title'] }}
                        </h2>

                        @if($group['place'] !== '')
                            <p class="text-sm text-slate-500">{{ $group['place'] }}</p>
                        @endif
                    </div>

                    <div class="flex flex-wrap items-center gap-2 text-xs font-semibold">
                        <span class="rounded-full bg-white px-3 py-1 text-slate-600 ring-1 ring-slate-200">
                            {{ $group['campaigns']->count() }} {{ $group['campaigns']->count() === 1 ? 'campaign' : 'campaigns' }}
                        </span>

                        @if($group['deleted'])
                            <span class="rounded-full bg-red-50 px-3 py-1 text-red-700 ring-1 ring-red-200">Flyer deleted</span>
                        @else
                            <a href="/admin/flyerCamps/{{ $group['flyerId'] }}" class="text-[#214e9b] hover:underline">
                                Open flyer →
                            </a>
                        @endif
                    </div>
                </div>

                {{-- this flyer's campaigns --}}
                <div class="divide-y divide-slate-100">
                    @foreach($group['campaigns'] as $c)
                        <div class="px-5 py-4 sm:px-6">

                            <div class="flex flex-wrap items-start justify-between gap-x-4 gap-y-2">
                                <div class="min-w-0 flex-1 basis-56">
                                    <div class="break-words text-sm font-semibold text-slate-900">
                                        {{ $c['area'] }}
                                        @include('admin.flyer.campSource', ['adminAdded' => $c['admin_added']])
                                    </div>

                                    <div class="mt-0.5 break-words text-sm text-slate-600">
                                        {{ $c['subject'] ?: 'No subject' }}
                                    </div>
                                </div>

                                <span class="shrink-0 rounded-full px-3 py-1 text-xs font-bold {{ $statuses[$c['status']]['class'] }}">
                                    {{ $statuses[$c['status']]['label'] }}
                                </span>
                            </div>

                            <dl class="mt-3 grid grid-cols-2 gap-x-4 gap-y-3 text-sm sm:grid-cols-4">
                                <div>
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Requested</dt>
                                    <dd class="font-medium text-slate-700">{{ $time($c['requested']) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Started</dt>
                                    <dd class="font-medium text-slate-700">{{ $time($c['started']) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Completed</dt>
                                    <dd class="font-medium text-slate-700">{{ $time($c['completed']) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Emails</dt>
                                    <dd class="font-medium text-slate-700">{{ $num($c['emails']) }}</dd>
                                </div>
                            </dl>

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
