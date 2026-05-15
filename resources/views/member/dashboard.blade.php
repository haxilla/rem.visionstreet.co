@include('public.layout.head')

<body data-section="member" class="relative min-h-screen bg-[#f0f2f7] font-sans text-slate-800">

@include('public.layout.nav')

@php
    use Illuminate\Support\Carbon;

    $flyers     = $data['propflyers'] ?? collect();
    $campaigns  = $data['propdelivs'] ?? collect();

    $agent            = optional($flyers->first())->theAgent;
    $campaignsByFlyer = $campaigns->groupBy('propflyer_id');

    $completedCampaigns = $campaigns->filter(fn($c) => !empty($c->emComplete));
    $pendingCampaigns   = $campaigns->filter(fn($c) => empty($c->emStart) && empty($c->emComplete));

    $sentFlyers = $flyers->filter(function ($flyer) use ($campaignsByFlyer) {
        $stats          = $flyer->theStats;
        $flyerCampaigns = $campaignsByFlyer->get($flyer->id, collect());
        return (int)(optional($stats)->xAgtSent ?? 0) > 0
            || (int)(optional($stats)->xDeliveryCount ?? 0) > 0
            || $flyerCampaigns->filter(fn($c) => !empty($c->emComplete))->count() > 0;
    });

    $unsentFlyers = $flyers->filter(fn($f) => !$sentFlyers->contains('id', $f->id));

    $totalDelivered = $completedCampaigns->sum('totalEmails');
    $totalViews     = $sentFlyers->sum(fn($f) => optional($f->theStats)->xWebViews ?? 0);

    // Closure — safe in Blade, won't cause redeclaration errors
    $photoUrl = function ($flyer) {
        $photo = optional($flyer->thePhotos)->first();
        if (!$photo || !$flyer->theMeta) return null;
        return 'https://realtyrepublic.com/hqphotos/'
            . $flyer->theMeta->zipDir . '/'
            . $flyer->theMeta->mlsDir . '/'
            . $photo->photoName;
    };

    $money = fn($v) => ($v === null || $v === '') ? 'Price N/A' : '$' . number_format((float)$v);
@endphp

<main
    class="transition-all duration-300 min-h-screen pt-24"
    :class="collapsed ? 'ml-20' : 'ml-64'"
