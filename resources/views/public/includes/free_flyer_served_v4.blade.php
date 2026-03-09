{{-- =========================================
   FREE FLYER CONNECTED UNIT (BLUE / INDIGO CTA)
========================================= --}}
@php
    $brandBlue = $brandBlue ?? '#2c4273';
@endphp

<div class="w-full pt-8 pb-10 lg:pt-12 lg:pb-14">
    <div class="mx-auto max-w-[1000px] px-4 sm:px-6 lg:px-8">

        <div class="overflow-hidden rounded-[28px] shadow-[0_18px_48px_rgba(0,0,0,.12)]">
            <div class="grid grid-cols-1 md:grid-cols-2 items-stretch">

                {{-- LEFT PANEL --}}
                <div class="bg-[#2c4273] px-8 py-10 lg:px-12 lg:py-12 flex items-center">
                    <div class="ml-auto max-w-[420px] text-center">

                        <div class="flex items-center justify-end gap-3 text-[12px] tracking-[0.18em] uppercase text-white/60">
                            Flyer Creation Wizard

                            <div class="flex h-[48px] w-[48px] items-center justify-center rounded-full border border-white/40">
                                <i class="ti-wand text-[16px] text-white"></i>
                            </div>
                        </div>

                        <h3 class="mt-6 font-display text-[34px] leading-[1.05] text-white">
                            Start With a<br>Free Flyer
                        </h3>

                        <div class="mt-4 ml-auto h-[2px] w-20 bg-white/40"></div>

                        <p class="mt-6 text-[16px] leading-8 text-white/90">
                            Enter your email and a property address or MLS number and we’ll instantly create a draft.
                        </p>

                        <div class="mt-5 text-[13px] text-white/60">
                            Takes less than 30 seconds to start.
                        </div>

                    </div>
                </div>

                {{-- FORM PANEL --}}
                <div class="bg-[#f4f5f8] px-8 py-10 lg:px-10 lg:py-12 flex items-center">

                    <div class="w-full max-w-[420px]">

                        <form method="post" action="#" class="space-y-6">
                            @csrf

                            {{-- EMAIL --}}
                            <div>
                                <label class="mb-2 block text-[12px] font-semibold uppercase tracking-[0.14em] text-[#5c6b82]">
                                    Email
                                </label>

                                <input
                                    type="email"
                                    name="email"
                                    placeholder="Your email"
                                    class="block w-full rounded-[16px] border border-[#c9cfdb] bg-white px-5 py-3.5 text-[16px] text-gray-800 outline-none focus:border-[#5d78ff]"
                                >
                            </div>

                            {{-- ADDRESS --}}
                            <div>
                                <label class="mb-2 block text-[12px] font-semibold uppercase tracking-[0.14em] text-[#5c6b82]">
                                    Address or MLS#
                                </label>

                                <input
                                    type="text"
                                    name="listing_input"
                                    placeholder="Address or MLS# of listing"
                                    class="block w-full rounded-[16px] border border-[#c9cfdb] bg-white px-5 py-3.5 text-[16px] text-gray-800 outline-none focus:border-[#5d78ff]"
                                >
                            </div>

                            {{-- BUTTON --}}
                            <button
                                type="submit"
                                class="w-full rounded-full py-3.5 text-[16px] font-semibold text-white
                                    transition-all duration-200 hover:-translate-y-[1px]"
                                style="
                                    font-family: inherit;
                                    background:
                                        linear-gradient(90deg,#5668d9,#6a63e3,#7a66e8) padding-box,
                                        linear-gradient(135deg,#7fb9ff,#8c72ff,#a36cff) border-box;
                                    border:2px solid transparent;
                                    box-shadow:0 8px 18px rgba(0,0,0,.16);
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