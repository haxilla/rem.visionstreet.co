@include('public.layout.head')

<body data-section="member" class="relative min-h-screen bg-slate-50 font-sans text-slate-800">

@include('public.layout.nav')

@php
    use App\Models\Core\Propflyer;
    use App\Models\Core\Propdelivnow;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Carbon;
    use Illuminate\Support\Str;

    $member = Auth::guard('member')->user();
    $agentID = Auth::guard('member')->id();

    $flyers = Propflyer::with([
        'theAgent',
        'theStats',
        'theMeta',
        'thePhotos' => function ($query) {
            $query->where('def', 1);
        },
    ])
    ->where('propagent_id', $agentID)
    ->orderByDesc('creationDate')
    ->get();

    $campaigns = Propdelivnow::with([
        'theFlyer.theMeta',
        'theFlyer.theStats',
        'theFlyer.thePhotos' => function ($query) {
            $query->where('def', 1);
        },
    ])
    ->where('propagent_id', $agentID)
    ->orderByDesc('created_at')
    ->get();

    $campaignsByFlyer = $campaigns->groupBy('propflyer_id');

    $pendingCampaigns = $campaigns->filter(function ($camp) {
        return empty($camp->emStart) && empty($camp->emComplete);
    });

    $inProgressCampaigns = $campaigns->filter(function ($camp) {
        return !empty($camp->emStart) && empty($camp->emComplete);
    });

    $completedCampaigns = $campaigns->filter(function ($camp) {
        return !empty($camp->emComplete);
    });

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
            return Carbon::parse($value)->format('M j, Y g:i A');
        } catch (\Exception $e) {
            return $value;
        }
    };

    $flyerAddress = function ($flyer) {
        return trim(($flyer->xFullStreet ?? '') . ', ' . ($flyer->xCity ?? '') . ', ' . ($flyer->state ?? $flyer->xState ?? '') . ' ' . ($flyer->xxZip ?? $flyer->xZip ?? ''));
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
                            <span class="font-semibold">{{ $member->agtFullName }}</span>.
                        </div>
                    </div>

                    <a href="/admin" class="inline-flex items-center rounded-xl bg-amber-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-amber-700">
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
                            {{ $member->agtFullName }}
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                        <div class="rounded-2xl bg-white/10 px-5 py-4 backdrop-blur-sm ring-1 ring-white/20">
                            <div class="text-xs uppercase tracking-wide text-blue-100">Flyers</div>
                            <div class="mt-2 text-3xl font-bold text-white">{{ $flyers->count() }}</div>
                        </div>

                        <div class="rounded-2xl bg-white/10 px-5 py-4 backdrop-blur-sm ring-1 ring-white/20">
                            <div class="text-xs uppercase tracking-wide text-blue-100">Pending</div>
                            <div class="mt-2 text-3xl font-bold text-white">{{ $pendingCampaigns->count() }}</div>
                        </div>

                        <div class="rounded-2xl bg-white/10 px-5 py-4 backdrop-blur-sm ring-1 ring-white/20">
                            <div class="text-xs uppercase tracking-wide text-blue-100">Sent</div>
                            <div class="mt-2 text-3xl font-bold text-white">{{ $completedCampaigns->count() }}</div>
                        </div>

                        <div class="rounded-2xl bg-white/10 px-5 py-4 backdrop-blur-sm ring-1 ring-white/20">
                            <div class="text-xs uppercase tracking-wide text-blue-100">Views</div>
                            <div class="mt-2 text-3xl font-bold text-white">{{ number_format($totalViews) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-8 p-8 xl:grid-cols-[300px_1fr]">

                <aside>
                    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-4">
                        <div class="mb-4 px-3">
                            <div class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">
                                Actions
                            </div>
                        </div>

                        <nav class="space-y-2">
                            <a href="#" class="group flex items-center justify-between rounded-2xl bg-white px-4 py-4 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-0.5 hover:shadow-md">
                                <span class="font-semibold text-slate-700 group-hover:text-[#214e9b]">Create New Flyer</span>
                                <span class="text-slate-300 group-hover:text-[#214e9b]">→</span>
                            </a>

                            <a href="#flyers" class="group flex items-center justify-between rounded-2xl bg-white px-4 py-4 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-0.5 hover:shadow-md">
                                <span class="font-semibold text-slate-700 group-hover:text-[#214e9b]">My Flyers</span>
                                <span class="text-slate-300 group-hover:text-[#214e9b]">→</span>
                            </a>

                            <a href="#campaigns" class="group flex items-center justify-between rounded-2xl bg-white px-4 py-4 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-0.5 hover:shadow-md">
                                <span class="font-semibold text-slate-700 group-hover:text-[#214e9b]">Campaign History</span>
                                <span class="text-slate-300 group-hover:text-[#214e9b]">→</span>
                            </a>

                            <a href="#" class="group flex items-center justify-between rounded-2xl bg-white px-4 py-4 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-0.5 hover:shadow-md">
                                <span class="font-semibold text-slate-700 group-hover:text-[#214e9b]">Purchase Credits</span>
                                <span class="text-slate-300 group-hover:text-[#214e9b]">→</span>
                            </a>
                        </nav>
                    </div>

                    <div class="mt-6 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">
                            Delivery Summary
                        </div>

                        <div class="mt-5 space-y-4">
                            <div>
                                <div class="text-sm text-slate-500">Total Emails Delivered</div>
                                <div class="text-2xl font-black text-slate-900">{{ number_format($totalDelivered) }}</div>
                            </div>

                            <div>
                                <div class="text-sm text-slate-500">In Progress</div>
                                <div class="text-2xl font-black text-slate-900">{{ $inProgressCampaigns->count() }}</div>
                            </div>

                            <div>
                                <div class="text-sm text-slate-500">Completed Campaigns</div>
                                <div class="text-2xl font-black text-slate-900">{{ $completedCampaigns->count() }}</div>
                            </div>
                        </div>
                    </div>
                </aside>

                <section id="flyers">

                    <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold tracking-tight text-slate-900">
                                Your Flyers
                            </h2>
                            <p class="mt-1 text-sm text-slate-500">
                                Each flyer shows its photo, listing details, delivery status, and campaign activity.
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
                        <div class="space-y-6">
                            @foreach($flyers as $flyer)
                                @php
                                    $img = $photoUrl($flyer);
                                    $stats = $flyer->theStats;
                                    $flyerCampaigns = $campaignsByFlyer->get($flyer->id, collect());

                                    $flyerPending = $flyerCampaigns->filter(fn($camp) => empty($camp->emStart) && empty($camp->emComplete));
                                    $flyerInProgress = $flyerCampaigns->filter(fn($camp) => !empty($camp->emStart) && empty($camp->emComplete));
                                    $flyerCompleted = $flyerCampaigns->filter(fn($camp) => !empty($camp->emComplete));

                                    $latestCampaign = $flyerCampaigns->sortByDesc('created_at')->first();
                                @endphp

<article class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

    <div class="relative h-48 bg-slate-200">
        @if($img)
            <img
                src="{{ $img }}"
                alt="{{ $flyer->xFullStreet }}"
                class="h-full w-full object-cover"
            >
        @else
            <div class="flex h-full items-center justify-center bg-slate-100 text-sm font-semibold text-slate-400">
                No Photo
            </div>
        @endif

        @if($flyerPending->count())
            <div class="absolute left-4 top-4 rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-800 shadow-sm">
                Requested
            </div>
        @elseif($flyerInProgress->count())
            <div class="absolute left-4 top-4 rounded-full bg-blue-100 px-3 py-1 text-xs font-bold text-blue-800 shadow-sm">
                Sending
            </div>
        @elseif($flyerCompleted->count())
            <div class="absolute left-4 top-4 rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800 shadow-sm">
                Sent
            </div>
        @else
            <div class="absolute left-4 top-4 rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700 shadow-sm">
                No Campaign
            </div>
        @endif
    </div>

    <div class="p-5">
        <h3 class="line-clamp-1 text-lg font-black tracking-tight text-slate-900">
            {{ $flyer->xFullStreet }}
        </h3>

        <p class="mt-1 line-clamp-1 text-sm text-slate-500">
            {{ $flyer->xCity }}, {{ $flyer->state ?? $flyer->xState }} {{ $flyer->xxZip ?? $flyer->xZip }}
        </p>

        <div class="mt-3 flex flex-wrap gap-2 text-xs font-semibold">
            <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-700">
                {{ $money($flyer->xListPrice) }}
            </span>

            @if($flyer->xxBeds)
                <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-700">
                    {{ $flyer->xxBeds }} Beds
                </span>
            @endif

            @if($flyer->xxBaths)
                <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-700">
                    {{ $flyer->xxBaths }} Baths
                </span>
            @endif
        </div>

        <div class="mt-5 grid grid-cols-3 gap-2">
            <div class="rounded-2xl bg-slate-50 p-3 text-center ring-1 ring-slate-200">
                <div class="text-[10px] font-bold uppercase tracking-wide text-slate-400">
                    Campaigns
                </div>
                <div class="mt-1 text-lg font-black text-slate-900">
                    {{ $flyerCampaigns->count() }}
                </div>
            </div>

            <div class="rounded-2xl bg-slate-50 p-3 text-center ring-1 ring-slate-200">
                <div class="text-[10px] font-bold uppercase tracking-wide text-slate-400">
                    Emails
                </div>
                <div class="mt-1 text-lg font-black text-slate-900">
                    {{ number_format($flyerCompleted->sum('totalEmails')) }}
                </div>
            </div>

            <div class="rounded-2xl bg-slate-50 p-3 text-center ring-1 ring-slate-200">
                <div class="text-[10px] font-bold uppercase tracking-wide text-slate-400">
                    Views
                </div>
                <div class="mt-1 text-lg font-black text-slate-900">
                    {{ number_format(optional($stats)->xWebViews ?? 0) }}
                </div>
            </div>
        </div>

        @if($latestCampaign)
            <div class="mt-5 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="text-xs font-bold uppercase tracking-wide text-slate-400">
                    Latest Campaign
                </div>

                <div class="mt-2 line-clamp-2 text-sm font-bold text-slate-800">
                    {{ $latestCampaign->emSubject ?: 'No subject saved' }}
                </div>

                <div class="mt-2 text-xs text-slate-500">
                    Sent:
                    <span class="font-semibold">
                        {{ $dateFmt($latestCampaign->emComplete) }}
                    </span>
                </div>

                <div class="mt-1 text-xs text-slate-500">
                    Area:
                    <span class="font-semibold">
                        {{ $latestCampaign->emArea_display ?: $latestCampaign->emArea ?: 'Not specified' }}
                    </span>
                </div>
            </div>
        @else
            <div class="mt-5 rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-4 text-sm text-slate-500">
                No campaign has been requested for this flyer yet.
            </div>
        @endif

        <div class="mt-5 flex gap-2">
            @if($flyer->url_slug)
                <a
                    href="/homedetails/{{ $flyer->url_slug }}"
                    class="flex-1 rounded-xl bg-[#214e9b] px-4 py-2 text-center text-sm font-bold text-white transition hover:bg-[#183b78]"
                >
                    View
                </a>
            @endif

            <a
                href="#"
                class="flex-1 rounded-xl border border-slate-200 bg-white px-4 py-2 text-center text-sm font-bold text-slate-700 transition hover:bg-slate-50"
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