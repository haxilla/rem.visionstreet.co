{{-- =========================================
   TRUST AD + FREE FLYER CONNECTED UNIT
========================================= --}}
@php
    $brandBlue = $brandBlue ?? '#1e3566';
    $brandGold = $brandGold ?? '#f0d28a';
@endphp

<section class="w-full py-10 lg:py-14">
    <div class="mx-auto max-w-[1100px] px-4 sm:px-6 lg:px-8">

        <div class="overflow-hidden rounded-[28px] shadow-[0_18px_48px_rgba(0,0,0,.16)]">
            <div class="grid grid-cols-1 md:grid-cols-[425px_minmax(0,1fr)] items-stretch">

                {{-- LEFT: TRUST AD --}}
                <div class="bg-white flex items-stretch">
                    <img
                        src="{{ asset('images/realtyemails-served-thousands.jpg') }}"
                        alt="RealtyEmails has served thousands of agents since 2006"
                        class="block w-full h-auto md:h-full object-contain object-center bg-white"
                    >
                </div>

                {{-- RIGHT: FREE FLYER PANEL --}}
                <div class="bg-[#1e3566] px-6 py-6 sm:px-7 sm:py-7 lg:px-8 lg:py-8">
                    <div class="grid grid-cols-1 gap-8 lg:grid-cols-[280px_minmax(0,400px)] lg:items-start lg:justify-between">

                        {{-- LEFT SIDE: TEXT --}}
                        <div class="flex flex-col items-start text-left">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-[52px] w-[52px] items-center justify-center rounded-full border-2 shrink-0"
                                    style="border-color: {{ $brandGold }}; background: rgba(255,255,255,.06);"
                                >
                                    <i class="ti-wand text-[18px]" style="color: {{ $brandGold }};"></i>
                                </div>

                                <div class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/60">
                                    Flyer Creation Wizard
                                </div>
                            </div>

                            <h3 class="font-display mt-5 text-[28px] leading-[1.02] text-white sm:text-[30px]">
                                Start With a<br>Free Flyer
                            </h3>

                            <div class="mt-4 h-[2px] w-20 rounded-full" style="background: {{ $brandGold }};"></div>

                            <p class="mt-5 max-w-[270px] text-[16px] leading-8 text-white/88">
                                Enter your name, email and a property address or MLS number and we’ll instantly generate a flyer draft you can preview.
                            </p>

                            <div class="mt-4 text-[12px] text-white/55">
                                Takes less than 30 seconds to start.
                            </div>
                        </div>

                        {{-- RIGHT SIDE: FORM --}}
                        <div class="w-full max-w-[400px] lg:ml-auto self-center">
                            <form method="post" action="#" class="space-y-4">
                                @csrf

                                {{-- NAME --}}
                                <div>
                                    <label class="mb-2 block text-[12px] font-semibold uppercase tracking-[0.14em] text-white/60">
                                        Your Name
                                    </label>
                                    <input
                                        type="text"
                                        name="name"
                                        placeholder="Your name"
                                        class="block w-full rounded-[16px] border border-gray-200 bg-white px-5 py-3.5 text-[16px] text-gray-800 outline-none"
                                    >
                                </div>

                                {{-- EMAIL --}}
                                <div>
                                    <label class="mb-2 block text-[12px] font-semibold uppercase tracking-[0.14em] text-white/60">
                                        Email
                                    </label>
                                    <input
                                        type="email"
                                        name="email"
                                        placeholder="Your email"
                                        class="block w-full rounded-[16px] border border-gray-200 bg-white px-5 py-3.5 text-[16px] text-gray-800 outline-none"
                                    >
                                </div>

                                {{-- ADDRESS --}}
                                <div>
                                    <label class="mb-2 block text-[12px] font-semibold uppercase tracking-[0.14em] text-white/60">
                                        Address or MLS#
                                    </label>
                                    <input
                                        type="text"
                                        name="listing_input"
                                        placeholder="Address or MLS# of listing"
                                        class="block w-full rounded-[16px] border border-gray-200 bg-white px-5 py-3.5 text-[16px] text-gray-800 outline-none"
                                    >
                                </div>

                                <button
                                    type="submit"
                                    class="w-full rounded-full py-4 text-[16px] font-semibold text-[#1d2f5f] shadow-lg transition hover:-translate-y-[1px]"
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