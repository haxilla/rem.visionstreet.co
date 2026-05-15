@include('public.layout.head')

<body
    data-section="member"
    class="relative min-h-screen bg-slate-50 font-sans text-slate-800"
>

@include('public.layout.nav')

@php
    use Illuminate\Support\Carbon;

    $flyers = $data['propflyers'] ?? collect();
    $campaigns = $data['propdelivs'] ?? collect();

    $agent = optional($flyers->first())->theAgent;

    $campaignsByFlyer = $campaigns->groupBy('propflyer_id');

    $pendingCampaigns = $campaigns->filter(function ($camp) {
        return empty($camp->emStart) && empty($camp->emComplete);
    });

    $completedCampaigns = $campaigns->filter(function ($camp) {
        return !empty($camp->emComplete);
    });

    $sentFlyersCount = $flyers->filter(function ($flyer) use ($campaignsByFlyer) {
        $stats = $flyer->theStats;
        $flyerCampaigns = $campaignsByFlyer->get($flyer->id, collect());

        return (int) (optional($stats)->xAgtSent ?? 0) > 0
            || (int) (optional($stats)->xDeliveryCount ?? 0) > 0
            || $flyerCampaigns->filter(fn($camp) => !empty($camp->emComplete))->count() > 0;
    })->count();

    $notSentFlyersCount = $flyers->count() - $sentFlyersCount;

    $totalDelivered = $completedCampaigns->sum('totalEmails');

    $totalViews = $flyers->sum(function ($flyer) {
        return optional($flyer->theStats)->xWebViews ?? 0;
    });

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

    $money = function ($value) {
        if ($value === null || $value === '') {
            return 'Price unavailable';
        }

        return '$' . number_format((float) $value);
    };

    $dateFmt = function ($value) {
        if (!$value) {
            return 'Not yet';
        }

        try {
            return Carbon::parse($value)->format('M j, Y');
        } catch (\Exception $e) {
            return $value;
        }
    };
@endphp

<main
    class="transition-all duration-300 min-h-screen pt-24 relative"
    :class="collapsed ? 'ml-20' : 'ml-64'"
