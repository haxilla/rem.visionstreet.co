{{-- ABOUT PAGE - REALTYEMAILS STYLE / TAILWIND ONLY --}}
@php
    $brandBlue   = '#243f86';
    $brandBlue2  = '#2d4f9d';
    $brandText   = '#24428b';
    $brandMuted  = '#66779a';

    $heroImg     = 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=1600&q=80';
    $storyImg    = 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1200&q=80';
    $agentsImg   = 'https://images.unsplash.com/photo-1560520653-9e0e4c89eb11?auto=format&fit=crop&w=1200&q=80';
    $listingImg1 = 'https://images.unsplash.com/photo-1570129477492-45c003edd2be?auto=format&fit=crop&w=1000&q=80';
    $listingImg2 = 'https://images.unsplash.com/photo-1600585154526-990dced4db0d?auto=format&fit=crop&w=1000&q=80';
    $listingImg3 = 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=1000&q=80';
@endphp

<div class="w-full bg-[#f3f4f7] text-[#1f2f57]">

    {{-- HERO --}}
    <section class="px-4 pb-8 pt-8 sm:px-6 lg:px-8 lg:pb-12 lg:pt-10">
        <div class="mx-auto max-w-[1380px] overflow-hidden rounded-[28px] bg-white shadow-[0_18px_55px_rgba(20,32,74,.12)]">
            <div class="grid grid-cols-1 lg:grid-cols-[1.05fr_.95fr]">
                {{-- Left image --}}
                <div class="relative min-h-[360px] lg:min-h-[620px]">
                    <img
                        src="{{ $heroImg }}"
                        alt="Luxury home exterior"
                        class="absolute inset-0 h-full w-full object-cover"
                    >
                    <div class="absolute inset-0 bg-gradient-to-r from-black/45 via-black/18 to-transparent"></div>

                    <div class="absolute left-5 top-5 z-10 sm:left-8 sm:top-8">
                        <div class="inline-flex items-center rounded-full bg-white/15 px-4 py-2 text-[11px] font-semibold uppercase tracking-[0.28em] text-white backdrop-blur-sm ring-1 ring-white/20">
                            About RealtyEmails
                        </div>
                    </div>

                    <div class="absolute bottom-5 left-5 right-5 z-10 sm:bottom-8 sm:left-8 sm:right-auto sm:max-w-[420px]">
                        <div class="rounded-[22px] bg-[#182338]/70 p-4 text-white shadow-xl backdrop-blur-md ring-1 ring-white/10 sm:p-5">
                            <div class="text-[11px] font-semibold uppercase tracking-[0.22em] text-white/70">
                                Real estate marketing that moves
                            </div>
                            <div class="mt-2 text-[26px] font-semibold leading-tight sm:text-[30px]">
                                Helping listings get seen, shared, and remembered.
                            </div>
                            <div class="mt-3 text-[14px] leading-6 text-white/82">
                                RealtyEmails was built to give agents a faster, cleaner, more professional way to promote homes beyond the MLS.
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right content --}}
                <div class="flex items-center bg-gradient-to-br from-[{{ $brandBlue }}] via-[{{ $brandBlue2 }}] to-[#21386e] px-6 py-12 sm:px-10 lg:px-14 lg:py-16">
                    <div class="mx-auto max-w-[560px] text-white lg:mx-0">
                        <div class="inline-flex items-center rounded-full bg-white/10 px-4 py-2 text-[11px] font-semibold uppercase tracking-[0.28em] text-white/80 ring-1 ring-white/15">
                            More than a flyer
                        </div>

                        <h1 class="mt-6 font-serif text-[40px] leading-[.96] tracking-[-0.03em] text-white sm:text-[54px] lg:text-[66px]">
                            We Help Agents Turn Listings Into Momentum
                        </h1>

                        <p class="mt-6 max-w-[520px] text-[17px] leading-8 text-white/86 sm:text-[18px]">
                            RealtyEmails makes it easy to create polished real estate e-flyers with a dedicated web presence for each property, so agents can market smarter, look more professional, and generate more visibility from day one.
                        </p>

                        <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div class="rounded-[20px] bg-white/10 p-5 ring-1 ring-white/12 backdrop-blur-sm">
                                <div class="text-[12px] font-semibold uppercase tracking-[0.22em] text-white/65">What we do</div>
                                <div class="mt-2 text-[16px] leading-7 text-white/92">
                                    Create beautiful, shareable listing promotions in minutes.
                                </div>
                            </div>
                            <div class="rounded-[20px] bg-white/10 p-5 ring-1 ring-white/12 backdrop-blur-sm">
                                <div class="text-[12px] font-semibold uppercase tracking-[0.22em] text-white/65">Why it matters</div>
                                <div class="mt-2 text-[16px] leading-7 text-white/92">
                                    Better presentation builds trust, clicks, and buyer interest.
                                </div>
                            </div>
                        </div>

                        <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                            <a
                                href="/free-trial"
                                class="inline-flex items-center justify-center rounded-full bg-white px-7 py-4 text-[15px] font-semibold text-[#243f86] shadow-sm transition hover:-translate-y-0.5"
                            >
                                Start Your Free Flyer
                            </a>

                            <a
                                href="/pricing"
                                class="inline-flex items-center justify-center rounded-full border border-white/18 bg-white/8 px-7 py-4 text-[15px] font-semibold text-white backdrop-blur-sm transition hover:bg-white/14"
                            >
                                View Pricing
                            </a>
                        </div>

                        <div class="mt-8 text-[13px] text-white/68">
                            Professional presentation • Fast setup • Designed for real estate agents
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- INTRO / MISSION --}}
    <section class="px-4 py-12 sm:px-6 lg:px-8 lg:py-16">
        <div class="mx-auto max-w-[1180px]">
            <div class="mx-auto max-w-[900px] text-center">
                <div class="text-[12px] font-semibold uppercase tracking-[0.34em] text-[#6b7ea8]">
                    Our Mission
                </div>
                <h2 class="mt-4 font-serif text-[38px] leading-tight text-[{{ $brandText }}] sm:text-[52px]">
                    Give Every Listing the Marketing Presence It Deserves
                </h2>
                <p class="mx-auto mt-5 max-w-[820px] text-[17px] leading-8 text-[#64748b]">
                    Too many great properties are under-marketed. RealtyEmails was created to help agents quickly build elegant digital flyers and property pages that make listings easier to email, easier to share, and easier for buyers to remember.
                </p>
            </div>

            <div class="mt-12 grid grid-cols-1 gap-6 md:grid-cols-3">
                <div class="rounded-[24px] border border-[#dbe3f3] bg-white p-7 shadow-[0_10px_30px_rgba(28,50,102,.05)]">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-[#eef3ff] text-[{{ $brandText }}]">
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M4 7h16v10H4z"/>
                            <path d="M4 8l8 6 8-6"/>
                        </svg>
                    </div>
                    <h3 class="mt-5 text-[22px] font-semibold text-[{{ $brandText }}]">Promote by Email</h3>
                    <p class="mt-3 text-[15px] leading-7 text-[#6b7280]">
                        Turn a listing into a polished promotional piece that is ready to send, distribute, and present professionally.
                    </p>
                </div>

                <div class="rounded-[24px] border border-[#dbe3f3] bg-white p-7 shadow-[0_10px_30px_rgba(28,50,102,.05)]">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-[#eef3ff] text-[{{ $brandText }}]">
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M15 8a3 3 0 1 0-3-3 3 3 0 0 0 3 3Z"/>
                            <path d="M6 14a3 3 0 1 0-3-3 3 3 0 0 0 3 3Z"/>
                            <path d="M18 21a3 3 0 1 0-3-3 3 3 0 0 0 3 3Z"/>
                            <path d="M8.6 12.5l3-1.7"/>
                            <path d="M13.4 17.2l1-5.4"/>
                        </svg>
                    </div>
                    <h3 class="mt-5 text-[22px] font-semibold text-[{{ $brandText }}]">Share Everywhere</h3>
                    <p class="mt-3 text-[15px] leading-7 text-[#6b7280]">
                        Use your flyer across social channels, direct outreach, text, and digital conversations without rebuilding content over and over.
                    </p>
                </div>

                <div class="rounded-[24px] border border-[#dbe3f3] bg-white p-7 shadow-[0_10px_30px_rgba(28,50,102,.05)]">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-[#eef3ff] text-[{{ $brandText }}]">
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M7 17h10"/>
                            <path d="M7 13h10"/>
                            <path d="M7 9h4"/>
                            <path d="M6 3h9l3 3v15H6z"/>
                            <path d="M15 3v4h4"/>
                        </svg>
                    </div>
                    <h3 class="mt-5 text-[22px] font-semibold text-[{{ $brandText }}]">Present Professionally</h3>
                    <p class="mt-3 text-[15px] leading-7 text-[#6b7280]">
                        Clean design helps your brand look more established while giving each home a more polished, memorable presentation.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- STORY / WHY --}}
    <section class="px-4 py-4 sm:px-6 lg:px-8 lg:py-8">
        <div class="mx-auto max-w-[1380px] overflow-hidden rounded-[28px] bg-white shadow-[0_18px_55px_rgba(20,32,74,.08)]">
            <div class="grid grid-cols-1 lg:grid-cols-[.9fr_1.1fr]">
                <div class="relative min-h-[340px] lg:min-h-[560px]">
                    <img
                        src="{{ $storyImg }}"
                        alt="Beautiful home exterior at sunset"
                        class="absolute inset-0 h-full w-full object-cover"
                    >
                    <div class="absolute inset-0 bg-gradient-to-t from-black/45 via-black/10 to-transparent"></div>
                </div>

                <div class="px-6 py-12 sm:px-10 lg:px-14 lg:py-16">
                    <div class="inline-flex items-center rounded-full bg-[#eef3ff] px-4 py-2 text-[11px] font-semibold uppercase tracking-[0.28em] text-[{{ $brandText }}]">
                        Why RealtyEmails Exists
                    </div>

                    <h2 class="mt-6 max-w-[760px] font-serif text-[36px] leading-tight text-[{{ $brandText }}] sm:text-[50px]">
                        Because Listing Marketing Shouldn’t Be Slow, Generic, or Difficult
                    </h2>

                    <div class="mt-6 space-y-5 text-[16px] leading-8 text-[#5f6d86]">
                        <p>
                            Agents already juggle photography, showings, pricing, paperwork, communication, and follow-up. Marketing should support that process, not add friction to it.
                        </p>
                        <p>
                            RealtyEmails was designed to simplify the promotional side of real estate by making it quick to launch a professional-looking flyer and online presentation for a property without needing complicated design software or a custom web build.
                        </p>
                        <p>
                            The result is a cleaner, faster way to showcase homes, support agents, and help listings stand out in a crowded market.
                        </p>
                    </div>

                    <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div class="rounded-[22px] bg-[#f7f9fd] p-5 ring-1 ring-[#e3eaf6]">
                            <div class="text-[28px] font-semibold text-[{{ $brandText }}]">Fast</div>
                            <div class="mt-2 text-[14px] leading-6 text-[#64748b]">Built for quick setup when timing matters.</div>
                        </div>
                        <div class="rounded-[22px] bg-[#f7f9fd] p-5 ring-1 ring-[#e3eaf6]">
                            <div class="text-[28px] font-semibold text-[{{ $brandText }}]">Clean</div>
                            <div class="mt-2 text-[14px] leading-6 text-[#64748b]">A polished look that supports agent credibility.</div>
                        </div>
                        <div class="rounded-[22px] bg-[#f7f9fd] p-5 ring-1 ring-[#e3eaf6]">
                            <div class="text-[28px] font-semibold text-[{{ $brandText }}]">Useful</div>
                            <div class="mt-2 text-[14px] leading-6 text-[#64748b]">Easy to email, share, promote, and revisit.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- HOW IT HELPS --}}
    <section class="px-4 py-14 sm:px-6 lg:px-8 lg:py-18">
        <div class="mx-auto max-w-[1200px]">
            <div class="text-center">
                <div class="text-[12px] font-semibold uppercase tracking-[0.34em] text-[#7285af]">
                    Built For Real Estate
                </div>
                <h2 class="mt-4 font-serif text-[38px] leading-tight text-[{{ $brandText }}] sm:text-[52px]">
                    What RealtyEmails Helps You Do
                </h2>
                <p class="mx-auto mt-5 max-w-[820px] text-[17px] leading-8 text-[#667085]">
                    Every part of the experience is designed to help agents present listings more effectively and extend marketing reach beyond a single upload.
                </p>
            </div>

            <div class="mt-12 grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="rounded-[26px] bg-gradient-to-br from-[{{ $brandBlue }}] via-[{{ $brandBlue2 }}] to-[#21386f] p-8 text-white shadow-[0_20px_55px_rgba(25,45,96,.16)]">
                    <h3 class="text-[28px] font-semibold leading-tight">Turn one listing into a complete promotional asset</h3>
                    <div class="mt-6 space-y-4 text-[15px] leading-7 text-white/84">
                        <div class="flex gap-3">
                            <div class="mt-2 h-2.5 w-2.5 shrink-0 rounded-full bg-white"></div>
                            <p>Create a flyer that feels polished, modern, and ready to represent your brand well.</p>
                        </div>
                        <div class="flex gap-3">
                            <div class="mt-2 h-2.5 w-2.5 shrink-0 rounded-full bg-white"></div>
                            <p>Give buyers and other agents an easy way to view and revisit your listing information.</p>
                        </div>
                        <div class="flex gap-3">
                            <div class="mt-2 h-2.5 w-2.5 shrink-0 rounded-full bg-white"></div>
                            <p>Save time by using one core property presentation across multiple marketing touchpoints.</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-[26px] border border-[#dbe3f3] bg-white p-8 shadow-[0_10px_30px_rgba(28,50,102,.05)]">
                    <h3 class="text-[28px] font-semibold leading-tight text-[{{ $brandText }}]">Support your business with better visibility</h3>
                    <div class="mt-6 space-y-4 text-[15px] leading-7 text-[#667085]">
                        <div class="flex gap-3">
                            <div class="mt-2 h-2.5 w-2.5 shrink-0 rounded-full bg-[{{ $brandText }}]"></div>
                            <p>Show sellers that you use thoughtful, professional marketing tools.</p>
                        </div>
                        <div class="flex gap-3">
                            <div class="mt-2 h-2.5 w-2.5 shrink-0 rounded-full bg-[{{ $brandText }}]"></div>
                            <p>Give each new listing a stronger launch and a more memorable first impression.</p>
                        </div>
                        <div class="flex gap-3">
                            <div class="mt-2 h-2.5 w-2.5 shrink-0 rounded-full bg-[{{ $brandText }}]"></div>
                            <p>Create consistent, brand-friendly presentation across your marketing efforts.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- FEATURE STRIP --}}
    <section class="px-4 pb-6 sm:px-6 lg:px-8">
        <div class="mx-auto grid max-w-[1380px] grid-cols-1 gap-6 lg:grid-cols-[1.02fr_.98fr]">
            <div class="overflow-hidden rounded-[26px] bg-white shadow-[0_16px_50px_rgba(24,43,92,.08)]">
                <div class="grid grid-cols-1 md:grid-cols-[1fr_1.1fr]">
                    <div class="relative min-h-[290px]">
                        <img src="{{ $agentsImg }}" alt="Agent working on laptop" class="absolute inset-0 h-full w-full object-cover">
                    </div>
                    <div class="p-7 sm:p-9">
                        <div class="text-[12px] font-semibold uppercase tracking-[0.28em] text-[#7285af]">
                            Simple Process
                        </div>
                        <h3 class="mt-4 font-serif text-[32px] leading-tight text-[{{ $brandText }}]">
                            Designed To Be Fast From Start To Share
                        </h3>
                        <div class="mt-6 space-y-4">
                            <div class="flex gap-4">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#eef3ff] text-[15px] font-semibold text-[{{ $brandText }}]">1</div>
                                <div>
                                    <div class="text-[17px] font-semibold text-[{{ $brandText }}]">Create</div>
                                    <div class="mt-1 text-[14px] leading-6 text-[#667085]">Build a polished listing presentation in minutes.</div>
                                </div>
                            </div>
                            <div class="flex gap-4">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#eef3ff] text-[15px] font-semibold text-[{{ $brandText }}]">2</div>
                                <div>
                                    <div class="text-[17px] font-semibold text-[{{ $brandText }}]">Share</div>
                                    <div class="mt-1 text-[14px] leading-6 text-[#667085]">Use it in email, social, direct outreach, and online promotion.</div>
                                </div>
                            </div>
                            <div class="flex gap-4">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#eef3ff] text-[15px] font-semibold text-[{{ $brandText }}]">3</div>
                                <div>
                                    <div class="text-[17px] font-semibold text-[{{ $brandText }}]">Stand Out</div>
                                    <div class="mt-1 text-[14px] leading-6 text-[#667085]">Give buyers and sellers a more professional impression of your marketing.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden rounded-[26px] bg-gradient-to-br from-[{{ $brandBlue }}] via-[{{ $brandBlue2 }}] to-[#22396e] p-8 text-white shadow-[0_16px_50px_rgba(24,43,92,.12)] sm:p-10">
                <div class="text-[12px] font-semibold uppercase tracking-[0.28em] text-white/65">
                    What makes us different
                </div>
                <h3 class="mt-4 font-serif text-[34px] leading-tight sm:text-[40px]">
                    Marketing Tools Built Around the Listing
                </h3>
                <p class="mt-5 text-[16px] leading-8 text-white/82">
                    RealtyEmails focuses on helping agents launch attractive, usable, property-centered marketing pieces that are fast to create and easy to distribute.
                </p>

                <div class="mt-8 space-y-4">
                    <div class="rounded-[20px] border border-white/12 bg-white/8 p-5 backdrop-blur-sm">
                        <div class="text-[16px] font-semibold">Professional design feel</div>
                        <div class="mt-1 text-[14px] leading-6 text-white/72">A cleaner presentation helps your listings feel more premium and intentional.</div>
                    </div>
                    <div class="rounded-[20px] border border-white/12 bg-white/8 p-5 backdrop-blur-sm">
                        <div class="text-[16px] font-semibold">Faster promotional setup</div>
                        <div class="mt-1 text-[14px] leading-6 text-white/72">Spend less time piecing together materials and more time promoting the property.</div>
                    </div>
                    <div class="rounded-[20px] border border-white/12 bg-white/8 p-5 backdrop-blur-sm">
                        <div class="text-[16px] font-semibold">Built for real-world sharing</div>
                        <div class="mt-1 text-[14px] leading-6 text-white/72">Email, social, print support, and direct property presentation all work together.</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- GALLERY / VISUAL PROOF --}}
    <section class="px-4 py-14 sm:px-6 lg:px-8 lg:py-16">
        <div class="mx-auto max-w-[1240px]">
            <div class="text-center">
                <div class="text-[12px] font-semibold uppercase tracking-[0.34em] text-[#7285af]">
                    Showcase Better
                </div>
                <h2 class="mt-4 font-serif text-[38px] leading-tight text-[{{ $brandText }}] sm:text-[52px]">
                    Beautiful Homes Deserve Beautiful Marketing
                </h2>
                <p class="mx-auto mt-5 max-w-[780px] text-[17px] leading-8 text-[#667085]">
                    RealtyEmails helps agents give each property a stronger visual presentation, whether the goal is sharing with buyers, promoting online, or reinforcing listing value with sellers.
                </p>
            </div>

            <div class="mt-10 grid grid-cols-1 gap-6 md:grid-cols-3">
                <div class="overflow-hidden rounded-[24px] bg-white shadow-[0_14px_38px_rgba(26,45,90,.08)]">
                    <div class="relative aspect-[4/4.5]">
                        <img src="{{ $listingImg1 }}" alt="Modern home exterior" class="absolute inset-0 h-full w-full object-cover">
                    </div>
                    <div class="p-5">
                        <div class="text-[11px] font-semibold uppercase tracking-[0.24em] text-[#7a8bb0]">Listing Presentation</div>
                        <div class="mt-2 text-[20px] font-semibold text-[{{ $brandText }}]">Lead With Strong First Impressions</div>
                    </div>
                </div>

                <div class="overflow-hidden rounded-[24px] bg-white shadow-[0_14px_38px_rgba(26,45,90,.08)]">
                    <div class="relative aspect-[4/4.5]">
                        <img src="{{ $listingImg2 }}" alt="Luxury home with large windows" class="absolute inset-0 h-full w-full object-cover">
                    </div>
                    <div class="p-5">
                        <div class="text-[11px] font-semibold uppercase tracking-[0.24em] text-[#7a8bb0]">Brand Credibility</div>
                        <div class="mt-2 text-[20px] font-semibold text-[{{ $brandText }}]">Look More Established and Prepared</div>
                    </div>
                </div>

                <div class="overflow-hidden rounded-[24px] bg-white shadow-[0_14px_38px_rgba(26,45,90,.08)]">
                    <div class="relative aspect-[4/4.5]">
                        <img src="{{ $listingImg3 }}" alt="Elegant interior living room" class="absolute inset-0 h-full w-full object-cover">
                    </div>
                    <div class="p-5">
                        <div class="text-[11px] font-semibold uppercase tracking-[0.24em] text-[#7a8bb0]">Shareable Assets</div>
                        <div class="mt-2 text-[20px] font-semibold text-[{{ $brandText }}]">Create Marketing That Travels Further</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- FINAL CTA --}}
    <section class="px-4 pb-14 pt-2 sm:px-6 lg:px-8 lg:pb-20">
        <div class="mx-auto max-w-[1380px] overflow-hidden rounded-[30px] bg-gradient-to-r from-[{{ $brandBlue }}] via-[{{ $brandBlue2 }}] to-[#20376d] shadow-[0_22px_60px_rgba(22,40,88,.18)]">
            <div class="grid grid-cols-1 items-center gap-8 px-6 py-10 sm:px-10 lg:grid-cols-[1.15fr_.85fr] lg:px-14 lg:py-14">
                <div>
                    <div class="text-[12px] font-semibold uppercase tracking-[0.34em] text-white/65">
                        Ready to get started?
                    </div>
                    <h2 class="mt-4 max-w-[760px] font-serif text-[38px] leading-tight text-white sm:text-[56px]">
                        Give Your Next Listing the Exposure It Deserves
                    </h2>
                    <p class="mt-5 max-w-[700px] text-[17px] leading-8 text-white/82">
                        Create a professional e-flyer and dedicated property presentation that helps your listing look stronger, travel farther, and support your brand from the very first impression.
                    </p>
                </div>

                <div class="rounded-[26px] bg-white p-6 shadow-xl sm:p-7">
                    <div class="text-[12px] font-semibold uppercase tracking-[0.28em] text-[#7285af]">
                        Start now
                    </div>
                    <div class="mt-3 text-[30px] font-serif leading-tight text-[{{ $brandText }}]">
                        Launch Your First Flyer
                    </div>
                    <p class="mt-3 text-[15px] leading-7 text-[#667085]">
                        Fast setup. Professional presentation. Built to help agents market listings more effectively.
                    </p>

                    <div class="mt-6 flex flex-col gap-3">
                        <a
                            href="/free-trial"
                            class="inline-flex items-center justify-center rounded-full bg-[{{ $brandBlue }}] px-6 py-4 text-[15px] font-semibold text-white transition hover:opacity-95"
                        >
                            Create FREE Flyer
                        </a>

                        <a
                            href="/pricing"
                            class="inline-flex items-center justify-center rounded-full border border-[#d9e2f2] bg-[#f8faff] px-6 py-4 text-[15px] font-semibold text-[{{ $brandText }}]"
                        >
                            View Pricing
                        </a>
                    </div>

                    <div class="mt-5 text-center text-[12px] text-[#8b97ad]">
                        No credit card required • Launch in minutes
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>