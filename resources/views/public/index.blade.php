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
    <!--
    <section class="bg-[#f5f5f7] py-12 lg:py-16">
        @include('public.includes.top_views')
    </section>
    <section>
        @include('public.includes.top_views_3')
    </section>
-->
{{-- TOP VIEWED + FREE TRIAL SIDEBAR --}}
@php
    $topViewedTitle = $topViewedTitle ?? "Today's <span class='text-[#214e9b]'>TOP VIEWED</span>";
    $topViewedItems = ($mostViews ?? collect())->take(4);
    $sectionMax     = $sectionMax ?? '1600px';
    $brandBlue      = $brandBlue ?? '#214e9b';
    $brandGold      = $brandGold ?? '#e79a63';
@endphp

<section class="w-full bg-[#f5f5f7] py-12 lg:py-16">
    <div class="mx-auto w-full max-w-screen-2xl px-4 sm:px-6 lg:px-10" style="max-width: {{ $sectionMax }};">

        {{-- Header --}}
        <div class="text-center max-w-4xl mx-auto">
            <div class="flex justify-center">
                <i class="ti-bookmark text-[28px] leading-none" style="color: {{ $brandBlue }};"></i>
            </div>

            <h2 class="font-display mt-2 text-[30px] sm:text-[42px] font-medium leading-none text-[#1c1d22]">
                {!! $topViewedTitle !!}
            </h2>

            <div class="mt-5 text-[15px] sm:text-[17px] leading-7 text-gray-600 max-w-3xl mx-auto">
                See the most popular listings on Realty Emails today. This section highlights properties drawing standout interest and offers a quick snapshot of what is resonating most right now.
            </div>
        </div>

        {{-- Main layout --}}
        <div class="mt-10 grid grid-cols-1 xl:grid-cols-[2fr_1fr] gap-8 xl:gap-8 items-start">

            {{-- LEFT: 2x2 most viewed grid --}}
            <div class="rounded-[28px] bg-white shadow-[0_12px_34px_rgba(0,0,0,.05)] ring-1 ring-black/5 overflow-hidden">
                <div class="border-b border-[#e7e9ef] px-6 py-5 sm:px-8">
                    <div class="text-[13px] font-semibold uppercase tracking-[0.16em] text-gray-500">
                        Popular Listings
                    </div>
                    <div class="mt-1 text-[20px] sm:text-[24px] font-semibold text-[#1d2433]">
                        Most viewed properties right now
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2">
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

                        <article class="group p-5 sm:p-6 border-b border-[#edf0f5] md:[&:nth-last-child(-n+2)]:border-b-0 md:odd:border-r md:border-[#edf0f5]">
                            <div class="overflow-hidden rounded-[22px] bg-[#e8e8ec] h-[220px] sm:h-[240px]">
                                @if($listingImg)
                                    <img
                                        src="{{ $listingImg }}"
                                        alt="{{ $street }}"
                                        class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.03]"
                                    >
                                @endif
                            </div>

                            <div class="mt-4 flex flex-wrap items-center gap-3">
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
                        </article>
                    @endforeach
                </div>
            </div>

            {{-- RIGHT: Free trial sidebar --}}
            <aside class="rounded-[28px] bg-[#eceaf8] px-6 py-8 sm:px-8 sm:py-10 shadow-[0_12px_34px_rgba(0,0,0,.04)] ring-1 ring-black/5">
                <div class="flex flex-col items-center text-center">
                    <div
                        class="flex h-[84px] w-[84px] items-center justify-center rounded-full border-[4px]"
                        style="background-color: {{ $brandBlue }}; border-color: {{ $brandGold }};"
                    >
                        <i class="ti-wand text-[34px]" style="color: #f5c14d;"></i>
                    </div>

                    <div class="mt-5 text-[15px] italic leading-none" style="color: {{ $brandBlue }};">
                        Flyer Creation Wizard
                    </div>

                    <h3 class="mt-3 text-[40px] sm:text-[46px] font-extrabold leading-none" style="color: {{ $brandBlue }};">
                        FREE TRIAL!
                    </h3>

                    <p class="mt-5 max-w-[300px] text-[18px] leading-8" style="color: {{ $brandBlue }};">
                        If it’s your listing and online anywhere, our system will find it and auto create it.
                    </p>
                </div>

                <form class="mt-8 space-y-4" method="post" action="#">
                    @csrf

                    <input
                        type="email"
                        name="email"
                        class="w-full rounded-full border-0 bg-white px-6 py-4 text-center text-[16px] text-gray-700 placeholder:text-gray-500 outline-none shadow-sm"
                        placeholder="Your Email"
                    >

                    <input
                        type="text"
                        name="listing_input"
                        class="w-full rounded-full border-0 bg-white px-6 py-4 text-center text-[16px] text-gray-700 placeholder:text-gray-500 outline-none shadow-sm"
                        placeholder="Address or MLS# of Flyer to Create"
                    >

                    <div class="pt-3 flex justify-center">
                        <button
                            type="submit"
                            class="inline-flex items-center justify-center rounded-full px-8 py-4 text-[16px] font-semibold text-white shadow-[0_8px_18px_rgba(0,0,0,.12)] transition hover:brightness-105"
                            style="background-color: {{ $brandBlue }}; box-shadow: 0 0 0 4px {{ $brandGold }} inset;"
                        >
                            Create FREE Flyer
                        </button>
                    </div>
                </form>
            </aside>

        </div>
    </div>
</section>





</body>
</html>