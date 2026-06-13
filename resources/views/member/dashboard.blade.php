@include('public.layout.head')

<body data-section="member" class="relative min-h-screen bg-[#f0f2f7] font-sans text-slate-800">

@php
    use Illuminate\Support\Carbon;

    $flyers     = $data['propflyers'] ?? collect();
    $campaigns  = $data['propdelivs'] ?? collect();

    $agent            = optional($flyers->first())->theAgent;
    $campaignsByFlyer = $campaigns->groupBy('propflyer_id');

    $photoUrl = function ($flyer) {
        $photo = optional($flyer->thePhotos)->first();
        if (!$photo || !$flyer->theMeta) return null;

        return 'https://realtyrepublic.com/hqphotos/'
            . $flyer->theMeta->zipDir . '/'
            . $flyer->theMeta->mlsDir . '/'
            . $photo->photoName;
    };

    $money = fn($v) => ($v === null || $v === '') ? 'Price N/A' : '$' . number_format((float)$v);

    $recentFlyers = $flyers
        ->sortByDesc(function ($flyer) use ($campaignsByFlyer) {
            $stats = $flyer->theStats;
            $campaignsForFlyer = $campaignsByFlyer->get($flyer->id, collect());

            return optional($stats)->xLastDeliveryDate
                ?? optional($campaignsForFlyer->sortByDesc('emComplete')->first())->emComplete
                ?? $flyer->created_at
                ?? $flyer->id;
        })
        ->take(5);
@endphp

