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
        @include('public.includes.top_views_2')
    </section>



    
{{-- ANNOUNCE YOUR LISTINGS / OCCASIONS SECTION --}}
@php
    $announceTitle = $announceTitle ?? 'Announce Your Listings';
    $announceIntro = $announceIntro ?? 'Realty Emails helps you promote the moments that matter most.';
    $announceBody  = $announceBody ?? 'From pre-MLS exposure to just-listed announcements, open houses, price reductions, and major updates, flyers give you a clear way to keep listings in front of buyers and agents at the right time.';
    $brandBlue     = $brandBlue ?? '#214e9b';
    $brandGold     = $brandGold ?? '#e79a63';

    $announceItems = $announceItems ?? [
        [
            'icon' => 'ti-eye',
            'title' => 'Pre-MLS',
            'text' => 'Create early interest before a property officially goes live.',
        ],
        [
            'icon' => 'ti-announcement',
            'title' => 'Just Listed',
            'text' => 'Announce a new listing with a polished flyer ready to share.',
        ],
        [
            'icon' => 'ti-home',
            'title' => 'Open House',
            'text' => 'Promote dates and times so more people know when to visit.',
        ],
        [
            'icon' => 'ti-trending-down',
            'title' => 'Reduced',
            'text' => 'Highlight a price improvement and renew interest in the property.',
        ],
        [
            'icon' => 'ti-reload',
            'title' => 'Updated',
            'text' => 'Share important listing changes, refreshed details, or new features.',
        ],
        [
            'icon' => 'ti-hummer',
            'title' => 'Builders',
            'text' => 'Promote communities, specs, quick move-ins, and special events.',
        ],
    ];
@endphp

<section class="w-full py-16 lg:py-20" style="background: linear-gradient(180deg, #eef3fb 0%, #f6f8fc 100%);">
    <div class="mx-auto w-full max-w-screen-2xl px-4 sm:px-6 lg:px-10" style="max-width: 1600px;">

        {{-- Top editorial block --}}
        <div class="grid grid-cols-1 xl:grid-cols-[auto_1fr] gap-10 xl:gap-14 items-center">

            {{-- Flyer visual - natural size, left of text --}}
            <div class="flex justify-center xl:justify-start">
                <img
                    src="{{ asset('images/2FlyerPerspective.gif') }}"
                    alt="Realty Emails flyer examples"
                    class="block h-auto w-auto max-w-full object-contain drop-shadow-[0_22px_28px_rgba(0,0,0,.12)]"
                >
            </div>

            {{-- Copy --}}
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

                <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4 max-w-[760px]">
                    <div class="flex items-start gap-3">
                        <span class="mt-[8px] h-2.5 w-2.5 rounded-full" style="background-color: {{ $brandGold }};"></span>
                        <span class="text-[15px] sm:text-[16px] text-gray-600">Use the right flyer for the right moment</span>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="mt-[8px] h-2.5 w-2.5 rounded-full" style="background-color: {{ $brandGold }};"></span>
                        <span class="text-[15px] sm:text-[16px] text-gray-600">Keep listings visible beyond the MLS</span>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="mt-[8px] h-2.5 w-2.5 rounded-full" style="background-color: {{ $brandGold }};"></span>
                        <span class="text-[15px] sm:text-[16px] text-gray-600">Promote milestones buyers and agents notice</span>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="mt-[8px] h-2.5 w-2.5 rounded-full" style="background-color: {{ $brandGold }};"></span>
                        <span class="text-[15px] sm:text-[16px] text-gray-600">Refresh attention when listings change</span>
                    </div>
                </div>
            </div>

        </div>

        {{-- Occasions row --}}
        <div class="mt-16 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-x-10 gap-y-10">
            @foreach($announceItems as $item)
                <article class="flex items-start gap-4">
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-white/85 shadow-[0_8px_18px_rgba(0,0,0,.04)] ring-1 ring-[#214e9b]/10">
                        <i class="{{ $item['icon'] }} text-[22px]" style="color: {{ $brandBlue }};"></i>
                    </div>

                    <div class="min-w-0">
                        <h3 class="text-[24px] font-semibold leading-tight text-[#214e9b]">
                            {{ $item['title'] }}
                        </h3>

                        <div class="mt-3 h-px w-full max-w-[220px] bg-[#dbe4f2]"></div>

                        <p class="mt-4 text-[15px] sm:text-[16px] leading-8 text-gray-600">
                            {{ $item['text'] }}
                        </p>
                    </div>
                </article>
            @endforeach
        </div>

    </div>
</section>


</body>
</html>