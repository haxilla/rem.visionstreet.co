<!DOCTYPE html>
<html lang="en" class="h-full bg-white text-gray-900">
@include('public.layout.head');

<body class="min-h-screen bg-white pt-[50px] linkcheck">
    @include('public.layout.nav')

@php

    $allPhotos = $details->thePhotos ?? collect();

    // GRID = small (500)
    $gridPhotos = $allPhotos->where('resized', '500')->values();

    // MODAL = best available
    $modalPhotos = $allPhotos->where('resized', '1000')->values();

    if ($modalPhotos->isEmpty()) {
        $modalPhotos = $allPhotos->filter(fn($p) => empty($p->resized))->values();
    }

    if ($modalPhotos->isEmpty()) {
        $modalPhotos = $gridPhotos;
    }

    if ($gridPhotos->isEmpty()) {
        $gridPhotos = $modalPhotos;
    }

    $hero = $gridPhotos->where('def', 1)->first() ?? $gridPhotos->first();

    $thumbs = $gridPhotos
        ->where('photoID', '!=', $hero?->photoID)
        ->take(4);

    $price = number_format($details->xListPrice ?? 0);
    $beds = $details->xxBeds ?: $details->xBeds;
    $baths = $details->xxBaths ?: $details->xBaths;
    $sqft = $details->xxSqft ?: $details->xSqft;
    $year = $details->xxYrBuilt ?: $details->xYrBuilt;

    $zip = $details->theMeta->zipDir ?? '';
    $mls = $details->theMeta->mlsDir ?? '';

    $photoPath = fn ($photo) => "https://realtyrepublic.com/hqphotos/{$zip}/{$mls}/{$photo->photoName}";

    $mapAddress = urlencode(
        $details->xFullStreet . ', ' .
        $details->xCity . ', ' .
        $details->xState . ' ' .
        $details->xZip
    );

    $agent = $details->theAgent ?? null;
    $office = $details->theOffice ?? null;

    $agentImg = null;

    if ($agent?->agtPhoto && $agent?->theAgentCleanup) {
        $agentImg = "https://realtyrepublic.com/agentPhotos/{$agent->theAgentCleanup->newRemID}/{$agent->agtPhoto}";
    } elseif ($agent?->agtPhoto && $office?->officeID) {
        $agentImg = "https://realtyemails.com/HQoffice/{$office->officeID}/{$agent->agtPhoto}";
    }

    $officeLogo = null;

    if ($agent?->agtLogo && $office?->officeID) {
        $officeLogo = "https://realtyrepublic.com/officeLogos/{$office->officeID}/{$agent->agtLogo}";
    }
@endphp

