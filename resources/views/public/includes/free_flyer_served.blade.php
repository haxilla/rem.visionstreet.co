{{-- =========================================
   TRUST + FREE FLYER CONNECTED SECTION
   Drop-in replacement
========================================= --}}
@php
    $brandBlue = $brandBlue ?? '#1e3566';
    $brandGold = $brandGold ?? '#f0d28a';
@endphp

<section class="w-full py-10 lg:py-14">
    <div class="mx-auto max-w-[1120px] px-4 sm:px-6 lg:px-8">

        <div class="overflow-hidden rounded-[30px] shadow-[0_20px_55px_rgba(0,0,0,.16)]">
            <div class="grid grid-cols-1 xl:grid-cols-[320px_minmax(0,1fr)]">

                {{-- LEFT: TRUST AD --}}
                <div class="bg-white">
                    <img
                        src="{{ asset('images/realtyemails-served-thousands.jpg') }}"
                        alt="RealtyEmails has served thousands of agents since 2006"
                        class="block w-full h-auto"
                    >
                </div>

                {{-- RIGHT: FREE FLYER PANEL --}}
                <div class="bg-[#1e3566] px-6 py-5 sm:px-7 sm:py-6 lg:px-8 lg:py-7">
                    <div class="grid grid-cols-1 gap-5 lg:grid-cols-[300px_400px] lg:justify-center lg:items-start">

                        {{-- LEFT SIDE: ICON + TEXT --}}
                        <div class="flex flex-col items-start text-left lg:pl-8">
                            <div
                                class="flex h-[58px] w-[58px] items-center justify-center rounded-full border-2"
                                style="border-color: {{ $brandGold }}; background: rgba(255,255,255,.06);"
                            >
                                <i class="ti-wand text-[20px]" style="color: {{ $brandGold }};"></i>
                            </div>

                            <div class="mt-4 text-[12px] font-semibold uppercase tracking-[0.18em] text-white/60">
                                Flyer Creation Wizard
                            </div>

                            <h3 class="font-display mt-2 text-[28px] leading-[1.02] text-white sm:text-[30px]">
                                Start With a<br>Free Flyer
                            </h3>

                            <div class="mt-4 h-[2px] w-20 rounded-full" style="background: {{ $brandGold }};"></div>

                            <p class="mt-4 max-w-[320px] text-[15px] leading-7 text-white/80">
                                Enter your email and a property address or MLS number and we’ll instantly generate a flyer draft you can preview.
                            </p>

                            <div class="mt-3 text-[12px] text-white/55">
                                Takes less than 30 seconds to start.
                            </div>
                        </div>

                        {{-- RIGHT SIDE: FORM --}}
                        <div class="w-full max-w-[420px] lg:ml-auto">
                            <form method="post" action="#" class="space-y-3">
                                @csrf

                                <div>
                                    <label class="mb-1.5 block text-[12px] font-semibold uppercase tracking-[0.14em] text-white/60">
                                        Email
                                    </label>

                                    <input
                                        type="email"
                                        name="email"
                                        placeholder="Your email"
                                        class="block w-full rounded-[14px] border border-gray-200 bg-white px-4 py-3 text-[15px] text-gray-800 outline-none"
                                    >
                                </div>

                                <div>
                                    <label class="mb-1.5 block text-[12px] font-semibold uppercase tracking-[0.14em] text-white/60">
                                        Address or MLS#
                                    </label>

                                    <input
                                        type="text"
                                        name="listing_input"
                                        placeholder="Address or MLS# of listing"
                                        class="block w-full rounded-[14px] border border-gray-200 bg-white px-4 py-3 text-[15px] text-gray-800 outline-none"
                                    >
                                </div>

                                <button
                                    type="submit"
                                    class="w-full rounded-full py-3.5 text-[15px] font-semibold text-[#1d2f5f] shadow-lg transition hover:-translate-y-[1px]"
                                    style="background: {{ $brandGold }};"
                                >
                                    Generate Free Flyer
                                </button>
                            </form>
                        </div>

                    </div>
                </div>

            </div>
        </div>

    </div>
</section>