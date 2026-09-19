@php
    $flyer    = $data['flyer'];
    $propInfo = $data['propInfo'];
@endphp

@include('member.layout.head')

{{-- The flyer template partials rely on these legacy stylesheets
     (member.layout.head doesn't include them). --}}
<link rel="stylesheet" type="text/css" href="/my/css/flyers/styles1pc.css">
<link rel="stylesheet" type="text/css" href="/my/css/flyers/styles2pb.css">
<link rel="stylesheet" type="text/css" href="/my/css/flyers/styles3pt.css">
<link rel="stylesheet" type="text/css" href="/my/css/flyers/styles4sp.css">
<link rel="stylesheet" type="text/css" href="/my/css/flyers/styles5pt.css">
<link rel="stylesheet" type="text/css" href="/my/css/flyers/flyerPreviews.css">

<body data-section="member" class="bg-slate-100">

@include('member.layout.nav')

<main class="pt-[88px] pb-10">

<div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">

    {{-- Two columns on wide screens: the form on the left, the live flyer
         on the right (sticky). Below lg it is one column and the flyer
         opens from a "Preview flyer" button instead. --}}
    <div class="lg:grid lg:grid-cols-[minmax(0,1fr)_420px] lg:gap-6 xl:grid-cols-[minmax(0,1fr)_520px] 2xl:grid-cols-[minmax(0,1fr)_640px]">

    <section class="min-w-0">

        {{-- PAGE HEADER --}}
        <div class="mb-3">

            <div class="flex flex-wrap items-baseline gap-x-3 gap-y-0.5">

                <h1 class="text-2xl font-black leading-tight text-slate-900">
                    {{ $flyer->xFullStreet }}
                </h1>

                <span class="text-xs font-bold uppercase tracking-wider text-[#123f91]">
                    Step 2 of 5
                </span>

            </div>

            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-slate-500">

                <span>{{ $flyer->xCity }}, {{ $flyer->state }} {{ $flyer->xZip }}</span>

                @if($flyer->xMlsNum)
                    <span class="font-bold uppercase tracking-wider text-[#123f91]">MLS #{{ $flyer->xMlsNum }}</span>
                @else
                    <span class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-xs font-bold uppercase tracking-wider text-amber-700">Non-MLS Listing</span>
                @endif

                <a
                    href="/member/flyer/create?flyerId={{ $flyer->id }}&return={{ request('return') ?: 'details' }}"
                    data-wizard-leave
                    class="font-bold text-[#123f91] hover:underline">
                    ← Edit Address
                </a>

            </div>

        </div>

        <div id="wizard-nav">
            @include('member.flyer.wizard',['flyer'=>$flyer])
        </div>

        @if($errors->any())

            <div class="mb-3 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm">

                <div class="font-bold text-red-700">
                    Please correct the following:
                </div>

                <ul class="mt-1 list-disc pl-5 text-red-600">

                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach

                </ul>

            </div>

        @endif

        <form
            id="detailsForm"
            method="POST"
            action="/member/flyer/save_details">

            @csrf

            <input
                type="hidden"
                name="flyerId"
                value="{{ $flyer->id }}">

            <input type="hidden" name="return" value="{{ request('return') }}">

            <input type="hidden" name="redirectAfterSave" value="">

            {{-- ===================== PROPERTY INFORMATION ===================== --}}

            <div class="wz-card mb-4">

                <h2 class="text-base font-black text-slate-900">Property Information</h2>
                <p class="mb-3 text-xs text-slate-500">Core facts shown on the flyer and used to help buyers find this listing.</p>

                <div class="grid grid-cols-2 gap-3 md:grid-cols-4">

                    <div>
                        <label for="xListPrice" class="wz-label">List Price</label>

                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-sm font-semibold text-slate-400">$</span>

                            <input
                                type="text"
                                id="xListPrice"
                                name="xListPrice"
                                value="{{ old('xListPrice',$flyer->xListPrice ?? '') }}"
                                class="wz-input pl-7">
                        </div>
                    </div>

                    <div>
                        <label for="xPropType" class="wz-label">Property Type</label>

                        <select id="xPropType" name="xPropType" class="wz-input">

                            @php $propType = old('xPropType', $flyer->theMeta->xPropType ?? ''); @endphp

                            <option value="">Select</option>
                            <option value="Residential" @selected($propType === 'Residential')>Residential</option>
                            <option value="Commercial" @selected($propType === 'Commercial')>Commercial</option>
                            <option value="Land" @selected($propType === 'Land')>Land</option>
                            <option value="Multi-Family" @selected($propType === 'Multi-Family')>Multi-Family</option>

                        </select>
                    </div>

                    <div>
                        <label for="xListingType" class="wz-label">Sale or Rental</label>

                        <select id="xListingType" name="xListingType" class="wz-input">

                            @php $listingType = old('xListingType', $flyer->theMeta->xListingType ?? ''); @endphp

                            <option value="">Select</option>
                            <option value="Sale" @selected($listingType === 'Sale')>Sale</option>
                            <option value="Rental" @selected($listingType === 'Rental')>Rental</option>

                        </select>
                    </div>

                    <div>
                        <label for="xYrBuilt" class="wz-label">Year Built</label>

                        <input
                            type="text"
                            id="xYrBuilt"
                            name="xYrBuilt"
                            value="{{ old('xYrBuilt', $flyer->xxYrBuilt ?: $flyer->xYrBuilt ?: '') }}"
                            class="wz-input">
                    </div>

                    <div>
                        <label for="xBeds" class="wz-label">Bedrooms</label>

                        <input
                            type="text"
                            id="xBeds"
                            name="xBeds"
                            value="{{ old('xBeds', $flyer->xxBeds ?: $flyer->xBeds ?: '') }}"
                            class="wz-input">
                    </div>

                    <div>
                        <label for="xBaths" class="wz-label">Bathrooms</label>

                        <input
                            type="text"
                            id="xBaths"
                            name="xBaths"
                            value="{{ old('xBaths', $flyer->xxBaths ?: $flyer->xBaths ?: '') }}"
                            class="wz-input">
                    </div>

                    <div>
                        <label for="xSqft" class="wz-label">Square Feet</label>

                        <input
                            type="text"
                            id="xSqft"
                            name="xSqft"
                            value="{{ old('xSqft', $flyer->xxSqft ?: $flyer->xSqft ?: '') }}"
                            class="wz-input">
                    </div>

                    <div>
                        <label for="xParking" class="wz-label">Parking</label>

                        <select id="xParking" name="xParking" class="wz-input">

                            @php $parking = old('xParking', $flyer->xParking ?? ''); @endphp

                            <option value="">Select</option>
                            @foreach(['1 Car Garage', '2 Car Garage', '3 Car Garage', '4 Car Garage', 'RV Parking'] as $option)
                                <option @selected($parking === $option)>{{ $option }}</option>
                            @endforeach

                        </select>
                    </div>

                    <div>
                        <label for="xPool" class="wz-label">Pool</label>

                        <select id="xPool" name="xPool" class="wz-input">

                            @php $pool = old('xPool', $flyer->xxPoolPvt ?: $flyer->xPoolPvt ?: ''); @endphp

                            <option value="">Select</option>
                            @foreach(['Private Pool', 'Community Pool', 'No Pool'] as $option)
                                <option @selected($pool === $option)>{{ $option }}</option>
                            @endforeach

                        </select>
                    </div>

                    <div class="col-span-2">
                        <label for="xIntersection" class="wz-label">Cross Streets</label>

                        <input
                            type="text"
                            id="xIntersection"
                            name="xIntersection"
                            value="{{ old('xIntersection',$flyer->theMap?->xIntersection ?? '') }}"
                            class="wz-input">
                    </div>

                </div>

            </div>

            {{-- ===================== FLYER HEADLINE ===================== --}}

            <div class="wz-card mb-4">

                <label for="xHeadline" class="text-base font-black text-slate-900">Flyer Headline</label>
                <p class="mb-2 text-xs text-slate-500">The main headline shown at the top of the flyer.</p>

                <input
                    type="text"
                    id="xHeadline"
                    name="xHeadline"
                    value="{{ old('xHeadline', $flyer->xHeadline ?: $flyer->xxHeadline ?: '') }}"
                    class="wz-input">

            </div>

            {{-- ===================== PROPERTY HIGHLIGHTS ===================== --}}

            <div class="wz-card mb-4">

                <h2 class="text-base font-black text-slate-900">Property Highlights</h2>
                <p class="mb-3 text-xs text-slate-500">Short feature highlights used in the flyer callout areas.</p>

                <div class="grid gap-x-4 gap-y-2 sm:grid-cols-2">

                    @for($i = 1; $i <= 8; $i++)

                        <div class="flex items-center gap-2">

                            <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-[#123f91]/10 text-xs font-black text-[#123f91]">
                                {{ $i }}
                            </div>

                            <input
                                type="text"
                                name="xb{{ $i }}"
                                aria-label="Highlight {{ $i }}"
                                value="{{ old('xb'.$i,$flyer->theRemarks?->{'xb'.$i} ?? '') }}"
                                maxlength="42"
                                class="wz-input">

                        </div>

                    @endfor

                </div>

            </div>

            {{-- ===================== AGENT REMARKS ===================== --}}

            <div class="wz-card mb-4">

                <label for="xPubRemarks" class="text-base font-black text-slate-900">Agent Remarks</label>
                <p class="mb-2 text-xs text-slate-500">The primary paragraph shown in the flyer body.</p>

                <textarea
                    id="xPubRemarks"
                    name="xPubRemarks"
                    rows="5"
                    class="wz-input">{{ old('xPubRemarks',$flyer->theRemarks?->xPubRemarks ?? '') }}</textarea>

            </div>

            {{-- ===================== ADDITIONAL RESOURCES ===================== --}}

            <div class="wz-card mb-4">

                <h2 class="text-base font-black text-slate-900">Additional Resources</h2>
                <p class="mb-3 text-xs text-slate-500">Optional links shown in the flyer link bar.</p>

                <div class="grid gap-3 md:grid-cols-2">

                    <div>
                        <label for="xVirtualTour" class="wz-label">Virtual Tour</label>

                        <input
                            type="text"
                            id="xVirtualTour"
                            name="xVirtualTour"
                            value="{{ old('xVirtualTour',$flyer->xVirtualTour ?? '') }}"
                            placeholder="https://"
                            class="wz-input">
                    </div>

                    <div>
                        <label for="xMlsLink" class="wz-label">MLS Listing Link</label>

                        <input
                            type="text"
                            id="xMlsLink"
                            name="xMlsLink"
                            value="{{ old('xMlsLink',$flyer->xMlsLink ?? '') }}"
                            placeholder="https://"
                            class="wz-input">
                    </div>

                </div>

            </div>

            {{-- Spacer so the pinned action bar never covers the last card --}}
            <div class="h-20"></div>

            {{-- ===================== PINNED ACTION BAR ===================== --}}
            {{-- Always in view, so "Save & Continue" never needs hunting for
                 on a long form. Saves everything on this page. --}}

            <div class="wz-pinned-bar fixed inset-x-0 bottom-0 z-40 border-t border-slate-200 bg-white/95 pb-4 pt-3 shadow-[0_-4px_16px_rgba(15,23,42,.08)] backdrop-blur">
                <div class="mx-auto flex max-w-[1400px] items-center justify-end gap-3 px-4 sm:px-6 lg:px-8">

                    <p class="mr-auto hidden text-xs font-semibold text-slate-500 sm:block">
                        Saves everything on this page.
                    </p>

                    {{-- Narrow screens only: the flyer isn't beside the form
                         there, so this opens it full-screen. --}}
                    <button type="button" id="openPreviewBtn" class="wz-btn wz-btn-secondary lg:hidden">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                        Preview flyer
                    </button>

                    <a href="/member/dashboard" class="wz-btn wz-btn-secondary">
                        Cancel
                    </a>

                    <button type="submit" class="wz-btn wz-btn-primary">
                        Save &amp; Continue →
                    </button>

                </div>
            </div>

        </form>

    </section>

    {{-- FLYER (right column on wide screens; hidden until "Preview flyer"
         opens it as a full-screen overlay on narrow screens). --}}
    <aside id="flyer-side" aria-label="Flyer preview">

        <div class="mb-2 flex items-center justify-between lg:hidden">
            <span class="text-sm font-black text-slate-900">Flyer preview</span>
            <button type="button" id="closePreviewBtn" class="wz-btn wz-btn-primary">Close</button>
        </div>

        <div class="wz-card p-3">

            <div class="mb-2 flex items-center justify-between text-xs font-semibold text-slate-500">
                <span>Live preview</span>
                <span id="flyer-live-status" role="status"></span>
            </div>

            <div class="flyer-stage">

                <div id="flyer-scale-wrapper">

                    <div id="flyer-live">
                        @include('member.flyer.previewPane', ['propInfo' => $propInfo])
                    </div>

                </div>

            </div>

        </div>

    </aside>

    </div>

</div>

</main>

<script src="/my/js/flyers/flyerSide.js"></script>

<script>
(function () {
    var form = document.getElementById('detailsForm');
    var redirectInput = form.querySelector('input[name="redirectAfterSave"]');
    var leaveLinks = document.querySelectorAll('#wizard-nav a[href], [data-wizard-leave]');

    // Leaving without saving (wizard nav, "Edit Address"): submit the form
    // first and tell the server where they were headed, so nothing typed
    // is lost.
    leaveLinks.forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            redirectInput.value = link.getAttribute('href');
            form.requestSubmit();
        });
    });

    // ---------------------------------------------------------------
    // Live preview: a moment after typing pauses, ask the server to draw
    // the flyer from the form's CURRENT (unsaved) values. Same templates
    // as the real flyer, so it can't drift. Nothing is saved by this.
    // ---------------------------------------------------------------
    var live      = document.getElementById('flyer-live');
    var statusEl  = document.getElementById('flyer-live-status');
    var timer     = null;
    var request   = null;
    var sequence  = 0;

    function refreshPreview() {
        var mine = ++sequence;

        // A newer edit supersedes any request still in flight.
        if (request) request.abort();
        request = new AbortController();

        live.classList.add('is-loading');
        statusEl.textContent = 'Updating…';

        fetch('/member/flyerPreview', {
            method: 'POST',
            body: new FormData(form),   // includes the CSRF token and flyerId
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            signal: request.signal
        })
            .then(function (response) {
                if (!response.ok) throw new Error('HTTP ' + response.status);
                return response.text();
            })
            .then(function (html) {
                if (mine !== sequence) return;   // stale response

                live.innerHTML = html;
                live.classList.remove('is-loading');
                statusEl.textContent = '';
                FlyerSide.scale();
            })
            .catch(function (err) {
                if (err.name === 'AbortError') return;

                live.classList.remove('is-loading');
                statusEl.textContent = 'Preview unavailable';
                console.error('Flyer preview failed:', err);
            });
    }

    form.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(refreshPreview, 500);
    });

    FlyerSide.init();
})();
</script>

@include('member.layout.footer')

</body>

</html>
