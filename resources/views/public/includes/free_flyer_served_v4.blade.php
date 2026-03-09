{{-- =========================================
   FREE FLYER CONNECTED UNIT
   SOFTER BLUE/PURPLE DEPTH VERSION
========================================= --}}
@php
    $brandBlue = $brandBlue ?? '#2c4273';
@endphp

<div class="w-full pt-8 pb-10 lg:pt-12 lg:pb-14">
    <div class="mx-auto max-w-[1000px] px-4 sm:px-6 lg:px-8">

        <div class="overflow-hidden rounded-[28px] shadow-[0_18px_48px_rgba(0,0,0,.12)]">
            <div class="grid grid-cols-1 md:grid-cols-2 items-stretch">

                {{-- LEFT PANEL --}}
                <div
                    class="relative overflow-hidden px-8 py-10 lg:px-12 lg:py-12 flex items-center before:absolute before:top-0 before:right-0 before:h-full before:w-[60px] before:bg-[#eef1f7] before:clip-path-[polygon(100%_0,0_50%,100%_100%)]"
                    style="
                        background:
                            radial-gradient(circle at 78% 22%, rgba(122,102,232,.16), transparent 24%),
                            radial-gradient(circle at 22% 82%, rgba(91,118,224,.16), transparent 34%),
                            linear-gradient(135deg, #2c4273 0%, #314984 48%, #3b4fa0 78%, #5e58c9 118%);
                    "
                >
                    {{-- subtle overlays --}}
                    <div class="pointer-events-none absolute inset-0">
                        <div class="absolute inset-x-0 top-0 h-px bg-white/10"></div>
                        <div class="absolute inset-0 bg-[linear-gradient(145deg,rgba(255,255,255,.03),transparent_34%,transparent_68%,rgba(123,102,232,.06))]"></div>
                    </div>

                    <div class="relative mx-auto max-w-[420px] text-center">

                        {{-- WIZARD HEADER --}}
                        <div class="flex items-center justify-center gap-3 text-[12px] tracking-[0.18em] uppercase text-white/65">
                            <span>Flyer Creation Wizard</span>

                            <div class="relative flex h-[48px] w-[48px] items-center justify-center rounded-full border border-white/30 bg-white/[0.025] shadow-[0_0_0_1px_rgba(255,255,255,.03)]">
                                <div class="pointer-events-none absolute inset-0 rounded-full bg-[linear-gradient(135deg,rgba(127,185,255,.10),rgba(163,108,255,.10))]"></div>
                                <i class="ti-wand relative text-[16px] text-white"></i>
                            </div>
                        </div>

                        <h3 class="mt-6 font-display text-[34px] leading-[1.05] text-white">
                            Start With a<br>Free Flyer
                        </h3>

                        {{-- separator tied softly to CTA palette --}}
                        <div
                            class="mt-5 mx-auto h-[2px] w-20 rounded-full"
                            style="
                                background: linear-gradient(90deg, rgba(127,185,255,.25), rgba(255,255,255,.75), rgba(163,108,255,.30));
                            "
                        ></div>

                        <p class="mt-6 text-[16px] leading-8 text-white/92">
                            Enter your email and a property address or MLS number and we’ll instantly create a draft.
                        </p>

                        <div class="mt-5 text-[13px] text-white/65">
                            Takes less than 30 seconds to start.
                        </div>
                    </div>
                </div>

                {{-- FORM PANEL --}}
                <div class="bg-[#eef1f7] md:border-l border-white/10 px-8 py-10 lg:px-10 lg:py-12 flex items-center">
                    <div class="mx-auto w-full max-w-[420px]">

                        <form method="post" action="#" class="space-y-6">
                            @csrf

                            {{-- EMAIL --}}
                            <div>
                                <label class="mb-2 block text-[12px] font-semibold uppercase tracking-[0.14em] text-[#50627f]">
                                    Email
                                </label>

                                <input
                                    type="email"
                                    name="email"
                                    placeholder="Your email"
                                    class="block w-full rounded-[16px] border border-[#c4cad8] bg-white px-5 py-3.5 text-[16px] text-gray-800 outline-none transition focus:border-[#7b66e8] focus:ring-2 focus:ring-[#7b66e8]/10"
                                >
                            </div>

                            {{-- ADDRESS --}}
                            <div>
                                <label class="mb-2 block text-[12px] font-semibold uppercase tracking-[0.14em] text-[#50627f]">
                                    Address or MLS#
                                </label>

                                <input
                                    type="text"
                                    name="listing_input"
                                    placeholder="Address or MLS# of listing"
                                    class="block w-full rounded-[16px] border border-[#c4cad8] bg-white px-5 py-3.5 text-[16px] text-gray-800 outline-none transition focus:border-[#7b66e8] focus:ring-2 focus:ring-[#7b66e8]/10"
                                >
                            </div>

                            {{-- BUTTON --}}
                            <button
                                type="submit"
                                class="w-full rounded-full py-3.5 text-[16px] font-semibold text-white transition-all duration-200 hover:-translate-y-[1px]"
                                style="
                                    font-family: inherit;
                                    background:
                                        linear-gradient(90deg,#5668d9,#6a63e3,#7a66e8) padding-box,
                                        linear-gradient(135deg,#7fb9ff,#8c72ff,#a36cff) border-box;
                                    border:2px solid transparent;
                                    box-shadow:
                                        0 8px 18px rgba(0,0,0,.16),
                                        0 0 0 1px rgba(255,255,255,.04) inset,
                                        0 0 18px rgba(140,114,255,.16);
                                "
                            >
                                Generate Flyer
                            </button>
                        </form>

                    </div>
                </div>

            </div>
        </div>

    </div>
</div>