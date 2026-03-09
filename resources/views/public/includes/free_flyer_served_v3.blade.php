{{-- =========================================
   FREE FLYER CONNECTED UNIT (CENTERED SPLIT)
========================================= --}}
@php
    $brandBlue = $brandBlue ?? '#1e3566';
    $brandGold = $brandGold ?? '#f0d28a';
    $formBg    = $formBg ?? '#f3ecdc';
    $buttonBg  = $buttonBg ?? '#dfb550';
@endphp

<div class="w-full pt-8 pb-10 lg:pt-12 lg:pb-14">
    <div class="mx-auto max-w-[1200px] px-4 sm:px-6 lg:px-8">

        <div class="overflow-hidden rounded-[28px] shadow-[0_18px_48px_rgba(0,0,0,.16)]">
            <div class="grid grid-cols-1 md:grid-cols-2 items-stretch">

                {{-- LEFT: BLUE TEXT PANEL --}}
                <div class="bg-[#1e3566] px-6 py-8 sm:px-7 sm:py-9 lg:px-10 lg:py-10 flex items-center">
                    <div class="ml-auto max-w-[420px] text-right">

                        <div class="flex items-center justify-end gap-3">
                            <div class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/60">
                                Flyer Creation Wizard
                            </div>

                            <div
                                class="flex h-[52px] w-[52px] items-center justify-center rounded-full border-2 shrink-0"
                                style="border-color: {{ $brandGold }}; background: rgba(255,255,255,.06);"
                            >
                                <i class="ti-wand text-[18px]" style="color: {{ $brandGold }};"></i>
                            </div>
                        </div>

                        <h3 class="font-display mt-6 text-[30px] leading-[1.05] text-white sm:text-[34px]">
                            Start With a<br>Free Flyer
                        </h3>

                        <div class="mt-4 ml-auto h-[2px] w-20 rounded-full" style="background: {{ $brandGold }};"></div>

                        <p class="mt-6 ml-auto max-w-[400px] text-[16px] leading-8 text-white/88">
                            Enter your email and a property address or MLS number and we’ll instantly create a draft.
                        </p>

                        <div class="mt-5 text-[13px] text-white/55">
                            Takes less than 30 seconds to start.
                        </div>
                    </div>
                </div>

                {{-- RIGHT: FORM PANEL --}}
                <div
                    class="bg-[#f3ecdc] px-6 py-7 sm:px-7 sm:py-8 lg:px-8 lg:py-9 flex items-center"
                    style="background: {{ $formBg }};"
                >
                    <div class="w-full max-w-[420px]">
                        <form method="post" action="#" class="space-y-5">
                            @csrf

                            {{-- EMAIL --}}
                            <div>
                                <label class="mb-2 block text-[12px] font-semibold uppercase tracking-[0.14em] text-[#5f6472]">
                                    Email
                                </label>
                                <input
                                    type="email"
                                    name="email"
                                    placeholder="Your email"
                                    class="block w-full rounded-[16px] border border-[#d9cfba] bg-white px-5 py-3.5 text-[16px] text-gray-800 outline-none"
                                >
                            </div>

                            {{-- ADDRESS --}}
                            <div>
                                <label class="mb-2 block text-[12px] font-semibold uppercase tracking-[0.14em] text-[#5f6472]">
                                    Address or MLS#
                                </label>
                                <input
                                    type="text"
                                    name="listing_input"
                                    placeholder="Address or MLS# of listing"
                                    class="block w-full rounded-[16px] border border-[#d9cfba] bg-white px-5 py-3.5 text-[16px] text-gray-800 outline-none"
                                >
                            </div>

                            <button
                                type="submit"
                                class="mt-2 w-full rounded-full py-4 text-[16px] font-semibold text-[#1e3566] shadow-[0_12px_24px_rgba(0,0,0,.14)] transition hover:-translate-y-[1px] hover:shadow-[0_16px_28px_rgba(0,0,0,.18)]"
                                style="background: {{ $buttonBg }};"
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