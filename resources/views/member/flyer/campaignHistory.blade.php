{{-- Only reachable through memberController::flyerCampaigns, which supplies
     $flyer, $campaigns and $summary (this file is also reachable by URL through
     the /member/{segments} convention, where none of them exist). --}}
@php abort_unless(isset($flyer, $campaigns, $summary), 404); @endphp

@include('member.layout.head', ['pageTitle' => 'Campaign History | Realty Emails'])

<body data-section="member" class="relative min-h-screen bg-[#f0f2f7] font-sans text-slate-800">

@include('member.layout.nav')

@php
    $photo = $flyer->thePhotos->first();

    $img = ($photo && $flyer->theMeta)
        ? '/hqphotos/' . $flyer->theMeta->zipDir . '/' . $flyer->theMeta->mlsDir . '/' . $photo->photoName
        : null;

    $location = trim(
        ($flyer->xCity ?? '') . ' ' .
        ($flyer->state ?? '') . ' ' .
        ($flyer->xxZip ?? $flyer->xZip ?? '')
    );

    $views = optional($flyer->theStats)->xWebViews ?? 0;

    $statuses = [
        'completed'  => ['label' => 'Completed',                  'class' => 'bg-emerald-100 text-emerald-700'],
        'delivering' => ['label' => 'Delivering',                 'class' => 'bg-blue-100 text-blue-700'],
        // Whether or not an admin has approved it yet is not the agent's concern:
        // both just mean "in the queue".
        'approved'   => ['label' => 'Added to Queue',             'class' => 'bg-indigo-100 text-indigo-700'],
        'pending'    => ['label' => 'Added to Queue',             'class' => 'bg-indigo-100 text-indigo-700'],
    ];

    $day  = fn($d) => $d ? $d->format('M j, Y') : '—';
    $time = fn($d) => $d ? $d->format('M j, Y g:i A') : '—';
    $num  = fn($n) => is_numeric($n) ? number_format($n) : '—';
@endphp

<main class="min-h-screen bg-[#f0f2f7] pt-[88px]">

    <div class="mx-auto w-full max-w-5xl px-4 pb-10 sm:px-6 lg:px-8">

        {{-- HEADER --}}
        <div class="mb-3 flex flex-wrap items-baseline justify-between gap-x-4 gap-y-1">
            <h1 class="text-2xl font-black leading-tight text-slate-900">Campaign History</h1>

            <a href="/member/dashboard" class="text-sm font-bold text-[#123f91] hover:underline">
                ← Back to My Flyers
            </a>
        </div>

        {{-- FLYER --}}
        <div class="wz-card mb-3 flex items-center gap-3 p-3">

            <a href="/member/flyer/preview?flyerId={{ $flyer->id }}" class="shrink-0">
                @if($img)
                    <img src="{{ $img }}" alt="{{ $flyer->xFullStreet }}" class="h-16 w-16 rounded-lg object-cover">
                @else
                    <div class="flex h-16 w-16 items-center justify-center rounded-lg bg-slate-100 text-[10px] font-bold text-slate-400">
                        No Photo
                    </div>
                @endif
            </a>

            <div class="min-w-0 flex-1">
                <a href="/member/flyer/preview?flyerId={{ $flyer->id }}"
                   class="block break-words text-base font-black text-[#123f91] hover:underline">
                    {{ $flyer->xFullStreet ?: 'Untitled Flyer' }}
                </a>

                <div class="text-sm text-slate-500">{{ $location ?: 'Location unavailable' }}</div>

                <div class="mt-0.5 text-xs font-semibold text-slate-500">
                    {{ number_format($views) }} {{ $views === 1 ? 'view' : 'views' }}
                    @if($flyer->xListPrice)
                        &middot; ${{ number_format((float) $flyer->xListPrice) }}
                    @endif
                </div>
            </div>

        </div>

        {{-- SUMMARY --}}
        <div class="mb-3 grid grid-cols-2 gap-3 md:grid-cols-4">

            <div class="wz-card p-3">
                <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">Campaigns Sent</div>
                <div class="mt-1 text-2xl font-black text-slate-900">{{ number_format($summary['sent']) }}</div>
            </div>

            <div class="wz-card p-3">
                <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">Emails Delivered</div>
                <div class="mt-1 text-2xl font-black text-slate-900">{{ number_format($summary['emails']) }}</div>
            </div>

            <div class="wz-card p-3">
                <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">Last Sent</div>
                <div class="mt-1 text-lg font-black text-slate-900">{{ $day($summary['lastSent']) }}</div>
            </div>

            <div class="wz-card p-3">
                <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">In Queue</div>
                <div class="mt-1 text-2xl font-black text-slate-900">{{ number_format($summary['inQueue']) }}</div>
            </div>

        </div>

        {{-- HISTORY: one card per campaign. Nothing is truncated or forced onto one
             line, so text wraps and the details re-flow (2 columns on a phone, 4 on
             wider screens) instead of being cut off when space runs out. --}}
        @if($campaigns->isEmpty())

            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center text-sm text-slate-500">
                No campaigns have been requested for this flyer yet.
            </div>

        @else

            <div class="space-y-3">
                @foreach($campaigns as $c)
                    <div class="wz-card p-4">

                        <div class="flex flex-wrap items-start justify-between gap-x-4 gap-y-2">

                            <div class="min-w-0 flex-1 basis-56">
                                @if($c['slot'])
                                    <div class="text-[11px] font-bold uppercase tracking-wide text-slate-400">
                                        Area {{ $c['slot'] }}
                                    </div>
                                @endif

                                <div class="break-words text-base font-black text-slate-900">
                                    {{ $c['area'] }}
                                </div>

                                <div class="mt-0.5 break-words text-sm text-slate-600">
                                    {{ $c['subject'] ?: 'No subject' }}
                                </div>
                            </div>

                            <span class="shrink-0 rounded-full px-3 py-1 text-xs font-bold {{ $statuses[$c['status']]['class'] }}">
                                {{ $statuses[$c['status']]['label'] }}
                            </span>

                        </div>

                        <dl class="mt-3 grid grid-cols-2 gap-x-4 gap-y-3 border-t border-slate-100 pt-3 text-sm sm:grid-cols-4">

                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Requested</dt>
                                <dd class="font-semibold text-slate-700">{{ $time($c['requested']) }}</dd>
                            </div>

                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Emails</dt>
                                <dd class="font-semibold text-slate-700">{{ $num($c['emails']) }}</dd>
                            </div>

                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Started</dt>
                                <dd class="font-semibold text-slate-700">{{ $time($c['started']) }}</dd>
                            </div>

                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Completed</dt>
                                <dd class="font-semibold text-slate-700">{{ $time($c['completed']) }}</dd>
                            </div>

                        </dl>

                    </div>
                @endforeach
            </div>

        @endif

    </div>

</main>

@include('public.layout.footer')

</body>
</html>
