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

  {{-- TOP VIEWED SECTION --}}
@php
    $topViewedTitle = $topViewedTitle ?? "Today's <span class='text-[#214e9b]'>TOP VIEWED</span>";
    $topViewedItems = $mostViews->take(3);

    $sectionMax = $sectionMax ?? '1600px';
    $brandBlue  = $brandBlue ?? '#214e9b';
@endphp

<section class="w-full bg-[#f6f6f8] py-10 lg:py-14">
    <div class="mx-auto w-full max-w-screen-2xl px-4 sm:px-6 lg:px-10" style="max-width: {{ $sectionMax }};">

        {{-- Header --}}
        <div class="text-center">
            <div class="flex items-center justify-center">
                {{-- simple bookmark icon --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#214e9b]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 21l-5-3-5 3V5a2 2 0 012-2h6a2 2 0 012 2v16z" />
                </svg>
            </div>

            <h2 class="font-display mt-2 text-[28px] sm:text-[38px] font-medium leading-none text-[#1e1e24]">
                {!! $topViewedTitle !!}
            </h2>

            <div class="mx-auto mt-5 h-px w-full max-w-[1800px] bg-[#dfdfe5]"></div>
        </div>

        {{-- Cards --}}
        <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-0 overflow-hidden">

            @foreach($topViewedItems as $the)
                @php
                    $photoObj = $the->thePhotos->where('def','=','1')->first();
                    $photo = $photoObj?->photoName;

                    $listingImg = null;
                    if ($photo) {
                        $listingImg = "https://realtyrepublic.com/hqphotos/{$the->theMeta->zipDir}/{$the->theMeta->mlsDir}/{$photo}";
                    }

                    $agentImg = null;
                    if (!empty($the->theAgent->agtPhoto) && !empty($the->theAgent->theAgentCleanup)) {
                        $agentImg = "https://realtyrepublic.com/agentPhotos/{$the->theAgent->theAgentCleanup->newRemID}/{$the->theAgent->agtPhoto}";
                    } elseif (!empty($the->theAgent->agtPhoto)) {
                        $agentImg = "https://realtyemails.com/HQoffice/{$the->theOffice->officeID}/{$the->theAgent->agtPhoto}";
                    }

                    $street      = $the->xFullStreet ?? '';
                    $cityLine    = trim(($the->xCity ?? '') . ' ' . ($the->xState ?? '') . ' ' . ($the->xxZip ?? ''));
                    $agentName   = $the->theAgent->agtFullName ?? '';
                    $officeName  = $the->theOffice->officeName ?? 'Real Broker';

                    $beds        = $the->xBeds ?? null;
                    $baths       = $the->xBaths ?? null;
                    $sqft        = $the->xSqft ?? $the->xSqFt ?? null;
                    $price       = $the->xPrice ?? $the->xListPrice ?? null;

                    $priceLabel  = $price ? '$' . number_format((float) $price) : null;
                @endphp

                <article class="group bg-transparent">
                    {{-- top text area --}}
                    <div class="px-4 pb-3 pt-3 text-center sm:px-6">
                        <a href="#" class="block text-[16px] sm:text-[17px] font-medium leading-tight text-[#3159a6] hover:text-[#1f4694] transition">
                            {{ $street }}
                        </a>

                        <div class="mt-1 text-[13px] text-gray-600">
                            {{ $cityLine }}
                        </div>
                    </div>

                    {{-- image area --}}
                    <div class="relative overflow-hidden bg-gray-200">
                        <div class="aspect-[1.9/1] w-full">
                            @if($listingImg)
                                <img
                                    src="{{ $listingImg }}"
                                    alt="{{ $street }}"
                                    class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.02]"
                                >
                            @else
                                <div class="flex h-full w-full items-center justify-center bg-gray-300 text-sm text-gray-600">
                                    No Image
                                </div>
                            @endif
                        </div>

                        @if($priceLabel)
                            <div class="absolute left-1/2 top-0 z-10 -translate-x-1/2 -translate-y-0">
                                <span class="inline-flex rounded-b-lg bg-[#214e9b] px-3 py-1.5 text-[13px] font-semibold text-white shadow-sm">
                                    {{ $priceLabel }}
                                </span>
                            </div>
                        @endif

                        @if($beds || $baths || $sqft)
                            <div class="absolute bottom-3 left-1/2 z-10 -translate-x-1/2">
                                <div class="flex items-center divide-x divide-gray-300 overflow-hidden rounded bg-white/95 text-[12px] text-gray-700 shadow-sm ring-1 ring-black/5 backdrop-blur-sm">
                                    @if($beds)
                                        <span class="px-3 py-1">{{ $beds }} Beds</span>
                                    @endif
                                    @if($baths)
                                        <span class="px-3 py-1">{{ $baths }} Baths</span>
                                    @endif
                                    @if($sqft)
                                        <span class="px-3 py-1">{{ number_format((float) $sqft) }} Sqft</span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- agent area --}}
                    <div class="flex items-start justify-center gap-3 px-4 py-8 sm:px-6">
                        @if($agentImg)
                            <img
                                src="{{ $agentImg }}"
                                alt="{{ $agentName }}"
                                class="h-14 w-14 rounded object-cover ring-1 ring-black/10"
                            >
                        @else
                            <div class="flex h-14 w-14 items-center justify-center rounded bg-gray-200 text-xs text-gray-500 ring-1 ring-black/10">
                                Agent
                            </div>
                        @endif

                        <div class="min-w-0">
                            <div class="text-[13px] text-gray-500">
                                Listed by:
                            </div>

                            <div class="mt-0.5 text-[15px] leading-tight">
                                <a href="#" class="font-medium text-[#3159a6] hover:text-[#1f4694] transition">
                                    {{ $agentName }}
                                </a>
                            </div>

                            <div class="mt-1 text-[13px] text-gray-600">
                                {{ $officeName }}
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach

        </div>
    </div>
</section>

</body>
</html>