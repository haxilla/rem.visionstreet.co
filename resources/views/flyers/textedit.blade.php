@include('member.layout.head')

<body class="min-h-screen bg-slate-100 text-slate-950">

@include('member.layout.nav')

@php
    $remarks = $propInfo->theRemarks ?? null;
    $map = $propInfo->theMap ?? null;

    $headlineValue = $propInfo->xxHeadline ?: $propInfo->xHeadline;

    $bedsValue = $propInfo->xxBeds ?: $propInfo->xBeds;
    $bathsValue = $propInfo->xxBaths ?: $propInfo->xBaths;
    $sqftValue = $propInfo->xxSqft ?: $propInfo->xSqft;
    $yearValue = $propInfo->xxYrBuilt ?: $propInfo->xYrBuilt;
    $zipValue = $propInfo->xxZip ?: $propInfo->xZip;
    $virtualTourValue = $propInfo->xxVirtualTour ?: $propInfo->xVirtualTour;

    $poolType = '';

    if (!empty($propInfo->xxPoolPvt) || !empty($propInfo->xPoolPvt)) {
        $poolType = 'private';
    }

    if (!empty($propInfo->xPoolCommunity)) {
        $poolType = 'community';
    }

    $parkingValue = $propInfo->xParking ?? '';

    $defaultPhoto = $propInfo->thePhotos
        ? $propInfo->thePhotos->where('def', 1)->first()
        : null;

    $defaultPhotoUrl = null;

    if ($defaultPhoto && $propInfo->theMeta) {
        $defaultPhotoUrl =
            'https://realtyrepublic.com/hqphotos/'
            . $propInfo->theMeta->zipDir . '/'
            . $propInfo->theMeta->mlsDir . '/'
            . $defaultPhoto->photoName;
    }
@endphp

