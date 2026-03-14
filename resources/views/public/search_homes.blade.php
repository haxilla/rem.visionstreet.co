@include('public.layout.head')

<body data-section="admin" class="linkcheck relative bg-[#f3f4f7] min-h-screen font-sans text-slate-800 postgres">

    @include('public.layout.nav')

    <main class="transition-all duration-300 min-h-screen pt-24 relative" :class="collapsed ? 'ml-20' : 'ml-64'">
        <div class="mx-8 mr-8 lg:mx-10">

            {{-- HERO --}}
            <section class="overflow-hidden rounded-[30px] bg-gradient-to-br from-[#213c7a] via-[#2f4f95] to-[#36579f] shadow-[0_20px_60px_rgba(23,43,99,.18)]">
                <div class="grid grid-cols-1 lg:grid-cols-[1.2fr_.8fr] items-stretch">
                    <div class="relative min-h-[340px] lg:min-h-[420px]">
                        <img
                            src="{{ asset('images/featured-home-placeholder.jpg') }}"
                            alt="Featured property"
                            class="absolute inset-0 h-full w-full object-cover"
                        />
                        <div class="absolute inset-0 bg-gradient-to-r from-black/35 via-black/15 to-transparent"></div>

                        <div class="absolute left-6 top-6 z-10">
                            <div class="inline-flex rounded-full bg-white/15 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.22em] text-white ring-1 ring-white/20 backdrop-blur-sm">
                                Recent Listings
                            </div>
                        </div>

                        <div class="absolute left-6 bottom-6 z-10 max-w-[320px] rounded-[20px] border border-white/10 bg-black/35 p-4 text-white shadow-xl backdrop-blur-md">
                            <div class="text-[11px] uppercase tracking-[0.18em] text-white/70">RealtyEmails</div>
                            <div class="mt-2 text-[20px] font-semibold leading-tight">Browse Newly Created E-Flyers</div>
                            <div class="mt-2 text-[14px] leading-6 text-white/80">
                                Explore recent property flyers in a cleaner, modern gallery experience.
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <div class="w-full px-8 py-10 lg:px-12 lg:py-14 text-white">
                            <div class="inline-flex rounded-full bg-white/10 px-4 py-2 text-[11px] font-semibold uppercase tracking-[0.28em] ring-1 ring-white/15">
                                Slide Show Gallery
                            </div>

                            <h1 class="mt-6 font-serif text-[42px] leading-[0.95] sm:text-[54px] lg:text-[62px]">
                                View Homes<br>In Style
                            </h1>

                            <p class="mt-6 max-w-[500px] text-[16px] leading-8 text-white/82">
                                See the latest e-flyers your members have created, presented in a modern searchable gallery with clean cards and pagination.
                            </p>

                            <div class="mt-8 flex flex-wrap gap-4">
                                <a href="#results" class="inline-flex items-center justify-center rounded-full bg-white px-6 py-3 text-[14px] font-semibold text-[#27478d] shadow-md transition hover:-translate-y-[1px]">
                                    Browse Listings
                                </a>
                                <a href="/pricing" class="inline-flex items-center justify-center rounded-full border border-white/25 bg-white/8 px-6 py-3 text-[14px] font-semibold text-white backdrop-blur-sm transition hover:bg-white/14">
                                    View Pricing
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- RESULTS HEADER --}}
            <section id="results" class="pt-12 lg:pt-16">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <div class="text-[12px] font-semibold uppercase tracking-[0.28em] text-[#4f6db5]">
                            Property Gallery
                        </div>
                        <h2 class="mt-3 font-serif text-[34px] leading-none text-[#21408a] sm:text-[46px]">
                            Recent Listings
                        </h2>
                        <p class="mt-4 max-w-[700px] text-[15px] leading-7 text-slate-600">
                            Showing recently created flyers from the last 30 days.
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 text-[14px] text-slate-500">
                        <div class="rounded-full border border-[#d7ddec] bg-white px-4 py-2 shadow-sm">
                            {{ $data['searchAll']->total() }} total listings
                        </div>
                        <div class="rounded-full border border-[#d7ddec] bg-white px-4 py-2 shadow-sm">
                            Page {{ $data['searchAll']->currentPage() }} of {{ $data['searchAll']->lastPage() }}
                        </div>
                    </div>
                </div>
            </section>

            {{-- GRID --}}
            <section class="pt-8 pb-14">
                <div class="grid grid-cols-1 gap-8 xl:grid-cols-2 2xl:grid-cols-3">

                    @forelse($data['searchAll'] as $flyer)
                        @php
                            $street = trim($flyer->xFullStreet ?? '');
                            $city   = trim($flyer->xCity ?? '');
                            $state  = trim($flyer->xState ?? '');
                            $zip    = trim($flyer->xZip ?? $flyer->xxZip ?? '');

                            $beds  = $flyer->xBeds ?: $flyer->xxBeds;
                            $baths = $flyer->xBaths ?: $flyer->xxBaths;
                            $sqft  = $flyer->xSqft ?: $flyer->xxSqft;
                            $price = $flyer->xListPrice;

                            $agentName  = $flyer->theAgent->agtFullName ?? 'Listing Agent';
                            $agentPhone = $flyer->theAgent->agtMainPhone ?? '';
                            $agentPhoto = $flyer->theAgent->agtPhoto ?? '';
                            $officeName = $flyer->theOffice->officeName ?? '';

                            $slug = $flyer->url_slug ?? null;
                            $code = $flyer->flyer_code ?? null;

                            $viewUrl = $slug
                                ? url('/' . ltrim($slug, '/'))
                                : ($code ? url('/flyer/' . $code) : '#');

                            $photo = null;

                            if (!empty($flyer->theMeta->sk1 ?? null) && !empty($flyer->theMeta->mlsDir ?? null)) {
                                $photo = asset('storage/' . trim($flyer->theMeta->mlsDir, '/') . '/' . ltrim($flyer->theMeta->sk1, '/'));
                            }

                            $hasPhoto = !empty($photo);
                        @endphp

                        <article class="group overflow-hidden rounded-[28px] border border-[#d9dfec] bg-white shadow-[0_12px_30px_rgba(32,56,117,.08)] transition duration-300 hover:-translate-y-[3px] hover:shadow-[0_20px_46px_rgba(32,56,117,.14)]">
                            <div class="relative">
                                <a href="{{ $viewUrl }}" class="block">
                                    <div class="relative h-[240px] overflow-hidden bg-gradient-to-br from-[#e8edf8] to-[#dce5f5]">
                                        @if($hasPhoto)
                                            <img
                                                src="{{ $photo }}"
                                                alt="{{ $street }}"
                                                class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.03]"
                                            />
                                        @else
                                            <div class="flex h-full w-full items-center justify-center text-center text-slate-400">
                                                <div>
                                                    <div class="text-[13px] uppercase tracking-[0.25em]">No Photo</div>
                                                    <div class="mt-2 text-[15px]">Listing image coming soon</div>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-black/10 to-transparent"></div>

                                        <div class="absolute left-5 top-5">
                                            <div class="inline-flex items-center rounded-full bg-white/14 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.22em] text-white ring-1 ring-white/20 backdrop-blur-sm">
                                                Recent Flyer
                                            </div>
                                        </div>

                                        <div class="absolute right-5 top-5 rounded-full bg-white/92 px-4 py-2 text-[14px] font-semibold text-[#24468d] shadow-lg">
                                            @if(!empty($price))
                                                ${{ number_format((float) $price) }}
                                            @else
                                                Call for price
                                            @endif
                                        </div>

                                        <div class="absolute left-5 bottom-5 right-5 text-white">
                                            <h3 class="text-[24px] font-semibold leading-tight drop-shadow-sm">
                                                {{ $street ?: 'Property Listing' }}
                                            </h3>
                                            <div class="mt-2 text-[14px] text-white/85">
                                                {{ collect([$city, $state, $zip])->filter()->implode(', ') }}
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="p-6">
                                <div class="grid grid-cols-3 gap-3">
                                    <div class="rounded-2xl bg-[#f5f7fc] px-4 py-3 text-center">
                                        <div class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[#6780b8]">Beds</div>
                                        <div class="mt-1 text-[20px] font-semibold text-[#233f84]">{{ $beds ?: '—' }}</div>
                                    </div>
                                    <div class="rounded-2xl bg-[#f5f7fc] px-4 py-3 text-center">
                                        <div class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[#6780b8]">Baths</div>
                                        <div class="mt-1 text-[20px] font-semibold text-[#233f84]">{{ $baths ?: '—' }}</div>
                                    </div>
                                    <div class="rounded-2xl bg-[#f5f7fc] px-4 py-3 text-center">
                                        <div class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[#6780b8]">Sq Ft</div>
                                        <div class="mt-1 text-[20px] font-semibold text-[#233f84]">
                                            {{ !empty($sqft) ? number_format((float) $sqft) : '—' }}
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-6 flex items-center gap-4 rounded-[22px] border border-[#e0e6f2] bg-[#fafbfe] p-4">
                                    <div class="h-14 w-14 overflow-hidden rounded-full bg-[#dce5f5] ring-2 ring-white shadow-sm shrink-0">
                                        @if(!empty($agentPhoto))
                                            <img src="{{ $agentPhoto }}" alt="{{ $agentName }}" class="h-full w-full object-cover">
                                        @else
                                            <div class="flex h-full w-full items-center justify-center text-[18px] font-semibold text-[#32539c]">
                                                {{ strtoupper(substr($agentName, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="min-w-0">
                                        <div class="truncate text-[16px] font-semibold text-[#1f3c82]">{{ $agentName }}</div>
                                        @if($officeName)
                                            <div class="truncate text-[14px] text-slate-500">{{ $officeName }}</div>
                                        @endif
                                        @if($agentPhone)
                                            <div class="mt-1 text-[14px] text-slate-500">{{ $agentPhone }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="mt-6 flex items-center justify-between gap-4">
                                    <div class="text-[13px] text-slate-500">
                                        {{ $flyer->created_at ? \Carbon\Carbon::parse($flyer->created_at)->format('M j, Y') : '' }}
                                    </div>

                                    <a
                                        href="{{ $viewUrl }}"
                                        class="inline-flex items-center justify-center rounded-full bg-[#2c4d94] px-5 py-3 text-[14px] font-semibold text-white shadow-[0_10px_22px_rgba(44,77,148,.22)] transition hover:bg-[#233f84]"
                                    >
                                        View Flyer
                                    </a>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="col-span-full rounded-[28px] border border-dashed border-[#cdd7eb] bg-white px-8 py-16 text-center shadow-sm">
                            <div class="text-[12px] font-semibold uppercase tracking-[0.28em] text-[#6d84bb]">
                                No Results
                            </div>
                            <h3 class="mt-4 font-serif text-[34px] text-[#21408a]">No listings found</h3>
                            <p class="mx-auto mt-4 max-w-[560px] text-[15px] leading-7 text-slate-500">
                                There are no recent flyers to display right now.
                            </p>
                        </div>
                    @endforelse

                </div>
            </section>

            {{-- PAGINATION --}}
            @if($data['searchAll']->hasPages())
                <section class="pb-20">
                    <div class="rounded-[26px] border border-[#d9dfec] bg-white px-6 py-5 shadow-sm">
                        {{ $data['searchAll']->links() }}
                    </div>
                </section>
            @endif

        </div>
    </main>

    @include('public.layout.footer')

</body>
</html>
