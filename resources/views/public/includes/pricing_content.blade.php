@php
    $singlePrice = 25;

    $plans = [
        [
            'name' => '1 Credit',
            'price' => 25,
            'qty' => 1,
            'cta' => 'Buy 1 Credit',
            'featured' => false,
        ],
        [
            'name' => '3 Credits',
            'price' => 50,
            'qty' => 3,
            'cta' => 'Buy 3 Credits',
            'featured' => true,
        ],
        [
            'name' => '5 Credits',
            'price' => 75,
            'qty' => 5,
            'cta' => 'Buy 5 Credits',
            'featured' => false,
        ],
        [
            'name' => '10 Credits',
            'price' => 100,
            'qty' => 10,
            'cta' => 'Buy 10 Credits',
            'featured' => false,
        ],
        [
            'name' => '15 Credits',
            'price' => 135,
            'qty' => 15,
            'cta' => 'Buy 15 Credits',
            'featured' => false,
        ],
        [
            'name' => '20 Credits',
            'price' => 160,
            'qty' => 20,
            'cta' => 'Buy 20 Credits',
            'featured' => false,
        ],
    ];
@endphp

<div class="bg-[#f6f7fb] text-[#1d2a44]">

    {{-- HERO --}}
    <section class="relative overflow-hidden bg-gradient-to-br from-[#213d78] via-[#284785] to-[#1b2f63]">
        <div class="absolute inset-0 opacity-[0.08]">
            <div class="absolute -top-24 -left-24 h-72 w-72 rounded-full bg-white blur-3xl"></div>
            <div class="absolute bottom-0 right-0 h-80 w-80 rounded-full bg-[#8ea8e8] blur-3xl"></div>
        </div>

        <div class="relative mx-auto max-w-[1300px] px-6 py-16 sm:px-8 lg:px-10 lg:py-20">
            <div class="grid items-center gap-10 lg:grid-cols-[1.15fr_.85fr]">

                <div class="text-white">
                    <div class="inline-flex items-center rounded-full border border-white/15 bg-white/10 px-4 py-1.5 text-[11px] font-semibold uppercase tracking-[0.24em] text-white/85 backdrop-blur-sm">
                        Simple Pricing
                    </div>

                    <h1 class="mt-6 max-w-[700px] font-serif text-[42px] leading-[0.98] text-white sm:text-[54px] lg:text-[66px]">
                        Flexible Pricing
                        <span class="block text-white/90">For Every Listing</span>
                    </h1>

                    <p class="mt-6 max-w-[650px] text-[16px] leading-7 text-white/78 sm:text-[18px]">
                        Buy only what you need, or take advantage of our limited-time unlimited flyer special.
                        Designed for agents who want polished exposure without complicated pricing.
                    </p>

                    <div class="mt-8 flex flex-wrap gap-4">
                        <a href="#pricing"
                           class="inline-flex items-center justify-center rounded-full bg-white px-7 py-3 text-[15px] font-semibold text-[#213d78] shadow-lg shadow-black/10 transition hover:-translate-y-[1px] hover:bg-[#f4f7ff]">
                            View Pricing
                        </a>

                        <a href="#special"
                           class="inline-flex items-center justify-center rounded-full border border-white/20 bg-white/10 px-7 py-3 text-[15px] font-semibold text-white backdrop-blur-sm transition hover:bg-white/15">
                            See Unlimited Offer
                        </a>
                    </div>
                </div>

                <div>
                    <div class="rounded-[30px] border border-white/10 bg-white/10 p-5 shadow-[0_20px_60px_rgba(0,0,0,0.18)] backdrop-blur-sm">
                        <div class="overflow-hidden rounded-[24px] bg-white shadow-xl">
                            <div class="bg-gradient-to-r from-[#2b4a89] to-[#35579b] px-6 py-5 text-white">
                                <div class="text-[11px] font-semibold uppercase tracking-[0.22em] text-white/70">
                                    Limited Time
                                </div>
                                <div class="mt-2 font-serif text-[34px] leading-none">
                                    Unlimited
                                </div>
                                <div class="mt-2 text-[14px] text-white/82">
                                    3 months of unlimited e-flyers
                                </div>
                            </div>

                            <div class="px-6 py-6">
                                <div class="flex items-end gap-2">
                                    <span class="font-serif text-[52px] leading-none text-[#203b76]">$99</span>
                                    <span class="pb-1 text-[14px] font-medium text-[#64748b]">special offer</span>
                                </div>

                                <p class="mt-4 text-[15px] leading-7 text-[#51607a]">
                                    Perfect for agents who want to create and promote multiple listings
                                    over a 3-month stretch without worrying about credit limits.
                                </p>

                                <ul class="mt-6 space-y-3 text-[14px] text-[#31405e]">
                                    <li class="flex items-start gap-3">
                                        <span class="mt-1 h-2.5 w-2.5 rounded-full bg-[#31559b]"></span>
                                        Unlimited e-flyer creation for 3 months
                                    </li>
                                    <li class="flex items-start gap-3">
                                        <span class="mt-1 h-2.5 w-2.5 rounded-full bg-[#31559b]"></span>
                                        Great for active inventory and fast-moving agents
                                    </li>
                                    <li class="flex items-start gap-3">
                                        <span class="mt-1 h-2.5 w-2.5 rounded-full bg-[#31559b]"></span>
                                        Optional limited-time promotional offer
                                    </li>
                                </ul>

                                <a href="#"
                                   class="mt-7 inline-flex w-full items-center justify-center rounded-full bg-gradient-to-r from-[#233f7b] to-[#35579b] px-6 py-3 text-[15px] font-semibold text-white shadow-[0_10px_24px_rgba(35,63,123,0.28)] transition hover:-translate-y-[1px]">
                                    Claim Unlimited Special
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- CREDIT PRICING --}}
    <section id="pricing" class="mx-auto max-w-[1300px] px-6 py-16 sm:px-8 lg:px-10 lg:py-20">
        <div class="mx-auto max-w-[780px] text-center">
            <div class="text-[12px] font-semibold uppercase tracking-[0.22em] text-[#5b6f98]">
                Standard Packages
            </div>
            <h2 class="mt-3 font-serif text-[40px] leading-none text-[#23407c] sm:text-[54px]">
                Credit Pricing
            </h2>
            <p class="mt-5 text-[16px] leading-7 text-[#5f6f89]">
                Choose the package that fits your current marketing needs. Buy a little or stock up and save.
            </p>
        </div>

        <div class="mt-12 grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
            @foreach($plans as $plan)
                @php
                    $per = $plan['price'] / $plan['qty'];
                    $save = ($singlePrice * $plan['qty']) - $plan['price'];
                @endphp

                <div class="group relative overflow-hidden rounded-[28px] border {{ $plan['featured'] ? 'border-[#31559b]/30 bg-gradient-to-b from-white to-[#eef3ff]' : 'border-[#dbe3f2] bg-white' }} p-7 shadow-[0_16px_40px_rgba(31,54,105,0.08)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_22px_50px_rgba(31,54,105,0.12)]">

                    <div class="absolute right-5 top-5 rounded-full px-3 py-1 text-[10px] font-bold uppercase tracking-[0.18em] shadow-sm {{ $plan['featured'] ? 'bg-[#2c4d90] text-white' : 'bg-[#eef3fb] text-[#45629a]' }}">
                        ${{ number_format($per, 2) }}/flyer
                        @if($plan['qty'] > 1)
                            • Save ${{ number_format($save, 0) }}
                        @endif
                    </div>

                    <div class="pr-24">
                        <div class="text-[12px] font-semibold uppercase tracking-[0.22em] text-[#6b7f9e]">
                            {{ $plan['name'] }}
                        </div>

                        <div class="mt-4 flex items-end gap-2">
                            <span class="font-serif text-[52px] leading-none text-[#1f3972]">
                                ${{ $plan['price'] }}
                            </span>
                        </div>

                        <div class="mt-4 text-[17px] font-semibold text-[#2f4f8f]">
                            {{ $plan['qty'] }} E-Flyer Credit{{ $plan['qty'] > 1 ? 's' : '' }}
                        </div>
                    </div>

                    <div class="mt-8">
                        <a href="#"
                           class="inline-flex w-full items-center justify-center rounded-full {{ $plan['featured'] ? 'bg-gradient-to-r from-[#223d78] to-[#35579b] text-white shadow-[0_12px_24px_rgba(34,61,120,0.24)]' : 'border border-[#cad5ea] bg-white text-[#23407c]' }} px-6 py-3 text-[15px] font-semibold transition hover:-translate-y-[1px]">
                            {{ $plan['cta'] }}
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- SPECIAL OFFER --}}
    <section id="special" class="px-6 pb-16 sm:px-8 lg:px-10 lg:pb-20">
        <div class="mx-auto max-w-[1300px] overflow-hidden rounded-[34px] bg-gradient-to-r from-[#1d3366] via-[#26427d] to-[#33579b] shadow-[0_22px_60px_rgba(26,47,99,0.22)]">
            <div class="grid items-center gap-8 px-8 py-10 sm:px-10 lg:grid-cols-[1.15fr_.85fr] lg:px-14 lg:py-14">

                <div class="text-white">
                    <div class="inline-flex rounded-full border border-white/15 bg-white/10 px-4 py-1.5 text-[11px] font-semibold uppercase tracking-[0.2em] text-white/80">
                        Limited Time Special
                    </div>

                    <h3 class="mt-5 font-serif text-[38px] leading-[1.02] sm:text-[48px]">
                        3 Months Unlimited
                        <span class="block text-white/88">For Just $99</span>
                    </h3>

                    <p class="mt-5 max-w-[650px] text-[17px] leading-8 text-white/78">
                        Need more flexibility? Skip the credit counting and run unlimited e-flyers for three full months.
                        This is a strong option for active agents, teams, and high-volume listing periods.
                    </p>
                </div>

                <div class="rounded-[28px] bg-white/10 p-6 backdrop-blur-sm">
                    <div class="rounded-[24px] bg-white px-7 py-8 text-center shadow-xl">
                        <div class="text-[12px] font-semibold uppercase tracking-[0.22em] text-[#61759a]">
                            Special Price
                        </div>
                        <div class="mt-3 font-serif text-[64px] leading-none text-[#223d78]">
                            $99
                        </div>
                        <div class="mt-3 text-[18px] font-semibold text-[#35579b]">
                            Unlimited E-Flyers / 3 Months
                        </div>
                        <p class="mt-4 text-[15px] leading-7 text-[#5c6c86]">
                            Optional promotional offer for a limited time.
                        </p>

                        <a href="#"
                           class="mt-7 inline-flex w-full items-center justify-center rounded-full bg-gradient-to-r from-[#223d78] to-[#35579b] px-6 py-3 text-[15px] font-semibold text-white shadow-[0_12px_28px_rgba(34,61,120,0.24)] transition hover:-translate-y-[1px]">
                            Get Unlimited Access
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- BOTTOM NOTE --}}
    <section class="px-6 pb-16 sm:px-8 lg:px-10">
        <div class="mx-auto max-w-[1000px] text-center">
            <h4 class="font-serif text-[34px] text-[#23407c] sm:text-[42px]">
                Straightforward Pricing. Better Exposure.
            </h4>
            <p class="mt-4 text-[16px] leading-8 text-[#60708b]">
                Whether you need one flyer or ongoing listing promotion, RealtyEmails gives you a polished,
                agent-friendly way to market your properties online and in print.
            </p>
        </div>
    </section>

</div>