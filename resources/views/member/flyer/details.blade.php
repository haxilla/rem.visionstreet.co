@php
    $flyer = $data['flyer'];
@endphp

@include('member.layout.head')

<body data-section="member" class="bg-slate-100">

@include('member.layout.nav')

<main class="pt-24 pb-16">

<div class="mx-auto max-w-6xl px-6">

    {{-- ========================================================= --}}
    {{-- PAGE HEADER --}}
    {{-- ========================================================= --}}

    <div class="mb-10">

        <div class="rounded-3xl bg-white p-8 shadow-sm">

            @if($flyer->xMlsNum)

                <div class="text-sm font-bold uppercase tracking-[.25em] text-[#123f91]">

                    MLS #{{ $flyer->xMlsNum }}

                </div>

            @else

                <div class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-bold uppercase tracking-wider text-amber-700">

                    Non-MLS Listing

                </div>

            @endif

            <h1 class="mt-2 text-4xl font-black text-slate-900">

                {{ $flyer->xFullStreet }}

            </h1>

            <div class="mt-1 text-lg text-slate-500">

                {{ $flyer->xCity }},
                {{ $flyer->state }}
                {{ $flyer->xZip }}

            </div>

            <div class="mt-6">

                <a
                    href="/member/flyer/create?flyerId={{ $flyer->id }}&return={{ request('return') ?: 'details' }}"
                    data-wizard-leave
                    class="inline-flex items-center rounded-xl bg-slate-100 px-5 py-3 font-semibold text-slate-700 hover:bg-slate-200">

                    ← Edit Address

                </a>

            </div>

        </div>

    </div>

    <div id="wizard-nav">
        @include('member.flyer.wizard',['flyer'=>$flyer])
    </div>

    @if($errors->any())

        <div class="mt-8 rounded-2xl border border-red-200 bg-red-50 p-5">

            <div class="font-bold text-red-700">

                Please correct the following:

            </div>

            <ul class="mt-3 list-disc pl-5 text-red-600">

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif

    <form
        id="detailsForm"
        method="POST"
        action="/member/flyer/save_details"
        class="mt-8">

        @csrf

        <input
            type="hidden"
            name="flyerId"
            value="{{ $flyer->id }}">

        <input type="hidden" name="return" value="{{ request('return') }}">

        <input type="hidden" name="redirectAfterSave" value="">

        {{-- ========================================================= --}}
        {{-- PROPERTY INFORMATION --}}
        {{-- ========================================================= --}}

        <div class="mb-8 overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-black/5">

            <div class="bg-[#123f91] px-10 py-7">

                <h2 class="text-2xl font-black text-white">

                    Property Information

                </h2>

                <p class="mt-1 text-sm text-white/70">

                    Core facts shown on the flyer and used to help buyers find this listing.

                </p>

            </div>

            <div class="p-10">

                <div class="grid gap-x-8 gap-y-6 lg:grid-cols-4">

                    <div>

                        <label class="mb-2 block text-sm font-semibold text-slate-600">

                            List Price

                        </label>

                        <div class="relative">

                            <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center font-semibold text-slate-400">$</span>

                            <input
                                type="text"
                                name="xListPrice"
                                value="{{ old('xListPrice',$flyer->xListPrice ?? '') }}"
                                class="w-full rounded-2xl border border-slate-300 py-3 pl-8 pr-4 transition focus:border-[#123f91] focus:outline-none focus:ring-4 focus:ring-[#123f91]/10">

                        </div>

                    </div>

                    <div>

                        <label class="mb-2 block text-sm font-semibold text-slate-600">

                            Property Type

                        </label>

                        <select
                            name="xPropType"
                            class="w-full rounded-2xl border border-slate-300 px-4 py-3 transition focus:border-[#123f91] focus:outline-none focus:ring-4 focus:ring-[#123f91]/10">

                            @php $propType = old('xPropType', $flyer->theMeta->xPropType ?? ''); @endphp

                            <option value="">Select</option>
                            <option value="Residential" @selected($propType === 'Residential')>Residential</option>
                            <option value="Commercial" @selected($propType === 'Commercial')>Commercial</option>
                            <option value="Land" @selected($propType === 'Land')>Land</option>
                            <option value="Multi-Family" @selected($propType === 'Multi-Family')>Multi-Family</option>

                        </select>

                    </div>

                    <div>

                        <label class="mb-2 block text-sm font-semibold text-slate-600">

                            Sale or Rental

                        </label>

                        <select
                            name="xListingType"
                            class="w-full rounded-2xl border border-slate-300 px-4 py-3 transition focus:border-[#123f91] focus:outline-none focus:ring-4 focus:ring-[#123f91]/10">

                            @php $listingType = old('xListingType', $flyer->theMeta->xListingType ?? ''); @endphp

                            <option value="">Select</option>
                            <option value="Sale" @selected($listingType === 'Sale')>Sale</option>
                            <option value="Rental" @selected($listingType === 'Rental')>Rental</option>

                        </select>

                    </div>

                    <div>

                        <label class="mb-2 block text-sm font-semibold text-slate-600">

                            Year Built

                        </label>

                        <input
                            type="text"
                            name="xYrBuilt"
                            value="{{ old('xYrBuilt', $flyer->xxYrBuilt ?: $flyer->xYrBuilt ?: '') }}"
                            class="w-full rounded-2xl border border-slate-300 px-4 py-3 transition focus:border-[#123f91] focus:outline-none focus:ring-4 focus:ring-[#123f91]/10">

                    </div>

                </div>

                <div class="mt-8 grid gap-x-8 gap-y-6 border-t border-slate-100 pt-8 sm:grid-cols-3 lg:grid-cols-4">

                    <div>

                        <label class="mb-2 flex items-center gap-2 text-sm font-semibold text-slate-600">

                            <svg class="h-4 w-4 text-[#123f91]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12h18M5 12V7a2 2 0 012-2h10a2 2 0 012 2v5M4 18v-3a1 1 0 011-1h14a1 1 0 011 1v3M3 21h18" />
                            </svg>

                            Bedrooms

                        </label>

                        <input
                            type="text"
                            name="xBeds"
                            value="{{ old('xBeds', $flyer->xxBeds ?: $flyer->xBeds ?: '') }}"
                            class="w-full rounded-2xl border border-slate-300 px-4 py-3 transition focus:border-[#123f91] focus:outline-none focus:ring-4 focus:ring-[#123f91]/10">

                    </div>

                    <div>

                        <label class="mb-2 flex items-center gap-2 text-sm font-semibold text-slate-600">

                            <svg class="h-4 w-4 text-[#123f91]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 12h16M6 12V6a2 2 0 012-2h2v3M4 12v6a2 2 0 002 2h12a2 2 0 002-2v-6" />
                            </svg>

                            Bathrooms

                        </label>

                        <input
                            type="text"
                            name="xBaths"
                            value="{{ old('xBaths', $flyer->xxBaths ?: $flyer->xBaths ?: '') }}"
                            class="w-full rounded-2xl border border-slate-300 px-4 py-3 transition focus:border-[#123f91] focus:outline-none focus:ring-4 focus:ring-[#123f91]/10">

                    </div>

                    <div>

                        <label class="mb-2 flex items-center gap-2 text-sm font-semibold text-slate-600">

                            <svg class="h-4 w-4 text-[#123f91]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 8V4h4M20 8V4h-4M4 16v4h4M20 16v4h-4" />
                            </svg>

                            Square Feet

                        </label>

                        <input
                            type="text"
                            name="xSqft"
                            value="{{ old('xSqft', $flyer->xxSqft ?: $flyer->xSqft ?: '') }}"
                            class="w-full rounded-2xl border border-slate-300 px-4 py-3 transition focus:border-[#123f91] focus:outline-none focus:ring-4 focus:ring-[#123f91]/10">

                    </div>

                    <div>

                        <label class="mb-2 block text-sm font-semibold text-slate-600">

                            Parking

                        </label>

                        <select
                            name="xParking"
                            class="w-full rounded-2xl border border-slate-300 px-4 py-3 transition focus:border-[#123f91] focus:outline-none focus:ring-4 focus:ring-[#123f91]/10">

                            <option value="">Select</option>
                            <option>1 Car Garage</option>
                            <option>2 Car Garage</option>
                            <option>3 Car Garage</option>
                            <option>4 Car Garage</option>
                            <option>RV Parking</option>

                        </select>

                    </div>

                    <div>

                        <label class="mb-2 block text-sm font-semibold text-slate-600">

                            Pool

                        </label>

                        <select
                            name="xPool"
                            class="w-full rounded-2xl border border-slate-300 px-4 py-3 transition focus:border-[#123f91] focus:outline-none focus:ring-4 focus:ring-[#123f91]/10">

                            <option value="">Select</option>
                            <option>Private Pool</option>
                            <option>Community Pool</option>
                            <option>No Pool</option>

                        </select>

                    </div>

                    <div class="sm:col-span-3 lg:col-span-2">

                        <label class="mb-2 block text-sm font-semibold text-slate-600">

                            Cross Streets

                        </label>

                        <input
                            type="text"
                            name="xIntersection"
                            value="{{ old('xIntersection',$flyer->theMap?->xIntersection ?? '') }}"
                            class="w-full rounded-2xl border border-slate-300 px-4 py-3 transition focus:border-[#123f91] focus:outline-none focus:ring-4 focus:ring-[#123f91]/10">

                    </div>

                </div>

            </div>

        </div>

        {{-- ========================================================= --}}
        {{-- FLYER HEADLINE --}}
        {{-- ========================================================= --}}

        <div class="mb-8 overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-black/5">

            <div class="border-b border-slate-100 px-10 py-7">

                <h2 class="text-2xl font-black text-slate-900">

                    Flyer Headline

                </h2>

                <p class="mt-1 text-sm text-slate-500">

                    The main headline shown at the top of the flyer.

                </p>

            </div>

            <div class="p-10">

                <input
                    type="text"
                    name="xHeadline"
                    value="{{ old('xHeadline',$flyer->xHeadline ?? '') }}"
                    class="w-full rounded-2xl border border-slate-300 px-4 py-3 transition focus:border-[#123f91] focus:outline-none focus:ring-4 focus:ring-[#123f91]/10">

            </div>

        </div>

        {{-- ========================================================= --}}
        {{-- PROPERTY HIGHLIGHTS --}}
        {{-- ========================================================= --}}

        <div class="mb-8 overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-black/5">

            <div class="border-b border-slate-100 px-10 py-7">

                <h2 class="text-2xl font-black text-slate-900">

                    Property Highlights

                </h2>

                <p class="mt-1 text-sm text-slate-500">

                    Short feature highlights used in the flyer callout areas.

                </p>

            </div>

            <div class="grid gap-4 p-10 sm:grid-cols-2">

                @for($i = 1; $i <= 8; $i++)

                    <div class="flex items-center gap-3">

                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[#123f91]/10 text-sm font-black text-[#123f91]">

                            {{ $i }}

                        </div>

                        <input
                            type="text"
                            name="xb{{ $i }}"
                            value="{{ old('xb'.$i,$flyer->theRemarks?->{'xb'.$i} ?? '') }}"
                            maxlength="42"
                            class="w-full rounded-2xl border border-slate-300 px-4 py-3 transition focus:border-[#123f91] focus:outline-none focus:ring-4 focus:ring-[#123f91]/10">

                    </div>

                @endfor

            </div>

        </div>

        {{-- ========================================================= --}}
        {{-- AGENT REMARKS --}}
        {{-- ========================================================= --}}

        <div class="mb-8 overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-black/5">

            <div class="border-b border-slate-100 px-10 py-7">

                <h2 class="text-2xl font-black text-slate-900">

                    Agent Remarks

                </h2>

                <p class="mt-1 text-sm text-slate-500">

                    The primary paragraph shown in the flyer body.

                </p>

            </div>

            <div class="p-10">

                <textarea
                    name="xPubRemarks"
                    rows="8"
                    class="w-full rounded-2xl border border-slate-300 px-4 py-3 transition focus:border-[#123f91] focus:outline-none focus:ring-4 focus:ring-[#123f91]/10">{{ old('xPubRemarks',$flyer->theRemarks?->xPubRemarks ?? '') }}</textarea>

            </div>

        </div>

        {{-- ========================================================= --}}
        {{-- ADDITIONAL RESOURCES --}}
        {{-- ========================================================= --}}

        <div class="mb-8 overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-black/5">

            <div class="border-b border-slate-100 px-10 py-7">

                <h2 class="text-2xl font-black text-slate-900">

                    Additional Resources

                </h2>

                <p class="mt-1 text-sm text-slate-500">

                    Optional links shown in the flyer link bar.

                </p>

            </div>

            <div class="grid gap-6 p-10 lg:grid-cols-2">

                <div>

                    <label class="mb-2 block text-sm font-semibold text-slate-600">

                        Virtual Tour

                    </label>

                    <input
                        type="text"
                        name="xVirtualTour"
                        value="{{ old('xVirtualTour',$flyer->xVirtualTour ?? '') }}"
                        placeholder="https://"
                        class="w-full rounded-2xl border border-slate-300 px-4 py-3 transition focus:border-[#123f91] focus:outline-none focus:ring-4 focus:ring-[#123f91]/10">

                </div>

                <div>

                    <label class="mb-2 block text-sm font-semibold text-slate-600">

                        MLS Listing Link

                    </label>

                    <input
                        type="text"
                        name="xMlsLink"
                        value="{{ old('xMlsLink',$flyer->xMlsLink ?? '') }}"
                        placeholder="https://"
                        class="w-full rounded-2xl border border-slate-300 px-4 py-3 transition focus:border-[#123f91] focus:outline-none focus:ring-4 focus:ring-[#123f91]/10">

                </div>

            </div>

        </div>

        {{-- ========================================================= --}}
        {{-- FORM ACTIONS - applies to every section above, not just --}}
        {{-- Additional Resources, so it's a standalone bar rather --}}
        {{-- than nested inside the last card. --}}
        {{-- ========================================================= --}}

        <div class="mt-2 overflow-hidden rounded-3xl bg-slate-800">

            <div class="flex items-center justify-between px-10 py-6">

                <p class="text-sm font-semibold text-slate-300">

                    Saves everything on this page.

                </p>

                <div class="flex items-center gap-4">

                    <a
                        href="/member/dashboard"
                        class="rounded-xl border border-slate-500 px-6 py-3 font-semibold text-slate-200 hover:bg-slate-700">

                        Cancel

                    </a>

                    <button
                        type="submit"
                        class="rounded-xl bg-[#123f91] px-8 py-3 font-bold text-white hover:bg-[#0d2f6e]">

                        Save &amp; Continue →

                    </button>

                </div>

            </div>

        </div>

    </form>

</div>

</main>

<script>
(function () {
    var form = document.getElementById('detailsForm');
    var redirectInput = form.querySelector('input[name="redirectAfterSave"]');
    var leaveLinks = document.querySelectorAll('#wizard-nav a[href], [data-wizard-leave]');

    leaveLinks.forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            redirectInput.value = link.getAttribute('href');
            form.requestSubmit();
        });
    });
})();
</script>

@include('member.layout.footer')

</body>

</html>
