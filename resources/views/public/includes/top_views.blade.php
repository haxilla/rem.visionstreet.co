{{-- TOP VIEWED CAROUSEL SECTION --}}
@php
    $topViewedTitle = $topViewedTitle ?? "Today's <span class='text-[#214e9b]'>TOP VIEWED</span>";
    $topViewedItems = $mostViews ?? collect();
    $sectionMax     = $sectionMax ?? '1600px';
    $brandBlue      = $brandBlue ?? '#214e9b';
@endphp

<div class="mx-auto w-full max-w-screen-2xl px-4 sm:px-6 lg:px-10" style="max-width: {{ $sectionMax }};">

    {{-- Header --}}
    <div class="text-center mb-15">
        <div class="flex justify-center">
            <i class="ti-bookmark text-[35px] leading-none" style="color: {{ $brandBlue }};"></i>
        </div>

        <h2 class="font-display mt-2 text-[30px] sm:text-[42px] font-medium leading-none text-[#1c1d22]">
            {!! $topViewedTitle !!}
        </h2>
        <p class="mx-auto mt-4 max-w-xl text-[15px] text-gray-600">
            Check out some of the most popular listings on RealtyEmails right now.
        </p>

    </div>

    <div class="mt-8">

        <div class="swiper topViewedSwiper" data-swiper="top-viewed">

            <div class="swiper-button-prev" aria-label="Previous top viewed listings"></div>
            <div class="swiper-button-next" aria-label="Next top viewed listings"></div>

            <div class="swiper-wrapper">

                @foreach($topViewedItems as $the)
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

                        $beds  = $the->xBeds ?? null;
                        $baths = $the->xBaths ?? null;
                        $sqft  = $the->xSqft ?? $the->xSqFt ?? null;
                        $price = $the->xPrice ?? $the->xListPrice ?? null;

                        $priceLabel = $price ? '$' . number_format((float) $price) : null;
                    @endphp

                    <div class="swiper-slide h-auto">
                        <article class="h-full">

                            {{-- address area --}}
                            <div class="pb-3 text-center">
                                <a href="#" class="block text-[16px] sm:text-[17px] font-medium leading-tight hover:opacity-85 transition" style="color: {{ $brandBlue }};">
                                    {{ $street }}
                                </a>

                                <div class="mt-1 text-[13px] text-gray-600">
                                    {{ $cityLine }}
                                </div>
                            </div>

                            {{-- image band --}}
                            <div class="relative overflow-hidden bg-[#e8e8ec]">
                                <div class="h-[260px] sm:h-[290px] lg:h-[320px] w-full">
                                    @if($listingImg)
                                        <img
                                            src="{{ $listingImg }}"
                                            alt="{{ $street }}"
                                            class="h-full w-full object-cover"
                                        >
                                    @endif
                                </div>

                                @if($priceLabel)
                                    <div class="absolute left-1/2 top-0 z-10 -translate-x-1/2">
                                        <span
                                            class="inline-flex rounded-b-lg px-4 py-1.5 text-[13px] font-semibold text-white shadow-sm"
                                            style="background-color: {{ $brandBlue }};"
                                        >
                                            {{ $priceLabel }}
                                        </span>
                                    </div>
                                @endif

                                @if($beds || $baths || $sqft)
                                    <div class="absolute bottom-3 left-1/2 z-10 -translate-x-1/2">
                                        <div class="flex items-center overflow-hidden rounded bg-white/95 text-[12px] text-gray-700 shadow-sm ring-1 ring-black/5">
                                            @if($beds)
                                                <span class="px-3 py-1">{{ $beds }} Beds</span>
                                            @endif

                                            @if($baths)
                                                <span class="border-l border-gray-300 px-3 py-1">{{ $baths }} Baths</span>
                                            @endif

                                            @if($sqft)
                                                <span class="border-l border-gray-300 px-3 py-1">{{ number_format((float) $sqft) }} Sqft</span>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- agent --}}
                            <div class="flex items-start justify-center gap-4 px-4 py-8">
                                @if($agentImg)
                                    <img
                                        src="{{ $agentImg }}"
                                        alt="{{ $agentName }}"
                                        class="h-18 w-auto max-w-none rounded object-cover ring-1 ring-black/10"
                                    >
                                @endif

                                <div class="min-w-0 pt-1">
                                    <div class="text-[13px] text-gray-500">
                                        Listed by:
                                    </div>

                                    <div class="mt-0.5 text-[15px] leading-tight">
                                        <a href="#" class="font-medium hover:opacity-85 transition" style="color: {{ $brandBlue }};">
                                            {{ $agentName }}
                                        </a>
                                    </div>

                                    <div class="mt-1 text-[13px] text-gray-600">
                                        {{ $officeName }}
                                    </div>
                                </div>
                            </div>

                        </article>
                    </div>
                @endforeach

            </div>
        </div>
    </div>
</div>