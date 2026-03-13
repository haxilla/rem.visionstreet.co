{{-- TOP VIEWED - 6 PROPERTY GRID --}}
@php
    $topViewedTitle = $topViewedTitle ?? "Today's <span class='text-[#214e9b]'>TOP VIEWED</span>";
    $topViewedItems = ($mostViews ?? collect())->take(3);
    $topLuxuryItems = ($topLuxury ?? collect())->take(3);

    $sectionMax     = $sectionMax ?? '1600px';
    $brandBlue      = $brandBlue ?? '#214e9b';
    $brandGold      = $brandGold ?? '#e79a63';
@endphp

<div
    class="w-full pt-12 pb-16 lg:pt-14 lg:pb-20"
    style="
        background:
            linear-gradient(180deg, #f3f5fa 0%, #eef1f7 16%, #eef1f7 100%);
        border-top: 1px solid rgba(255,255,255,.65);
    "
>
    <div class="mx-auto max-w-[1300px] px-4 sm:px-6 lg:px-10">

        {{-- Header --}}
        <div class="mb-10 text-center">
            <div class="flex justify-center">
                <i class="ti-bookmark text-[28px]" style="color: {{ $brandBlue }}"></i>
            </div>

            <h2 class="font-display mt-2 text-[32px] leading-none text-[#1c1d22] sm:text-[42px]">
                {!! $topViewedTitle !!}
            </h2>

            <div class="mx-auto mt-4 max-w-sm text-[16px] leading-7 text-gray-600">
                Discover the listings getting the most attention on Realty Emails right now.
            </div>
        </div>

        {{-- Property Grid --}}
        <div class="grid grid-cols-1 gap-7 md:grid-cols-2 xl:grid-cols-3">
            @foreach($topViewedItems as $index => $the)

                @php
                    $photoObj = $the->thePhotos->where('def','=','1')->first();
                    $photo    = $photoObj?->photoName;

                    $listingImg = null;
                    if ($photo && !empty($the->theMeta?->zipDir) && !empty($the->theMeta?->mlsDir)) {
                        $listingImg = "https://realtyrepublic.com/hqphotos/{$the->theMeta->zipDir}/{$the->theMeta->mlsDir}/{$photo}";
                    }

                    $listingURL="https://realtyrepublic.com/homedetails/{$the->url_slug}";

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
                    $price      = $the->xPrice ?? $the->xListPrice ?? null;
                    $priceLabel = $price ? '$' . number_format((float) $price) : null;
                @endphp

                <article class="group rounded-[24px] bg-white p-6 shadow-[0_8px_24px_rgba(0,0,0,.06)] ring-1 ring-black/5 transition hover:-translate-y-[2px] hover:shadow-[0_18px_40px_rgba(0,0,0,.10)]">

                    {{-- Photo --}}
                    <div class="h-[230px] overflow-hidden rounded-[18px] bg-[#e8e8ec]">
                        @if($listingImg)
                            <a href="{{ $listingURL }}" target="_blank">
                                <img
                                    src="{{ $listingImg }}"
                                    alt="{{ $street }}"
                                    class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.035]"
                                >
                            </a>
                        @endif
                    </div>

                    {{-- Meta --}}
                    <div class="mt-4 flex items-center gap-3">
                        <div class="text-[11px] font-semibold uppercase tracking-[0.16em] text-gray-400">
                            #{{ $index + 1 }} Trending
                        </div>

                        @if($priceLabel)
                            <span class="inline-flex rounded-full bg-[#214e9b] px-3 py-1 text-[11px] font-semibold text-white">
                                {{ $priceLabel }}
                            </span>
                        @endif
                    </div>

                    {{-- Address --}}
                    <a href="#" class="mt-3 block text-[22px] font-semibold leading-tight text-[#214e9b] hover:opacity-80">
                        {{ $street }}
                    </a>

                    <div class="mt-2 text-[15px] text-gray-600">
                        {{ $cityLine }}
                    </div>

                    {{-- Agent --}}
                    <div class="mt-5 flex items-start gap-3">
                        @if($agentImg)
                            <img
                                src="{{ $agentImg }}"
                                alt="{{ $agentName }}"
                                class="h-16 w-auto rounded-xl object-cover ring-1 ring-black/10"
                            >
                        @endif

                        <div>
                            <div class="text-[12px] text-gray-500">
                                Listed by:
                            </div>

                            <div class="text-[17px] font-medium leading-tight text-[#214e9b]">
                                {{ $agentName }}
                            </div>

                            <div class="text-[14px] text-gray-600">
                                {{ $officeName }}
                            </div>
                        </div>
                    </div>

                </article>

            @endforeach
        </div>

        {{-- Header --}}
        <div class="my-10 text-center">
            <div class="flex justify-center">
                <i class="ti-bookmark text-[28px]" style="color: {{ $brandBlue }}"></i>
            </div>

            <h2 class="font-display mt-2 text-[32px] leading-none text-[#1c1d22] sm:text-[42px]">
                Today's <span class='text-[#214e9b]'>TOP LUXURY</span>
            </h2>
            <div class="mx-auto mt-4 max-w-sm text-[16px] leading-7 text-gray-600">
                Trending Luxury Listings on Realty Emails right now.
            </div>
        </div>

        {{-- Property Grid --}}
        <div class="grid grid-cols-1 gap-7 md:grid-cols-2 xl:grid-cols-3">
            @foreach($topLuxuryItems as $index => $the)

                @php
                    $photoObj = $the->thePhotos->where('def','=','1')->first();
                    $photo    = $photoObj?->photoName;

                    $listingImg = null;
                    if ($photo && !empty($the->theMeta?->zipDir) && !empty($the->theMeta?->mlsDir)) {
                        $listingImg = "https://realtyrepublic.com/hqphotos/{$the->theMeta->zipDir}/{$the->theMeta->mlsDir}/{$photo}";
                    }

                    $listingURL="https://realtyrepublic.com/homedetails/{$the->url_slug}";

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
                    $price      = $the->xPrice ?? $the->xListPrice ?? null;
                    $priceLabel = $price ? '$' . number_format((float) $price) : null;
                @endphp

                <article class="group rounded-[24px] bg-white p-6 shadow-[0_8px_24px_rgba(0,0,0,.06)] ring-1 ring-black/5 transition hover:-translate-y-[2px] hover:shadow-[0_18px_40px_rgba(0,0,0,.10)]">

                    {{-- Photo --}}
                    <div class="h-[230px] overflow-hidden rounded-[18px] bg-[#e8e8ec]">
                        @if($listingImg)
                            <a href="{{ $listingURL }}" target="_blank">
                                <img
                                    src="{{ $listingImg }}"
                                    alt="{{ $street }}"
                                    class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.035]"
                                >
                            </a>
                        @endif
                        
                    </div>

                    {{-- Meta --}}
                    <div class="mt-4 flex items-center gap-3">
                        <div class="text-[11px] font-semibold uppercase tracking-[0.16em] text-gray-400">
                            #{{ $index + 1 }} Trending
                        </div>

                        @if($priceLabel)
                            <span class="inline-flex rounded-full bg-[#214e9b] px-3 py-1 text-[11px] font-semibold text-white">
                                {{ $priceLabel }}
                            </span>
                        @endif
                    </div>

                    {{-- Address --}}
                    <a href="#" class="mt-3 block text-[22px] font-semibold leading-tight text-[#214e9b] hover:opacity-80">
                        {{ $street }}
                    </a>

                    <div class="mt-2 text-[15px] text-gray-600">
                        {{ $cityLine }}
                    </div>

                    {{-- Agent --}}
                    <div class="mt-5 flex items-start gap-3">
                        @if($agentImg)
                            <img
                                src="{{ $agentImg }}"
                                alt="{{ $agentName }}"
                                class="h-16 w-auto rounded-xl object-cover ring-1 ring-black/10"
                            >
                        @endif

                        <div>
                            <div class="text-[12px] text-gray-500">
                                Listed by:
                            </div>

                            <div class="text-[17px] font-medium leading-tight text-[#214e9b]">
                                {{ $agentName }}
                            </div>

                            <div class="text-[14px] text-gray-600">
                                {{ $officeName }}
                            </div>
                        </div>
                    </div>

                </article>

            @endforeach
        </div>

    </div>
</div>