<style>
.sdb { width: 100%; font-family: Arial, sans-serif; background: #fff; border: 1px solid #ddd; box-sizing: border-box; overflow: hidden; }
.sdb-top { background: #0d2d6e; padding: 24px 20px 18px; text-align: center; }
.sdb-eyebrow { color: #f0c040; font-size: 10px; letter-spacing: 2.5px; text-transform: uppercase; font-weight: 700; margin: 0 0 6px; }
.sdb-headline { color: #fff; font-size: 15px; font-weight: 700; line-height: 1.35; margin: 0 0 14px; }
.sdb-badge { width: 80px; height: 80px; background: #f0c040; border-radius: 50%; margin: 0 auto 6px; display: flex; align-items: center; justify-content: center; border: 3px solid rgba(255,255,255,0.3); }
.sdb-price { color: #0d2d6e; font-size: 30px; font-weight: 700; line-height: 1; }
.sdb-from { color: rgba(255,255,255,0.75); font-size: 10px; letter-spacing: 1.5px; text-transform: uppercase; margin: 4px 0 0; }
.sdb-band { background: #f0c040; padding: 9px 16px; text-align: center; }
.sdb-band p { margin: 0; color: #0d2d6e; font-size: 10px; letter-spacing: 2px; text-transform: uppercase; font-weight: 700; }
.sdb-list { background: #fff; padding: 14px 18px 8px; }
.sdb-list ul { list-style: none; margin: 0; padding: 0; }
.sdb-list li { display: flex; align-items: flex-start; gap: 8px; padding: 6px 0; border-bottom: 1px solid #eef0f5; font-size: 12px; color: #222; line-height: 1.4; }
.sdb-list li:last-child { border-bottom: none; }
.sdb-check { color: #f0c040; font-weight: 900; font-size: 14px; flex-shrink: 0; }
.sdb-more { text-align: center; padding: 2px 0 8px; font-size: 12px; font-weight: 700; color: #0d2d6e; letter-spacing: 1px; text-transform: uppercase; }
.sdb-cta { padding: 10px 16px 18px; }
.sdb-btn { display: block; width: 100%; background: #f0c040; color: #0d2d6e; text-decoration: none; text-align: center; border-radius: 3px; padding: 12px 0; font-size: 11px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; box-sizing: border-box; }
.sdb-btn:hover { background: #e6b800; }
</style>

<section class="max-w-[1280px] mx-auto px-4 py-4 text-slate-950">

    {{-- Photo grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-2 rounded-xl overflow-hidden">

        <div class="relative h-[445px] bg-slate-200">
            @if($hero)
                <img
                    src="{{ $photoPath($hero) }}"
                    class="w-full h-full object-cover cursor-pointer"
                    data-photo-open="0"
                    alt="{{ $details->xFullStreet }}"
                >
            @endif

            <div class="absolute top-4 left-4 bg-white rounded-md px-3 py-1.5 text-sm font-semibold shadow">
                <span class="inline-block w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                For sale
            </div>
        </div>

        <div class="grid grid-cols-2 gap-2 h-[445px]">
            @foreach($thumbs as $photo)
                <div class="relative bg-slate-200 overflow-hidden">
                    <img
                        src="{{ $photoPath($photo) }}"
                        class="w-full h-full object-cover cursor-pointer"
                        data-photo-open="{{ $loop->iteration }}"
                        alt=""
                    >

                    @if($loop->last)
                        <button
                            type="button"
                            data-photo-open="0"
                            class="absolute bottom-4 right-4 bg-white rounded-md px-5 py-3 text-sm font-bold shadow-lg flex items-center gap-2">
                            <span class="text-xl leading-none">▦</span>
                            See all {{ $modalPhotos->count() }} photos
                        </button>
                    @endif
                </div>
            @endforeach
        </div>

    </div>

    {{-- Main content + right column --}}
    <div class="grid grid-cols-1 lg:grid-cols-[1fr_260px] gap-9 mt-6">

        {{-- Left: main content --}}
        <main>

            <div class="mb-3">
                <span class="inline-block bg-red-100 text-red-700 text-sm font-bold px-2 py-1 rounded">
                    Special statement goes here
                </span>
            </div>

            <div class="flex flex-wrap items-end gap-x-10 gap-y-3">

                <div class="min-w-[360px]">
                    <h1 class="text-4xl font-bold tracking-tight">
                        ${{ $price }}
                    </h1>

                    <div class="text-xl mt-1">
                        {{ $details->xFullStreet }}, {{ $details->xCity }}, {{ $details->xState }} {{ $details->xZip }}
                    </div>
                </div>

                <div class="flex gap-10 text-center">
                    <div>
                        <div class="text-4xl font-bold">{{ $beds }}</div>
                        <div class="text-xl">beds</div>
                    </div>

                    <div>
                        <div class="text-4xl font-bold">{{ $baths }}</div>
                        <div class="text-xl">baths</div>
                    </div>

                    <div>
                        <div class="text-4xl font-bold">{{ number_format($sqft) }}</div>
                        <div class="text-xl">sqft</div>
                    </div>
                </div>

            </div>

            <div class="mt-5">
                <a href="#"
                   class="inline-block bg-sky-100 text-blue-900 font-semibold rounded-lg px-4 py-2">
                    Est.: <strong>$3,950/mo</strong>
                    <span class="ml-2">Get pre-qualified</span>
                </a>
            </div>

            {{-- Facts --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-6 max-w-[760px]">

                <div class="bg-slate-100 rounded px-4 py-4 flex items-center gap-3">
                    <span class="text-xl">⌂</span>
                    <span>{{ $details->theRemarks->xb1 }}</span>
                </div>

                <div class="bg-slate-100 rounded px-4 py-4 flex items-center gap-3">
                    <span class="text-xl">⚒</span>
                    <span>{{ $details->theRemarks->xb2 }}</span>
                </div>

                <div class="bg-slate-100 rounded px-4 py-4 flex items-center gap-3">
                    <span class="text-xl">♙</span>
                    <span>{{ $details->theRemarks->xb3 }}</span>
                </div>

                <div class="bg-slate-100 rounded px-4 py-4 flex items-center gap-3">
                    <span class="text-xl">▰</span>
                    <span>{{ $details->theRemarks->xb4 }}</span>
                </div>

                <div class="bg-slate-100 rounded px-4 py-4 flex items-center gap-3">
                    <span class="text-xl">▥</span>
                    <span>{{ $details->theRemarks->xb5 }}</span>
                </div>

                <div class="bg-slate-100 rounded px-4 py-4 flex items-center gap-3">
                    <span class="text-xl">♧</span>
                    <span>{{ $details->theRemarks->xb6 }}</span>
                </div>

            </div>

            <hr class="my-8 border-slate-300 max-w-[760px]">

            <div class="max-w-[760px] mb-6 rounded-xl overflow-hidden border border-slate-200">
                <iframe
                    width="100%"
                    height="450"
                    class="w-full rounded-xl"
                    style="border:0;"
                    loading="lazy"
                    allowfullscreen
                    src="https://www.google.com/maps?q={{ $mapAddress }}&output=embed">
                </iframe>
            </div>

            <hr class="my-8 border-slate-300 max-w-[760px]">

            {{-- Remarks --}}
            <section class="max-w-[760px]">
                <h2 class="text-2xl font-bold mb-5">What's special</h2>

                <p class="text-lg leading-8">
                    {{ $details->theRemarks->xPubRemarks ?? '' }}
                </p>
            </section>

            {{-- Bottom Agent Section --}}
            <div class="max-w-[760px] mt-10 border border-slate-300 bg-white overflow-hidden">

                <div class="flex items-center min-h-[135px]">

                    {{-- Agent Photo --}}
                    @if($agentImg)
                        <div class="w-[95px] h-[125px] flex-shrink-0 overflow-hidden ml-2">
                            <img
                                src="{{ $agentImg }}"
                                alt="{{ $agent?->agtFullName }}"
                                class="w-full h-full object-cover"
                            >
                        </div>
                    @endif

                    {{-- Agent Details --}}
                    <div class="px-4 flex-1 text-[14px] leading-[1.45] text-[#000066] py-3">

                        @if($agent?->agtFullName)
                            <div class="text-[18px] leading-tight font-bold text-[#000066] mb-1">
                                {{ $agent->agtFullName }}
                            </div>
                        @endif

                        @if($office?->officeName)
                            <div>{{ $office->officeName }}</div>
                        @endif

                        @if($office?->officeAddress1)
                            <div>{{ $office->officeAddress1 }}</div>
                        @endif

                        @if($office?->officeCity)
                            <div>
                                {{ $office->officeCity }},
                                {{ $office->officeState }}
                                {{ $office->officeZip }}
                            </div>
                        @endif

                        @if($agent?->agtMainPhone)
                            <div>{{ $agent->agtMainPhone }}</div>
                        @endif
                        <div>
                            <button>
                                Email Agent
                            </button>
                        </div>

                    </div>

                    {{-- Office Logo --}}
                    @if($officeLogo)
                        <div class="w-[190px] h-[90px] flex-shrink-0 flex items-center justify-center px-3 overflow-hidden">

                            <img
                                src="{{ $officeLogo }}"
                                alt="{{ $office?->officeName }}"
                                style="max-width: 200px; max-height: 70px; width: auto; height: auto; object-fit: contain;"
                            >

                        </div>
                    @endif

                </div>

            </div>

        </main>

        {{-- Right column: promo only --}}
        <div class="flex flex-col gap-4">

            {{-- Promo --}}
            <div class="sdb">
                <div class="sdb-top">
                    <p class="sdb-eyebrow">For Real Estate Agents</p>
                    <p class="sdb-headline">Email your listing to thousands of interested buyers &amp; agents — instantly</p>
                    <div class="sdb-badge"><span class="sdb-price">$9</span></div>
                    <p class="sdb-from">Starting at just $9</p>
                </div>

                <div class="sdb-band"><p>Premium Services for Less</p></div>

                <div class="sdb-list">
                    <ul>
                        <li><span class="sdb-check">&#10003;</span> Instant Proof &amp; Delivery</li>
                        <li><span class="sdb-check">&#10003;</span> Instant Copy to Home Seller</li>
                        <li><span class="sdb-check">&#10003;</span> Flyers Saved &amp; Editable for Resends</li>
                        <li><span class="sdb-check">&#10003;</span> Upload Unlimited Photos</li>
                        <li><span class="sdb-check">&#10003;</span> FREE Web Page Slide Show</li>
                        <li><span class="sdb-check">&#10003;</span> FREE Page View Reports</li>
                        <li><span class="sdb-check">&#10003;</span> Personal Contact Copy Center</li>
                        <li><span class="sdb-check">&#10003;</span> Multiple Flyer Templates</li>
                    </ul>
                </div>

                <div class="sdb-more">And More!</div>

                <div class="sdb-cta">
                    <a href="#" class="sdb-btn">Send My Listing Now</a>
                </div>
            </div>

        </div>{{-- end right column --}}

    </div>{{-- end main grid --}}

</section>

<div id="photoModal" class="hidden fixed inset-0 z-[9999] bg-black">

    <div class="absolute top-0 left-0 right-0 z-30 h-16 bg-black flex items-center justify-between px-5">
        <button
            id="photoModalClose"
            type="button"
            class="text-white text-sm font-bold"
        >
            ✕ Close
        </button>

        <div class="text-white text-sm font-semibold">
            {{ $details->xFullStreet }}
        </div>
    </div>

    <button
        type="button"
        class="photo-modal-prev absolute left-4 top-1/2 z-30 -translate-y-1/2 bg-white text-black rounded-full w-12 h-12 text-3xl"
    >
        ‹
    </button>

    <button
        type="button"
        class="photo-modal-next absolute right-4 top-1/2 z-30 -translate-y-1/2 bg-white text-black rounded-full w-12 h-12 text-3xl"
    >
        ›
    </button>

    <div class="swiper photo-modal-main h-[calc(100vh-150px)] pt-16">
        <div class="swiper-wrapper">
            @foreach($modalPhotos as $photo)
                <div class="swiper-slide !flex items-center justify-center">
                    <img
                        src="{{ $photoPath($photo) }}"
                        class="max-h-[calc(100vh-180px)] max-w-[90vw] object-contain"
                        alt=""
                    >
                </div>
            @endforeach
        </div>
    </div>

    <div class="absolute bottom-0 left-0 right-0 z-30 h-[110px] bg-black px-6 py-4">
        <div class="swiper photo-modal-thumbs max-w-5xl mx-auto">
            <div class="swiper-wrapper">
                @foreach($modalPhotos as $photo)
                    <div class="swiper-slide cursor-pointer opacity-60">
                        <img
                            src="{{ $photoPath($photo) }}"
                            class="h-20 w-full object-cover rounded"
                            alt=""
                        >
                    </div>
                @endforeach
            </div>
        </div>
    </div>

</div>

@vite(['resources/js/photo-modal.js'])
</body>
</html>