<div class="min-h-screen lg:flex">

    {{-- LEFT SIDEBAR --}}
    <aside class="lg:sticky lg:top-0 lg:h-screen lg:w-72 bg-[#111827] text-white shadow-xl">
        <div class="flex h-full flex-col p-6">

            <div class="mb-8">
                <div class="text-2xl font-black">
                    RealtyEmails<span class="text-blue-400">.com</span>
                </div>
                <div class="mt-1 text-sm text-slate-400">
                    Member Dashboard
                </div>
            </div>

            <nav class="space-y-2 text-sm font-semibold">
                <a href="/member/create-flyer" class="block rounded-xl px-4 py-3 hover:bg-white/10">
                    Create New Flyer
                </a>

                <a href="/member/resend-flyer" class="block rounded-xl px-4 py-3 hover:bg-white/10">
                    Resend Flyer
                </a>

                <a href="/member/campaigns" class="block rounded-xl px-4 py-3 hover:bg-white/10">
                    Campaigns
                </a>

                <a href="/member/agent-info" class="block rounded-xl px-4 py-3 hover:bg-white/10">
                    Agent Info
                </a>

                <a href="/member/account" class="block rounded-xl px-4 py-3 hover:bg-white/10">
                    Account Info
                </a>
            </nav>

            <div class="mt-auto border-t border-white/10 pt-5">
                <div class="mb-4 text-sm text-slate-400">
                    Logged in as<br>
                    <span class="font-semibold text-white">{{ $agent->agtFullName ?? 'Member' }}</span>
                </div>

                <a href="/member/logout" class="block rounded-xl bg-red-600 px-4 py-3 text-center text-sm font-bold text-white hover:bg-red-700">
                    Log Out
                </a>
            </div>

        </div>
    </aside>

    {{-- RIGHT CONTENT --}}
    <main class="flex-1 px-6 py-8 lg:px-10">

        {{-- HEADER --}}
        <div class="mb-8 rounded-3xl bg-white p-7 shadow-sm ring-1 ring-black/5">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h1 class="text-3xl font-black text-slate-900">
                        Welcome Back
                    </h1>
                    <p class="mt-1 text-slate-500">
                        {{ $agent->agtFullName ?? 'Member' }}
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                    <div class="rounded-2xl bg-slate-50 px-5 py-4 text-center">
                        <div class="text-xs font-bold uppercase text-slate-400">Flyers</div>
                        <div class="text-2xl font-black text-slate-900">{{ $flyers->count() }}</div>
                    </div>

                    <div class="rounded-2xl bg-slate-50 px-5 py-4 text-center">
                        <div class="text-xs font-bold uppercase text-slate-400">Campaigns</div>
                        <div class="text-2xl font-black text-slate-900">{{ $campaigns->count() }}</div>
                    </div>

                    <div class="rounded-2xl bg-slate-50 px-5 py-4 text-center">
                        <div class="text-xs font-bold uppercase text-slate-400">Sent</div>
                        <div class="text-2xl font-black text-slate-900">
                            {{ $campaigns->filter(fn($c) => !empty($c->emComplete))->count() }}
                        </div>
                    </div>

                    <div class="rounded-2xl bg-slate-50 px-5 py-4 text-center">
                        <div class="text-xs font-bold uppercase text-slate-400">Pending</div>
                        <div class="text-2xl font-black text-slate-900">
                            {{ $campaigns->filter(fn($c) => empty($c->emStart) && empty($c->emComplete))->count() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- RECENT FLYERS --}}
        <section>
            <div class="mb-5">
                <h2 class="text-2xl font-black text-slate-900">
                    5 Most Recent Flyers
                </h2>
                <p class="text-sm text-slate-500">
                    Recent flyer activity, delivery stats, and quick actions.
                </p>
            </div>

            @if($recentFlyers->isEmpty())
                <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center text-slate-500">
                    No flyers found.
                </div>
            @else
                <div class="space-y-5">
                    @foreach($recentFlyers as $flyer)
                        @php
                            $img = $photoUrl($flyer);
                            $stats = $flyer->theStats;
                            $flyerCampaigns = $campaignsByFlyer->get($flyer->id, collect());

                            $completedForFlyer = $flyerCampaigns->filter(fn($c) => !empty($c->emComplete));
                            $pendingForFlyer = $flyerCampaigns->filter(fn($c) => empty($c->emStart) && empty($c->emComplete));

                            $emailCount = $completedForFlyer->sum('totalEmails');
                            $viewCount = optional($stats)->xWebViews ?? 0;

                            $lastSentRaw = optional($stats)->xLastDeliveryDate
                                ?? optional($completedForFlyer->sortByDesc('emComplete')->first())->emComplete;

                            $lastSent = $lastSentRaw
                                ? Carbon::parse($lastSentRaw)->format('M j, Y')
                                : 'Not sent yet';

                            $location = trim(
                                ($flyer->xCity ?? '') . ' ' .
                                ($flyer->state ?? $flyer->xState ?? '') . ' ' .
                                ($flyer->xxZip ?? $flyer->xZip ?? '')
                            );
                        @endphp

                        <article class="overflow-hidden rounded-3xl bg-white p-5 shadow-sm ring-1 ring-black/5">
                            <div class="flex flex-col gap-5 md:flex-row">

                                {{-- THUMBNAIL --}}
                                <div class="h-40 w-full shrink-0 overflow-hidden rounded-2xl bg-slate-200 md:w-56">
                                    @if($img)
                                        <img src="{{ $img }}"
                                             alt="{{ $flyer->xFullStreet }}"
                                             class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full items-center justify-center text-sm font-semibold text-slate-400">
                                            No Photo
                                        </div>
                                    @endif
                                </div>

                                {{-- INFO --}}
                                <div class="flex flex-1 flex-col justify-between">
                                    <div>
                                        <div class="mb-2 flex flex-wrap items-center gap-2">
                                            <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700">
                                                {{ $money($flyer->xListPrice) }}
                                            </span>

                                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">
                                                Last Sent: {{ $lastSent }}
                                            </span>

                                            @if($pendingForFlyer->count())
                                                <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-700">
                                                    Requested
                                                </span>
                                            @endif
                                        </div>

                                        <h3 class="text-xl font-black text-[#123f91]">
                                            {{ $flyer->xFullStreet ?: 'Untitled Flyer' }}
                                        </h3>

                                        <div class="mt-1 text-sm text-slate-500">
                                            {{ $location ?: 'Location unavailable' }}
                                        </div>

                                        <div class="mt-4 grid grid-cols-2 gap-3 text-sm sm:grid-cols-4">
                                            <div class="rounded-xl bg-slate-50 p-3">
                                                <div class="text-xs font-bold uppercase text-slate-400">Emails Sent</div>
                                                <div class="text-lg font-black text-slate-800">{{ number_format($emailCount) }}</div>
                                            </div>

                                            <div class="rounded-xl bg-slate-50 p-3">
                                                <div class="text-xs font-bold uppercase text-slate-400">Views</div>
                                                <div class="text-lg font-black text-slate-800">{{ number_format($viewCount) }}</div>
                                            </div>

                                            <div class="rounded-xl bg-slate-50 p-3">
                                                <div class="text-xs font-bold uppercase text-slate-400">Campaigns</div>
                                                <div class="text-lg font-black text-slate-800">{{ $flyerCampaigns->count() }}</div>
                                            </div>

                                            <div class="rounded-xl bg-slate-50 p-3">
                                                <div class="text-xs font-bold uppercase text-slate-400">Pending</div>
                                                <div class="text-lg font-black text-slate-800">{{ $pendingForFlyer->count() }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- ACTIONS --}}
                                    <div class="mt-5 flex flex-wrap gap-2">
                                        @if($flyer->url_slug)
                                            <a href="/homedetails/{{ $flyer->url_slug }}"
                                               class="rounded-xl bg-[#123f91] px-4 py-2 text-sm font-bold text-white hover:bg-[#0f3274]">
                                                View Flyer
                                            </a>
                                        @endif

                                        <a href="/member/campaigns/{{ $flyer->id }}"
                                           class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-bold text-slate-700 hover:bg-slate-50">
                                            Campaigns
                                        </a>

                                        <a href="/member/send-campaign/{{ $flyer->id }}"
                                           class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-bold text-emerald-700 hover:bg-emerald-100">
                                            Resend
                                        </a>
                                    </div>
                                </div>

                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>

    </main>
</div>

@include('public.layout.footer')

</body>
</html>