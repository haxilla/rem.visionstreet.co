@php
    $headline = $headline ?? 'Send Professional E-Flyers for Your Next Listing Event';
    $eyebrow  = $eyebrow ?? 'FLYER OCCASIONS';
    $copy     = $copy ?? 'Use RealtyEmails for every stage of your listing — from pre-MLS marketing to open houses, price reductions, updated listings, and new construction phases.';

    $flyerImage = $flyerImage ?? asset('images/luxury-flyers-example.png');

    $occasionCards = $occasionCards ?? [
        ['title' => 'Pre-MLS',     'desc' => 'Build interest before launch', 'icon' => 'eye'],
        ['title' => 'Just Listed', 'desc' => 'Promote your newest listing',  'icon' => 'home'],
        ['title' => 'Open House',  'desc' => 'Advertise showing events',     'icon' => 'calendar'],
        ['title' => 'Reduced',     'desc' => 'Highlight pricing updates',    'icon' => 'tag'],
        ['title' => 'Updated',     'desc' => 'Share listing changes',        'icon' => 'refresh'],
        ['title' => 'Builders',    'desc' => 'Market new homes',             'icon' => 'building'],
    ];
@endphp

<div class="w-full py-16 lg:py-24">
    <div class="mx-auto max-w-[1600px] px-4 sm:px-6 lg:px-8">

        {{-- TOP: flyer left, headline right from md up --}}
        <div class="grid grid-cols-1 md:grid-cols-[250px_minmax(0,1fr)] lg:grid-cols-[300px_minmax(0,1fr)] xl:grid-cols-[360px_minmax(0,1fr)] items-start gap-8 md:gap-10 lg:gap-14">

            {{-- LEFT: FLYERS --}}
            <div class="md:pt-2 md:-mt-10 lg:-mt-16">
                <div class="mx-auto max-w-[240px] sm:max-w-[260px] md:max-w-[250px] lg:max-w-[300px] xl:max-w-[360px] md:mx-0">
                    <img
                        src="{{ $flyerImage }}"
                        alt="Luxury real estate flyer examples"
                        class="block w-full h-auto select-none drop-shadow-[0_22px_40px_rgba(15,23,42,0.14)]"
                    >
                </div>

                <div class="mt-3 text-center md:text-left italic text-sm text-gray-500">
                    ** Flyers shown are examples only **
                </div>
            </div>

            {{-- RIGHT: HEADLINE / COPY --}}
            <div class="md:pt-2">
                <div class="mb-4 inline-flex items-center gap-3">
                    <span class="inline-flex h-11 w-11 items-center justify-center rounded-full bg-[#e8eefb] text-[#214e9b] shadow-inner">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 11v2"/>
                            <path d="M6 10v4"/>
                            <path d="M10 9.5 18 6v12l-8-3.5"/>
                            <path d="M10 9.5v5"/>
                            <path d="M6 14h4l1.5 4H9.5A2.5 2.5 0 0 1 7 15.5V14Z"/>
                        </svg>
                    </span>

                    <span class="text-[12px] font-semibold uppercase tracking-[0.28em] text-[#5f7dbc]">
                        {{ $eyebrow }}
                    </span>
                </div>

                <h2 class="max-w-[18ch] md:max-w-[20ch] xl:max-w-[24ch] font-serif text-[2.1rem] leading-[1.05] text-[#18233b] sm:text-[2.6rem] lg:text-[3rem] xl:text-[3.2rem]">
                    {{ $headline }}
                </h2>

                <p class="mt-6 max-w-[820px] text-[1rem] leading-8 text-[#5b6475] sm:text-[1.08rem]">
                    {{ $copy }}
                </p>
            </div>
        </div>

        {{-- BOTTOM: cards underneath --}}
        <div class="mt-12 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach ($occasionCards as $card)
                <div class="group min-w-0 rounded-[20px] border border-[#e4e7ef] bg-white/85 px-5 py-4 shadow-[0_8px_24px_rgba(15,23,42,0.05)] transition duration-300 hover:-translate-y-0.5 hover:shadow-[0_14px_30px_rgba(15,23,42,0.08)]">
                    <div class="flex items-start gap-3">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[#eef3ff] text-[#214e9b] transition duration-300 group-hover:bg-[#214e9b] group-hover:text-white">
                            @switch($card['icon'])
                                @case('eye')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6-10-6-10-6Z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                    @break

                                @case('home')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 10.5 12 3l9 7.5"/>
                                        <path d="M5 9.5V21h14V9.5"/>
                                        <path d="M9 21v-6h6v6"/>
                                    </svg>
                                    @break

                                @case('calendar')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="5" width="18" height="16" rx="2"/>
                                        <path d="M16 3v4M8 3v4M3 10h18"/>
                                    </svg>
                                    @break

                                @case('tag')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20.59 13.41 12 22l-9-9V4h9l8.59 8.59a2 2 0 0 1 0 2.82Z"/>
                                        <circle cx="7.5" cy="8.5" r="1.5"/>
                                    </svg>
                                    @break

                                @case('refresh')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 12a9 9 0 1 1-2.64-6.36"/>
                                        <path d="M21 3v6h-6"/>
                                    </svg>
                                    @break

                                @case('building')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 21h18"/>
                                        <path d="M5 21V7l7-4 7 4v14"/>
                                        <path d="M9 10h.01M15 10h.01M9 14h.01M15 14h.01"/>
                                    </svg>
                                    @break
                            @endswitch
                        </div>

                        <div class="min-w-0">
                            <h3 class="text-[1rem] sm:text-[1.05rem] font-semibold leading-6 tracking-[-0.02em] text-[#1d2a44]">
                                {{ $card['title'] }}
                            </h3>
                            <p class="mt-1 text-[0.88rem] leading-6 text-[#687182]">
                                {{ $card['desc'] }}
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</div>