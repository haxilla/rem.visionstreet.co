@include('member.layout.head')

<body class="bg-slate-100 min-h-screen text-slate-900">

@include('member.layout.nav')

@php
    $remarks = $propInfo->theRemarks ?? null;
    $map = $propInfo->theMap ?? null;
@endphp

<div class="max-w-6xl mx-auto px-4 py-8">

    {{-- Top Bar --}}
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="text-sm font-bold uppercase tracking-wide text-blue-900">
                Edit Flyer Text
            </div>
            <h1 class="text-3xl font-bold text-slate-950 mt-1">
                {{ $propInfo->xFullStreet }}
            </h1>
            <div class="text-slate-600 mt-1">
                {{ $propInfo->xCity }}, {{ $propInfo->xState }} {{ $propInfo->xZip }}
            </div>
        </div>

        <div class="flex gap-3">
            <a href="/member/flyer/{{ $propInfo->id }}"
               class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-700 shadow-sm hover:bg-slate-50">
                Back to Flyer
            </a>

            <button form="flyerTextForm"
                    type="submit"
                    class="inline-flex items-center justify-center rounded-lg bg-emerald-700 px-6 py-3 text-sm font-bold text-white shadow-sm hover:bg-emerald-800">
                Save Text
            </button>
        </div>
    </div>

    <form id="flyerTextForm" method="POST" action="" class="space-y-6">
        @csrf

        {{-- Property Header --}}
        <section class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="bg-gradient-to-r from-[#1b2f63] to-[#2a4486] px-6 py-4">
                <h2 class="text-white text-lg font-bold">Property Header</h2>
                <p class="text-blue-100 text-sm mt-1">Main headline and core listing details.</p>
            </div>

            <div class="p-6 grid grid-cols-1 gap-5">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Headline</label>
                    <textarea name="xHeadline" rows="3"
                              class="w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-800 focus:ring-blue-800">{{ old('xHeadline', $propInfo->xHeadline) }}</textarea>
                </div>
            </div>
        </section>

        {{-- Property Details --}}
        <section class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                <h2 class="text-lg font-bold text-slate-950">Property Details</h2>
                <p class="text-sm text-slate-500 mt-1">These appear in the address and summary areas of the flyer.</p>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                <div class="lg:col-span-2">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Street Address</label>
                    <input name="xFullStreet" value="{{ old('xFullStreet', $propInfo->xFullStreet) }}"
                           class="w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-800 focus:ring-blue-800">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">City</label>
                    <input name="xCity" value="{{ old('xCity', $propInfo->xCity) }}"
                           class="w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-800 focus:ring-blue-800">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">State</label>
                    <input name="xState" value="{{ old('xState', $propInfo->xState) }}"
                           class="w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-800 focus:ring-blue-800">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Zip</label>
                    <input name="xZip" value="{{ old('xZip', $propInfo->xZip) }}"
                           class="w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-800 focus:ring-blue-800">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">MLS Number</label>
                    <input name="xMlsNum" value="{{ old('xMlsNum', $propInfo->xMlsNum) }}"
                           class="w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-800 focus:ring-blue-800">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Beds</label>
                    <input name="xxBeds" value="{{ old('xxBeds', $propInfo->xxBeds ?: $propInfo->xBeds) }}"
                           class="w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-800 focus:ring-blue-800">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Baths</label>
                    <input name="xxBaths" value="{{ old('xxBaths', $propInfo->xxBaths ?: $propInfo->xBaths) }}"
                           class="w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-800 focus:ring-blue-800">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Sqft</label>
                    <input name="xxSqft" value="{{ old('xxSqft', $propInfo->xxSqft ?: $propInfo->xSqft) }}"
                           class="w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-800 focus:ring-blue-800">
                </div>
            </div>
        </section>

        {{-- Main Remarks --}}
        <section class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                <h2 class="text-lg font-bold text-slate-950">Main Remarks</h2>
                <p class="text-sm text-slate-500 mt-1">The primary description shown in the flyer body.</p>
            </div>

            <div class="p-6">
                <label class="block text-sm font-bold text-slate-700 mb-2">Public Remarks</label>
                <textarea name="xPubRemarks" rows="8"
                          class="w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-800 focus:ring-blue-800">{{ old('xPubRemarks', $remarks->xPubRemarks ?? '') }}</textarea>
            </div>
        </section>

        {{-- Feature Bullets --}}
        <section class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                <h2 class="text-lg font-bold text-slate-950">Feature Bullets</h2>
                <p class="text-sm text-slate-500 mt-1">Short highlights used throughout the flyer.</p>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                @for($i = 1; $i <= 8; $i++)
                    @php $field = 'xb' . $i; @endphp

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">
                            Bullet Point {{ $i }}
                        </label>
                        <input name="{{ $field }}"
                               value="{{ old($field, $remarks->$field ?? '') }}"
                               class="w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-800 focus:ring-blue-800">
                    </div>
                @endfor
            </div>
        </section>

        {{-- Location --}}
        <section class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                <h2 class="text-lg font-bold text-slate-950">Location</h2>
                <p class="text-sm text-slate-500 mt-1">Displayed as Major Cross Streets.</p>
            </div>

            <div class="p-6">
                <label class="block text-sm font-bold text-slate-700 mb-2">Major Cross Streets</label>
                <input name="xIntersection"
                       value="{{ old('xIntersection', $map->xIntersection ?? '') }}"
                       class="w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-800 focus:ring-blue-800">
            </div>
        </section>

        {{-- Online Links --}}
        <section class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                <h2 class="text-lg font-bold text-slate-950">Online Links</h2>
                <p class="text-sm text-slate-500 mt-1">Optional links shown in the flyer link bar.</p>
            </div>

            <div class="p-6 grid grid-cols-1 gap-5">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">MLS Link</label>
                    <input name="xMlsLink" value="{{ old('xMlsLink', $propInfo->xMlsLink) }}"
                           class="w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-800 focus:ring-blue-800">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Virtual Tour Link</label>
                    <input name="xVirtualTour" value="{{ old('xVirtualTour', $propInfo->xVirtualTour) }}"
                           class="w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-800 focus:ring-blue-800">
                </div>
            </div>
        </section>

        {{-- Bottom Save --}}
        <div class="sticky bottom-0 bg-slate-100/95 backdrop-blur border-t border-slate-200 py-4 flex justify-end gap-3">
            <a href="/member/flyer/{{ $propInfo->id }}"
               class="rounded-lg border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-700 shadow-sm hover:bg-slate-50">
                Cancel
            </a>

            <button type="submit"
                    class="rounded-lg bg-emerald-700 px-7 py-3 text-sm font-bold text-white shadow-sm hover:bg-emerald-800">
                Save Text Changes
            </button>
        </div>

    </form>
</div>

</body>
</html>