@php
    $headline = $headline ?? 'Professional Flyers for Every Listing Event';
    $eyebrow  = $eyebrow ?? 'FLYER OCCASIONS';
    $copy     = $copy ?? 'Use RealtyEmails for every stage of your listing — from pre-MLS marketing to open houses, price reductions, updated listings, and new construction phases.';

    $flyerImage = $flyerImage ?? asset('images/luxury-flyers-example.png');

    $occasionCards = $occasionCards ?? [
        [
            'title' => 'Pre-MLS',
            'desc'  => 'Generate interest before going live',
            'icon'  => 'eye',
        ],
        [
            'title' => 'Just Listed',
            'desc'  => 'Promote your newest listing launch',
            'icon'  => 'home',
        ],
        [
            'title' => 'Open House',
            'desc'  => 'Advertise upcoming showing events',
            'icon'  => 'calendar',
        ],
        [
            'title' => 'Reduced',
            'desc'  => 'Highlight new pricing to attract buyers',
            'icon'  => 'tag',
        ],
        [
            'title' => 'Updated',
            'desc'  => 'Share listing changes and fresh details',
            'icon'  => 'refresh',
        ],
        [
            'title' => 'Builders',
            'desc'  => 'Market new construction opportunities',
            'icon'  => 'building',
        ],
    ];
@endphp

<div class="w-full bg-[#f5f6fa] py-16 lg:py-24">
    <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">
        <div class="overflow-hidden rounded-[32px] border border-[#e6e8ee] bg-gradient-to-br from-[#eef3fb] via-white to-[#f8f8fb] shadow-[0_20px_60px_rgba(16,24,40,0.08)]">
            <div class="grid grid-cols-1 lg:grid-cols-[1.05fr_.95fr] items-center gap-8 lg:gap-0">

                {{-- LEFT: FLYER VISUAL --}}
                <div class="relative px-6 py-10 sm:px-10 lg:px-14 lg:py-16">
                    {{-- subtle background pattern --}}
                    <div class="absolute inset-0 opacity-[0.035] pointer-events-none"
                         style="background-image:
                            linear-gradient(to right, #214e9b 1px, transparent 1px),
                            linear-gradient(to bottom, #214e9b 1px, transparent 1px);
                            background-size: 44px 44px;">
                    </div>

                    <div class="relative mx-auto max-w-[640px]">
                        <img
                            src="{{ $flyerImage }}"
                            alt="Luxury real estate flyer examples"
                            class="block w-full h-auto select-none drop-shadow-[0_30px_55px_rgba(15,23,42,0.18)]"
                        >
                    </div>
                    <div class="text-center italic text-sm text-gray-500 mt-2">
                        ** Flyers shown are examples only **
                    </div>
                </div>

                {{-- RIGHT: CONTENT --}}
                <div class="px-6 py-10 sm:px-10 lg:px-12 lg:py-16">
                    <div class="max-w-[620px]">
                        <div class="mb-4 inline-flex items-center gap-3">
                            <span class="inline-flex h-11 w-11 items-center justify-center rounded-full bg-[#e8eefb] text-[#214e9b] shadow-inner">
                                {{-- megaphone / flyer icon --}}
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

                        <h2 class="max-w-[12ch] font-serif text-[2.2rem] leading-[1.05] text-[#18233b] sm:text-[2.8rem] lg:text-[3.35rem]">
                            {{ $headline }}
                        </h2>

                        <p class="mt-6 max-w-[560px] text-[1.05rem] leading-8 text-[#5b6475] sm:text-[1.12rem]">
                            {{ $copy }}
                        </p>

                        <div class="mt-10 grid grid-cols-1 gap-4 sm:grid-cols-2">
                            @foreach ($occasionCards as $card)
                                <div class="group rounded-[20px] border border-[#e4e7ef] bg-white/85 p-5 shadow-[0_8px_24px_rgba(15,23,42,0.05)] transition duration-300 hover:-translate-y-0.5 hover:shadow-[0_14px_30px_rgba(15,23,42,0.08)]">
                                    <div class="flex items-start gap-4">
                                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-[#eef3ff] text-[#214e9b] transition duration-300 group-hover:bg-[#214e9b] group-hover:text-white">
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

                                        <div>
                                            <h3 class="text-[1.35rem] font-semibold tracking-[-0.02em] text-[#1d2a44]">
                                                {{ $card['title'] }}
                                            </h3>
                                            <p class="mt-1 text-[0.98rem] leading-7 text-[#687182]">
                                                {{ $card['desc'] }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>