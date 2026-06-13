<div class="min-h-screen bg-[#f0f2f7] flex">

    {{-- LEFT SIDEBAR --}}
    <aside class="sticky top-0 h-screen w-[260px] shrink-0 bg-[#101827] text-white p-6">
        <div class="mb-10">
            <div class="text-xl font-black">RealtyEmails.com</div>
            <div class="text-sm text-slate-400">Member Dashboard</div>
        </div>

        <nav class="space-y-2 text-sm font-bold">
            <a href="/member/create-flyer" class="block rounded-lg px-4 py-3 hover:bg-white/10">Create New Flyer</a>
            <a href="/member/resend-flyer" class="block rounded-lg px-4 py-3 hover:bg-white/10">Resend Flyer</a>
            <a href="/member/campaigns" class="block rounded-lg px-4 py-3 hover:bg-white/10">Campaigns</a>
            <a href="/member/agent-info" class="block rounded-lg px-4 py-3 hover:bg-white/10">Agent Info</a>
            <a href="/member/account" class="block rounded-lg px-4 py-3 hover:bg-white/10">Account Info</a>
            <a href="/member/logout" class="block rounded-lg px-4 py-3 text-red-300 hover:bg-red-500/10">Log Out</a>
        </nav>
    </aside>

    {{-- RIGHT CONTENT --}}
    <main class="flex-1 p-8">

        <div class="mb-6">
            <h1 class="text-3xl font-black text-slate-900">Recent Flyers</h1>
            <p class="text-sm text-slate-500">Your 5 most recent flyers.</p>
        </div>

        <div class="space-y-4">
            @foreach($recentFlyers as $flyer)
                @php
                    $img = $photoUrl($flyer);
                    $stats = $flyer->theStats;
                    $flyerCampaigns = $campaignsByFlyer->get($flyer->id, collect());

                    $completedForFlyer = $flyerCampaigns->filter(fn($c) => !empty($c->emComplete));
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

                <div class="flex items-center gap-5 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-black/5">

                    {{-- SMALL THUMBNAIL --}}
                    <div class="h-[95px] w-[140px] shrink-0 overflow-hidden rounded-xl bg-slate-200">
                        @if($img)
                            <img src="{{ $img }}" class="h-full w-full object-cover" alt="">
                        @else
                            <div class="flex h-full items-center justify-center text-xs font-bold text-slate-400">
                                No Photo
                            </div>
                        @endif
                    </div>

                    {{-- FLYER INFO --}}
                    <div class="min-w-0 flex-1">
                        <div class="text-lg font-black text-[#123f91] truncate">
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
                        </div>
                    </div>

                    {{-- ACTIONS --}}
                    <div class="flex shrink-0 gap-2">
                        @if($flyer->url_slug)
                            <a href="/homedetails/{{ $flyer->url_slug }}"
                               class="rounded-lg bg-[#123f91] px-4 py-2 text-xs font-bold text-white">
                                View
                            </a>
                        @endif

                        <a href="/member/send-campaign/{{ $flyer->id }}"
                           class="rounded-lg bg-emerald-50 px-4 py-2 text-xs font-bold text-emerald-700 ring-1 ring-emerald-200">
                            Resend
                        </a>
                    </div>

                </div>
            @endforeach
        </div>

    </main>
</div>