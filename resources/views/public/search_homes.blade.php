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

<body data-section="admin" class="relative min-h-screen bg-slate-100 font-sans text-slate-800 postgres">

@include('public.layout.nav')

<main class="transition-all duration-300 min-h-screen pt-24" :class="collapsed ? 'ml-20' : 'ml-64'">
<div class="px-5 lg:px-10 py-8 max-w-[1400px] mx-auto">


    {{-- ══ HERO ══════════════════════════════════════════════════════════ --}}
    @if($featured)
    @php
        $fImg    = $listingImg($featured);
        $fAgt    = $agentImg($featured);
        $fPrice  = $priceLabel($featured);
        $fURL    = "https://realtyrepublic.com/homedetails/{$featured->url_slug}";
        $fStreet = $featured->xFullStreet;
        $fCity   = trim("{$featured->xCity} {$featured->xState} {$featured->xxZip}");
    @endphp

    {{-- Hero card: photo left, details right --}}
    <section class="bg-white rounded-3xl overflow-hidden shadow-xl flex flex-col lg:flex-row mb-6">

        {{-- Left: large property photo --}}
        <div class="lg:w-2/3 shrink-0">
            @if($fImg)
                <img src="{{ $fImg }}" alt="{{ $fStreet }}"
                     class="w-full h-72 lg:h-full object-cover">
            @else
                <div class="w-full h-72 lg:h-full bg-gradient-to-br from-slate-200 to-slate-300 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            @endif
        </div>

        {{-- Right: address, price, agent --}}
        <div class="flex flex-col justify-between p-8 lg:p-10 lg:w-1/3">

            <div>
                <span class="text-[10px] font-bold tracking-[0.2em] uppercase text-slate-400">Featured Listing</span>

                <h1 class="mt-3 text-2xl lg:text-3xl font-bold text-slate-800 leading-snug">
                    {{ $fStreet }}
                </h1>
                <div class="mt-1 text-slate-500 text-base">{{ $fCity }}</div>

                @if($fPrice)
                    <div class="mt-4 text-3xl font-bold text-slate-900">{{ $fPrice }}</div>
                @endif

                <a href="{{ $fURL }}" target="_blank"
                   class="inline-flex items-center gap-2 mt-6 bg-slate-800 hover:bg-slate-700 text-white font-semibold text-sm px-6 py-3 rounded-full transition-colors shadow">
                    View Listing
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>

            {{-- Agent --}}
            <div class="mt-8 pt-6 border-t border-slate-100 flex items-center gap-4">
                @if($fAgt)
                    <img src="{{ $fAgt }}" alt="{{ $featured->theAgent->agtFullName }}"
                         class="h-24 w-auto rounded-xl object-cover border border-slate-200 shadow-sm shrink-0">
                @else
                    <div class="h-24 w-20 rounded-xl bg-slate-100 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                @endif
                <div>
                    <div class="text-[10px] font-bold tracking-widest uppercase text-slate-400 mb-1">Listed by</div>
                    <div class="font-semibold text-slate-800 text-sm leading-snug">
                        {{ $featured->theAgent->agtFullName }}
                    </div>
                    <div class="text-xs text-slate-500 mt-0.5">
                        {{ $featured->theOffice->officeName ?? '' }}
                    </div>
                </div>
            </div>

        </div>
    </section>
    @endif


    {{-- ══ SEARCH BAR (below hero) ════════════════════════════════════════ --}}
    <div class="mb-8">
        <form method="GET" action="">
            <div class="flex rounded-2xl overflow-hidden shadow-sm bg-white border border-slate-200">
                <input
                    type="text"
                    name="q"
                    value="{{ $searchValue }}"
                    placeholder="Search address, city, zip, agent…"
                    class="flex-1 px-5 py-4 text-sm text-slate-700 placeholder-slate-400 outline-none"
                >
                <button type="submit"
                        class="bg-slate-800 hover:bg-slate-700 text-white font-semibold text-sm px-7 transition-colors">
                    Search
                </button>
            </div>
        </form>
    </div>


    {{-- ══ CONTENT GRID ════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-[1fr_300px] gap-8">


        {{-- ── LISTINGS ──────────────────────────────────────────────────── --}}
        <div>
            <div class="flex items-baseline gap-3 mb-5">
                <h2 class="text-2xl font-bold text-slate-800">Latest Listings</h2>
                <span class="text-xs font-semibold tracking-widest uppercase text-slate-400">
                    {{ number_format($paginator->total()) }} properties
                </span>
            </div>

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
               class="flex bg-white rounded-2xl overflow-hidden shadow-sm border border-slate-200/80 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 group">

                {{-- Property photo: fills full card height, wide rectangle --}}
                @if($img)
                    <img src="{{ $img }}" alt="{{ $street }}"
                         class="w-72 min-w-[18rem] self-stretch object-cover group-hover:scale-[1.02] transition-transform duration-300 shrink-0">
                @else
                    <div class="w-72 min-w-[18rem] self-stretch bg-slate-100 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                @endif

                {{-- Info: address + price top, agent bottom --}}
                <div class="flex flex-col justify-between flex-1 px-6 py-5">

                    <div>
                        <div class="text-[17px] font-bold text-slate-800 leading-snug">{{ $street }}</div>
                        <div class="text-sm text-slate-500 mt-0.5">{{ $city }}</div>
                        @if($price)
                            <div class="mt-2 text-xl font-bold text-slate-900">{{ $price }}</div>
                        @endif
                    </div>

                    {{-- Agent below address --}}
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

            {{-- Free trial CTA --}}
            <div class="bg-gradient-to-br from-blue-900 to-blue-700 rounded-2xl p-6 text-white shadow-lg">
                <div class="text-xs font-bold tracking-widest uppercase text-blue-300 mb-2">Get Started</div>
                <div class="text-xl font-bold mb-1">Create a Free Flyer</div>
                <p class="text-blue-200 text-sm leading-relaxed mb-5">
                    Build a professional listing flyer in minutes. No credit card required.
                </p>
                <input
                    type="email"
                    placeholder="Your email address"
                    class="w-full bg-white/15 border border-white/25 rounded-xl px-4 py-2.5 text-sm text-white placeholder-blue-300 outline-none focus:bg-white/20 transition-colors mb-3"
                >
                <button class="w-full bg-white hover:bg-slate-100 text-blue-900 font-bold text-sm py-3 rounded-xl transition-colors">
                    Start Free →
                </button>
            </div>

            {{-- Quick search --}}
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80">
                <div class="text-xs font-bold tracking-widest uppercase text-slate-400 mb-1">Search</div>
                <div class="text-lg font-bold text-slate-800 mb-4">Find a Listing</div>
                <form method="GET" action="">
                    <input
                        type="text"
                        name="q"
                        value="{{ $searchValue }}"
                        placeholder="City, zip, or agent…"
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-700 placeholder-slate-400 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition mb-3"
                    >
                    <button type="submit"
                            class="w-full bg-slate-800 hover:bg-slate-700 text-white font-semibold text-sm py-3 rounded-xl transition-colors">
                        Search Listings
                    </button>
                </form>
            </div>

            {{-- Stats --}}
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80">
                <div class="text-xs font-bold tracking-widest uppercase text-slate-400 mb-1">Overview</div>
                <div class="text-lg font-bold text-slate-800 mb-4">By the Numbers</div>
                <div class="grid grid-cols-2 gap-3">
                    @foreach([
                        ['num' => number_format($paginator->total()), 'label' => 'Listings'],
                        ['num' => '50+',  'label' => 'Markets'],
                        ['num' => '24/7', 'label' => 'Live Data'],
                        ['num' => 'Free', 'label' => 'To Start'],
                    ] as $stat)
                    <div class="bg-slate-50 rounded-xl p-3 text-center border border-slate-100">
                        <div class="text-2xl font-bold text-blue-900">{{ $stat['num'] }}</div>
                        <div class="text-[10px] font-semibold tracking-widest uppercase text-slate-400 mt-0.5">
                            {{ $stat['label'] }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

        </aside>

    </div>

</div>
</main>

@include('public.layout.footer')

</body>