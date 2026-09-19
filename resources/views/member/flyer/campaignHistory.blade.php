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
        'approved'   => ['label' => 'Approved, waiting to send',  'class' => 'bg-indigo-100 text-indigo-700'],
        'pending'    => ['label' => 'Awaiting approval',          'class' => 'bg-amber-100 text-amber-700'],
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
                   class="block truncate text-base font-black text-[#123f91] hover:underline">
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

        {{-- HISTORY --}}
        @if($campaigns->isEmpty())

            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center text-sm text-slate-500">
                No campaigns have been requested for this flyer yet.
            </div>

        @else

            {{-- wide screens: table --}}
            <div class="wz-card hidden overflow-hidden p-0 md:block">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr class="text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <th class="px-4 py-3">Requested</th>
                            <th class="px-4 py-3">Area</th>
                            <th class="px-4 py-3">Subject</th>
                            <th class="px-4 py-3 text-right">Emails</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Completed</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($campaigns as $c)
                            <tr>
                                <td class="whitespace-nowrap px-4 py-3 text-slate-600">{{ $day($c['requested']) }}</td>
                                <td class="px-4 py-3 font-semibold text-slate-900">{{ $c['area'] }}</td>
                                <td class="max-w-xs truncate px-4 py-3 text-slate-600" title="{{ $c['subject'] }}">{{ $c['subject'] ?: '—' }}</td>
                                <td class="px-4 py-3 text-right text-slate-600">{{ $num($c['emails']) }}</td>
                                <td class="whitespace-nowrap px-4 py-3">
                                    <span class="rounded-full px-3 py-1 text-xs font-bold {{ $statuses[$c['status']]['class'] }}">
                                        {{ $statuses[$c['status']]['label'] }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-slate-600">{{ $time($c['completed']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- narrow screens: one card per campaign --}}
            <div class="space-y-3 md:hidden">
                @foreach($campaigns as $c)
                    <div class="wz-card p-3">

                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="font-black text-slate-900">{{ $c['area'] }}</div>
                                <div class="text-xs text-slate-500">Requested {{ $day($c['requested']) }}</div>
                            </div>

                            <span class="shrink-0 rounded-full px-3 py-1 text-xs font-bold {{ $statuses[$c['status']]['class'] }}">
                                {{ $statuses[$c['status']]['label'] }}
                            </span>
                        </div>

                        @if($c['subject'])
                            <div class="mt-2 text-sm text-slate-600">{{ $c['subject'] }}</div>
                        @endif

                        <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-xs font-semibold text-slate-500">
                            <span>{{ $num($c['emails']) }} emails</span>
                            @if($c['completed'])
                                <span>Completed {{ $time($c['completed']) }}</span>
                            @endif
                        </div>

                    </div>
                @endforeach
            </div>

        @endif

    </div>

</main>

@include('public.layout.footer')

</body>
</html>
