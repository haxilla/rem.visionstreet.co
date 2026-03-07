{{-- MOST VIEWED / POPULAR LISTINGS + INLINE SIGNUP FORM --}}
@php
    $topViewedTitle = $topViewedTitle ?? "Today's <span class='text-[#214e9b]'>TOP VIEWED</span>";
    $topViewedItems = ($mostViews ?? collect())->take(4);
    $sectionMax     = $sectionMax ?? '1600px';
    $brandBlue      = $brandBlue ?? '#214e9b';
@endphp

<div class="mx-auto w-full max-w-screen-2xl px-4 sm:px-6 lg:px-10" style="max-width: {{ $sectionMax }};">

    {{-- Main layout --}}
    <div class="mt-10 grid grid-cols-1 xl:grid-cols-[1.08fr_.92fr] gap-8 xl:gap-10 items-stretch">

        {{-- LEFT: Larger image / cleaner listing rows --}}
        <div class="rounded-[28px] bg-white shadow-[0_12px_34px_rgba(0,0,0,.05)] ring-1 ring-black/5 overflow-hidden">
            <div class="border-b border-[#e7e9ef] px-6 py-5 sm:px-8">
                <div class="text-[13px] font-semibold uppercase tracking-[0.16em] text-gray-500">
                    Popular Listings
                </div>
                {{-- Header --}}
                <div class="text-center max-w-4xl mx-auto">
                    <div class="flex justify-center">
                        <i class="ti-bookmark text-[28px] leading-none" style="color: {{ $brandBlue }};"></i>
                    </div>

                    <h2 class="font-display mt-2 text-[30px] sm:text-[42px] font-medium leading-none text-[#1c1d22]">
                        {!! $topViewedTitle !!}
                    </h2>

                    <div class="mt-5 text-[15px] sm:text-[17px] leading-7 text-gray-600 max-w-3xl mx-auto">
                        See the most popular listings on Realty Emails today. From eye-catching homes to standout presentations, these are the listings attracting the most attention on the platform right now.
                    </div>
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
                        <div class="grid grid-cols-1 md:grid-cols-[250px_1fr] gap-5 md:gap-6 items-start">

                            {{-- Larger photo --}}
                            <div class="overflow-hidden rounded-[15px] bg-[#e8e8ec] h-[165px] sm:h-[185px] md:h-[175px] lg:h-[190px]">
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
        <div class="relative overflow-hidden rounded-[30px] min-h-[760px] lg:min-h-[820px] text-white shadow-[0_20px_50px_rgba(17,31,61,.22)]">

            {{-- Background image --}}
            <div
                class="absolute inset-0 bg-cover bg-center"
                style="background-image: url('{{ asset('images/twilight_realty_emails_promo.jpg') }}');"
            ></div>

            {{-- Dark luxury overlay --}}
            <div class="absolute inset-0 bg-[linear-gradient(135deg,rgba(10,20,48,.88)_0%,rgba(24,43,84,.80)_38%,rgba(23,40,74,.62)_62%,rgba(15,24,45,.78)_100%)]"></div>

            {{-- Soft vignette --}}
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(255,255,255,.10),transparent_24%),radial-gradient(circle_at_bottom_right,rgba(255,255,255,.08),transparent_24%)]"></div>

            {{-- Content --}}
            <div class="relative z-10 flex h-full flex-col justify-between px-7 py-8 sm:px-10 sm:py-10 lg:px-12 lg:py-12">

                {{-- Top copy --}}
                <div class="max-w-[540px]">
                    <div class="inline-flex items-center rounded-full border border-white/15 bg-white/10 px-4 py-1.5 text-[12px] font-semibold uppercase tracking-[0.16em] text-white/85 backdrop-blur-sm">
                        Limited Time Offering
                    </div>

                    <h3 class="font-display mt-6 text-[38px] sm:text-[48px] lg:text-[56px] leading-[0.98] tracking-tight text-white">
                        One Price.<br>
                        All Your Listings.
                    </h3>

                    <div class="mt-5 text-[22px] sm:text-[28px] font-medium leading-tight text-[#f0d28a]">
                        3 Months for $99
                    </div>

                    <p class="mt-6 max-w-[500px] text-[16px] sm:text-[18px] leading-8 text-white/88">
                        Advertise every listing you have for one low price. Each listing may be sent once, with free resends available after 30 days upon request.
                    </p>

                    <div class="mt-7 space-y-3 text-[15px] sm:text-[16px] text-white/84">
                        <div class="flex items-start gap-3">
                            <span class="mt-[3px] h-2.5 w-2.5 rounded-full bg-[#f0d28a]"></span>
                            <span>No per-listing upcharges</span>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="mt-[3px] h-2.5 w-2.5 rounded-full bg-[#f0d28a]"></span>
                            <span>Clean, professional flyer exposure for multiple properties</span>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="mt-[3px] h-2.5 w-2.5 rounded-full bg-[#f0d28a]"></span>
                            <span>Simple pricing designed for active agents and brokers</span>
                        </div>
                    </div>
                </div>

                {{-- Form card --}}
                <div class="mt-10 w-full max-w-[560px] rounded-[28px] border border-white/14 bg-[rgba(13,24,50,.58)] p-5 sm:p-7 backdrop-blur-md shadow-[0_18px_40px_rgba(0,0,0,.22)]">
                    <div class="text-[12px] font-semibold uppercase tracking-[0.16em] text-white/65">
                        Get Started
                    </div>

                    <div class="mt-2 text-[24px] sm:text-[30px] font-semibold leading-tight text-white">
                        Join Realty Emails Today and Promote Your Listings
                    </div>

                    <form class="mt-6 grid grid-cols-1 gap-4" method="post" action="#">
                        @csrf
                        <div>
                            <label class="mb-1.5 block text-[13px] font-medium text-white/80">Your Name</label>
                            <input
                                type="text"
                                name="name"
                                class="w-full rounded-2xl border border-white/12 bg-white/96 px-4 py-3 text-[15px] text-gray-900 placeholder:text-gray-400 outline-none"
                                placeholder="Your name"
                            >
                        </div>

                        <div>
                            <label class="mb-1.5 block text-[13px] font-medium text-white/80">Your Email</label>
                            <input
                                type="email"
                                name="email"
                                class="w-full rounded-2xl border border-white/12 bg-white/96 px-4 py-3 text-[15px] text-gray-900 placeholder:text-gray-400 outline-none"
                                placeholder="you@example.com"
                            >
                        </div>

                        <div>
                            <label class="mb-1.5 block text-[13px] font-medium text-white/80">Brokerage Name</label>
                            <input
                                type="text"
                                name="brokerage_name"
                                class="w-full rounded-2xl border border-white/12 bg-white/96 px-4 py-3 text-[15px] text-gray-900 placeholder:text-gray-400 outline-none"
                                placeholder="Your brokerage"
                            >
                        </div>

                        <div class="pt-2">
                            <button
                                type="submit"
                                class="inline-flex w-full items-center justify-center rounded-full bg-[#f0d28a] px-8 py-3.5 text-[14px] font-semibold tracking-[0.08em] text-[#1d2f5f] shadow-lg transition hover:brightness-105"
                            >
                                SUBMIT DETAILS
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>

    </div>
</div> 