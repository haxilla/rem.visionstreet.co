<!DOCTYPE html>
<html lang="en" class="h-full bg-white text-gray-900">
@include('public.layout.head');

<body class="min-h-screen bg-white pt-[50px] linkcheck">
    @include('public.layout.nav')
</body>

@php
    $photos = $details->thePhotos ?? collect();

    $hero = $photos->where('def', 1)->first() ?? $photos->first();
    $thumbs = $photos->where('photoID', '!=', $hero?->photoID)->take(4);

    $price = number_format($details->xListPrice ?? 0);
    $beds = $details->xxBeds ?: $details->xBeds;
    $baths = $details->xxBaths ?: $details->xBaths;
    $sqft = $details->xxSqft ?: $details->xSqft;
    $year = $details->xxYrBuilt ?: $details->xYrBuilt;
    $zip = $details->xxZip;
    $mls = $details->xMlsNum;

    $photoPath = fn ($photo) => "https://realtyrepublic.com/hqphotos/{$zip}/{$mls}/{$photo->photoName}";
@endphp

<section class="max-w-[1280px] mx-auto px-4 py-4 text-slate-950">

    {{-- Photo grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-2 rounded-xl overflow-hidden">

        <div class="relative h-[445px] bg-slate-200">
            @if($hero)
                <img
                    src="{{ $photoPath($hero) }}"
                    class="w-full h-full object-cover"
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
                        class="w-full h-full object-cover"
                        alt=""
                    >

                    @if($loop->last)
                        <button class="absolute bottom-4 right-4 bg-white rounded-md px-5 py-3 text-sm font-bold shadow-lg flex items-center gap-2">
                            <span class="text-xl leading-none">▦</span>
                            See all {{ $photos->count() }} photos
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
                    Price cut: $20K (4/1)
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
                    <span>Single Family Residence</span>
                </div>

                <div class="bg-slate-100 rounded px-4 py-4 flex items-center gap-3">
                    <span class="text-xl">⚒</span>
                    <span>Built in {{ $year }}</span>
                </div>

                <div class="bg-slate-100 rounded px-4 py-4 flex items-center gap-3">
                    <span class="text-xl">♙</span>
                    <span>0.44 Acres Lot</span>
                </div>

                <div class="bg-slate-100 rounded px-4 py-4 flex items-center gap-3">
                    <span class="text-xl">▰</span>
                    <span>$-- Zestimate®</span>
                </div>

                <div class="bg-slate-100 rounded px-4 py-4 flex items-center gap-3">
                    <span class="text-xl">▥</span>
                    <span>$283/sqft</span>
                </div>

                <div class="bg-slate-100 rounded px-4 py-4 flex items-center gap-3">
                    <span class="text-xl">♧</span>
                    <span>$427/mo HOA</span>
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

</html>