>
    <div class="mx-auto w-full max-w-[1200px] px-6 pb-16">

        {{-- ── Impersonation banner ── --}}
        @if(session()->has('impersonator_admin_id'))
            <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <div class="text-sm font-bold uppercase tracking-wide text-amber-800">Admin Impersonation Active</div>
                        <div class="mt-1 text-sm text-amber-700">
                            Viewing account as <span class="font-semibold">{{ $agent->agtFullName ?? 'this member' }}</span>.
                        </div>
                    </div>
                    <a href="/admin" class="rounded-xl bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700">
                        Return to Admin
                    </a>
                </div>
            </div>
        @endif

        {{-- ── Header ── --}}
        <div class="mb-8 overflow-hidden rounded-3xl bg-gradient-to-r from-[#1b2f63] via-[#223a75] to-[#2a4486] px-8 py-8 shadow-xl">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <div class="text-3xl font-black tracking-tight text-white">
                        RealtyEmails<span class="text-blue-300">.com</span>
                    </div>
                    <p class="mt-1 text-sm text-blue-200">Member Dashboard</p>
                    <h1 class="mt-4 text-4xl font-bold text-white">Welcome Back</h1>
                    <p class="mt-1 text-lg text-blue-200">{{ $agent->agtFullName ?? 'Member' }}</p>
                </div>

                <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
                    @foreach([
                        ['Total Flyers', $flyers->count()],
                        ['Sent',         $sentFlyers->count()],
                        ['Incomplete',   $unsentFlyers->count()],
                        ['Pending',      $pendingCampaigns->count()],
                    ] as [$label, $val])
                        <div class="rounded-2xl bg-white/10 px-5 py-4 ring-1 ring-white/20 text-center">
                            <div class="text-xs uppercase tracking-wide text-blue-200">{{ $label }}</div>
                            <div class="mt-2 text-3xl font-bold text-white">{{ $val }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ── Summary stats ── --}}
        <div class="mb-10 grid grid-cols-1 gap-4 md:grid-cols-3">
            @foreach([
                ['Emails Delivered',    number_format($totalDelivered)],
                ['Completed Campaigns', $completedCampaigns->count()],
                ['Flyer Views',         number_format($totalViews)],
            ] as [$label, $val])
                <div class="rounded-2xl bg-white px-6 py-5 shadow-sm ring-1 ring-slate-200">
                    <div class="text-xs font-bold uppercase tracking-wide text-slate-400">{{ $label }}</div>
                    <div class="mt-2 text-3xl font-black text-slate-900">{{ $val }}</div>
                </div>
            @endforeach
        </div>

        {{-- ══════════════════════════════════════════
             SENT FLYERS
        ══════════════════════════════════════════ --}}
        <section id="sent-flyers" class="mb-14">

            <div class="mb-6 flex items-center gap-3">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-700" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                    </svg>
                </span>
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-slate-900">Sent Flyers</h2>
                    <p class="text-sm text-slate-500">Flyers that have been emailed or have completed delivery activity.</p>
                </div>
            </div>

            @if($sentFlyers->isEmpty())
                <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center">
                    <div class="text-lg font-semibold text-slate-500">No sent flyers yet.</div>
                </div>
            @else
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($sentFlyers as $flyer)
                        @php
                            $img               = $photoUrl($flyer);
                            $stats             = $flyer->theStats;
                            $flyerCampaigns    = $campaignsByFlyer->get($flyer->id, collect());
                            $completedForFlyer = $flyerCampaigns->filter(fn($c) => !empty($c->emComplete));
                            $pendingForFlyer   = $flyerCampaigns->filter(fn($c) => empty($c->emStart) && empty($c->emComplete));
                            $emailCount        = $completedForFlyer->sum('totalEmails');
                            $viewCount         = optional($stats)->xWebViews ?? 0;
                        @endphp

                        <article class="flex flex-col overflow-hidden rounded-2xl bg-white shadow-md ring-1 ring-slate-200 transition-shadow duration-200 hover:shadow-xl">

                            {{-- Photo: inline styles so nothing can be purged by Tailwind --}}
                            <div style="position:relative; height:200px; overflow:hidden; background:#e2e8f0; flex-shrink:0;">
                                @if($img)
                                    <img
                                        src="{{ $img }}"
                                        alt="{{ $flyer->xFullStreet }}"
                                        style="position:absolute; top:0; left:0; width:100%; height:100%; object-fit:cover;"
                                    >
                                @else
                                    <div style="display:flex; height:100%; align-items:center; justify-content:center; font-size:.875rem; font-weight:600; color:#94a3b8;">
                                        No Photo Available
                                    </div>
                                @endif

                                <div style="position:absolute; top:12px; left:12px; display:flex; gap:6px;">
                                    <span style="background:#059669; color:#fff; font-size:10px; font-weight:700; letter-spacing:.06em; text-transform:uppercase; padding:3px 10px; border-radius:999px;">
                                        Sent
                                    </span>
                                    @if($pendingForFlyer->count())
                                        <span style="background:#d97706; color:#fff; font-size:10px; font-weight:700; letter-spacing:.06em; text-transform:uppercase; padding:3px 10px; border-radius:999px;">
                                            Requested
                                        </span>
                                    @endif
                                </div>

                                <div style="position:absolute; bottom:12px; right:12px;">
                                    <span style="background:rgba(18,63,145,.88); color:#fff; font-size:11px; font-weight:700; padding:4px 12px; border-radius:999px;">
                                        {{ $money($flyer->xListPrice) }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex flex-1 flex-col p-5">
                                <h3 class="line-clamp-1 text-[15px] font-bold text-[#123f91]">
                                    {{ $flyer->xFullStreet }}
                                </h3>
                                <p class="mt-1 text-sm text-slate-500">
                                    {{ trim($flyer->xCity . ' ' . ($flyer->state ?? $flyer->xState) . ' ' . ($flyer->xxZip ?? $flyer->xZip)) }}
                                </p>

                                <div class="mt-3 flex items-center gap-3 text-xs text-slate-400">
                                    <span>{{ number_format($emailCount) }} sent</span>
                                    <span>·</span>
                                    <span>{{ number_format($viewCount) }} views</span>
                                </div>

                                <div class="mt-auto pt-4">
                                    <div class="flex items-center gap-2.5 border-t border-slate-100 pt-4">
                                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[#1b2f63] text-xs font-bold text-white">
                                            {{ strtoupper(substr($agent->agtFullName ?? 'A', 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="text-[11px] text-slate-400">Listed by</div>
                                            <div class="truncate text-sm font-semibold text-slate-700">{{ $agent->agtFullName ?? '—' }}</div>
                                        </div>
                                    </div>

                                    <div class="mt-3 grid grid-cols-3 gap-2">
                                        @if($flyer->url_slug)
                                            <a href="/homedetails/{{ $flyer->url_slug }}"
                                               class="rounded-xl bg-[#123f91] py-2 text-center text-xs font-bold text-white hover:bg-[#0f3274]">
                                                View
                                            </a>
                                        @else
                                            <span class="rounded-xl bg-slate-100 py-2 text-center text-xs font-bold text-slate-400">No Link</span>
                                        @endif

                                        <a href="/member/campaigns/{{ $flyer->id }}"
                                           class="rounded-xl border border-slate-200 py-2 text-center text-xs font-bold text-slate-600 hover:bg-slate-50">
                                            Campaigns
                                        </a>

                                        <a href="/member/send-campaign/{{ $flyer->id }}"
                                           class="rounded-xl border border-emerald-200 bg-emerald-50 py-2 text-center text-xs font-bold text-emerald-700 hover:bg-emerald-100">
                                            Send
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- ══════════════════════════════════════════
             UNSENT / INCOMPLETE FLYERS
        ══════════════════════════════════════════ --}}
        <section id="unsent-flyers">

            <div class="mb-6 flex items-center gap-3">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-amber-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-amber-700" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                </span>
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-slate-900">Incomplete / Unsent Flyers</h2>
                    <p class="text-sm text-slate-500">Flyers that have not been emailed yet.</p>
                </div>
            </div>

            @if($unsentFlyers->isEmpty())
                <div class="rounded-2xl border border-slate-200 bg-white p-6 text-sm font-semibold text-slate-500">
                    No incomplete or unsent flyers found.
                </div>
            @else
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($unsentFlyers as $flyer)
                        @php
                            $img            = $photoUrl($flyer);
                            $flyerCampaigns = $campaignsByFlyer->get($flyer->id, collect());
                            $hasRequest     = $flyerCampaigns->filter(fn($c) => empty($c->emStart) && empty($c->emComplete))->count() > 0;
                        @endphp

                        <article class="flex flex-col overflow-hidden rounded-2xl bg-white shadow-md ring-1 ring-slate-200 transition-shadow duration-200 hover:shadow-xl">

                            <div style="position:relative; height:200px; overflow:hidden; background:#e2e8f0; flex-shrink:0;">
                                @if($img)
                                    <img
                                        src="{{ $img }}"
                                        alt="{{ $flyer->xFullStreet }}"
                                        style="position:absolute; top:0; left:0; width:100%; height:100%; object-fit:cover;"
                                    >
                                @else
                                    <div style="display:flex; height:100%; align-items:center; justify-content:center; font-size:.875rem; font-weight:600; color:#94a3b8;">
                                        No Photo Available
                                    </div>
                                @endif

                                <div style="position:absolute; top:12px; left:12px;">
                                    @if($hasRequest)
                                        <span style="background:#d97706; color:#fff; font-size:10px; font-weight:700; letter-spacing:.06em; text-transform:uppercase; padding:3px 10px; border-radius:999px;">
                                            Requested
                                        </span>
                                    @else
                                        <span style="background:#475569; color:#fff; font-size:10px; font-weight:700; letter-spacing:.06em; text-transform:uppercase; padding:3px 10px; border-radius:999px;">
                                            Not Sent
                                        </span>
                                    @endif
                                </div>

                                <div style="position:absolute; bottom:12px; right:12px;">
                                    <span style="background:rgba(18,63,145,.88); color:#fff; font-size:11px; font-weight:700; padding:4px 12px; border-radius:999px;">
                                        {{ $money($flyer->xListPrice) }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex flex-1 flex-col p-5">
                                <h3 class="line-clamp-1 text-[15px] font-bold text-[#123f91]">
                                    {{ $flyer->xFullStreet ?: 'Untitled Flyer' }}
                                </h3>
                                <p class="mt-1 text-sm text-slate-500">
                                    {{ trim($flyer->xCity . ' ' . ($flyer->state ?? $flyer->xState) . ' ' . ($flyer->xxZip ?? $flyer->xZip)) }}
                                </p>

                                <div class="mt-3 text-xs italic text-slate-400">
                                    No campaigns sent yet — ready to go!
                                </div>

                                <div class="mt-auto pt-4">
                                    <div class="flex items-center gap-2.5 border-t border-slate-100 pt-4">
                                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-300 text-xs font-bold text-white">
                                            {{ strtoupper(substr($agent->agtFullName ?? 'A', 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="text-[11px] text-slate-400">Listed by</div>
                                            <div class="truncate text-sm font-semibold text-slate-700">{{ $agent->agtFullName ?? '—' }}</div>
                                        </div>
                                    </div>

                                    <div class="mt-3 grid grid-cols-3 gap-2">
                                        @if($flyer->url_slug)
                                            <a href="/homedetails/{{ $flyer->url_slug }}"
                                               class="rounded-xl bg-[#123f91] py-2 text-center text-xs font-bold text-white hover:bg-[#0f3274]">
                                                View
                                            </a>
                                        @else
                                            <span class="rounded-xl bg-slate-100 py-2 text-center text-xs font-bold text-slate-400">No Link</span>
                                        @endif

                                        <a href="/member/campaigns/{{ $flyer->id }}"
                                           class="rounded-xl border border-slate-200 py-2 text-center text-xs font-bold text-slate-600 hover:bg-slate-50">
                                            Campaigns
                                        </a>

                                        <a href="/member/send-campaign/{{ $flyer->id }}"
                                           class="rounded-xl border border-emerald-200 bg-emerald-50 py-2 text-center text-xs font-bold text-emerald-700 hover:bg-emerald-100">
                                            Send
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>

    </div>
</main>

@include('public.layout.footer')

</body>
</html>