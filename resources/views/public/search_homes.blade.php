@include('public.layout.head')

@php
$paginator   = $data['searchAll'];
$pageItems   = collect($paginator->items());
$featured    = $pageItems->first();
$listings    = $pageItems->slice(1);
$searchValue = request('q', '');

$listingImg = function($item) {
    $photoObj = $item->thePhotos->where('def', '=', '1')->first();
    $photo    = $photoObj?->photoName;
    if ($photo && $item->theMeta?->zipDir && $item->theMeta?->mlsDir) {
        return "https://realtyrepublic.com/hqphotos/{$item->theMeta->zipDir}/{$item->theMeta->mlsDir}/{$photo}";
    }
    return null;
};

$agentImg = function($item) {
    if (!empty($item->theAgent?->agtPhoto) && !empty($item->theAgent?->theAgentCleanup?->newRemID)) {
        return "https://realtyrepublic.com/agentPhotos/{$item->theAgent->theAgentCleanup->newRemID}/{$item->theAgent->agtPhoto}";
    }
    return null;
};

$priceLabel = function($item) {
    $price = $item->xPrice ?? $item->xListPrice;
    return $price ? '$' . number_format($price) : null;
};
@endphp

<body data-section="admin" class="relative min-h-screen bg-[#f0f2f7] font-sans text-slate-800 postgres">

@include('public.layout.nav')

