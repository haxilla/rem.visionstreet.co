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

        {{-- Main layout --}}
        <div class="grid grid-cols-1 xl:grid-cols-[2fr_1fr] gap-8 xl:gap-8 items-start">

            {{-- LEFT: Top viewed container --}}
            <div class="rounded-[30px] bg-white shadow-[0_14px_36px_rgba(0,0,0,.05)] ring-1 ring-black/5 overflow-hidden">
                {{-- Header --}}
                <div class="px-6 py-6 sm:px-8 sm:py-7 text-center border-b border-[#edf0f5]">
                    <div class="flex justify-center">
                        <i class="ti-bookmark text-[28px] leading-none" style="color: {{ $brandBlue }};"></i>
                    </div>

                    <h2 class="font-display mt-2 text-[30px] sm:text-[42px] font-medium leading-none text-[#1c1d22]">
                        {!! $topViewedTitle !!}
                    </h2>

                    <div class="mt-5 text-[15px] sm:text-[17px] leading-7 text-gray-600 max-w-sm mx-auto">
                        Discover the listings getting the most attention on Realty Emails right now.
                    </div>
                </div>

                {{-- Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6 sm:p-7">
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

                        <article class="group rounded-[24px] bg-white p-5 sm:p-6 shadow-[0_8px_22px_rgba(0,0,0,.06)] ring-1 ring-black/5 transition duration-300 hover:-translate-y-[3px] hover:shadow-[0_16px_38px_rgba(0,0,0,.10)]">
                            {{-- Photo --}}
                            <div class="overflow-hidden rounded-[18px] bg-[#e8e8ec] h-[220px] sm:h-[240px]">
                                @if($listingImg)
                                    <img
                                        src="{{ $listingImg }}"
                                        alt="{{ $street }}"
                                        class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.035]"
                                    >
                                @endif
                            </div>

                            {{-- Meta pills --}}
                            <div class="mt-4 flex flex-wrap items-center gap-3">
                                <div class="text-[11px] font-semibold uppercase tracking-[0.16em] text-gray-400">
                                    #{{ $index + 1 }} Trending
                                </div>

                                @if($priceLabel)
                                    <span
                                        class="inline-flex rounded-full bg-[#214e9b]/95 px-3 py-1 text-[11px] font-semibold text-white backdrop-blur-sm"
                                    >
                                        {{ $priceLabel }}
                                    </span>
                                @endif
                            </div>

                            {{-- Address --}}
                            <div class="mt-3">
                                <a href="#" class="block text-[22px] sm:text-[24px] font-semibold leading-tight text-[#214e9b] transition hover:opacity-80">
                                    {{ $street }}
                                </a>
                            </div>

                            <div class="mt-2 text-[14px] sm:text-[15px] text-gray-600">
                                {{ $cityLine }}
                            </div>

                            {{-- Home details --}}
                            @if($beds || $baths || $sqft)
                                <div class="mt-3 flex flex-wrap items-center gap-x-3 gap-y-1 text-[13px] text-gray-600">
                                    @if($beds)<span>{{ $beds }} Beds</span>@endif
                                    @if($baths)<span>• {{ $baths }} Baths</span>@endif
                                    @if($sqft)<span>• {{ number_format((float) $sqft) }} Sqft</span>@endif
                                </div>
                            @endif

                            {{-- Agent --}}
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

            {{-- RIGHT: Polished free trial sidebar --}}
<aside class="flex items-center">
    <div class="w-full rounded-[30px] bg-white p-6 sm:p-8 shadow-[0_14px_36px_rgba(0,0,0,.05)] ring-1 ring-black/5">

        {{-- Top badge --}}
        <div class="flex justify-center">
            <div
                class="flex h-[68px] w-[68px] items-center justify-center rounded-full border-2 shadow-sm"
                style="border-color: {{ $brandGold }}; background-color: rgba(33,78,155,.06);"
            >
                <i class="ti-wand text-[24px]" style="color: {{ $brandBlue }};"></i>
            </div>
        </div>

        {{-- Eyebrow --}}
        <div class="mt-5 text-center text-[12px] font-semibold uppercase tracking-[0.16em] text-gray-500">
            Flyer Creation Wizard
        </div>

        {{-- Heading --}}
        <h3 class="font-display mt-3 text-center text-[34px] sm:text-[38px] leading-[1.02] text-[#1c1d22]">
            Create Your<br>First Flyer Free
        </h3>

        {{-- Supporting copy --}}
        <p class="mx-auto mt-4 max-w-[290px] text-center text-[15px] leading-7 text-gray-600">
            Enter your email and a property address or MLS number to instantly create a polished flyer draft.
        </p>

        {{-- Divider --}}
        <div class="mx-auto mt-6 h-px w-full bg-[#edf0f5]"></div>

        {{-- Form --}}
        <form class="mt-6 space-y-4" method="post" action="#">
            @csrf

            <div>
                <label class="mb-1.5 block text-[13px] font-medium text-[#374151]">
                    Email
                </label>
                <input
                    type="email"
                    name="email"
                    class="w-full rounded-[16px] border border-[#dde3ee] bg-white px-5 py-3.5 text-[15px] text-gray-800 placeholder:text-gray-400 outline-none transition focus:border-[#214e9b] focus:ring-2 focus:ring-[#214e9b]/10"
                    placeholder="Your email"
                >
            </div>

            <div>
                <label class="mb-1.5 block text-[13px] font-medium text-[#374151]">
                    Address or MLS#
                </label>
                <input
                    type="text"
                    name="listing_input"
                    class="w-full rounded-[16px] border border-[#dde3ee] bg-white px-5 py-3.5 text-[15px] text-gray-800 placeholder:text-gray-400 outline-none transition focus:border-[#214e9b] focus:ring-2 focus:ring-[#214e9b]/10"
                    placeholder="Address or MLS# of flyer to create"
                >
            </div>

            <div class="pt-2">
                <button
                    type="submit"
                    class="inline-flex w-full items-center justify-center rounded-full px-6 py-3.5 text-[15px] font-semibold text-white shadow-[0_10px_20px_rgba(33,78,155,.18)] transition hover:-translate-y-[1px] hover:brightness-105"
                    style="background-color: {{ $brandBlue }};"
                >
                    Create Free Flyer
                </button>
            </div>
        </form>

        {{-- Footer note --}}
        <div class="mt-5 text-center text-[12px] leading-6 text-gray-500">
            Fast, simple, and designed to help your listing stand out.
        </div>
    </div>
</aside>

        </div>
    </div>
</section>




</body>
</html>