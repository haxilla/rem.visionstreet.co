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



    
{{-- ANNOUNCE YOUR LISTINGS / OCCASIONS SECTION --}}
@php
    $announceTitle = $announceTitle ?? 'Announce Your Listings';
    $announceIntro = $announceIntro ?? 'Every listing has moments worth promoting.';
    $announceBody  = $announceBody ?? 'From pre-MLS exposure to open houses, price improvements, and major updates, Realty Emails helps you create the right flyer for the right occasion so your listing stays visible when it matters most.';
    $brandBlue     = $brandBlue ?? '#214e9b';
    $brandGold     = $brandGold ?? '#e79a63';

    $announceTags = $announceTags ?? [
        'Pre-MLS',
        'Just Listed',
        'Open House',
        'Reduced',
        'Updated',
        'Builders',
    ];
@endphp

<section class="w-full py-16 lg:py-20" style="background: linear-gradient(180deg, #eef3fb 0%, #f6f8fc 100%);">
    <div class="mx-auto w-full max-w-screen-2xl px-4 sm:px-6 lg:px-10" style="max-width: 1600px;">

        <div class="grid grid-cols-1 xl:grid-cols-[1fr_.95fr] gap-10 xl:gap-14 items-center">

            {{-- LEFT COPY --}}
            <div class="max-w-[760px]">
                <div class="inline-flex items-center gap-3">
                    <span class="flex h-12 w-12 items-center justify-center rounded-full bg-white/80 shadow-[0_8px_18px_rgba(33,78,155,.08)] ring-1 ring-[#214e9b]/10">
                        <i class="ti-announcement text-[20px]" style="color: {{ $brandBlue }};"></i>
                    </span>

                    <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-[#214e9b]/70">
                        Flyer Occasions
                    </span>
                </div>

                <h2 class="font-display mt-6 text-[36px] sm:text-[48px] lg:text-[56px] leading-[0.98] tracking-tight text-[#1c1d22]">
                    {{ $announceTitle }}
                </h2>

                <div class="mt-5 h-[2px] w-24 rounded-full" style="background-color: {{ $brandGold }};"></div>

                <p class="mt-6 text-[20px] sm:text-[24px] leading-[1.45] text-[#214e9b]">
                    {{ $announceIntro }}
                </p>

                <p class="mt-5 max-w-[700px] text-[16px] sm:text-[18px] leading-8 text-gray-600">
                    {{ $announceBody }}
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    @foreach($announceTags as $tag)
                        <span class="inline-flex items-center rounded-full border border-[#d9e3f3] bg-white/75 px-4 py-2 text-[14px] font-medium text-[#214e9b] shadow-[0_6px_14px_rgba(0,0,0,.03)]">
                            {{ $tag }}
                        </span>
                    @endforeach
                </div>
            </div>

            {{-- RIGHT VISUAL --}}
            <div class="relative flex justify-center xl:justify-end">
                {{-- background accent only, no box --}}
                <div class="absolute inset-y-[12%] left-[10%] right-[10%] rounded-[40px] bg-[radial-gradient(circle_at_center,rgba(33,78,155,.10),rgba(33,78,155,0)_68%)] blur-2xl"></div>

                <div class="relative w-full max-w-[560px]">
                    {{-- subtle floating decorative shapes --}}
                    <div class="absolute left-0 top-[10%] h-24 w-24 rounded-full bg-white/55 blur-xl"></div>
                    <div class="absolute right-[8%] top-0 h-20 w-20 rounded-full bg-[#214e9b]/8 blur-xl"></div>
                    <div class="absolute bottom-[12%] left-[12%] h-28 w-28 rounded-full bg-[#e79a63]/10 blur-2xl"></div>

                    <img
                        src="{{ asset('images/2FlyerPerspective.gif') }}"
                        alt="Realty Emails flyer examples"
                        class="relative z-10 mx-auto w-full max-w-[420px] sm:max-w-[470px] lg:max-w-[520px] h-auto object-contain drop-shadow-[0_26px_34px_rgba(0,0,0,.12)]"
                    >
                </div>
            </div>

        </div>

        {{-- BOTTOM STRIP --}}
        <div class="mt-14 rounded-[28px] bg-white/78 px-6 py-6 sm:px-8 shadow-[0_10px_28px_rgba(0,0,0,.04)] ring-1 ring-black/5 backdrop-blur-sm">
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 xl:gap-10">
                <div>
                    <div class="text-[12px] font-semibold uppercase tracking-[0.16em] text-gray-400">
                        Before the market
                    </div>
                    <div class="mt-2 text-[18px] font-semibold text-[#214e9b]">
                        Build early interest
                    </div>
                    <p class="mt-2 text-[15px] leading-7 text-gray-600">
                        Use flyers for pre-MLS visibility and just-listed announcements.
                    </p>
                </div>

                <div>
                    <div class="text-[12px] font-semibold uppercase tracking-[0.16em] text-gray-400">
                        During the campaign
                    </div>
                    <div class="mt-2 text-[18px] font-semibold text-[#214e9b]">
                        Keep attention active
                    </div>
                    <p class="mt-2 text-[15px] leading-7 text-gray-600">
                        Promote open houses, updated details, and momentum-driving changes.
                    </p>
                </div>

                <div>
                    <div class="text-[12px] font-semibold uppercase tracking-[0.16em] text-gray-400">
                        Special situations
                    </div>
                    <div class="mt-2 text-[18px] font-semibold text-[#214e9b]">
                        Support unique property needs
                    </div>
                    <p class="mt-2 text-[15px] leading-7 text-gray-600">
                        Highlight reductions, builder inventory, and special community announcements.
                    </p>
                </div>
            </div>
        </div>

    </div>
</section>


</body>
</html>