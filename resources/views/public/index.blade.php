{{-- resources/views/home/hero.blade.php --}}
<!DOCTYPE html>
<html lang="en" class="h-full bg-white text-gray-900">
@include('public.layout.head');

<body class="min-h-screen bg-white pt-[50px]">
    @include('public.layout.nav')
    <section>
        @include('public.includes.hero_card')
    </section>
    <section>
        @include('public.includes.features_section')
    </section>
    <section >
        @include('public.includes.top_views_4up')
    </section>



    {{-- ANNOUNCE YOUR LISTINGS / USE CASES SECTION --}}
@php
    $announceTitle = $announceTitle ?? 'Announce Your Listings';
    $announceIntro = $announceIntro ?? 'From new listings to open houses and price improvements, Realty Emails helps you promote the moments that matter most.';
    $announceText  = $announceText  ?? 'Use flyers for the occasions buyers and agents care about most — and keep your listings in front of the right audience at the right time.';
    $brandBlue     = $brandBlue ?? '#214e9b';
    $brandGold     = $brandGold ?? '#e79a63';

    $announceItems = $announceItems ?? [
        [
            'icon' => 'ti-eye',
            'title' => 'Pre-MLS',
            'text' => 'Create early interest before a property officially hits the market.',
        ],
        [
            'icon' => 'ti-announcement',
            'title' => 'Just Listed',
            'text' => 'Announce a brand new listing with a polished flyer ready to share.',
        ],
        [
            'icon' => 'ti-home',
            'title' => 'Open House',
            'text' => 'Promote upcoming dates and times so more people know when to visit.',
        ],
        [
            'icon' => 'ti-trending-down',
            'title' => 'Reduced',
            'text' => 'Highlight a price improvement and renew attention on the listing.',
        ],
        [
            'icon' => 'ti-reload',
            'title' => 'Updated',
            'text' => 'Share important listing updates, refreshed details, or new features.',
        ],
        [
            'icon' => 'ti-hummer',
            'title' => 'Builders',
            'text' => 'Promote communities, quick move-ins, specs, and special builder events.',
        ],
    ];
@endphp

<section class="w-full bg-[#f5f5f7] py-14 lg:py-18">
    <div class="mx-auto w-full max-w-screen-2xl px-4 sm:px-6 lg:px-10" style="max-width: 1600px;">

        {{-- Top intro --}}
        <div class="rounded-[30px] bg-white px-6 py-10 sm:px-10 sm:py-12 shadow-[0_14px_36px_rgba(0,0,0,.05)] ring-1 ring-black/5">
            <div class="grid grid-cols-1 xl:grid-cols-[1.1fr_.9fr] gap-10 xl:gap-12 items-center">

                {{-- Left copy --}}
                <div class="max-w-[760px]">
                    <div class="inline-flex items-center gap-3 rounded-full border border-[#dfe6f3] bg-[#f7f9fd] px-4 py-2">
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-[#214e9b]/8">
                            <i class="ti-announcement text-[18px]" style="color: {{ $brandBlue }};"></i>
                        </span>
                        <span class="text-[12px] font-semibold uppercase tracking-[0.16em] text-gray-500">
                            Flyer Use Cases
                        </span>
                    </div>

                    <h2 class="font-display mt-6 text-[34px] sm:text-[46px] lg:text-[54px] leading-[0.98] tracking-tight text-[#1c1d22]">
                        {{ $announceTitle }}
                    </h2>

                    <p class="mt-5 max-w-[680px] text-[16px] sm:text-[18px] leading-8 text-gray-600">
                        {{ $announceIntro }}
                    </p>

                    <p class="mt-4 max-w-[680px] text-[15px] sm:text-[16px] leading-8 text-gray-500">
                        {{ $announceText }}
                    </p>

                    <div class="mt-7 flex flex-wrap gap-x-6 gap-y-3 text-[14px] sm:text-[15px] text-gray-600">
                        <div class="flex items-center gap-3">
                            <span class="h-2.5 w-2.5 rounded-full" style="background-color: {{ $brandGold }};"></span>
                            <span>Promote important listing milestones</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="h-2.5 w-2.5 rounded-full" style="background-color: {{ $brandGold }};"></span>
                            <span>Stay visible beyond the MLS</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="h-2.5 w-2.5 rounded-full" style="background-color: {{ $brandGold }};"></span>
                            <span>Use the right message for the right moment</span>
                        </div>
                    </div>
                </div>

                {{-- Right visual --}}
                <div class="flex justify-center xl:justify-end">
                    <div class="relative">
                        <div class="absolute inset-0 rounded-full bg-[#214e9b]/6 blur-3xl scale-110"></div>

                        <div class="relative rounded-[28px] bg-[linear-gradient(180deg,#ffffff_0%,#f7f9fd_100%)] p-6 sm:p-8 shadow-[0_18px_40px_rgba(0,0,0,.08)] ring-1 ring-black/5">
                            <img
                                src="{{ asset('images/2FlyerPerspective.gif') }}"
                                alt="Realty Emails flyer examples"
                                class="mx-auto w-full max-w-[320px] sm:max-w-[360px] h-auto object-contain"
                            >
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Occasions grid --}}
        <div class="mt-10 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-7">
            @foreach($announceItems as $item)
                <article class="group rounded-[26px] bg-white p-6 sm:p-7 shadow-[0_8px_24px_rgba(0,0,0,.05)] ring-1 ring-black/5 transition duration-300 hover:-translate-y-[2px] hover:shadow-[0_16px_36px_rgba(0,0,0,.08)]">
                    <div class="flex items-start gap-4">
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-[#214e9b]/8 ring-1 ring-[#214e9b]/10">
                            <i class="{{ $item['icon'] }} text-[22px]" style="color: {{ $brandBlue }};"></i>
                        </div>

                        <div class="min-w-0">
                            <div class="text-[11px] font-semibold uppercase tracking-[0.16em] text-gray-400">
                                Occasion
                            </div>

                            <h3 class="mt-1 text-[24px] font-semibold leading-tight text-[#214e9b]">
                                {{ $item['title'] }}
                            </h3>
                        </div>
                    </div>

                    <div class="mt-5 h-px w-full bg-[#edf0f5]"></div>

                    <p class="mt-5 text-[15px] sm:text-[16px] leading-8 text-gray-600">
                        {{ $item['text'] }}
                    </p>
                </article>
            @endforeach
        </div>

    </div>
</section>



</body>
</html>