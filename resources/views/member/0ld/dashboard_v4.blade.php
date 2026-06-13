@include('public.layout.head')

<body data-section="member" class="relative min-h-screen bg-[#f0f2f7] font-sans text-slate-800">

@include('member.layout.nav')

@php
    use Illuminate\Support\Carbon;

    $flyers     = collect($data['propflyers'] ?? []);
    $campaigns  = collect($data['propdelivs'] ?? []);

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

    $flyersWithStatus = $flyers->map(function ($flyer) use ($campaignsByFlyer) {
        $stats = $flyer->theStats;
        $flyerCampaigns = $campaignsByFlyer->get($flyer->id, collect());

        $completedForFlyer = $flyerCampaigns->filter(fn($c) => !empty($c->emComplete));

        $lastSentRaw = optional($stats)->xLastDeliveryDate
            ?? optional($completedForFlyer->sortByDesc('emComplete')->first())->emComplete;

        $flyer->dashboard_last_sent_raw = $lastSentRaw;
        $flyer->dashboard_is_sent = !empty($lastSentRaw);

        return $flyer;
    });

    $unsentFlyers = $flyersWithStatus
        ->filter(fn($flyer) => !$flyer->dashboard_is_sent)
        ->sortByDesc(fn($flyer) => $flyer->created_at ?? $flyer->id);

    $recentFlyers = $flyersWithStatus
        ->filter(fn($flyer) => $flyer->dashboard_is_sent)
        ->sortByDesc(fn($flyer) => $flyer->dashboard_last_sent_raw)
        ->take(5);

    $renderFlyerCard = function ($flyer) use ($photoUrl, $money, $campaignsByFlyer) {
        $img = $photoUrl($flyer);
        $stats = $flyer->theStats;
        $flyerCampaigns = $campaignsByFlyer->get($flyer->id, collect());

        $completedForFlyer = $flyerCampaigns->filter(fn($c) => !empty($c->emComplete));
        $pendingForFlyer   = $flyerCampaigns->filter(fn($c) => empty($c->emStart) && empty($c->emComplete));

        $emailCount = $completedForFlyer->sum('totalEmails');
        $viewCount  = optional($stats)->xWebViews ?? 0;

        $lastSentRaw = $flyer->dashboard_last_sent_raw ?? null;

        $lastSent = $lastSentRaw
            ? Carbon::parse($lastSentRaw)->format('M j, Y')
            : null;

        $location = trim(
            ($flyer->xCity ?? '') . ' ' .
            ($flyer->state ?? $flyer->xState ?? '') . ' ' .
            ($flyer->xxZip ?? $flyer->xZip ?? '')
        );

        ob_start();
@endphp

        <article class="flex items-center gap-5 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-black/5">

            {{-- THUMBNAIL --}}
            <div style="width:120px;height:82px;flex:0 0 120px;overflow:hidden;border-radius:12px;background:#e2e8f0;">
                @if($img)
                    <img src="{{ $img }}"
                         alt="{{ $flyer->xFullStreet }}"
                         style="width:120px;height:82px;object-fit:cover;display:block;">
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

                    @if(!$flyer->dashboard_is_sent)
                        <span class="rounded-full bg-amber-100 px-3 py-1 text-amber-700">
                            Draft
                        </span>
                    @else
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-600">
                            Last Sent: {{ $lastSent }}
                        </span>

                        <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-600">
                            {{ number_format($emailCount) }} Sent
                        </span>

                        <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-600">
                            {{ number_format($viewCount) }} Views
                        </span>
                    @endif

                    @if($pendingForFlyer->count())
                        <span class="rounded-full bg-amber-100 px-3 py-1 text-amber-700">
                            Requested
                        </span>
                    @endif
                </div>
            </div>

            {{-- ACTIONS --}}
            <div class="flex shrink-0 gap-2">

                @if(!$flyer->dashboard_is_sent)

                    <a href="/member/send-campaign/{{ $flyer->id }}"
                       class="rounded-lg bg-amber-50 px-4 py-2 text-xs font-bold text-amber-700 ring-1 ring-amber-200 hover:bg-amber-100">
                        Resume
                    </a>

                    <a href="/member/delete-flyer/{{ $flyer->id }}"
                       onclick="return confirm('Delete this flyer?')"
                       class="rounded-lg bg-red-50 px-4 py-2 text-xs font-bold text-red-700 ring-1 ring-red-200 hover:bg-red-100">
                        Delete
                    </a>

                @else

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

                @endif

            </div>

        </article>

@php
        return ob_get_clean();
    };
@endphp

<main class="min-h-screen bg-[#f0f2f7] pt-24">
    <div class="mx-auto flex w-full max-w-[1400px] gap-8 px-6 pb-16">

        {{-- LEFT STICKY SIDEBAR --}}
        <aside class="sticky top-28 h-fit w-[240px] shrink-0 rounded-3xl bg-white p-5 shadow-sm ring-1 ring-black/5">
            <nav class="space-y-2 text-sm font-bold">
                <a href="/member/create-flyer" class="block rounded-xl px-4 py-3 text-slate-700 hover:bg-slate-100">
                    Create New Flyer
                </a>

                <a href="/member/resend-flyer" class="block rounded-xl px-4 py-3 text-slate-700 hover:bg-slate-100">
                    Resend Flyer
                </a>

                <a href="/member/campaigns" class="block rounded-xl px-4 py-3 text-slate-700 hover:bg-slate-100">
                    Campaigns
                </a>

                <a href="/member/agent-info" class="block rounded-xl px-4 py-3 text-slate-700 hover:bg-slate-100">
                    Agent Info
                </a>

                <a href="/member/account" class="block rounded-xl px-4 py-3 text-slate-700 hover:bg-slate-100">
                    Account Info
                </a>

                <a href="/logout" class="block rounded-xl px-4 py-3 text-red-600 hover:bg-red-50">
                    Log Out
                </a>
            </nav>
        </aside>

        {{-- RIGHT CONTENT --}}
        <section class="min-w-0 flex-1">

            <div class="mb-6">
                <h1 class="text-3xl font-black text-slate-900">
                    Welcome Back
                </h1>
                <p class="mt-1 text-sm text-slate-500">
                    {{ $agent->agtFullName ?? 'Member' }}
                </p>
            </div>

            @if($unsentFlyers->isNotEmpty())
                <div class="mb-5">
                    <h2 class="text-2xl font-black text-slate-900">
                        Unsent Flyers
                    </h2>
                    <p class="text-sm text-slate-500">
                        Flyers that have not been delivered yet.
                    </p>
                </div>

                <div class="mb-8 space-y-4">
                    @foreach($unsentFlyers as $flyer)
                        {!! $renderFlyerCard($flyer) !!}
                    @endforeach
                </div>
            @endif

            <div class="mb-5">
                <h2 class="text-2xl font-black text-slate-900">
                    5 Most Recent Sent Flyers
                </h2>
                <p class="text-sm text-slate-500">
                    Recent flyer activity, delivery stats, and quick actions.
                </p>
            </div>

            @if($recentFlyers->isEmpty())
                <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center text-slate-500">
                    No sent flyers found.
                </div>
            @else
                <div class="space-y-4">
                    @foreach($recentFlyers as $flyer)
                        {!! $renderFlyerCard($flyer) !!}
                    @endforeach
                </div>
            @endif

        </section>

    </div>
</main>

@include('public.layout.footer')

</body>
</html>