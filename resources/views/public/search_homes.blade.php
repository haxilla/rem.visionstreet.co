@include('public.layout.head')

<body class="linkcheck relative bg-[#eef2f6] min-h-screen font-sans text-slate-800 postgres">

    @include('public.layout.nav')

    <main class="transition-all duration-300 min-h-screen pt-24 relative" :class="collapsed ? 'ml-20' : 'ml-64'">
        <div class="mx-6 lg:mx-10">

            <div class="mx-auto max-w-[1320px]">
                <div class="grid grid-cols-1 gap-8 xl:grid-cols-[280px_minmax(0,1fr)]">

                    {{-- LEFT SIDEBAR --}}
                    <aside class="space-y-6">

                        <div class="rounded-[26px] border border-[#d8e0ec] bg-white p-6 shadow-[0_8px_24px_rgba(0,0,0,.05)]">
                            <div class="text-center">
                                <div class="text-[12px] font-semibold uppercase tracking-[0.22em] text-[#6a82b7]">
                                    Realty Emails
                                </div>

                                <h2 class="mt-3 font-serif text-[32px] leading-none text-[#214e9b]">
                                    Gallery
                                </h2>

                                <p class="mt-4 text-[15px] leading-7 text-slate-600">
                                    View the latest e-flyers your members have created on the system.
                                </p>

                                <div class="mt-5 rounded-[18px] bg-[#f5f8fc] px-4 py-4 text-[14px] leading-6 text-slate-600">
                                    Public examples of past e-flyers presented in a cleaner, updated layout.
                                </div>
                            </div>
                        </div>

                        <div class="overflow-hidden rounded-[26px] border border-[#d8e0ec] bg-white shadow-[0_8px_24px_rgba(0,0,0,.05)]">
                            <div class="bg-gradient-to-br from-[#214e9b] via-[#2a56ab] to-[#3e6fca] px-6 py-6 text-white text-center">
                                <div class="text-[12px] font-semibold uppercase tracking-[0.22em] text-white/75">
                                    Send an E-Flyer
                                </div>

                                <div class="mt-3 text-[18px] font-semibold leading-tight">
                                    Premium Services<br>For Less
                                </div>

                                <div class="mt-5 inline-flex h-20 w-20 items-center justify-center rounded-full bg-[#f4b13d] text-[30px] font-bold text-white shadow-lg">
                                    $9
                                </div>

                                <div class="mt-4 text-[13px] text-white/80">
                                    as low as
                                </div>
                            </div>

                            <div class="px-6 py-6">
                                <ul class="space-y-3 text-[14px] leading-6 text-slate-600">
                                    <li>Instant proof &amp; delivery</li>
                                    <li>Seller sent a copy immediately</li>
                                    <li>Flyers saved &amp; editable for resends</li>
                                    <li>Upload unlimited photos</li>
                                    <li>FREE web page slide show</li>
                                    <li>FREE printable flyers</li>
                                    <li>Personal contact copy center</li>
                                </ul>

                                <a
                                    href="/pricing"
                                    class="mt-6 inline-flex w-full items-center justify-center rounded-full bg-[#214e9b] px-5 py-3 text-[14px] font-semibold text-white shadow-[0_10px_24px_rgba(33,78,155,.18)] transition hover:bg-[#193f84]"
                                >
                                    View Pricing
                                </a>
                            </div>
                        </div>

                        <div class="rounded-[26px] border border-[#d8e0ec] bg-white p-6 shadow-[0_8px_24px_rgba(0,0,0,.05)]">
                            <div class="text-[12px] font-semibold uppercase tracking-[0.22em] text-[#6a82b7]">
                                Results
                            </div>

                            <div class="mt-3 text-[26px] font-serif leading-none text-[#214e9b]">
                                {{ $data['searchAll']->total() }}
                            </div>

                            <div class="mt-2 text-[14px] text-slate-600">
                                total listings found
                            </div>

                            <div class="mt-5 rounded-[18px] bg-[#f5f8fc] px-4 py-4 text-[14px] text-slate-600">
                                Page {{ $data['searchAll']->currentPage() }} of {{ $data['searchAll']->lastPage() }}
                            </div>
                        </div>

                    </aside>

                    {{-- MAIN RESULTS --}}
                    <section class="min-w-0">

                        <div class="rounded-[28px] border border-[#d8e0ec] bg-white p-6 shadow-[0_8px_24px_rgba(0,0,0,.05)] lg:p-8">
                            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                                <div>
                                    <div class="text-[12px] font-semibold uppercase tracking-[0.24em] text-[#6a82b7]">
                                        Slide Show Gallery
                                    </div>

                                    <h1 class="mt-3 font-serif text-[36px] leading-none text-[#214e9b] sm:text-[44px]">
                                        Recent E-Flyers
                                    </h1>

                                    <p class="mt-4 max-w-[760px] text-[15px] leading-7 text-slate-600">
                                        View the latest e-flyers your members have created on the system.
                                        Only examples of past e-flyers — not intended as a public home search.
                                    </p>
                                </div>

                                <div class="flex flex-wrap gap-3 text-[13px] text-slate-500">
                                    <div class="rounded-full border border-[#d7ddec] bg-[#f8fafe] px-4 py-2">
                                        Most Recent
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 space-y-5">

                            @forelse($data['searchAll'] as $the)
                                @php
                                    $photoObj = $the->thePhotos->where('def','=','1')->first();
                                    $photo    = $photoObj?->photoName;

                                    $listingImg = null;
                                    if ($photo && !empty($the->theMeta?->zipDir) && !empty($the->theMeta?->mlsDir)) {
                                        $listingImg = "https://realtyrepublic.com/hqphotos/{$the->theMeta->zipDir}/{$the->theMeta->mlsDir}/{$photo}";
                                    }

                                    $listingURL = !empty($the->url_slug)
                                        ? "https://realtyrepublic.com/homedetails/{$the->url_slug}"
                                        : '#';

                                    $agentImg = null;
                                    if (!empty($the->theAgent?->agtPhoto) && !empty($the->theAgent?->theAgentCleanup?->newRemID)) {
                                        $agentImg = "https://realtyrepublic.com/agentPhotos/{$the->theAgent->theAgentCleanup->newRemID}/{$the->theAgent->agtPhoto}";
                                    } elseif (!empty($the->theAgent?->agtPhoto) && !empty($the->theOffice?->officeID)) {
                                        $agentImg = "https://realtyemails.com/HQoffice/{$the->theOffice->officeID}/{$the->theAgent->agtPhoto}";
                                    }

                                    $street     = $the->xFullStreet ?? '';
                                    $city       = $the->xCity ?? '';
                                    $state      = $the->xState ?? '';
                                    $zip        = $the->xZip ?? $the->xxZip ?? '';
                                    $cityLine   = trim($city . ', ' . $state . ' ' . $zip, ', ');

                                    $agentName  = $the->theAgent->agtFullName ?? '';
                                    $officeName = $the->theOffice->officeName ?? '';

                                    $price      = $the->xPrice ?? $the->xListPrice ?? null;
                                    $priceLabel = $price ? '$' . number_format((float) $price) : null;

                                    $beds       = $the->xBeds ?: $the->xxBeds;
                                    $baths      = $the->xBaths ?: $the->xxBaths;
                                    $sqft       = $the->xSqft ?: $the->xxSqft;

                                    $dateLabel  = !empty($the->created_at)
                                        ? \Carbon\Carbon::parse($the->created_at)->format('M j, Y')
                                        : null;
                                @endphp

                                <article class="overflow-hidden rounded-[26px] border border-[#d8e0ec] bg-white shadow-[0_8px_24px_rgba(0,0,0,.05)] transition hover:shadow-[0_18px_40px_rgba(0,0,0,.09)]">
                                    <div class="grid grid-cols-1 md:grid-cols-[260px_minmax(0,1fr)]">

                                        {{-- IMAGE --}}
                                        <div class="relative bg-[#e8edf5]">
                                            <a href="{{ $listingURL }}" target="_blank" class="block h-full">
                                                <div class="h-[220px] md:h-full min-h-[220px] overflow-hidden">
                                                    @if($listingImg)
                                                        <img
                                                            src="{{ $listingImg }}"
                                                            alt="{{ $street }}"
                                                            class="h-full w-full object-cover transition duration-500 hover:scale-[1.03]"
                                                        >
                                                    @else
                                                        <div class="flex h-full w-full items-center justify-center text-center text-slate-400">
                                                            <div>
                                                                <div class="text-[12px] font-semibold uppercase tracking-[0.24em]">No Photo</div>
                                                                <div class="mt-2 text-[14px]">Image unavailable</div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </a>
                                        </div>

                                        {{-- CONTENT --}}
                                        <div class="p-5 lg:p-6">
                                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                                <div class="min-w-0">
                                                    <a
                                                        href="{{ $listingURL }}"
                                                        target="_blank"
                                                        class="block text-[24px] font-semibold leading-tight text-[#214e9b] transition hover:opacity-80"
                                                    >
                                                        {{ $street }}
                                                    </a>

                                                    <div class="mt-2 text-[15px] text-slate-600">
                                                        {{ $cityLine }}
                                                    </div>
                                                </div>

                                                @if($priceLabel)
                                                    <div class="shrink-0 rounded-full bg-[#214e9b] px-4 py-2 text-[14px] font-semibold text-white shadow-sm">
                                                        {{ $priceLabel }}
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="mt-5 flex flex-wrap gap-3">
                                                <div class="rounded-full bg-[#f3f6fc] px-4 py-2 text-[14px] text-slate-700">
                                                    {{ $beds ?: '—' }} bed
                                                </div>
                                                <div class="rounded-full bg-[#f3f6fc] px-4 py-2 text-[14px] text-slate-700">
                                                    {{ $baths ?: '—' }} bath
                                                </div>
                                                <div class="rounded-full bg-[#f3f6fc] px-4 py-2 text-[14px] text-slate-700">
                                                    {{ !empty($sqft) ? number_format((float) $sqft) : '—' }} sq ft
                                                </div>
                                                @if($dateLabel)
                                                    <div class="rounded-full bg-[#f8fafc] px-4 py-2 text-[13px] text-slate-500">
                                                        {{ $dateLabel }}
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="mt-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                                <div class="flex items-center gap-4 min-w-0">
                                                    <div class="h-16 shrink-0 overflow-hidden rounded-[16px] bg-[#e7edf8] ring-1 ring-black/10">
                                                        @if($agentImg)
                                                            <img
                                                                src="{{ $agentImg }}"
                                                                alt="{{ $agentName }}"
                                                                class="h-full w-full object-cover"
                                                            >
                                                        @else
                                                            <div class="flex h-full w-full items-center justify-center text-[20px] font-semibold text-[#214e9b]">
                                                                {{ $agentName ? strtoupper(substr($agentName, 0, 1)) : 'A' }}
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <div class="min-w-0">
                                                        <div class="text-[12px] text-slate-500">
                                                            Listed by
                                                        </div>

                                                        <div class="truncate text-[18px] font-medium leading-tight text-[#214e9b]">
                                                            {{ $agentName }}
                                                        </div>

                                                        @if($officeName)
                                                            <div class="truncate text-[14px] text-slate-600">
                                                                {{ $officeName }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <a
                                                    href="{{ $listingURL }}"
                                                    target="_blank"
                                                    class="inline-flex items-center justify-center rounded-full border border-[#c9d5eb] bg-white px-5 py-3 text-[14px] font-semibold text-[#214e9b] transition hover:border-[#214e9b] hover:bg-[#f7faff]"
                                                >
                                                    View Listing
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </article>

                            @empty
                                <div class="rounded-[26px] border border-dashed border-[#cfd9ea] bg-white px-8 py-16 text-center shadow-[0_8px_24px_rgba(0,0,0,.05)]">
                                    <div class="text-[12px] font-semibold uppercase tracking-[0.24em] text-[#6a82b7]">
                                        No Results
                                    </div>
                                    <h3 class="mt-4 font-serif text-[36px] leading-none text-[#214e9b]">
                                        No recent flyers found
                                    </h3>
                                    <p class="mx-auto mt-4 max-w-[560px] text-[15px] leading-7 text-slate-600">
                                        There are no recent property flyers available to display right now.
                                    </p>
                                </div>
                            @endforelse

                        </div>

                        @if($data['searchAll']->hasPages())
                            <div class="mt-8 rounded-[24px] border border-[#d8e0ec] bg-white px-6 py-5 shadow-[0_8px_24px_rgba(0,0,0,.05)]">
                                {{ $data['searchAll']->links() }}
                            </div>
                        @endif

                    </section>

                </div>
            </div>

        </div>
    </main>

    @include('public.layout.footer')

</body>
</html>