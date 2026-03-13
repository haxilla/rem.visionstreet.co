@php
    $brandName   = $brandName   ?? 'RealtyEmails';
    $headline    = $headline    ?? 'Maximize Exposure<br>On Your Listing!';
    $subLines    = $subLines    ?? [
        'Easily Create Professional Real Estate',
        'E-Flyers with a FREE Website',
        'for YOU & Your Property!',
    ];
    $ctaText     = $ctaText     ?? 'Create FREE Flyer!';

    $heroMinH = $heroMinH ?? 'min-h-[520px]';
@endphp

{{-- White page, centered card --}}
<div class="w-full bg-white px-4 sm:px-6 lg:px-8 py-6 lg:py-10">
    <div class="mx-auto w-full max-w-screen-2xl" style="max-width: 1600px;">
        <div class="grid grid-cols-1 lg:grid-cols-2 {{ $heroMinH }} overflow-hidden rounded-2xl bg-white shadow-lg ring-1 ring-black/10">
            {{-- LEFT: Swiper (listings) --}}
            <div class="relative overflow-hidden bg-black">

                <div class="swiper h-full" data-swiper="hero">
                <div class="swiper-wrapper">

                    @foreach($newAdds as $the)
                        @php
                            $photo = $the->thePhotos->where('def','=','1')->first()->photoName;
                            $listingImg = "https://realtyrepublic.com/hqphotos/{$the->theMeta->zipDir}/{$the->theMeta->mlsDir}/{$photo}";

                            $agentImg = null;
                            if ($the->theAgent->agtPhoto && $the->theAgent->theAgentCleanup) {
                                $agentImg = "https://realtyrepublic.com/agentPhotos/{$the->theAgent->theAgentCleanup->newRemID}/{$the->theAgent->agtPhoto}";
                            } elseif ($the->theAgent->agtPhoto) {
                                $agentImg = "https://realtyemails.com/HQoffice/{$the->theOffice->officeID}/{$the->theAgent->agtPhoto}";
                            }

                            $listingURL= "https://realtyrepublic.com/homedetails/{{ $the->url_slug }}";
                            dd($listingURL);
                            $street   = $the->xFullStreet;
                            $cityLine = "{$the->xCity}, {$the->xState} {$the->xxZip}";
                            $agentName  = $the->theAgent->agtFullName;
                            $officeName = $the->theOffice->officeName;
                            $agentPhone = $the->theAgent->agtMainPhone;

                            $badgeText = $badgeText ?? 'Featured';
                        @endphp

                        <div class="swiper-slide">
                            <a href="{{ $listingURL }}">
                                <div class="relative {{ $heroMinH }}">

                                    {{-- background photo link --}}

                                    <img
                                        src="{{ $listingImg }}"
                                        alt="{{ $street }}"
                                        class="absolute inset-0 h-full w-full object-cover"
                                    />

                                    {{-- readability overlay --}}
                                    <div class="absolute inset-0 bg-black/20"></div>

                                    {{-- top-left listing info --}}
                                    <div class="absolute left-6 top-6 z-10 text-white drop-shadow">
                                        <div class="inline-flex items-center rounded-full bg-black/35 px-3 py-1 text-[11px] font-medium tracking-wide ring-1 ring-white/10 backdrop-blur-sm">
                                            {{ $badgeText }}
                                        </div>

                                        <div class="mt-3 text-[15px] font-medium leading-snug">
                                            {{ $street }}
                                        </div>
                                        <div class="mt-1 text-[12px] font-normal opacity-90">
                                            {{ $cityLine }}
                                        </div>
                                    </div>

                                    {{-- bottom-left agent card --}}
                                    <div class="absolute bottom-6 left-6 z-10 flex items-center gap-3 rounded-xl bg-black/40 px-3.5 py-3 text-white backdrop-blur-sm ring-1 ring-white/10">
                                        @if($agentImg)
                                        <img
                                            src="{{ $agentImg }}"
                                            alt="{{ $agentName }}"
                                            class="h-16 w-auto rounded-lg ring-1 ring-white/25 shadow-sm"
                                        />
                                        @endif

                                        <div class="leading-tight">
                                            <div class="text-[13px] font-semibold">
                                                {{ $agentName }}
                                            </div>
                                            <div class="mt-0.5 text-[12px] font-normal opacity-85">
                                                {{ $officeName }}
                                            </div>
                                            <div class="mt-1 text-[12px] font-normal opacity-85">
                                                {{ $agentPhone }}
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </a>
                        </div>
                    @endforeach

                </div>
                </div>
            </div>

            {{-- RIGHT: Marketing panel (blue/navy, no "cutting line") --}}
            <div class="relative flex items-center justify-center overflow-hidden bg-gradient-to-b from-[#2f4f7f] via-[#2c3f73] to-[#1e2f57] px-8 py-14 text-white">
                {{-- REMOVED the mid highlight line that was "cutting" the headline --}}

                <div class="w-full max-w-md text-center">

                {{-- Headline --}}
                <h1 class="font-display text-[34px] sm:text-[42px] font-medium tracking-tight leading-[1.05]">
                    {!! $headline !!}
                </h1>

                {{-- Supporting lines --}}
                <div class="mt-8 space-y-2 text-[14px] sm:text-[15px] font-normal opacity-90">
                    @foreach($subLines as $line)
                    <div>{{ $line }}</div>
                    @endforeach
                </div>

                {{-- CTA --}}
                <div class="mt-10">
                    <a
                    class="inline-flex items-center justify-center rounded-full border border-white/25 bg-white/10 px-8 py-3 text-[13px] font-semibold tracking-wide shadow-sm hover:bg-white/15 ring-1 ring-white/10 backdrop-blur-sm"
                    >
                    {{ $ctaText }}
                    </a>
                    <div class="mt-4 text-[12px] font-normal opacity-75">
                    No credit card required • Launch in minutes
                    </div>
                </div>

                </div>
            </div>
        </div>
    </div>
</div>