@include('public.layout.head')

@php
$paginator   = $data['searchAll'];
$pageItems   = collect($paginator->items());
$featured    = $pageItems->first();
$listings    = $pageItems->slice(1);
$searchValue = request('q', '');
@endphp

<body data-section="admin" class="linkcheck relative min-h-screen bg-[#eef2f7] font-sans text-slate-800 postgres">

```
@include('public.layout.nav')

<main class="transition-all duration-300 min-h-screen pt-24 relative" :class="collapsed ? 'ml-20' : 'ml-64'">
    <div class="mx-5 lg:mx-10">
        <div class="mx-auto max-w-[1450px]">

            <section class="relative overflow-hidden rounded-[34px] bg-gradient-to-br from-[#16336f] via-[#214893] to-[#3e67c8] shadow-[0_30px_80px_rgba(25,48,109,.22)]">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,.18),transparent_28%),radial-gradient(circle_at_bottom_left,rgba(255,255,255,.10),transparent_24%)]"></div>
                <div class="absolute -left-24 top-10 h-64 w-64 rounded-full bg-white/8 blur-3xl"></div>
                <div class="absolute right-10 bottom-0 h-48 w-48 rounded-full bg-white/10 blur-3xl"></div>

                <div class="relative grid grid-cols-1 xl:grid-cols-[560px_minmax(0,1fr)] gap-8 items-stretch px-8 py-10">

                    <div class="flex flex-col justify-center text-white xl:pr-2">
                        <div class="inline-flex items-center self-start rounded-full border border-white/20 bg-white/10 px-4 py-2 text-[11px] uppercase tracking-[0.25em] backdrop-blur-sm">
                            Slide Show Gallery
                        </div>

                        <h1 class="mt-6 font-serif text-[52px] leading-[0.95]">
                            Discover Homes
                            <br>
                            With Impact
                        </h1>

                        <p class="mt-5 max-w-[460px] text-[16px] leading-8 text-white/85">
                            Browse recent Realty Emails listings in a richer gallery layout with a featured property, larger imagery, and a fast search experience.
                        </p>

                        <form method="GET" action="" class="mt-8 max-w-[560px]">
                            <div class="overflow-hidden rounded-[22px] bg-white shadow-[0_18px_44px_rgba(0,0,0,.16)]">
                                <div class="flex flex-col md:flex-row md:items-center">
                                    <div class="flex min-w-0 flex-1 items-center px-5 py-4">
                                        <svg class="mr-3 h-5 w-5 shrink-0 text-[#3659a8]" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M8.5 3a5.5 5.5 0 1 0 3.471 9.768l3.63 3.631a.75.75 0 1 0 1.06-1.06l-3.63-3.631A5.5 5.5 0 0 0 8.5 3ZM4.5 8.5a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z" clip-rule="evenodd"/>
                                        </svg>

                                        <input
                                            type="text"
                                            name="q"
                                            value="{{ $searchValue }}"
                                            placeholder="Search address, city, zip, agent..."
                                            class="w-full border-0 bg-transparent p-0 text-[15px] text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-0"
                                        >
                                    </div>

                                    <div class="px-4 pb-4 md:px-4 md:pb-0">
                                        <button
                                            type="submit"
                                            class="inline-flex w-full items-center justify-center rounded-full bg-[#244a98] px-6 py-3 text-[14px] font-semibold text-white shadow-[0_12px_26px_rgba(36,74,152,.28)] transition hover:bg-[#1b3f88] md:w-auto"
                                        >
                                            Search Listings
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="mt-4 flex flex-wrap gap-2 text-[12px] text-white/72">
                            <span class="rounded-full bg-white/10 px-3 py-1 ring-1 ring-white/10">Phoenix</span>
                            <span class="rounded-full bg-white/10 px-3 py-1 ring-1 ring-white/10">Scottsdale</span>
                            <span class="rounded-full bg-white/10 px-3 py-1 ring-1 ring-white/10">Mesa</span>
                            <span class="rounded-full bg-white/10 px-3 py-1 ring-1 ring-white/10">Tempe</span>
                            <span class="rounded-full bg-white/10 px-3 py-1 ring-1 ring-white/10">Luxury</span>
                        </div>

                        <div class="mt-7 flex flex-wrap gap-3">
                            <a href="#gallery" class="inline-flex items-center justify-center rounded-full bg-white px-5 py-3 text-[14px] font-semibold text-[#214893] shadow-[0_14px_34px_rgba(0,0,0,.15)]">
                                Browse Gallery
                            </a>

                            <a href="/pricing" class="inline-flex items-center justify-center rounded-full border border-white/30 bg-white/10 px-5 py-3 text-[14px] font-semibold text-white backdrop-blur-sm">
                                View Pricing
                            </a>
                        </div>
                    </div>

                    @if($featured)
                        @php
                            $photoObj = $featured->thePhotos->where('def','=','1')->first();
                            $photo    = $photoObj?->photoName;

                            $listingImg = null;
                            if ($photo && $featured->theMeta?->zipDir && $featured->theMeta?->mlsDir) {
                                $listingImg = "https://realtyrepublic.com/hqphotos/{$featured->theMeta->zipDir}/{$featured->theMeta->mlsDir}/{$photo}";
                            }

                            $listingURL = "https://realtyrepublic.com/homedetails/{$featured->url_slug}";

                            $agentImg = null;
                            if (!empty($featured->theAgent?->agtPhoto) && !empty($featured->theAgent?->theAgentCleanup?->newRemID)) {
                                $agentImg = "https://realtyrepublic.com/agentPhotos/{$featured->theAgent->theAgentCleanup->newRemID}/{$featured->theAgent->agtPhoto}";
                            }

                            $street     = $featured->xFullStreet;
                            $cityLine   = trim(($featured->xCity ?? '') . ' ' . ($featured->xState ?? '') . ' ' . ($featured->xxZip ?? ''));
                            $price      = $featured->xPrice ?? $featured->xListPrice;
                            $priceLabel = $price ? '$' . number_format($price) : null;
                            $beds       = $featured->xBeds ?: $featured->xxBeds;
                            $baths      = $featured->xBaths ?: $featured->xxBaths;
                            $sqft       = $featured->xSqft ?: $featured->xxSqft;
                        @endphp

                        <div class="flex items-center">
                            <div class="w-full overflow-hidden rounded-[30px] bg-white shadow-[0_26px_60px_rgba(11,27,68,.28)] ring-1 ring-white/30">
                                <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1.4fr)_320px]">
                                    <a href="{{ $listingURL }}" target="_blank" class="block bg-[#dfe7f6]">
                                        @if($listingImg)
                                            <img
                                                src="{{ $listingImg }}"
                                                alt="{{ $street }}"
                                                class="h-[520px] w-full object-cover"
                                            >
                                        @else
                                            <div class="flex h-[420px] items-center justify-center text-center text-slate-400">
                                                <div>
                                                    <div class="text-[12px] font-semibold uppercase tracking-[0.24em]">No Photo</div>
                                                    <div class="mt-2 text-[14px]">Image unavailable</div>
                                                </div>
                                            </div>
                                        @endif
                                    </a>

                                    <div class="flex flex-col justify-between p-6 lg:p-7">
                                        <div>
                                            <div class="inline-flex rounded-full bg-[#eef3fb] px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-[#5d79b8]">
                                                Featured Listing
                                            </div>

                                            <a href="{{ $listingURL }}" target="_blank" class="mt-4 block text-[28px] font-semibold leading-tight text-[#214e9b] hover:opacity-80">
                                                {{ $street }}
                                            </a>

                                            <div class="mt-2 text-[15px] text-slate-600">
                                                {{ $cityLine }}
                                            </div>

                                            @if($priceLabel)
                                                <div class="mt-4 text-[28px] font-semibold text-slate-900">
                                                    {{ $priceLabel }}
                                                </div>
                                            @endif

                                            <div class="mt-5 flex flex-wrap gap-2">
                                                <span class="rounded-full bg-[#f3f6fc] px-4 py-2 text-[13px] text-slate-700">{{ $beds ?: '—' }} bed</span>
                                                <span class="rounded-full bg-[#f3f6fc] px-4 py-2 text-[13px] text-slate-700">{{ $baths ?: '—' }} bath</span>
                                                <span class="rounded-full bg-[#f3f6fc] px-4 py-2 text-[13px] text-slate-700">{{ !empty($sqft) ? number_format((float) $sqft) : '—' }} sq ft</span>
                                            </div>
                                        </div>

                                        <div class="mt-6 border-t border-slate-200 pt-5">
                                            <div class="flex items-center gap-3">
                                                @if($agentImg)
                                                    <img src="{{ $agentImg }}" class="h-16 w-auto rounded-xl object-cover">
                                                @endif

                                                <div class="min-w-0">
                                                    <div class="font-medium text-slate-900">
                                                        {{ $featured->theAgent->agtFullName }}
                                                    </div>

                                                    <div class="text-sm text-gray-600">
                                                        {{ $featured->theOffice->officeName ?? '' }}
                                                    </div>
                                                </div>
                                            </div>

                                            <a href="{{ $listingURL }}" target="_blank" class="mt-5 inline-flex items-center justify-center rounded-full bg-[#214e9b] px-5 py-3 text-[14px] font-semibold text-white shadow-[0_12px_26px_rgba(33,78,155,.22)]">
                                                View Listing
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>
            </section>

            <div id="gallery" class="mt-10 grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_320px] gap-10">

                <div class="space-y-8">
                    @foreach($listings as $the)
                        @php
                            $photoObj = $the->thePhotos->where('def','=','1')->first();
                            $photo    = $photoObj?->photoName;

                            $listingImg = null;
                            if ($photo && $the->theMeta?->zipDir && $the->theMeta?->mlsDir) {
                                $listingImg = "https://realtyrepublic.com/hqphotos/{$the->theMeta->zipDir}/{$the->theMeta->mlsDir}/{$photo}";
                            }

                            $listingURL = "https://realtyrepublic.com/homedetails/{$the->url_slug}";

                            $agentImg = null;
                            if (!empty($the->theAgent?->agtPhoto) && !empty($the->theAgent?->theAgentCleanup?->newRemID)) {
                                $agentImg = "https://realtyrepublic.com/agentPhotos/{$the->theAgent->theAgentCleanup->newRemID}/{$the->theAgent->agtPhoto}";
                            }

                            $street     = $the->xFullStreet;
                            $cityLine   = trim(($the->xCity ?? '') . ' ' . ($the->xState ?? '') . ' ' . ($the->xxZip ?? ''));
                            $price      = $the->xPrice ?? $the->xListPrice;
                            $priceLabel = $price ? '$' . number_format($price) : null;
                            $beds       = $the->xBeds ?: $the->xxBeds;
                            $baths      = $the->xBaths ?: $the->xxBaths;
                            $sqft       = $the->xSqft ?: $the->xxSqft;
                        @endphp

                        <article class="overflow-hidden rounded-[28px] bg-white shadow-[0_14px_36px_rgba(23,43,99,.08)] ring-1 ring-black/5 transition hover:shadow-[0_24px_54px_rgba(23,43,99,.12)]">
                            <a href="{{ $listingURL }}" target="_blank" class="block">
                                @if($listingImg)
                                    <img
                                        src="{{ $listingImg }}"
                                        alt="{{ $street }}"
                                        class="h-[360px] w-full object-cover"
                                    >
                                @else
                                    <div class="flex h-[360px] items-center justify-center bg-[#dfe7f6] text-center text-slate-400">
                                        <div>
                                            <div class="text-[12px] font-semibold uppercase tracking-[0.24em]">No Photo</div>
                                            <div class="mt-2 text-[14px]">Image unavailable</div>
                                        </div>
                                    </div>
                                @endif
                            </a>

                            <div class="p-6 lg:p-7">
                                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                    <div class="min-w-0">
                                        <a href="{{ $listingURL }}" target="_blank" class="block text-[26px] font-semibold leading-tight text-[#214e9b] hover:opacity-80">
                                            {{ $street }}
                                        </a>

                                        <div class="mt-2 text-[15px] text-slate-600">
                                            {{ $cityLine }}
                                        </div>
                                    </div>

                                    @if($priceLabel)
                                        <div class="shrink-0 rounded-full bg-[#214e9b] px-4 py-2 text-[14px] font-semibold text-white shadow-sm">
                                            {{ $priceLabel }}
                                        </div>
                                    @endif
                                </div>

                                <div class="mt-5 flex flex-wrap gap-2">
                                    <span class="rounded-full bg-[#f3f6fc] px-4 py-2 text-[13px] text-slate-700">{{ $beds ?: '—' }} bed</span>
                                    <span class="rounded-full bg-[#f3f6fc] px-4 py-2 text-[13px] text-slate-700">{{ $baths ?: '—' }} bath</span>
                                    <span class="rounded-full bg-[#f3f6fc] px-4 py-2 text-[13px] text-slate-700">{{ !empty($sqft) ? number_format((float) $sqft) : '—' }} sq ft</span>
                                </div>

                                <div class="mt-6 flex items-center gap-4">
                                    @if($agentImg)
                                        <img src="{{ $agentImg }}" class="h-16 w-auto rounded-xl object-cover">
                                    @endif

                                    <div class="min-w-0">
                                        <div class="font-medium text-slate-900">
                                            {{ $the->theAgent->agtFullName }}
                                        </div>

                                        <div class="text-sm text-gray-600">
                                            {{ $the->theOffice->officeName ?? '' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach

                    <div class="pt-2">
                        {{ $paginator->withQueryString()->links() }}
                    </div>
                </div>



                <aside class="space-y-6">
                    <div class="overflow-hidden rounded-[28px] bg-white shadow-[0_14px_36px_rgba(23,43,99,.08)] ring-1 ring-black/5">
                        <div class="bg-gradient-to-br from-[#214893] to-[#3a66c7] px-6 py-6 text-white">
                            <div class="text-[11px] uppercase tracking-[0.24em] text-white/75">
                                Free Trial Offer
                            </div>

                            <div class="mt-3 font-serif text-[34px] leading-none">
                                Create Your
                                <br>
                                First Flyer Free
                            </div>

                            <p class="mt-4 text-[14px] leading-7 text-white/82">
                                Launch a flyer in minutes and see how it looks before you ever buy credits.
                            </p>
                        </div>

                        <div class="p-6">
                            <form>
                                <label class="text-[12px] font-semibold uppercase tracking-[0.18em] text-[#6b82b7]">
                                    Email Address
                                </label>

                                <input
                                    type="email"
                                    placeholder="you@example.com"
                                    class="mt-2 w-full rounded-[16px] border border-slate-200 px-4 py-3 text-[15px] outline-none transition focus:border-[#214e9b]"
                                >

                                <button class="mt-4 inline-flex w-full items-center justify-center rounded-full bg-[#214e9b] px-5 py-3 text-[14px] font-semibold text-white shadow-[0_12px_26px_rgba(33,78,155,.22)]">
                                    Start Free Trial
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="rounded-[28px] bg-white p-6 shadow-[0_14px_36px_rgba(23,43,99,.08)] ring-1 ring-black/5">
                        <div class="text-[12px] font-semibold uppercase tracking-[0.22em] text-[#6a82b7]">
                            Quick Search
                        </div>

                        <div class="mt-3 text-[26px] font-serif leading-none text-[#214e9b]">
                            {{ $paginator->total() }}
                        </div>

                        <div class="mt-2 text-[14px] text-slate-600">
                            listings found
                        </div>

                        <form method="GET" action="" class="mt-5 space-y-3">
                            <input
                                type="text"
                                name="q"
                                value="{{ $searchValue }}"
                                placeholder="City, zip, or address"
                                class="w-full rounded-[16px] border border-slate-200 px-4 py-3 text-[15px] outline-none transition focus:border-[#214e9b]"
                            >

                            <button class="inline-flex w-full items-center justify-center rounded-full border border-[#cbd8ee] bg-[#f8fbff] px-5 py-3 text-[14px] font-semibold text-[#214e9b]">
                                Search Listings
                            </button>
                        </form>

                        <div class="mt-5 rounded-[18px] bg-[#f5f8fc] px-4 py-4 text-[14px] leading-7 text-slate-600">
                            Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}
                        </div>
                    </div>
                </aside>

            </div>

        </div>
    </div>
</main>

@include('public.layout.footer')

</body>
