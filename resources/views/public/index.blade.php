{{-- resources/views/home/hero.blade.php --}}
<!DOCTYPE html>
<html lang="en" class="h-full bg-white text-gray-900">
@include('public.layout.head');

<body class="min-h-screen bg-white pt-[50px]">
  @include('public.layout.nav')
  <section>
    @include('public.includes.hero_card')
  </section>
  <section>
    @include('public.includes.features_section')
  </section>
  <section class="w-full bg-[#f5f5f7] py-10 lg:py-14">
    @include('public.includes.top_views')
  </section>
{{-- MOST VIEWED / POPULAR LISTINGS + INLINE SIGNUP FORM --}}
@php
    $topViewedTitle = $topViewedTitle ?? "Today's <span class='text-[#214e9b]'>TOP VIEWED</span>";
    $topViewedItems = ($mostViews ?? collect())->take(4);
    $sectionMax     = $sectionMax ?? '1600px';
    $brandBlue      = $brandBlue ?? '#214e9b';
@endphp

<section class="w-full bg-[#f5f5f7] py-12 lg:py-16">
    <div class="mx-auto w-full max-w-screen-2xl px-4 sm:px-6 lg:px-10" style="max-width: {{ $sectionMax }};">

        {{-- Header --}}
        <div class="text-center">
            <div class="flex justify-center">
                <i class="ti-bookmark text-[28px] leading-none" style="color: {{ $brandBlue }};"></i>
            </div>

            <h2 class="font-display mt-2 text-[30px] sm:text-[42px] font-medium leading-none text-[#1c1d22]">
                {!! $topViewedTitle !!}
            </h2>

            <div class="mx-auto mt-5 h-px w-full bg-[#ddddE5]"></div>
        </div>

        {{-- Main layout --}}
        <div class="mt-10 grid grid-cols-1 xl:grid-cols-[1.08fr_.92fr] gap-8 xl:gap-10 items-stretch">

            {{-- LEFT: Larger image / cleaner listing rows --}}
            <div class="rounded-[28px] bg-white shadow-[0_12px_34px_rgba(0,0,0,.05)] ring-1 ring-black/5 overflow-hidden">
                <div class="border-b border-[#e7e9ef] px-6 py-5 sm:px-8">
                    <div class="text-[13px] font-semibold uppercase tracking-[0.16em] text-gray-500">
                        Popular Listings
                    </div>
                    <div class="mt-1 text-[20px] sm:text-[24px] font-semibold text-[#1d2433]">
                        Most viewed properties right now
                    </div>
                </div>

                <div class="divide-y divide-[#edf0f5]">
                    @foreach($topViewedItems as $index => $the)
                        @php
                            $photoObj = $the->thePhotos->where('def','=','1')->first();
                            $photo    = $photoObj?->photoName;

                            $listingImg = null;
                            if ($photo && !empty($the->theMeta?->zipDir) && !empty($the->theMeta?->mlsDir)) {
                                $listingImg = "https://realtyrepublic.com/hqphotos/{$the->theMeta->zipDir}/{$the->theMeta->mlsDir}/{$photo}";
                            }

                            $agentImg = null;
                            if (!empty($the->theAgent?->agtPhoto) && !empty($the->theAgent?->theAgentCleanup?->newRemID)) {
                                $agentImg = "https://realtyrepublic.com/agentPhotos/{$the->theAgent->theAgentCleanup->newRemID}/{$the->theAgent->agtPhoto}";
                            } elseif (!empty($the->theAgent?->agtPhoto) && !empty($the->theOffice?->officeID)) {
                                $agentImg = "https://realtyemails.com/HQoffice/{$the->theOffice->officeID}/{$the->theAgent->agtPhoto}";
                            }

                            $street     = $the->xFullStreet ?? '';
                            $cityLine   = trim(($the->xCity ?? '') . ' ' . ($the->xState ?? '') . ' ' . ($the->xxZip ?? ''));
                            $agentName  = $the->theAgent->agtFullName ?? '';
                            $officeName = $the->theOffice->officeName ?? '';
                            $beds       = $the->xBeds ?? null;
                            $baths      = $the->xBaths ?? null;
                            $sqft       = $the->xSqft ?? $the->xSqFt ?? null;
                            $price      = $the->xPrice ?? $the->xListPrice ?? null;
                            $priceLabel = $price ? '$' . number_format((float) $price) : null;
                        @endphp

                        <article class="group px-5 py-5 sm:px-6 sm:py-6">
                            <div class="grid grid-cols-1 md:grid-cols-[220px_1fr] gap-5 md:gap-6 items-start">

                                {{-- Larger photo --}}
                                <div class="overflow-hidden rounded-[22px] bg-[#e8e8ec] h-[150px] sm:h-[170px] md:h-[160px] lg:h-[175px]">
                                    @if($listingImg)
                                        <img
                                            src="{{ $listingImg }}"
                                            alt="{{ $street }}"
                                            class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.03]"
                                        >
                                    @endif
                                </div>

                                {{-- Content --}}
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-3">
                                        <div class="text-[11px] font-semibold uppercase tracking-[0.16em] text-gray-400">
                                            #{{ $index + 1 }} Trending
                                        </div>

                                        @if($priceLabel)
                                            <span
                                                class="inline-flex rounded-full px-3 py-1 text-[11px] font-semibold text-white"
                                                style="background-color: {{ $brandBlue }};"
                                            >
                                                {{ $priceLabel }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="mt-3">
                                        <a href="#" class="block text-[24px] sm:text-[28px] font-semibold leading-tight text-[#214e9b] hover:opacity-80 transition">
                                            {{ $street }}
                                        </a>
                                    </div>

                                    <div class="mt-2 text-[14px] sm:text-[15px] text-gray-600">
                                        {{ $cityLine }}
                                    </div>

                                    @if($beds || $baths || $sqft)
                                        <div class="mt-3 flex flex-wrap items-center gap-x-3 gap-y-1 text-[13px] text-gray-600">
                                            @if($beds)<span>{{ $beds }} Beds</span>@endif
                                            @if($baths)<span>• {{ $baths }} Baths</span>@endif
                                            @if($sqft)<span>• {{ number_format((float) $sqft) }} Sqft</span>@endif
                                        </div>
                                    @endif

                                    <div class="mt-5 flex items-start gap-3">
                                        @if($agentImg)
                                            <img
                                                src="{{ $agentImg }}"
                                                alt="{{ $agentName }}"
                                                class="h-16 w-auto max-w-none rounded-xl object-cover ring-1 ring-black/10"
                                            >
                                        @endif

                                        <div class="min-w-0 pt-0.5">
                                            <div class="text-[12px] text-gray-500">Listed by:</div>
                                            <div class="mt-0.5 text-[17px] font-medium leading-tight text-[#214e9b]">
                                                {{ $agentName }}
                                            </div>
                                            <div class="mt-0.5 text-[14px] text-gray-600">
                                                {{ $officeName }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </article>
                    @endforeach
                </div>
            </div>

            {{-- RIGHT: Explanation + inline form --}}
            <div class="relative overflow-hidden rounded-[30px] bg-gradient-to-br from-[#27457d] via-[#213965] to-[#17284a] text-white shadow-[0_20px_50px_rgba(17,31,61,.26)]">
                <div class="absolute -right-16 top-[-40px] h-56 w-56 rounded-full bg-white/10 blur-3xl"></div>
                <div class="absolute left-[-40px] bottom-[-60px] h-64 w-64 rounded-full bg-[#6d8ed6]/20 blur-3xl"></div>
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,.16),transparent_28%),radial-gradient(circle_at_bottom_left,rgba(255,255,255,.12),transparent_26%)]"></div>

                <div class="relative h-full px-7 py-8 sm:px-9 sm:py-10 lg:px-10 lg:py-11">
                    <div class="inline-flex items-center rounded-full border border-white/15 bg-white/10 px-4 py-1.5 text-[12px] font-semibold uppercase tracking-[0.16em] text-white/85 backdrop-blur-sm">
                        Live Listing Activity
                    </div>

                    <h3 class="font-display mt-5 text-[34px] sm:text-[42px] leading-[1.04] tracking-tight">
                        See which flyers are getting the most attention.
                    </h3>

                    <p class="mt-5 max-w-xl text-[15px] sm:text-[16px] leading-7 text-white/82">
                        We track how many views each flyer receives and how often it gets shared, then use that activity to highlight the most popular listings on the site right now.
                    </p>

                    <div class="mt-7 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="rounded-2xl border border-white/12 bg-white/8 p-4 backdrop-blur-sm">
                            <div class="text-[12px] uppercase tracking-[0.14em] text-white/60">
                                What gets tracked
                            </div>
                            <div class="mt-2 text-[17px] font-semibold">
                                Views, shares, and listing engagement
                            </div>
                        </div>

                        <div class="rounded-2xl border border-white/12 bg-white/8 p-4 backdrop-blur-sm">
                            <div class="text-[12px] uppercase tracking-[0.14em] text-white/60">
                                Why it matters
                            </div>
                            <div class="mt-2 text-[17px] font-semibold">
                                More visibility means more momentum
                            </div>
                        </div>
                    </div>

                    {{-- Inline form --}}
                    <div class="mt-8 rounded-[24px] border border-white/12 bg-white/10 p-5 sm:p-6 backdrop-blur-md shadow-[0_10px_30px_rgba(0,0,0,.12)]">
                        <div class="text-[13px] font-semibold uppercase tracking-[0.16em] text-white/70">
                            Start Advertising
                        </div>

                        <div class="mt-2 text-[22px] sm:text-[26px] font-semibold leading-tight text-white">
                            Submit your listing details here.
                        </div>

                        <form class="mt-6 space-y-4" method="post" action="#">
                            @csrf

                            <div>
                                <label class="mb-1.5 block text-[13px] font-medium text-white/82">Name</label>
                                <input
                                    type="text"
                                    name="name"
                                    class="w-full rounded-2xl border border-white/12 bg-white/95 px-4 py-3 text-[15px] text-gray-900 placeholder:text-gray-400 outline-none ring-0 focus:border-white/30"
                                    placeholder="Your name"
                                >
                            </div>

                            <div>
                                <label class="mb-1.5 block text-[13px] font-medium text-white/82">Email</label>
                                <input
                                    type="email"
                                    name="email"
                                    class="w-full rounded-2xl border border-white/12 bg-white/95 px-4 py-3 text-[15px] text-gray-900 placeholder:text-gray-400 outline-none ring-0 focus:border-white/30"
                                    placeholder="you@example.com"
                                >
                            </div>

                            <div>
                                <label class="mb-1.5 block text-[13px] font-medium text-white/82">Brokerage Name</label>
                                <input
                                    type="text"
                                    name="brokerage_name"
                                    class="w-full rounded-2xl border border-white/12 bg-white/95 px-4 py-3 text-[15px] text-gray-900 placeholder:text-gray-400 outline-none ring-0 focus:border-white/30"
                                    placeholder="Your brokerage"
                                >
                            </div>

                            <div>
                                <label class="mb-1.5 block text-[13px] font-medium text-white/82">Address of listing to advertise</label>
                                <textarea
                                    name="listing_address"
                                    rows="3"
                                    class="w-full rounded-2xl border border-white/12 bg-white/95 px-4 py-3 text-[15px] text-gray-900 placeholder:text-gray-400 outline-none ring-0 focus:border-white/30 resize-none"
                                    placeholder="Enter the property address"
                                ></textarea>
                            </div>

                            <div class="pt-2">
                                <button
                                    type="submit"
                                    class="inline-flex w-full items-center justify-center rounded-full bg-white px-8 py-3.5 text-[14px] font-semibold tracking-[0.08em] text-[#1e3d78] shadow-lg transition hover:scale-[1.01] hover:bg-[#f4f7ff]"
                                >
                                    SIGN UP NOW
                                </button>
                            </div>
                        </form>

                        <div class="mt-4 text-[13px] text-white/68">
                            Start creating trackable flyers and see what gets results.
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>  

</body>
</html>