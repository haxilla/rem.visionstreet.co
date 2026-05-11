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
@endphp

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

    {{-- Main content --}}
    <div class="grid grid-cols-1 lg:grid-cols-[1fr_260px] gap-9 mt-6">

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

            {{-- Remarks --}}
            <section class="max-w-[760px]">
                <h2 class="text-2xl font-bold mb-5">What's special</h2>

                <p class="text-lg leading-8">
                    {{ $details->theRemarks->xPubRemarks ?? '' }}
                </p>
            </section>

        </main>

        {{-- Sidebar --}}

        <div style="margin:0; padding:20px; background:#f5f5f5; display:flex; justify-content:center;">

        <div style="width:240px; font-family:'Georgia',serif; background:#fff; border:1px solid #ddd; border-radius:4px; overflow:hidden; box-shadow: 2px 4px 16px rgba(0,0,0,0.10);">

            <div style="background:#1a3a5c; padding:22px 16px 14px; text-align:center;">
            <p style="color:#c8a84b; font-size:11px; letter-spacing:2px; text-transform:uppercase; margin:0 0 4px; font-family:Arial,sans-serif; font-weight:700;">Send an E-Flyer</p>
            <p style="color:#e8e0d0; font-size:11px; letter-spacing:1px; text-transform:uppercase; margin:0 0 12px; font-family:Arial,sans-serif;">Starting at</p>
            <div style="width:72px; height:72px; background:#c8a84b; border-radius:50%; margin:0 auto 10px; display:flex; align-items:center; justify-content:center; border:3px solid #fff;">
                <span style="color:#1a3a5c; font-size:28px; font-weight:700; font-family:Arial,sans-serif; line-height:1;">$9</span>
            </div>
            </div>

            <div style="background:#c8a84b; padding:10px 16px; text-align:center;">
            <p style="margin:0; color:#1a3a5c; font-size:11px; letter-spacing:2px; text-transform:uppercase; font-weight:700; font-family:Arial,sans-serif;">Premium Services for Less</p>
            </div>

            <div style="background:#fff; padding:14px 18px 6px;">
            <ul style="list-style:none; margin:0; padding:0;">
                <li style="display:flex; align-items:flex-start; gap:8px; padding:6px 0; border-bottom:1px solid #f0ede6; font-family:Arial,sans-serif; font-size:12.5px; color:#2c2c2a;">
                <span style="color:#c8a84b; font-weight:700; margin-top:1px;">&#10003;</span> Instant Proof &amp; Delivery
                </li>
                <li style="display:flex; align-items:flex-start; gap:8px; padding:6px 0; border-bottom:1px solid #f0ede6; font-family:Arial,sans-serif; font-size:12.5px; color:#2c2c2a;">
                <span style="color:#c8a84b; font-weight:700; margin-top:1px;">&#10003;</span> Instant Copy to Home Seller
                </li>
                <li style="display:flex; align-items:flex-start; gap:8px; padding:6px 0; border-bottom:1px solid #f0ede6; font-family:Arial,sans-serif; font-size:12.5px; color:#2c2c2a;">
                <span style="color:#c8a84b; font-weight:700; margin-top:1px;">&#10003;</span> Flyers Saved &amp; Editable for Resends
                </li>
                <li style="display:flex; align-items:flex-start; gap:8px; padding:6px 0; border-bottom:1px solid #f0ede6; font-family:Arial,sans-serif; font-size:12.5px; color:#2c2c2a;">
                <span style="color:#c8a84b; font-weight:700; margin-top:1px;">&#10003;</span> Upload Unlimited Photos
                </li>
                <li style="display:flex; align-items:flex-start; gap:8px; padding:6px 0; border-bottom:1px solid #f0ede6; font-family:Arial,sans-serif; font-size:12.5px; color:#2c2c2a;">
                <span style="color:#c8a84b; font-weight:700; margin-top:1px;">&#10003;</span> FREE Web Page Slide Show
                </li>
                <li style="display:flex; align-items:flex-start; gap:8px; padding:6px 0; border-bottom:1px solid #f0ede6; font-family:Arial,sans-serif; font-size:12.5px; color:#2c2c2a;">
                <span style="color:#c8a84b; font-weight:700; margin-top:1px;">&#10003;</span> FREE Page View Reports
                </li>
                <li style="display:flex; align-items:flex-start; gap:8px; padding:6px 0; border-bottom:1px solid #f0ede6; font-family:Arial,sans-serif; font-size:12.5px; color:#2c2c2a;">
                <span style="color:#c8a84b; font-weight:700; margin-top:1px;">&#10003;</span> Personal Contact Copy Center
                </li>
                <li style="display:flex; align-items:flex-start; gap:8px; padding:6px 0; font-family:Arial,sans-serif; font-size:12.5px; color:#2c2c2a;">
                <span style="color:#c8a84b; font-weight:700; margin-top:1px;">&#10003;</span> Multiple Flyer Templates
                </li>
            </ul>
            </div>

            <div style="padding:6px 18px 4px; text-align:center;">
            <p style="margin:0; font-family:Arial,sans-serif; font-size:13px; font-weight:700; color:#1a3a5c; letter-spacing:1px; text-transform:uppercase;">And More!</p>
            </div>

            <div style="padding:12px 16px 18px;">
            <a href="#" style="display:block; width:100%; background:#1a3a5c; color:#fff; text-decoration:none; text-align:center; border-radius:3px; padding:11px 0; font-family:Arial,sans-serif; font-size:12px; font-weight:700; letter-spacing:1.5px; text-transform:uppercase; box-sizing:border-box;">Get Started</a>
            </div>

        </div>

        </div>

        <aside class="lg:pt-0">
            <div class="border border-slate-300 rounded-xl p-5 sticky top-5">
                <button class="w-full bg-blue-600 hover:bg-blue-700 text-white rounded px-4 py-5 font-bold leading-tight">
                    Request a tour
                    <span class="block text-sm font-medium">
                        as early as tomorrow at 11:00 am
                    </span>
                </button>

                <button class="w-full mt-4 border border-blue-600 text-blue-700 rounded px-4 py-4 font-bold">
                    Contact agent
                </button>
            </div>
        </aside>

    </div>

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