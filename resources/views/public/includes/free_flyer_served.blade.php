<section class="w-full py-10 lg:py-14">
    <div class="mx-auto max-w-screen-2xl px-4 sm:px-6 lg:px-10">

        <div class="grid grid-cols-1 gap-8 xl:grid-cols-[420px_minmax(0,1fr)] items-start">

            {{-- LEFT: TRUST AD --}}
            <div class="overflow-hidden rounded-[30px] bg-white shadow-[0_15px_45px_rgba(0,0,0,.12)] self-start">
                <img
                    src="{{ asset('images/realtyemails-served-thousands.jpg') }}"
                    alt="RealtyEmails has served thousands of agents since 2006"
                    class="block w-full h-auto"
                >
            </div>

            {{-- RIGHT: COMPACT FREE FLYER PANEL --}}
            <div class="rounded-[30px] bg-[#1e3566] px-6 py-6 sm:px-8 sm:py-7 shadow-[0_20px_55px_rgba(0,0,0,.22)] self-start">
                <div class="grid grid-cols-1 gap-8 lg:grid-cols-[minmax(280px,380px)_minmax(320px,420px)] lg:items-start">

                    {{-- LEFT TEXT --}}
                    <div class="flex flex-col items-start text-left">
                        <div
                            class="flex h-[64px] w-[64px] items-center justify-center rounded-full border-2 shadow-lg"
                            style="border-color:#f0d28a; background: rgba(255,255,255,.06);"
                        >
                            <i class="ti-wand text-[22px]" style="color:#f0d28a;"></i>
                        </div>

                        <div class="mt-5 text-[12px] uppercase tracking-[0.18em] text-white/60 font-semibold">
                            Flyer Creation Wizard
                        </div>

                        <h3 class="font-display mt-3 text-[28px] sm:text-[32px] leading-[1.05] text-white">
                            Start With a<br>Free Flyer
                        </h3>

                        <div class="mt-5 h-[2px] w-20 rounded-full bg-[#f0d28a]"></div>

                        <p class="mt-5 max-w-[340px] text-[15px] leading-7 text-white/80">
                            Enter your email and a property address or MLS number and we’ll instantly generate a flyer draft you can preview.
                        </p>

                        <div class="mt-4 text-[12px] text-white/55">
                            Takes less than 30 seconds to start.
                        </div>
                    </div>

                    {{-- RIGHT FORM --}}
                    <div class="w-full">
                        <form method="post" action="#" class="space-y-4">
                            @csrf

                            <div>
                                <label class="mb-1.5 block text-[12px] uppercase tracking-[0.14em] text-white/60 font-semibold">
                                    Email
                                </label>
                                <input
                                    type="email"
                                    name="email"
                                    placeholder="Your email"
                                    class="w-full rounded-[14px] border border-gray-200 bg-white px-4 py-3 text-[15px] text-gray-800"
                                >
                            </div>

                            <div>
                                <label class="mb-1.5 block text-[12px] uppercase tracking-[0.14em] text-white/60 font-semibold">
                                    Address or MLS#
                                </label>
                                <input
                                    type="text"
                                    name="listing_input"
                                    placeholder="Address or MLS# of listing"
                                    class="w-full rounded-[14px] border border-gray-200 bg-white px-4 py-3 text-[15px] text-gray-800"
                                >
                            </div>

                            <button
                                type="submit"
                                class="w-full rounded-full py-3.5 text-[15px] font-semibold text-[#1d2f5f] shadow-lg transition hover:-translate-y-[1px]"
                                style="background:#f0d28a;"
                            >
                                Generate Free Flyer
                            </button>
                        </form>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>