<main class="mx-auto max-w-6xl px-4 pt-12 pb-10">

    {{-- Sticky Action Bar --}}
    <div class="sticky top-[72px] z-40 -mx-4 mb-8 border-b border-slate-300 bg-slate-100/95 px-4 py-3 backdrop-blur">
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-3">
            <a href="/member/flyer/{{ $propInfo->id }}"
               class="inline-flex items-center justify-center rounded-md border border-slate-300 bg-white px-4 py-2.5 text-sm font-extrabold text-slate-700 shadow-sm hover:bg-slate-50">
                <span class="mr-2 text-lg leading-none">←</span>
                Back to Flyer
            </a>

            <button form="flyerTextForm"
                    type="submit"
                    class="inline-flex items-center justify-center rounded-md bg-emerald-700 px-5 py-2.5 text-sm font-extrabold text-white shadow-sm hover:bg-emerald-800">
                Save Text
                <span class="ml-2 text-lg leading-none">→</span>
            </button>
        </div>
    </div>

    {{-- Header --}}
    <div class="mb-6 rounded-xl border border-slate-300 bg-white p-4 shadow-sm">
        <div class="flex items-start gap-4">
            @if($defaultPhotoUrl)
                <div class="shrink-0">
                    <img src="{{ $defaultPhotoUrl }}"
                         alt="Property Photo"
                         class="h-20 w-28 rounded-md border border-slate-300 bg-white object-cover shadow-sm sm:h-24 sm:w-36">
                </div>
            @endif

            <div class="min-w-0 flex-1">

                <div class="mt-1 text-xs font-bold text-slate-500 sm:text-sm">
                    MLS #{{ $propInfo->xMlsNum }}
                </div>

                <h1 class="mt-1 text-xl font-extrabold leading-snug text-slate-950 sm:text-3xl">
                    {{ $propInfo->xFullStreet }}
                </h1>

                <div class="mt-1 text-sm text-slate-600 sm:text-base">
                    {{ $propInfo->xCity }}, {{ $propInfo->xState }} {{ $zipValue }}
                </div>
            </div>
        </div>
    </div>

    <form id="flyerTextForm" method="POST" action="" class="space-y-6">
        @csrf

        {{-- Property Header --}}
        <section class="overflow-hidden rounded-xl border border-slate-300 bg-white shadow-sm">
            <div class="bg-gradient-to-r from-[#1b2f63] to-[#2a4486] px-6 py-5">
                <h2 class="text-xl font-extrabold text-white">Property Header</h2>
                <p class="mt-1 text-sm text-blue-100">
                    Main headline shown at the top of the flyer.
                </p>
            </div>

            <div class="p-6">
                <label class="mb-2 block text-sm font-extrabold text-blue-950">
                    Headline
                </label>

                <textarea name="xxHeadline"
                          rows="4"
                          class="block w-full rounded-md border border-slate-400 bg-white px-4 py-3 text-base leading-7 text-slate-950 shadow-inner outline-none focus:border-[#1b2f63] focus:ring-4 focus:ring-blue-900/10">{{ old('xxHeadline', $headlineValue) }}</textarea>
            </div>
        </section>

        {{-- Property Details --}}
        <section class="overflow-hidden rounded-xl border border-slate-300 bg-white shadow-sm">
            <div class="border-b border-slate-200 bg-slate-50 px-6 py-5">
                <h2 class="text-xl font-extrabold text-slate-950">Property Details</h2>
                <p class="mt-1 text-sm text-slate-600">
                    MLS number, address, and location details shown on the flyer.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-5 p-6 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-extrabold text-blue-950">MLS Number</label>
                    <input name="xMlsNum"
                           value="{{ old('xMlsNum', $propInfo->xMlsNum) }}"
                           class="block w-full rounded-md border border-slate-400 bg-white px-4 py-3 text-base text-slate-950 shadow-inner outline-none focus:border-[#1b2f63] focus:ring-4 focus:ring-blue-900/10">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-extrabold text-blue-950">Street Address</label>
                    <input name="xFullStreet"
                           value="{{ old('xFullStreet', $propInfo->xFullStreet) }}"
                           class="block w-full rounded-md border border-slate-400 bg-white px-4 py-3 text-base text-slate-950 shadow-inner outline-none focus:border-[#1b2f63] focus:ring-4 focus:ring-blue-900/10">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-extrabold text-blue-950">City</label>
                    <input name="xCity"
                           value="{{ old('xCity', $propInfo->xCity) }}"
                           class="block w-full rounded-md border border-slate-400 bg-white px-4 py-3 text-base text-slate-950 shadow-inner outline-none focus:border-[#1b2f63] focus:ring-4 focus:ring-blue-900/10">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-extrabold text-blue-950">State</label>
                    <input name="xState"
                           value="{{ old('xState', $propInfo->xState) }}"
                           class="block w-full rounded-md border border-slate-400 bg-white px-4 py-3 text-base text-slate-950 shadow-inner outline-none focus:border-[#1b2f63] focus:ring-4 focus:ring-blue-900/10">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-extrabold text-blue-950">Zip</label>
                    <input name="xxZip"
                           value="{{ old('xxZip', $zipValue) }}"
                           placeholder="Zip Code"
                           class="block w-full rounded-md border border-slate-400 bg-white px-4 py-3 text-base text-slate-950 shadow-inner outline-none focus:border-[#1b2f63] focus:ring-4 focus:ring-blue-900/10">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-extrabold text-blue-950">Major Cross Streets</label>
                    <input name="xIntersection"
                           value="{{ old('xIntersection', $map->xIntersection ?? '') }}"
                           class="block w-full rounded-md border border-slate-400 bg-white px-4 py-3 text-base text-slate-950 shadow-inner outline-none focus:border-[#1b2f63] focus:ring-4 focus:ring-blue-900/10">
                </div>
            </div>
        </section>

        {{-- Features --}}
        <section class="overflow-hidden rounded-xl border border-slate-300 bg-white shadow-sm">
            <div class="border-b border-slate-200 bg-slate-50 px-6 py-5">
                <h2 class="text-xl font-extrabold text-slate-950">Features</h2>
                <p class="mt-1 text-sm text-slate-600">
                    Core property features and searchable facts.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-5 p-6 md:grid-cols-2 lg:grid-cols-3">
                <div>
                    <label class="mb-2 block text-sm font-extrabold text-blue-950">Beds</label>
                    <input name="xxBeds" value="{{ old('xxBeds', $bedsValue) }}"
                           class="block w-full rounded-md border border-slate-400 bg-white px-4 py-3 text-base text-slate-950 shadow-inner outline-none focus:border-[#1b2f63] focus:ring-4 focus:ring-blue-900/10">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-extrabold text-blue-950">Baths</label>
                    <input name="xxBaths" value="{{ old('xxBaths', $bathsValue) }}"
                           class="block w-full rounded-md border border-slate-400 bg-white px-4 py-3 text-base text-slate-950 shadow-inner outline-none focus:border-[#1b2f63] focus:ring-4 focus:ring-blue-900/10">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-extrabold text-blue-950">Sqft</label>
                    <input name="xxSqft" value="{{ old('xxSqft', $sqftValue) }}"
                           class="block w-full rounded-md border border-slate-400 bg-white px-4 py-3 text-base text-slate-950 shadow-inner outline-none focus:border-[#1b2f63] focus:ring-4 focus:ring-blue-900/10">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-extrabold text-blue-950">Year Built</label>
                    <input name="xxYrBuilt" value="{{ old('xxYrBuilt', $yearValue) }}"
                           placeholder="Year Built"
                           class="block w-full rounded-md border border-slate-400 bg-white px-4 py-3 text-base text-slate-950 shadow-inner outline-none focus:border-[#1b2f63] focus:ring-4 focus:ring-blue-900/10">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-extrabold text-blue-950">Pool</label>
                    <select name="poolType"
                            class="block w-full rounded-md border border-slate-400 bg-white px-4 py-3 text-base text-slate-950 shadow-inner outline-none focus:border-[#1b2f63] focus:ring-4 focus:ring-blue-900/10">
                        <option value="" @selected(old('poolType', $poolType) === '')>Select Pool</option>
                        <option value="none" @selected(old('poolType', $poolType) === 'none')>No Pool</option>
                        <option value="private" @selected(old('poolType', $poolType) === 'private')>Private Pool</option>
                        <option value="community" @selected(old('poolType', $poolType) === 'community')>Community Pool</option>
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-extrabold text-blue-950">Parking</label>
                    <select name="xParking"
                            class="block w-full rounded-md border border-slate-400 bg-white px-4 py-3 text-base text-slate-950 shadow-inner outline-none focus:border-[#1b2f63] focus:ring-4 focus:ring-blue-900/10">
                        <option value="">Select Parking</option>
                        <option value="Slab" @selected(old('xParking', $parkingValue) === 'Slab')>Slab</option>
                        <option value="Carport" @selected(old('xParking', $parkingValue) === 'Carport')>Carport</option>
                        <option value="1 Garage" @selected(old('xParking', $parkingValue) === '1 Garage')>1 Garage</option>
                        <option value="2 Garage" @selected(old('xParking', $parkingValue) === '2 Garage')>2 Garage</option>
                        <option value="3 Garage" @selected(old('xParking', $parkingValue) === '3 Garage')>3 Garage</option>
                        <option value="4 Garage+" @selected(old('xParking', $parkingValue) === '4 Garage+')>4 Garage+</option>
                    </select>
                </div>
            </div>
        </section>

        {{-- Main Remarks --}}
        <section class="overflow-hidden rounded-xl border border-slate-300 bg-white shadow-sm">
            <div class="border-b border-slate-200 bg-slate-50 px-6 py-5">
                <h2 class="text-xl font-extrabold text-slate-950">Main Remarks</h2>
                <p class="mt-1 text-sm text-slate-600">
                    The primary paragraph shown in the flyer body.
                </p>
            </div>

            <div class="p-6">
                <label class="mb-2 block text-sm font-extrabold text-blue-950">Public Remarks</label>
                <textarea name="xPubRemarks"
                          rows="10"
                          class="block w-full rounded-md border border-slate-400 bg-white px-4 py-3 text-base leading-7 text-slate-950 shadow-inner outline-none focus:border-[#1b2f63] focus:ring-4 focus:ring-blue-900/10">{{ old('xPubRemarks', $remarks->xPubRemarks ?? '') }}</textarea>
            </div>
        </section>

        {{-- Feature Bullets --}}
        <section class="overflow-hidden rounded-xl border border-slate-300 bg-white shadow-sm">
            <div class="border-b border-slate-200 bg-slate-50 px-6 py-5">
                <h2 class="text-xl font-extrabold text-slate-950">Feature Bullets</h2>
                <p class="mt-1 text-sm text-slate-600">
                    Short feature highlights used in the flyer callout areas.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-5 p-6 md:grid-cols-2">
                @for($i = 1; $i <= 8; $i++)
                    @php $field = 'xb' . $i; @endphp

                    <div>
                        <label class="mb-2 block text-sm font-extrabold text-blue-950">
                            Bullet Point {{ $i }}
                        </label>

                        <input name="{{ $field }}"
                               value="{{ old($field, $remarks->$field ?? '') }}"
                               class="block w-full rounded-md border border-slate-400 bg-white px-4 py-3 text-base text-slate-950 shadow-inner outline-none focus:border-[#1b2f63] focus:ring-4 focus:ring-blue-900/10">
                    </div>
                @endfor
            </div>
        </section>

        {{-- Online Links --}}
        <section class="overflow-hidden rounded-xl border border-slate-300 bg-white shadow-sm">
            <div class="border-b border-slate-200 bg-slate-50 px-6 py-5">
                <h2 class="text-xl font-extrabold text-slate-950">Online Links</h2>
                <p class="mt-1 text-sm text-slate-600">
                    Optional links shown in the flyer link bar.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-5 p-6 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-extrabold text-blue-950">MLS Link</label>
                    <input name="xMlsLink"
                           value="{{ old('xMlsLink', $propInfo->xMlsLink) }}"
                           class="block w-full rounded-md border border-slate-400 bg-white px-4 py-3 text-base text-slate-950 shadow-inner outline-none focus:border-[#1b2f63] focus:ring-4 focus:ring-blue-900/10">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-extrabold text-blue-950">Virtual Tour Link</label>
                    <input name="xxVirtualTour"
                           value="{{ old('xxVirtualTour', $virtualTourValue) }}"
                           class="block w-full rounded-md border border-slate-400 bg-white px-4 py-3 text-base text-slate-950 shadow-inner outline-none focus:border-[#1b2f63] focus:ring-4 focus:ring-blue-900/10">
                </div>
            </div>
        </section>

    </form>
</main>

</body>
</html>