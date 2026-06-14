@include('member.layout.head')

<body class="min-h-screen bg-slate-100 text-slate-950">

@include('member.layout.nav')

@php
    $remarks = $propInfo->theRemarks ?? null;
    $map = $propInfo->theMap ?? null;

    $headlineValue = $propInfo->xxHeadline ?? $propInfo->xHeadline ?? '';
@endphp

<main class="mx-auto max-w-6xl px-4 pt-28 pb-10">

    {{-- Top Bar --}}
    <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <div class="text-sm font-extrabold uppercase tracking-wide text-[#1b2f63]">
                Edit Flyer Text
            </div>

            <h1 class="mt-1 text-3xl font-extrabold leading-tight text-slate-950">
                {{ $propInfo->xFullStreet }}
            </h1>

            <div class="mt-1 text-slate-600">
                {{ $propInfo->xCity }}, {{ $propInfo->xState }} {{ $propInfo->xZip }}
            </div>
        </div>

        <div class="flex gap-3">
            <a href="/member/flyer/{{ $propInfo->id }}"
               class="inline-flex items-center justify-center rounded-md border border-slate-300 bg-white px-5 py-3 text-sm font-extrabold text-slate-700 shadow-sm hover:bg-slate-50">
                Back to Flyer
            </a>

            <button form="flyerTextForm"
                    type="submit"
                    class="inline-flex items-center justify-center rounded-md bg-emerald-700 px-6 py-3 text-sm font-extrabold text-white shadow-sm hover:bg-emerald-800">
                Save Text
            </button>
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

                <div class="mt-2 text-xs text-slate-500">
                    This is the main descriptive headline beside the graphic header.
                </div>
            </div>
        </section>

        {{-- Property Details --}}
        <section class="overflow-hidden rounded-xl border border-slate-300 bg-white shadow-sm">
            <div class="border-b border-slate-200 bg-slate-50 px-6 py-5">
                <h2 class="text-xl font-extrabold text-slate-950">Property Details</h2>
                <p class="mt-1 text-sm text-slate-600">
                    Address and quick facts used in the flyer summary areas.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-5 p-6 md:grid-cols-12">
                <div class="md:col-span-6">
                    <label class="mb-2 block text-sm font-extrabold text-blue-950">Street Address</label>
                    <input name="xFullStreet"
                           value="{{ old('xFullStreet', $propInfo->xFullStreet) }}"
                           class="block w-full rounded-md border border-slate-400 bg-white px-4 py-3 text-base text-slate-950 shadow-inner outline-none focus:border-[#1b2f63] focus:ring-4 focus:ring-blue-900/10">
                </div>

                <div class="md:col-span-3">
                    <label class="mb-2 block text-sm font-extrabold text-blue-950">City</label>
                    <input name="xCity"
                           value="{{ old('xCity', $propInfo->xCity) }}"
                           class="block w-full rounded-md border border-slate-400 bg-white px-4 py-3 text-base text-slate-950 shadow-inner outline-none focus:border-[#1b2f63] focus:ring-4 focus:ring-blue-900/10">
                </div>

                <div class="md:col-span-3">
                    <label class="mb-2 block text-sm font-extrabold text-blue-950">State</label>
                    <input name="xState"
                           value="{{ old('xState', $propInfo->xState) }}"
                           class="block w-full rounded-md border border-slate-400 bg-white px-4 py-3 text-base text-slate-950 shadow-inner outline-none focus:border-[#1b2f63] focus:ring-4 focus:ring-blue-900/10">
                </div>

                <div class="md:col-span-3">
                    <label class="mb-2 block text-sm font-extrabold text-blue-950">Zip</label>
                    <input name="xZip"
                           value="{{ old('xZip', $propInfo->xZip) }}"
                           class="block w-full rounded-md border border-slate-400 bg-white px-4 py-3 text-base text-slate-950 shadow-inner outline-none focus:border-[#1b2f63] focus:ring-4 focus:ring-blue-900/10">
                </div>

                <div class="md:col-span-3">
                    <label class="mb-2 block text-sm font-extrabold text-blue-950">MLS Number</label>
                    <input name="xMlsNum"
                           value="{{ old('xMlsNum', $propInfo->xMlsNum) }}"
                           class="block w-full rounded-md border border-slate-400 bg-white px-4 py-3 text-base text-slate-950 shadow-inner outline-none focus:border-[#1b2f63] focus:ring-4 focus:ring-blue-900/10">
                </div>

                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm font-extrabold text-blue-950">Beds</label>
                    <input name="xxBeds"
                           value="{{ old('xxBeds', $propInfo->xxBeds ?: $propInfo->xBeds) }}"
                           class="block w-full rounded-md border border-slate-400 bg-white px-4 py-3 text-base text-slate-950 shadow-inner outline-none focus:border-[#1b2f63] focus:ring-4 focus:ring-blue-900/10">
                </div>

                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm font-extrabold text-blue-950">Baths</label>
                    <input name="xxBaths"
                           value="{{ old('xxBaths', $propInfo->xxBaths ?: $propInfo->xBaths) }}"
                           class="block w-full rounded-md border border-slate-400 bg-white px-4 py-3 text-base text-slate-950 shadow-inner outline-none focus:border-[#1b2f63] focus:ring-4 focus:ring-blue-900/10">
                </div>

                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm font-extrabold text-blue-950">Sqft</label>
                    <input name="xxSqft"
                           value="{{ old('xxSqft', $propInfo->xxSqft ?: $propInfo->xSqft) }}"
                           class="block w-full rounded-md border border-slate-400 bg-white px-4 py-3 text-base text-slate-950 shadow-inner outline-none focus:border-[#1b2f63] focus:ring-4 focus:ring-blue-900/10">
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

        {{-- Location --}}
        <section class="overflow-hidden rounded-xl border border-slate-300 bg-white shadow-sm">
            <div class="border-b border-slate-200 bg-slate-50 px-6 py-5">
                <h2 class="text-xl font-extrabold text-slate-950">Location</h2>
                <p class="mt-1 text-sm text-slate-600">
                    Displayed as Major Cross Streets on the flyer.
                </p>
            </div>

            <div class="p-6">
                <label class="mb-2 block text-sm font-extrabold text-blue-950">
                    Major Cross Streets
                </label>

                <input name="xIntersection"
                       value="{{ old('xIntersection', $map->xIntersection ?? '') }}"
                       class="block w-full rounded-md border border-slate-400 bg-white px-4 py-3 text-base text-slate-950 shadow-inner outline-none focus:border-[#1b2f63] focus:ring-4 focus:ring-blue-900/10">
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
                    <input name="xVirtualTour"
                           value="{{ old('xVirtualTour', $propInfo->xVirtualTour) }}"
                           class="block w-full rounded-md border border-slate-400 bg-white px-4 py-3 text-base text-slate-950 shadow-inner outline-none focus:border-[#1b2f63] focus:ring-4 focus:ring-blue-900/10">
                </div>
            </div>
        </section>

        {{-- Bottom Save Bar --}}
        <div class="sticky bottom-0 z-20 -mx-4 mt-8 border-t border-slate-300 bg-slate-100/95 px-4 py-4 backdrop-blur">
            <div class="mx-auto flex max-w-6xl justify-end gap-3">
                <a href="/member/flyer/{{ $propInfo->id }}"
                   class="inline-flex items-center justify-center rounded-md border border-slate-300 bg-white px-5 py-3 text-sm font-extrabold text-slate-700 shadow-sm hover:bg-slate-50">
                    Cancel
                </a>

                <button type="submit"
                        class="inline-flex items-center justify-center rounded-md bg-emerald-700 px-7 py-3 text-sm font-extrabold text-white shadow-sm hover:bg-emerald-800">
                    Save Text Changes
                </button>
            </div>
        </div>

    </form>
</main>

</body>
</html>