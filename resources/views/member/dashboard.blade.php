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

    $completedCampaigns = $campaigns->filter(function ($camp) {
        return !empty($camp->emComplete);
    });

    $pendingCampaigns = $campaigns->filter(function ($camp) {
        return empty($camp->emStart) && empty($camp->emComplete);
    });

    $sentFlyers = $flyers->filter(function ($flyer) use ($campaignsByFlyer) {
        $stats = $flyer->theStats;
        $flyerCampaigns = $campaignsByFlyer->get($flyer->id, collect());

        return (int) (optional($stats)->xAgtSent ?? 0) > 0
            || (int) (optional($stats)->xDeliveryCount ?? 0) > 0
            || $flyerCampaigns->filter(fn($camp) => !empty($camp->emComplete))->count() > 0;
    });

    $unsentFlyers = $flyers->filter(function ($flyer) use ($sentFlyers) {
        return !$sentFlyers->contains('id', $flyer->id);
    });

    $totalDelivered = $completedCampaigns->sum('totalEmails');

    $totalViews = $sentFlyers->sum(function ($flyer) {
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
                                Total Flyers
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
                                {{ $sentFlyers->count() }}
                            </div>
                        </div>

                        <div class="rounded-2xl bg-white/10 px-5 py-4 backdrop-blur-sm ring-1 ring-white/20">
                            <div class="text-xs uppercase tracking-wide text-blue-100">
                                Incomplete
                            </div>
                            <div class="mt-2 text-3xl font-bold text-white">
                                {{ $unsentFlyers->count() }}
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

                <div class="mb-8 grid grid-cols-1 gap-4 md:grid-cols-3">
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

                <section id="sent-flyers">
                    <div class="mb-5 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold tracking-tight text-slate-900">
                                Sent Flyers
                            </h2>

                            <p class="mt-1 text-sm text-slate-500">
                                Flyers that have already been emailed or have completed delivery activity.
                            </p>
                        </div>
                    </div>

                    @if($sentFlyers->isEmpty())
                        <div class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 p-10 text-center">
                            <div class="text-lg font-semibold text-slate-700">
                                No sent flyers yet.
                            </div>

                            <p class="mt-2 text-sm text-slate-500">
                                Completed flyer campaigns will appear here.
                            </p>
                        </div>
                    @else
                        <div class="class="grid grid-cols-1 gap-7 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4">
                            @foreach($sentFlyers as $flyer)
                                @php
                                    $img = $photoUrl($flyer);
                                    $stats = $flyer->theStats;
                                    $flyerCampaigns = $campaignsByFlyer->get($flyer->id, collect());

                                    $completedForFlyer = $flyerCampaigns->filter(function ($camp) {
                                        return !empty($camp->emComplete);
                                    });

                                    $pendingForFlyer = $flyerCampaigns->filter(function ($camp) {
                                        return empty($camp->emStart) && empty($camp->emComplete);
                                    });

                                    $latestCampaign = $flyerCampaigns->sortByDesc(function ($camp) {
                                        return $camp->emComplete ?? $camp->created_at ?? $camp->emRequest ?? $camp->emStart;
                                    })->first();

                                    $emailCount = $completedForFlyer->sum('totalEmails');
                                    $deliveryCount = optional($stats)->xDeliveryCount ?? $completedForFlyer->count();
                                    $viewCount = optional($stats)->xWebViews ?? 0;
                                    $lastDeliveryDate = optional($stats)->xLastDeliveryDate;
                                @endphp

<article class="max-w-[390px] rounded-[1.6rem] border border-slate-200 bg-white p-5 shadow-[0_18px_45px_rgba(15,23,42,0.08)]">    <div class="h-56 overflow-hidden rounded-2xl bg-slate-100">
        @if($img)
            <img src="{{ $img }}" alt="{{ $flyer->xFullStreet }}" class="h-full w-full object-cover">
        @else
            <div class="flex h-full items-center justify-center text-sm font-semibold text-slate-400">
                No Photo Available
            </div>
        @endif
    </div>

    <div class="mt-5 flex items-center gap-2">
        <span class="text-xs font-bold uppercase tracking-[0.22em] text-slate-400">
            Sent Flyer
        </span>

        <span class="rounded-full bg-[#123f91] px-3 py-1 text-xs font-bold text-white">
            {{ $money($flyer->xListPrice) }}
        </span>
    </div>

    <h3 class="mt-4 text-2xl font-medium leading-tight text-[#123f91]">
        {{ $flyer->xFullStreet }}
    </h3>

    <p class="mt-2 text-sm text-slate-600">
        {{ $flyer->xCity }}
        {{ $flyer->state ?? $flyer->xState }}
        {{ $flyer->xxZip ?? $flyer->xZip }}
    </p>

    <div class="mt-5 flex items-center gap-3">
        <div class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-slate-100 text-xs font-bold text-slate-400">
            {{ strtoupper(substr($agent->agtFullName ?? 'A', 0, 1)) }}
        </div>

        <div class="min-w-0">
            <div class="text-xs text-slate-500">
                Latest campaign:
            </div>

            <div class="truncate text-sm font-bold text-[#123f91]">
                {{ optional($latestCampaign)->emArea_display ?: optional($latestCampaign)->emArea ?: 'Campaign history available' }}
            </div>

            <div class="truncate text-sm text-slate-700">
                {{ number_format($emailCount) }} emails sent · {{ number_format($viewCount) }} views
            </div>
        </div>
    </div>

    <div class="mt-5 grid grid-cols-3 gap-2 border-t border-slate-100 pt-4">
        @if($flyer->url_slug)
            <a
                href="/homedetails/{{ $flyer->url_slug }}"
                class="rounded-xl bg-[#123f91] px-3 py-2 text-center text-xs font-bold text-white transition hover:bg-[#0f3274]"
            >
                View Flyer
            </a>
        @else
            <span class="rounded-xl bg-slate-100 px-3 py-2 text-center text-xs font-bold text-slate-400">
                No Link
            </span>
        @endif

        <a
            href="/member/campaigns/{{ $flyer->id }}"
            class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-center text-xs font-bold text-slate-700 transition hover:bg-slate-50"
        >
            Campaigns
        </a>

        <a
            href="/member/send-campaign/{{ $flyer->id }}"
            class="rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-center text-xs font-bold text-emerald-700 transition hover:bg-emerald-100"
        >
            Send
        </a>
    </div>
</article>
                            @endforeach
                        </div>
                    @endif
                </section>

                <section id="unsent-flyers" class="mt-12">
                    <div class="mb-5 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold tracking-tight text-slate-900">
                                Incomplete / Unsent Flyers
                            </h2>

                            <p class="mt-1 text-sm text-slate-500">
                                Flyers that have not been emailed yet. These are shown separately so the sent flyer dashboard stays clean.
                            </p>
                        </div>
                    </div>

                    @if($unsentFlyers->isEmpty())
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6 text-sm font-semibold text-slate-500">
                            No incomplete or unsent flyers found.
                        </div>
                    @else
                        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
                            <div class="hidden grid-cols-[80px_1fr_150px_160px_240px] gap-4 bg-slate-50 px-4 py-3 text-xs font-bold uppercase tracking-wide text-slate-400 md:grid">
                                <div>Photo</div>
                                <div>Flyer</div>
                                <div>Status</div>
                                <div>Created</div>
                                <div>Actions</div>
                            </div>

                            <div class="divide-y divide-slate-200">
                                @foreach($unsentFlyers as $flyer)
                                    @php
                                        $img = $photoUrl($flyer);
                                        $flyerCampaigns = $campaignsByFlyer->get($flyer->id, collect());

                                        $hasRequest = $flyerCampaigns->filter(function ($camp) {
                                            return empty($camp->emStart) && empty($camp->emComplete);
                                        })->count() > 0;

                                        $statusText = $hasRequest ? 'Campaign Requested' : 'Not Sent';
                                        $statusClass = $hasRequest
                                            ? 'bg-amber-100 text-amber-800'
                                            : 'bg-slate-100 text-slate-700';
                                    @endphp

                                    <div class="grid grid-cols-1 gap-4 px-4 py-4 md:grid-cols-[80px_1fr_150px_160px_240px] md:items-center">
                                        <div class="h-14 w-20 overflow-hidden rounded-xl bg-slate-100">
                                            @if($img)
                                                <img
                                                    src="{{ $img }}"
                                                    alt="{{ $flyer->xFullStreet }}"
                                                    class="h-full w-full object-cover"
                                                >
                                            @else
                                                <div class="flex h-full w-full items-center justify-center text-[10px] font-bold text-slate-400">
                                                    No Photo
                                                </div>
                                            @endif
                                        </div>

                                        <div>
                                            <div class="font-bold text-slate-900">
                                                {{ $flyer->xFullStreet ?: 'Untitled Flyer' }}
                                            </div>

                                            <div class="mt-1 text-sm text-slate-500">
                                                {{ $flyer->xCity }}
                                                {{ $flyer->state ?? $flyer->xState }}
                                                {{ $flyer->xxZip ?? $flyer->xZip }}
                                            </div>

                                            <div class="mt-1 text-xs text-slate-400">
                                                {{ $money($flyer->xListPrice) }}
                                            </div>
                                        </div>

                                        <div>
                                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $statusClass }}">
                                                {{ $statusText }}
                                            </span>
                                        </div>

                                        <div class="text-sm font-semibold text-slate-600">
                                            {{ $dateFmt($flyer->creationDate ?? $flyer->created_at) }}
                                        </div>

                                        <div class="grid grid-cols-3 gap-2">
                                            @if($flyer->url_slug)
                                                <a
                                                    href="/homedetails/{{ $flyer->url_slug }}"
                                                    class="rounded-lg bg-[#123f91] px-2 py-2 text-center text-xs font-bold text-white transition hover:bg-[#0f3274]"
                                                >
                                                    View
                                                </a>
                                            @else
                                                <span class="rounded-lg bg-slate-100 px-2 py-2 text-center text-xs font-bold text-slate-400">
                                                    No Link
                                                </span>
                                            @endif

                                            <a
                                                href="/member/campaigns/{{ $flyer->id }}"
                                                class="rounded-lg border border-slate-200 bg-white px-2 py-2 text-center text-xs font-bold text-slate-700 transition hover:bg-slate-50"
                                            >
                                                Campaigns
                                            </a>

                                            <a
                                                href="/member/send-campaign/{{ $flyer->id }}"
                                                class="rounded-lg border border-emerald-200 bg-emerald-50 px-2 py-2 text-center text-xs font-bold text-emerald-700 transition hover:bg-emerald-100"
                                            >
                                                Send
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
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