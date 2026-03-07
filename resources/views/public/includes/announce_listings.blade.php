{{-- COMPACT ANNOUNCE YOUR LISTINGS SECTION --}}
@php
    $announceTitle = $announceTitle ?? 'Announce Your Listings';
    $announceText  = $announceText ?? 'Use Realty Emails for the moments that matter most — from pre-MLS promotion and just-listed announcements to open houses, reductions, updates, and builder inventory.';
    $brandBlue     = $brandBlue ?? '#214e9b';
    $brandGold     = $brandGold ?? '#e79a63';

    $announceItems = $announceItems ?? [
        ['icon' => 'ti-eye', 'title' => 'Pre-MLS'],
        ['icon' => 'ti-announcement', 'title' => 'Just Listed'],
        ['icon' => 'ti-home', 'title' => 'Open House'],
        ['icon' => 'ti-stats-down', 'title' => 'Reduced'],
        ['icon' => 'ti-reload', 'title' => 'Updated'],
        ['icon' => 'ti-hummer', 'title' => 'Builders'],
    ];
@endphp

<div> class="w-full py-12 lg:py-14 bg-[#eef3fb]">
    <div class="mx-auto w-full max-w-screen-2xl px-4 sm:px-6 lg:px-10" style="max-width:1600px;">

        <div class="rounded-[28px] bg-white/72 px-6 py-8 sm:px-8 sm:py-9 shadow-[0_10px_28px_rgba(0,0,0,.04)] ring-1 ring-black/5 backdrop-blur-sm">

            {{-- Top row --}}
            <div class="grid grid-cols-1 lg:grid-cols-[auto_1fr] gap-8 lg:gap-10 items-center">

                {{-- Flyer image --}}
                <div class="flex justify-center lg:justify-start">
                    <img
                        src="{{ asset('images/2FlyerPerspective.gif') }}"
                        alt="Realty Emails flyer examples"
                        class="block h-auto w-auto max-w-full object-contain drop-shadow-[0_16px_24px_rgba(0,0,0,.10)]"
                    >
                </div>

                {{-- Copy --}}
                <div class="max-w-[760px]">
                    <div class="inline-flex items-center gap-3">
                        <span class="flex h-11 w-11 items-center justify-center rounded-full bg-[#214e9b]/8 ring-1 ring-[#214e9b]/10">
                            <i class="ti-announcement text-[18px]" style="color: {{ $brandBlue }};"></i>
                        </span>

                        <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-[#214e9b]/70">
                            Flyer Occasions
                        </span>
                    </div>

                    <h2 class="font-display mt-4 text-[32px] sm:text-[42px] leading-[1.02] text-[#1c1d22]">
                        {{ $announceTitle }}
                    </h2>

                    <p class="mt-4 max-w-[720px] text-[16px] sm:text-[18px] leading-8 text-gray-600">
                        {{ $announceText }}
                    </p>
                </div>
            </div>

            {{-- Compact icon row --}}
            <div class="mt-8 grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-5 sm:gap-6">
                @foreach($announceItems as $item)
                    <div class="flex items-center gap-3 rounded-[18px] bg-white px-4 py-4 shadow-[0_6px_16px_rgba(0,0,0,.04)] ring-1 ring-black/5">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#214e9b]/8 ring-1 ring-[#214e9b]/10">
                            <i class="{{ $item['icon'] }} text-[16px]" style="color: {{ $brandBlue }};"></i>
                        </span>

                        <span class="text-[15px] font-medium leading-tight text-[#214e9b]">
                            {{ $item['title'] }}
                        </span>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
</div>