<main class="transition-all duration-300 min-h-screen pt-24" :class="collapsed ? 'ml-20' : 'ml-64'">
<div class="px-5 lg:px-10 py-8 max-w-[1400px] mx-auto">


    {{-- ══ HERO: full-bleed photo with overlay + search panel right ══════════ --}}
    @if($featured)
    @php
        $fImg    = $listingImg($featured);
        $fAgt    = $agentImg($featured);
        $fPrice  = $priceLabel($featured);
        $fURL    = "https://realtyrepublic.com/homedetails/{$featured->url_slug}";
        $fStreet = $featured->xFullStreet;
        $fCity   = trim("{$featured->xCity} {$featured->xState} {$featured->xxZip}");
    @endphp

    <section class="rounded-2xl overflow-hidden shadow-md flex flex-col lg:flex-row mb-4" style="min-height: 520px;">

        {{-- Left: full-bleed photo, address top-left, agent pill bottom-left --}}
        <div class="relative lg:w-[62%] shrink-0" style="min-height: 520px;">

            {{-- Background photo --}}
            @if($fImg)
                <img src="{{ $fImg }}" alt="{{ $fStreet }}"
                     class="absolute inset-0 w-full h-full object-cover">
            @else
                <div class="absolute inset-0 bg-[#1b2d6b]"></div>
            @endif

            {{-- Subtle dark wash top-left only, so text is readable --}}
            <div class="absolute inset-0 bg-gradient-to-br from-black/50 via-black/10 to-transparent"></div>

            {{-- Address block: top-left --}}
            <div class="absolute top-5 left-5 z-10">
                <div class="text-[10px] font-bold tracking-widest uppercase text-white/70 mb-1">Featured</div>
                <h1 class="text-lg font-bold text-white leading-snug drop-shadow">{{ $fStreet }}</h1>
                <div class="text-white/80 text-sm">{{ $fCity }}</div>
                @if($fPrice)
                    <div class="text-white font-bold text-base mt-1 drop-shadow">{{ $fPrice }}</div>
                @endif
            </div>

            {{-- Agent pill: bottom-left --}}
            <div class="absolute bottom-5 left-5 z-10">
                <div class="flex items-center gap-3 bg-black/45 backdrop-blur-sm rounded-xl px-4 py-2.5">
                    @if($fAgt)
                        <img src="{{ $fAgt }}" alt="{{ $featured->theAgent->agtFullName }}"
                             class="h-12 w-auto rounded-lg object-cover shrink-0">
                    @endif
                    <div>
                        <div class="text-white font-semibold text-sm leading-snug">{{ $featured->theAgent->agtFullName }}</div>
                        <div class="text-white/65 text-xs">{{ $featured->theOffice->officeName ?? '' }}</div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Right: search panel --}}
        <div class="bg-white lg:w-[38%] flex flex-col justify-center p-8 lg:p-10">
            <div class="text-[10px] font-bold tracking-widest uppercase text-slate-400 mb-1">Browse Listings</div>
            <h2 class="text-2xl font-bold text-[#1b2d6b] mb-2">Search Homes</h2>
            <p class="text-sm text-slate-500 mb-6 leading-relaxed">Find listings by address, city, zip code, or agent name.</p>
            <form method="GET" action="">
                <div class="flex flex-col gap-3">
                    <input
                        type="text"
                        name="q"
                        value="{{ $searchValue }}"
                        placeholder="Address, city, zip, agent…"
                        class="w-full border border-slate-200 bg-slate-50 rounded-full px-5 py-3 text-sm text-slate-700 placeholder-slate-400 outline-none focus:border-[#1b2d6b] focus:ring-2 focus:ring-[#1b2d6b]/10 transition"
                    >
                    <button type="submit"
                            class="w-full bg-[#1b2d6b] hover:bg-[#243d8f] text-white font-bold text-sm py-3 rounded-full transition-colors">
                        Search Listings
                    </button>
                </div>
            </form>
        </div>

    </section>



    @endif

    {{-- ══ CONTENT GRID ════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-[1fr_300px] gap-8">


        {{-- ── LISTINGS COLUMN ───────────────────────────────────────────── --}}
        <div>

            {{-- Section heading + pagination inline --}}
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-[#1b2d6b]">Latest Listings</h2>
                {{ $paginator->withQueryString()->links() }}
            </div>

            {{-- Listing cards --}}
            <div class="space-y-4">
            @foreach($listings as $the)
            @php
                $img    = $listingImg($the);
                $agt    = $agentImg($the);
                $price  = $priceLabel($the);
                $url    = "https://realtyrepublic.com/homedetails/{$the->url_slug}";
                $street = $the->xFullStreet;
                $city   = trim("{$the->xCity} {$the->xState} {$the->xxZip}");
            @endphp

            <a href="{{ $url }}" target="_blank"
               class="flex bg-white rounded-2xl overflow-hidden shadow-sm border border-slate-200/80 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group">

                {{-- Property photo --}}
                @if($img)
                    <img src="{{ $img }}" alt="{{ $street }}"
                         class="w-72 min-w-[18rem] self-stretch object-cover group-hover:scale-[1.02] transition-transform duration-300 shrink-0">
                @else
                    <div class="w-72 min-w-[18rem] self-stretch bg-[#1b2d6b]/8 flex items-center justify-center shrink-0 min-h-[180px]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-[#1b2d6b]/20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                @endif

                {{-- Info --}}
                <div class="flex flex-col justify-between flex-1 px-6 py-5">

                    <div>
                        <div class="text-[17px] font-bold text-[#1b2d6b] leading-snug">{{ $street }}</div>
                        <div class="text-sm text-slate-500 mt-0.5">{{ $city }}</div>
                        @if($price)
                            <div class="mt-2 text-xl font-bold text-slate-800">{{ $price }}</div>
                        @endif
                    </div>

                    {{-- Agent --}}
                    <div class="flex items-center gap-3 pt-4 border-t border-slate-100 mt-4">
                        @if($agt)
                            <img src="{{ $agt }}" alt="{{ $the->theAgent->agtFullName }}"
                                 class="h-16 w-auto rounded-xl object-cover border border-slate-200 shadow-sm shrink-0">
                        @else
                            <div class="h-16 w-14 rounded-xl bg-slate-100 flex items-center justify-center shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                        @endif
                        <div>
                            <div class="text-[10px] font-bold tracking-widest uppercase text-slate-400 mb-0.5">Agent</div>
                            <div class="text-sm font-semibold text-slate-700 leading-snug">
                                {{ $the->theAgent->agtFullName }}
                            </div>
                            <div class="text-xs text-slate-400 mt-0.5">
                                {{ $the->theOffice->officeName ?? '' }}
                            </div>
                        </div>
                    </div>

                </div>
            </a>
            @endforeach
            </div>

            <div class="mt-8">
                {{ $paginator->withQueryString()->links() }}
            </div>
        </div>


        {{-- ── SIDEBAR ────────────────────────────────────────────────────── --}}
        <aside class="space-y-5">

            {{-- Free flyer CTA — matches the site's navy style --}}
            <div class="bg-[#1b2d6b] rounded-2xl p-7 text-white shadow-lg">
                <div class="text-[10px] font-bold tracking-widest uppercase text-white/50 mb-2">
                    Flyer Creation
                </div>
                <div class="text-2xl font-bold leading-snug mb-2">
                    Maximize Exposure<br>On Your Listing!
                </div>
                <p class="text-white/65 text-sm leading-relaxed mb-5">
                    Easily create professional real estate e-flyers with a free website for you &amp; your property.
                </p>
                <input
                    type="email"
                    placeholder="Your email address"
                    class="w-full bg-white/10 border border-white/20 rounded-full px-4 py-2.5 text-sm text-white placeholder-white/40 outline-none focus:bg-white/15 transition-colors mb-3"
                >
                <button class="w-full bg-white hover:bg-slate-100 text-[#1b2d6b] font-bold text-sm py-3 rounded-full transition-colors">
                    Create FREE Flyer!
                </button>
                <p class="text-center text-white/40 text-[11px] mt-2">No credit card required · Launch in minutes</p>
            </div>
            
        </aside>

    </div>

</div>
</main>

@include('public.layout.footer')

</body>