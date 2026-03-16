@include('public.layout.head')

<body data-section="admin" 
class="linkcheck relative bg-white min-h-screen font-sans text-gray-800 postgres">

  @include('public.layout.nav')

  <main class="transition-all duration-300 min-h-screen pt-24 relative"
  :class="collapsed ? 'ml-20' : 'ml-64'">
    <div class="ml-8 mr-8 lg:ml-10 lg:mr-10">
      <div class="pageswap p-6 w-full">
{{-- ABOUT PAGE - REALTYEMAILS STYLE / RESTRAINED VERSION --}}
@php
    $heroImg   = 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=1800&q=80';
    $storyImg  = 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1400&q=80';
@endphp

<div class="w-full bg-[#f4f5f8] text-[#1f2f57]">

    {{-- HERO --}}
    <section class="px-4 pb-8 pt-8 sm:px-6 lg:px-8 lg:pb-12 lg:pt-10">
        <div class="mx-auto max-w-[1380px] overflow-hidden rounded-[28px] bg-white shadow-[0_18px_50px_rgba(26,43,89,.10)]">
            <div class="grid grid-cols-1 lg:grid-cols-[1.02fr_.98fr]">
                <div class="relative min-h-[320px] lg:min-h-[560px]">
                    <img
                        src="{{ $heroImg }}"
                        alt="Beautiful home exterior"
                        class="absolute inset-0 h-full w-full object-cover"
                    >
                    <div class="absolute inset-0 bg-gradient-to-r from-black/38 via-black/12 to-transparent"></div>

                    <div class="absolute left-5 top-5 z-10 sm:left-8 sm:top-8">
                        <div class="inline-flex items-center rounded-full bg-white/15 px-4 py-2 text-[11px] font-semibold uppercase tracking-[0.28em] text-white backdrop-blur-sm ring-1 ring-white/20">
                            About RealtyEmails
                        </div>
                    </div>

                    <div class="absolute left-5 top-20 z-10 max-w-[300px] text-white sm:left-8 sm:top-24 sm:max-w-[380px]">
                        <div class="text-[30px] font-serif leading-[1.02] sm:text-[42px]">
                            Better Marketing For Every Listing
                        </div>
                    </div>
                </div>

                <div class="flex items-center bg-[#27458d] px-6 py-12 sm:px-10 lg:px-14 lg:py-16">
                    <div class="max-w-[520px] text-white">
                        <h1 class="font-serif text-[38px] leading-[1.02] tracking-[-0.02em] sm:text-[56px]">
                            Professional Real Estate Flyers, Made Simple
                        </h1>

                        <p class="mt-6 text-[17px] leading-8 text-white/85">
                            RealtyEmails helps agents create polished e-flyers and property pages that make listings easier to present, easier to share, and more memorable to buyers.
                        </p>

                        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                            <a
                                href="/free-trial"
                                class="inline-flex items-center justify-center rounded-full bg-white px-7 py-4 text-[15px] font-semibold text-[#27458d] transition hover:-translate-y-0.5"
                            >
                                Create FREE Flyer
                            </a>

                            <a
                                href="/pricing"
                                class="inline-flex items-center justify-center rounded-full border border-white/20 bg-white/5 px-7 py-4 text-[15px] font-semibold text-white transition hover:bg-white/10"
                            >
                                View Pricing
                            </a>
                        </div>

                        <div class="mt-6 text-[13px] text-white/68">
                            Built for agents who want cleaner, faster listing promotion.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- INTRO --}}
    <section class="px-4 py-12 sm:px-6 lg:px-8 lg:py-16">
        <div class="mx-auto max-w-[980px] text-center">
            <div class="text-[12px] font-semibold uppercase tracking-[0.32em] text-[#7285af]">
                What We Do
            </div>

            <h2 class="mt-4 font-serif text-[36px] leading-tight text-[#26448b] sm:text-[50px]">
                RealtyEmails Helps Listings Stand Out
            </h2>

            <p class="mt-6 text-[17px] leading-8 text-[#667085]">
                A listing deserves more than a basic upload and a wait-and-see approach. RealtyEmails gives agents a fast, professional way to present properties with attractive marketing materials that are ready to email, share, and promote.
            </p>
        </div>
    </section>

    {{-- THREE CORE POINTS --}}
    <section class="px-4 pb-8 sm:px-6 lg:px-8 lg:pb-12">
        <div class="mx-auto max-w-[1180px] grid grid-cols-1 gap-6 md:grid-cols-3">
            <div class="rounded-[24px] border border-[#dde5f2] bg-white p-8 shadow-[0_10px_30px_rgba(28,50,102,.05)]">
                <div class="text-[12px] font-semibold uppercase tracking-[0.26em] text-[#7285af]">
                    Create
                </div>
                <h3 class="mt-4 text-[24px] font-semibold text-[#26448b]">
                    Professional E-Flyers
                </h3>
                <p class="mt-4 text-[15px] leading-7 text-[#667085]">
                    Build clean, polished flyer pages that help each property look more refined and market-ready.
                </p>
            </div>

            <div class="rounded-[24px] border border-[#dde5f2] bg-white p-8 shadow-[0_10px_30px_rgba(28,50,102,.05)]">
                <div class="text-[12px] font-semibold uppercase tracking-[0.26em] text-[#7285af]">
                    Share
                </div>
                <h3 class="mt-4 text-[24px] font-semibold text-[#26448b]">
                    Promote Anywhere
                </h3>
                <p class="mt-4 text-[15px] leading-7 text-[#667085]">
                    Use your flyer across email, social media, direct outreach, and other digital marketing efforts.
                </p>
            </div>

            <div class="rounded-[24px] border border-[#dde5f2] bg-white p-8 shadow-[0_10px_30px_rgba(28,50,102,.05)]">
                <div class="text-[12px] font-semibold uppercase tracking-[0.26em] text-[#7285af]">
                    Present
                </div>
                <h3 class="mt-4 text-[24px] font-semibold text-[#26448b]">
                    Elevate Your Brand
                </h3>
                <p class="mt-4 text-[15px] leading-7 text-[#667085]">
                    A stronger presentation helps sellers feel confident and gives buyers a better first impression.
                </p>
            </div>
        </div>
    </section>

    {{-- WHY IT EXISTS --}}
    <section class="px-4 py-8 sm:px-6 lg:px-8 lg:py-12">
        <div class="mx-auto max-w-[1380px] overflow-hidden rounded-[28px] bg-white shadow-[0_18px_50px_rgba(26,43,89,.08)]">
            <div class="grid grid-cols-1 lg:grid-cols-[.95fr_1.05fr]">
                <div class="relative min-h-[300px] lg:min-h-[500px]">
                    <img
                        src="{{ $storyImg }}"
                        alt="Modern luxury home"
                        class="absolute inset-0 h-full w-full object-cover"
                    >
                    <div class="absolute inset-0 bg-gradient-to-t from-black/30 via-black/5 to-transparent"></div>
                </div>

                <div class="px-6 py-12 sm:px-10 lg:px-14 lg:py-16">
                    <div class="text-[12px] font-semibold uppercase tracking-[0.32em] text-[#7285af]">
                        Why RealtyEmails
                    </div>

                    <h2 class="mt-4 font-serif text-[36px] leading-tight text-[#26448b] sm:text-[48px]">
                        Built For Agents Who Want Better Listing Marketing
                    </h2>

                    <div class="mt-6 space-y-5 text-[16px] leading-8 text-[#667085]">
                        <p>
                            RealtyEmails was created around a simple idea: listing promotion should be easier, cleaner, and more effective.
                        </p>
                        <p>
                            Agents need a practical way to turn a property into a professional marketing piece without dealing with a complicated design process. RealtyEmails helps solve that by giving listings a polished digital presence that is quick to launch and easy to distribute.
                        </p>
                        <p>
                            The goal is not to add complexity. It is to make marketing feel more professional while saving time.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- SIMPLE VALUE SECTION --}}
    <section class="px-4 py-12 sm:px-6 lg:px-8 lg:py-16">
        <div class="mx-auto max-w-[1100px]">
            <div class="text-center">
                <div class="text-[12px] font-semibold uppercase tracking-[0.32em] text-[#7285af]">
                    The Value
                </div>
                <h2 class="mt-4 font-serif text-[36px] leading-tight text-[#26448b] sm:text-[50px]">
                    A Better Way To Support Your Listings
                </h2>
            </div>

            <div class="mt-10 grid grid-cols-1 gap-6 md:grid-cols-2">
                <div class="rounded-[24px] bg-[#27458d] p-8 text-white shadow-[0_14px_38px_rgba(35,62,126,.16)]">
                    <h3 class="text-[26px] font-semibold leading-tight">
                        What agents need
                    </h3>

                    <ul class="mt-6 space-y-4 text-[15px] leading-7 text-white/84">
                        <li class="flex gap-3">
                            <span class="mt-2 h-2.5 w-2.5 shrink-0 rounded-full bg-white"></span>
                            <span>A fast way to create attractive promotional materials</span>
                        </li>
                        <li class="flex gap-3">
                            <span class="mt-2 h-2.5 w-2.5 shrink-0 rounded-full bg-white"></span>
                            <span>A presentation that looks more polished and intentional</span>
                        </li>
                        <li class="flex gap-3">
                            <span class="mt-2 h-2.5 w-2.5 shrink-0 rounded-full bg-white"></span>
                            <span>Marketing pieces that can be easily shared and reused</span>
                        </li>
                    </ul>
                </div>

                <div class="rounded-[24px] border border-[#dde5f2] bg-white p-8 shadow-[0_10px_30px_rgba(28,50,102,.05)]">
                    <h3 class="text-[26px] font-semibold leading-tight text-[#26448b]">
                        What RealtyEmails provides
                    </h3>

                    <ul class="mt-6 space-y-4 text-[15px] leading-7 text-[#667085]">
                        <li class="flex gap-3">
                            <span class="mt-2 h-2.5 w-2.5 shrink-0 rounded-full bg-[#26448b]"></span>
                            <span>Professional real estate e-flyers and listing pages</span>
                        </li>
                        <li class="flex gap-3">
                            <span class="mt-2 h-2.5 w-2.5 shrink-0 rounded-full bg-[#26448b]"></span>
                            <span>A cleaner, more branded way to present properties</span>
                        </li>
                        <li class="flex gap-3">
                            <span class="mt-2 h-2.5 w-2.5 shrink-0 rounded-full bg-[#26448b]"></span>
                            <span>A practical marketing tool designed specifically for real estate</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="px-4 pb-14 pt-2 sm:px-6 lg:px-8 lg:pb-20">
        <div class="mx-auto max-w-[1180px] overflow-hidden rounded-[28px] bg-[#27458d] shadow-[0_20px_55px_rgba(28,50,102,.16)]">
            <div class="px-6 py-10 text-center sm:px-10 lg:px-14 lg:py-14">
                <div class="text-[12px] font-semibold uppercase tracking-[0.32em] text-white/65">
                    Get Started
                </div>

                <h2 class="mx-auto mt-4 max-w-[760px] font-serif text-[36px] leading-tight text-white sm:text-[52px]">
                    Give Your Next Listing A More Professional Presence
                </h2>

                <p class="mx-auto mt-5 max-w-[720px] text-[17px] leading-8 text-white/82">
                    RealtyEmails helps agents create polished listing promotions that are easy to launch, easy to share, and built to make a stronger impression.
                </p>

                <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                    <a
                        href="/free-trial"
                        class="inline-flex items-center justify-center rounded-full bg-white px-7 py-4 text-[15px] font-semibold text-[#27458d] transition hover:-translate-y-0.5"
                    >
                        Create FREE Flyer
                    </a>

                    <a
                        href="/pricing"
                        class="inline-flex items-center justify-center rounded-full border border-white/18 bg-white/5 px-7 py-4 text-[15px] font-semibold text-white transition hover:bg-white/10"
                    >
                        View Pricing
                    </a>
                </div>

                <div class="mt-6 text-[13px] text-white/68">
                    No credit card required • Launch in minutes
                </div>
            </div>
        </div>
    </section>
</div>
      </div>
    </div>
  </main>
  @include('public.layout.footer')
</body>
</html>