>
    <div class="ml-8 mr-8 lg:ml-10 lg:mr-10">

        @if(session()->has('impersonator_admin_id'))
            <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <div class="text-sm font-bold uppercase tracking-wide text-amber-800">
                            Admin Impersonation Active
                        </div>

                        <div class="mt-1 text-sm text-amber-700">
                            You are currently viewing this account as
                            <span class="font-semibold">{{ $agent->agtFullName ?? 'this member' }}</span>.
                        </div>
                    </div>

                    <a
                        href="/admin"
                        class="inline-flex items-center rounded-xl bg-amber-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-amber-700"
                    >
                        Return to Admin
                    </a>
                </div>
            </div>
        @endif

        <div class="overflow-hidden rounded-3xl bg-white shadow-xl ring-1 ring-slate-200">

            <div class="relative overflow-hidden bg-gradient-to-r from-[#1b2f63] via-[#223a75] to-[#2a4486] px-8 py-8">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.25),transparent_35%)]"></div>

                <div class="relative flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <div class="text-3xl font-black tracking-tight text-white">
                            RealtyEmails<span class="text-blue-200">.com</span>
                        </div>

                        <p class="mt-2 text-sm text-blue-100">
                            Member Dashboard
                        </p>

                        <h1 class="mt-4 text-4xl font-bold tracking-tight text-white">
                            Welcome Back
                        </h1>

                        <p class="mt-2 text-lg text-blue-100">
                            {{ $agent->agtFullName ?? 'Member' }}
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                        <div class="rounded-2xl bg-white/10 px-5 py-4 backdrop-blur-sm ring-1 ring-white/20">
                            <div class="text-xs uppercase tracking-wide text-blue-100">
                                Flyers
                            </div>
                            <div class="mt-2 text-3xl font-bold text-white">
                                {{ $flyers->count() }}
                            </div>
                        </div>

                        <div class="rounded-2xl bg-white/10 px-5 py-4 backdrop-blur-sm ring-1 ring-white/20">
                            <div class="text-xs uppercase tracking-wide text-blue-100">
                                Sent Flyers
                            </div>
                            <div class="mt-2 text-3xl font-bold text-white">
                                {{ $sentFlyersCount }}
                            </div>
                        </div>

                        <div class="rounded-2xl bg-white/10 px-5 py-4 backdrop-blur-sm ring-1 ring-white/20">
                            <div class="text-xs uppercase tracking-wide text-blue-100">
                                Not Sent
                            </div>
                            <div class="mt-2 text-3xl font-bold text-white">
                                {{ $notSentFlyersCount }}
                            </div>
                        </div>

                        <div class="rounded-2xl bg-white/10 px-5 py-4 backdrop-blur-sm ring-1 ring-white/20">
                            <div class="text-xs uppercase tracking-wide text-blue-100">
                                Pending
                            </div>
                            <div class="mt-2 text-3xl font-bold text-white">
                                {{ $pendingCampaigns->count() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-6 lg:p-8">

                <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                        <div class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">
                            Total Emails Delivered
                        </div>
                        <div class="mt-2 text-3xl font-black text-slate-900">
                            {{ number_format($totalDelivered) }}
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                        <div class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">
                            Completed Campaigns
                        </div>
                        <div class="mt-2 text-3xl font-black text-slate-900">
                            {{ $completedCampaigns->count() }}
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                        <div class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">
                            Flyer Views
                        </div>
                        <div class="mt-2 text-3xl font-black text-slate-900">
                            {{ number_format($totalViews) }}
                        </div>
                    </div>
                </div>

                <section id="flyers">
                    <div class="mb-5 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold tracking-tight text-slate-900">
                                Your Flyers
                            </h2>

                            <p class="mt-1 text-sm text-slate-500">
                                Smaller tiles with flyer status, recent campaign info, and quick actions.
                            </p>
                        </div>
                    </div>

                    @if($flyers->isEmpty())
                        <div class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 p-12 text-center">
                            <div class="text-lg font-semibold text-slate-700">
                                No flyers found.
                            </div>

                            <p class="mt-2 text-sm text-slate-500">
                                Create your first flyer to begin sending property email campaigns.
                            </p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4">
                            @foreach($flyers as $flyer)
                                @php
                                    $img = $photoUrl($flyer);
                                    $stats = $flyer->theStats;
                                    $flyerCampaigns = $campaignsByFlyer->get($flyer->id, collect());

                                    $flyerPending = $flyerCampaigns->filter(function ($camp) {
                                        return empty($camp->emStart) && empty($camp->emComplete);
                                    });

                                    $flyerCompleted = $flyerCampaigns->filter(function ($camp) {
                                        return !empty($camp->emComplete);
                                    });

                                    $hasBeenSent =
                                        (int) (optional($stats)->xAgtSent ?? 0) > 0
                                        || (int) (optional($stats)->xDeliveryCount ?? 0) > 0
                                        || $flyerCompleted->count() > 0;

                                    if ($flyerPending->count()) {
                                        $statusText = 'Requested';
                                        $statusClass = 'bg-amber-100 text-amber-800';
                                    } elseif ($hasBeenSent) {
                                        $statusText = 'Sent';
                                        $statusClass = 'bg-emerald-100 text-emerald-800';
                                    } else {
                                        $statusText = 'Not Sent';
                                        $statusClass = 'bg-slate-100 text-slate-700';
                                    }

                                    $latestCampaign = $flyerCampaigns->sortByDesc(function ($camp) {
                                        return $camp->created_at ?? $camp->emRequest ?? $camp->emStart ?? $camp->emComplete;
                                    })->first();

                                    $campaignCount = $flyerCampaigns->count();
                                    $emailCount = $flyerCompleted->sum('totalEmails');
                                    $viewCount = optional($stats)->xWebViews ?? 0;
                                    $deliveryCount = optional($stats)->xDeliveryCount ?? 0;
                                    $lastDeliveryDate = optional($stats)->xLastDeliveryDate;
                                @endphp

                                <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                                    <div class="relative h-28 bg-slate-200">
                                        @if($img)
                                            <img
                                                src="{{ $img }}"
                                                alt="{{ $flyer->xFullStreet }}"
                                                class="h-full w-full object-cover"
                                            >
                                        @else
                                            <div class="flex h-full items-center justify-center bg-slate-100 text-xs font-semibold text-slate-400">
                                                No Photo
                                            </div>
                                        @endif

                                        <div class="absolute left-3 top-3 rounded-full px-2.5 py-1 text-[11px] font-bold shadow-sm {{ $statusClass }}">
                                            {{ $statusText }}
                                        </div>
                                    </div>

                                    <div class="p-4">
                                        <h3 class="truncate text-base font-black tracking-tight text-slate-900">
                                            {{ $flyer->xFullStreet }}
                                        </h3>

                                        <p class="mt-0.5 truncate text-xs text-slate-500">
                                            {{ $flyer->xCity }},
                                            {{ $flyer->state ?? $flyer->xState }}
                                            {{ $flyer->xxZip ?? $flyer->xZip }}
                                        </p>

                                        <div class="mt-2 flex flex-wrap gap-1.5 text-[11px] font-semibold">
                                            <span class="rounded-full bg-slate-100 px-2 py-0.5 text-slate-700">
                                                {{ $money($flyer->xListPrice) }}
                                            </span>

                                            @if($flyer->xxBeds)
                                                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-slate-700">
                                                    {{ $flyer->xxBeds }} bd
                                                </span>
                                            @endif

                                            @if($flyer->xxBaths)
                                                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-slate-700">
                                                    {{ $flyer->xxBaths }} ba
                                                </span>
                                            @endif

                                            @if($flyer->xxSqft)
                                                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-slate-700">
                                                    {{ number_format((int) $flyer->xxSqft) }} sf
                                                </span>
                                            @endif
                                        </div>

                                        <div class="mt-3 grid grid-cols-4 gap-1.5">
                                            <div class="rounded-xl bg-slate-50 p-2 text-center ring-1 ring-slate-200">
                                                <div class="text-[9px] font-bold uppercase text-slate-400">
                                                    Camps
                                                </div>
                                                <div class="text-sm font-black text-slate-900">
                                                    {{ $campaignCount }}
                                                </div>
                                            </div>

                                            <div class="rounded-xl bg-slate-50 p-2 text-center ring-1 ring-slate-200">
                                                <div class="text-[9px] font-bold uppercase text-slate-400">
                                                    Sent
                                                </div>
                                                <div class="text-sm font-black text-slate-900">
                                                    {{ $deliveryCount }}
                                                </div>
                                            </div>

                                            <div class="rounded-xl bg-slate-50 p-2 text-center ring-1 ring-slate-200">
                                                <div class="text-[9px] font-bold uppercase text-slate-400">
                                                    Emails
                                                </div>
                                                <div class="text-sm font-black text-slate-900">
                                                    {{ number_format($emailCount) }}
                                                </div>
                                            </div>

                                            <div class="rounded-xl bg-slate-50 p-2 text-center ring-1 ring-slate-200">
                                                <div class="text-[9px] font-bold uppercase text-slate-400">
                                                    Views
                                                </div>
                                                <div class="text-sm font-black text-slate-900">
                                                    {{ number_format($viewCount) }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-3 rounded-xl border border-slate-200 bg-slate-50 p-3">
                                            @if($latestCampaign)
                                                <div class="truncate text-xs font-bold text-slate-800">
                                                    {{ $latestCampaign->emSubject ?: 'No subject saved' }}
                                                </div>

                                                <div class="mt-1 flex flex-wrap gap-x-3 gap-y-1 text-[11px] text-slate-500">
                                                    <span>
                                                        Requested:
                                                        <strong>{{ $dateFmt($latestCampaign->emRequest ?? $latestCampaign->created_at) }}</strong>
                                                    </span>

                                                    <span>
                                                        Sent:
                                                        <strong>{{ $dateFmt($latestCampaign->emComplete) }}</strong>
                                                    </span>
                                                </div>

                                                <div class="mt-1 truncate text-[11px] text-slate-500">
                                                    Area:
                                                    <strong>
                                                        {{ $latestCampaign->emArea_display ?: $latestCampaign->emArea ?: 'Not specified' }}
                                                    </strong>
                                                </div>
                                            @else
                                                <div class="text-xs font-semibold text-slate-500">
                                                    No campaign requested yet.
                                                </div>

                                                <div class="mt-1 text-[11px] text-slate-400">
                                                    Last sent: {{ $dateFmt($lastDeliveryDate) }}
                                                </div>
                                            @endif
                                        </div>

                                        <div class="mt-3 grid grid-cols-3 gap-1.5">
                                            @if($flyer->url_slug)
                                                <a
                                                    href="/homedetails/{{ $flyer->url_slug }}"
                                                    class="rounded-lg bg-[#214e9b] px-2 py-2 text-center text-[11px] font-bold text-white transition hover:bg-[#183b78]"
                                                >
                                                    View Flyer
                                                </a>
                                            @else
                                                <span class="rounded-lg bg-slate-100 px-2 py-2 text-center text-[11px] font-bold text-slate-400">
                                                    No Link
                                                </span>
                                            @endif

                                            <a
                                                href="/member/campaigns/{{ $flyer->id }}"
                                                class="rounded-lg border border-slate-200 bg-white px-2 py-2 text-center text-[11px] font-bold text-slate-700 transition hover:bg-slate-50"
                                            >
                                                Campaigns
                                            </a>

                                            <a
                                                href="/member/send-campaign/{{ $flyer->id }}"
                                                class="rounded-lg border border-emerald-200 bg-emerald-50 px-2 py-2 text-center text-[11px] font-bold text-emerald-700 transition hover:bg-emerald-100"
                                            >
                                                Send
                                            </a>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </section>

            </div>
        </div>
    </div>
</main>

@include('public.layout.footer')

</body>
</html>