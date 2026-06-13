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

        if (!$photo || !$flyer->theMeta) {
            return null;
        }

        return 'https://realtyrepublic.com/hqphotos/'
            . $flyer->theMeta->zipDir . '/'
            . $flyer->theMeta->mlsDir . '/'
            . $photo->photoName;
    };

    $money = fn($v) => ($v === null || $v === '') ? 'Price N/A' : '$' . number_format((float)$v);

    $recentFlyers = $flyers
        ->sortByDesc(function ($flyer) use ($campaignsByFlyer) {
            $stats = $flyer->theStats;
            $flyerCampaigns = $campaignsByFlyer->get($flyer->id, collect());

            return optional($stats)->xLastDeliveryDate
                ?? optional($flyerCampaigns->sortByDesc('emComplete')->first())->emComplete
                ?? $flyer->created_at
                ?? $flyer->id;
        })
        ->take(5);
@endphp

<div class="min-h-screen bg-[#f0f2f7] lg:flex">

    {{-- LEFT SIDEBAR --}}
    <aside class="lg:sticky lg:top-0 lg:h-screen lg:w-[260px] shrink-0 bg-[#101827] text-white shadow-xl">
        <div class="flex h-full flex-col p-6">

            <div class="mb-10">
                <div class="text-xl font-black">
                    RealtyEmails<span class="text-blue-400">.com</span>
                </div>
                <div class="mt-1 text-sm text-slate-400">
                    Member Dashboard
                </div>
            </div>

            <nav class="space-y-2 text-sm font-bold">
                <a href="/member/create-flyer" class="block rounded-lg px-4 py-3 hover:bg-white/10">
                    Create New Flyer
                </a>

                <a href="/member/resend-flyer" class="block rounded-lg px-4 py-3 hover:bg-white/10">
                    Resend Flyer
                </a>

                <a href="/member/campaigns" class="block rounded-lg px-4 py-3 hover:bg-white/10">
                    Campaigns
                </a>

                <a href="/member/agent-info" class="block rounded-lg px-4 py-3 hover:bg-white/10">
                    Agent Info
                </a>

                <a href="/member/account" class="block rounded-lg px-4 py-3 hover:bg-white/10">
                    Account Info
                </a>
            </nav>

            <div class="mt-auto border-t border-white/10 pt-5">
                <div class="mb-4 text-sm text-slate-400">
                    Logged in as<br>
                    <span class="font-semibold text-white">
                        {{ $agent->agtFullName ?? 'Member' }}
                    </span>
                </div>

                <a href="/member/logout" class="block rounded-lg bg-red-600 px-4 py-3 text-center text-sm font-bold text-white hover:bg-red-700">
                    Log Out
                </a>
            </div>

        </div>
    </aside>

    {{-- RIGHT CONTENT --}}
    <main class="flex-1 p-6 lg:p-10">

        {{-- PAGE HEADER --}}
        <div class="mb-8">
            <h1 class="text-3xl font-black text-slate-900">
                Welcome Back
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                {{ $agent->agtFullName ?? 'Member' }}
            </p>
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
                <div class="space-y-4">
                    @foreach($recentFlyers as $flyer)
                        @php
                            $img = $photoUrl($flyer);
                            $stats = $flyer->theStats;
                            $flyerCampaigns = $campaignsByFlyer->get($flyer->id, collect());

                            $completedForFlyer = $flyerCampaigns->filter(fn($c) => !empty($c->emComplete));
                            $pendingForFlyer   = $flyerCampaigns->filter(fn($c) => empty($c->emStart) && empty($c->emComplete));

                            $emailCount = $completedForFlyer->sum('totalEmails');
                            $viewCount  = optional($stats)->xWebViews ?? 0;

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

                        <article class="flex items-center gap-5 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-black/5">

                            {{-- SMALL THUMBNAIL --}}
                            <div class="h-[95px] w-[140px] shrink-0 overflow-hidden rounded-xl bg-slate-200">
                                @if($img)
                                    <img src="{{ $img }}"
                                         alt="{{ $flyer->xFullStreet }}"
                                         class="h-full w-full object-cover">
                                @else
                                    <div class="flex h-full items-center justify-center text-xs font-bold text-slate-400">
                                        No Photo
                                    </div>
                                @endif
                            </div>

                            {{-- FLYER INFO --}}
                            <div class="min-w-0 flex-1">
                                <div class="truncate text-lg font-black text-[#123f91]">
                                    {{ $flyer->xFullStreet ?: 'Untitled Flyer' }}
                                </div>

                                <div class="mt-1 text-sm text-slate-500">
                                    {{ $location ?: 'Location unavailable' }}
                                </div>

                                <div class="mt-3 flex flex-wrap gap-2 text-xs font-bold">
                                    <span class="rounded-full bg-blue-50 px-3 py-1 text-blue-700">
                                        {{ $money($flyer->xListPrice) }}
                                    </span>

                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-600">
                                        Last Sent: {{ $lastSent }}
                                    </span>

                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-600">
                                        {{ number_format($emailCount) }} Sent
                                    </span>

                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-600">
                                        {{ number_format($viewCount) }} Views
                                    </span>

                                    @if($pendingForFlyer->count())
                                        <span class="rounded-full bg-amber-100 px-3 py-1 text-amber-700">
                                            Requested
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- ACTIONS --}}
                            <div class="flex shrink-0 gap-2">
                                @if($flyer->url_slug)
                                    <a href="/homedetails/{{ $flyer->url_slug }}"
                                       class="rounded-lg bg-[#123f91] px-4 py-2 text-xs font-bold text-white hover:bg-[#0f3274]">
                                        View
                                    </a>
                                @endif

                                <a href="/member/campaigns/{{ $flyer->id }}"
                                   class="rounded-lg border border-slate-200 px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50">
                                    Campaigns
                                </a>

                                <a href="/member/send-campaign/{{ $flyer->id }}"
                                   class="rounded-lg bg-emerald-50 px-4 py-2 text-xs font-bold text-emerald-700 ring-1 ring-emerald-200 hover:bg-emerald-100">
                                    Resend
                                </a>
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