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

{{-- TOP VIEWED + FREE FLYER SECTION --}}

<section class="w-full bg-[#f5f5f7] py-12 lg:py-16">

<div class="mx-auto max-w-screen-2xl px-4 sm:px-6 lg:px-10">

<div class="grid grid-cols-1 lg:grid-cols-[2fr_1fr] gap-10 items-start">

{{-- LEFT SIDE --}}
<div>

    {{-- Header --}}
    <div class="text-center mb-10">

        <div class="flex justify-center">
            <i class="ti-bookmark text-[28px]" style="color: {{ $brandBlue }}"></i>
        </div>

        <h2 class="font-display mt-2 text-[32px] sm:text-[42px] leading-none text-[#1c1d22]">
            {!! $topViewedTitle !!}
        </h2>

        <div class="mt-4 text-[16px] leading-7 text-gray-600 max-w-sm mx-auto">
            Discover the listings getting the most attention on Realty Emails right now.
        </div>

    </div>


    {{-- Property Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-7">

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
        $price      = $the->xPrice ?? $the->xListPrice ?? null;
        $priceLabel = $price ? '$' . number_format((float) $price) : null;
        @endphp

        <article class="group rounded-[24px] bg-white p-6 shadow-[0_8px_24px_rgba(0,0,0,.06)] ring-1 ring-black/5 transition hover:-translate-y-[2px] hover:shadow-[0_18px_40px_rgba(0,0,0,.10)]">

            {{-- Photo --}}
            <div class="overflow-hidden rounded-[18px] h-[230px] bg-[#e8e8ec]">

                @if($listingImg)
                <img
                    src="{{ $listingImg }}"
                    alt="{{ $street }}"
                    class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.035]"
                >
                @endif

            </div>

            {{-- Meta --}}
            <div class="mt-4 flex items-center gap-3">

                <div class="text-[11px] uppercase tracking-[0.16em] text-gray-400 font-semibold">
                    #{{ $index+1 }} Trending
                </div>

                @if($priceLabel)
                <span class="inline-flex rounded-full bg-[#214e9b] px-3 py-1 text-[11px] font-semibold text-white">
                    {{ $priceLabel }}
                </span>
                @endif

            </div>

            {{-- Address --}}
            <a href="#" class="block mt-3 text-[22px] font-semibold leading-tight text-[#214e9b] hover:opacity-80">
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

                    <div class="text-[17px] text-[#214e9b] font-medium leading-tight">
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



{{-- RIGHT SIDEBAR --}}
<aside class="flex items-start">

<div class="relative w-full rounded-[30px] bg-[#1e3566] p-7 sm:p-8 shadow-[0_20px_55px_rgba(0,0,0,.22)] overflow-hidden">

    <div class="absolute -top-16 right-[-40px] w-64 h-64 bg-white/8 blur-3xl rounded-full"></div>
    <div class="absolute bottom-[-50px] left-[-40px] w-72 h-72 bg-[#f0d28a]/10 blur-3xl rounded-full"></div>

    <div class="relative z-10">

        <div class="flex justify-center">
            <div class="flex items-center justify-center w-[70px] h-[70px] rounded-full border-2 shadow-lg"
                 style="border-color: {{ $brandGold }}; background: rgba(255,255,255,.06);">
                <i class="ti-wand text-[24px]" style="color:#f0d28a;"></i>
            </div>
        </div>

        <div class="mt-5 text-center text-[12px] uppercase tracking-[0.18em] text-white/60 font-semibold">
            Flyer Creation Wizard
        </div>

        <h3 class="font-display text-center text-[36px] leading-[1.05] mt-3 text-white">
            Start With a<br>Free Flyer
        </h3>

        <div class="mx-auto mt-5 w-20 h-[2px] bg-[#f0d28a] rounded-full"></div>

        <p class="text-center text-[15px] text-white/80 leading-7 mt-5 max-w-[300px] mx-auto">
            Enter your email and a property address or MLS number and we'll instantly generate a flyer draft you can preview.
        </p>

        <form method="post" action="#" class="mt-7 space-y-4">
        @csrf

            <div>
                <label class="block text-[12px] uppercase tracking-[0.14em] text-white/60 font-semibold mb-1.5">
                    Email
                </label>

                <input
                    type="email"
                    name="email"
                    placeholder="Your email"
                    class="w-full rounded-[14px] bg-white px-4 py-3 text-[15px] text-gray-800 border border-gray-200"
                >
            </div>

            <div>
                <label class="block text-[12px] uppercase tracking-[0.14em] text-white/60 font-semibold mb-1.5">
                    Address or MLS#
                </label>

                <input
                    type="text"
                    name="listing_input"
                    placeholder="Address or MLS# of listing"
                    class="w-full rounded-[14px] bg-white px-4 py-3 text-[15px] text-gray-800 border border-gray-200"
                >
            </div>

            <button
                type="submit"
                class="w-full rounded-full py-3.5 text-[15px] font-semibold text-[#1d2f5f] shadow-lg transition hover:-translate-y-[1px]"
                style="background:#f0d28a;"
            >
                Generate Free Flyer
            </button>

        </form>

        <div class="text-center text-[12px] text-white/55 mt-5">
            Takes less than 30 seconds to start.
        </div>

    </div>
</div>

</aside>


</div>
</div>
</section>



</body>
</html>