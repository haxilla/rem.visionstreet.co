{{-- FEATURES SECTION (Themify Icons) --}}
@php
// Keep typography consistent with hero: use the SAME display font for headline
$kicker = $kicker ?? 'Looking to do <span class="font-semibold text-[#214e9b]">MORE</span> than just MLS &amp; wait?';
$title  = $title  ?? 'Get Your Listing Noticed!';
$sub    = $sub    ?? 'Each flyer includes the following...';

$items = $items ?? [
    [
    'icon'  => 'ti-email',
    'title' => 'EMAIL',
    'text'  => 'THOUSANDS Of Interested Local RE Agents.<br>(AZ &amp; NV)',
    ],
    [
    'icon'  => 'ti-comments',
    'title' => 'SHARE',
    'text'  => 'Easily Post to Social Media Sites (Facebook,<br>Twitter, etc.)',
    ],
    [
    'icon'  => 'ti-printer',
    'title' => 'PRINT',
    'text'  => 'Print Color Brochures with a Custom Link to<br>view your Online Flyer!',
    ],
    [
    'icon'  => 'ti-world',
    'title' => 'ONLINE',
    'text'  => 'Each Flyer Includes custom landing page with<br><span class="italic">YOUR</span> contact info',
    ],
    [
    'icon'  => 'ti-stats-up',
    'title' => 'TRACK',
    'text'  => 'Monitor the progress of your campaigns and<br>see how many times your flyer is viewed',
    ],
    [
    'icon'  => 'ti-bolt',
    'title' => 'AUTO IMPORT',
    'text'  => 'If its on the MLS or anywhere online, just enter<br>address or MLS# for instant creation',
    ],
];

// Slightly stronger soft-violet presence (still not neon/hot pink)
// Blue -> periwinkle -> soft violet
$iconGradient = $iconGradient ?? 'bg-gradient-to-b from-[#2e56a3] via-[#4b79d6] to-[#8d7ae6]';

$brandBlue    = $brandBlue ?? '#214e9b';
$dividerColor = $dividerColor ?? 'bg-[#e6ebf6]';
@endphp

<div class="mx-auto w-full max-w-screen-2xl px-4 sm:px-6 lg:px-10 py-14 lg:py-16" style="max-width:1600px;">

    {{-- Header --}}
    <div class="text-center">
        <div class="text-[15px] sm:text-[16px] text-gray-800">
            {!! $kicker !!}
        </div>

        {{-- MATCH hero headline font: font-display --}}
        <h2 class="font-display mt-3 text-[34px] sm:text-[46px] font-medium tracking-tight leading-[1.05]" style="color: {{ $brandBlue }};">
            {{ $title }}
        </h2>

        <div class="mt-2 text-[14px] sm:text-[15px] text-gray-800">
            {{ $sub }}
        </div>

        {{-- squiggle --}}
        <div class="mt-5 flex justify-center">
            <span class="text-[22px] leading-none tracking-[0.22em] text-gray-900">~~~</span>
        </div>
    </div>

    {{-- Grid --}}
    <div class="mt-12 grid grid-cols-1 md:grid-cols-3">

    @foreach($items as $i => $it)
        @php
        $col = $i % 3;                  // 0,1,2
        $isSecondRow = $i >= 3;         // bottom row
        $needsLeftDivider = $col !== 0; // add vertical line on cols 2 & 3
        @endphp

        <div class="relative px-8 py-12 text-center">

        {{-- vertical dividers (desktop) --}}
        @if($needsLeftDivider)
            <div class="hidden md:block absolute left-0 top-8 bottom-8 w-px {{ $dividerColor }}"></div>
        @endif

        {{-- horizontal divider between the two rows --}}
        @if($isSecondRow)
            <div class="absolute left-8 right-8 top-0 h-px {{ $dividerColor }}"></div>
        @endif

        {{-- Icon (stronger soft violet presence, still clean) --}}
        <div class="mx-auto flex h-16 w-16 items-center justify-center">
            <i class="{{ $it['icon'] }} text-[56px] text-transparent bg-clip-text {{ $iconGradient }}"></i>
        </div>

        {{-- Title --}}
        <div class="mt-5 text-[18px] font-extrabold tracking-wide" style="color: {{ $brandBlue }};">
            {{ $it['title'] }}
        </div>

        {{-- Text --}}
        <div class="mt-3 text-[14px] leading-relaxed text-gray-900">
            {!! $it['text'] !!}
        </div>

        </div>
    @endforeach

    </div>

    {{-- Footer line --}}
    <div class="mt-14 text-center">
    <div class="text-[20px] font-extrabold tracking-wide" style="color: {{ $brandBlue }};">
        PROVIDING AN EASY WAY
    </div>
    <div class="mt-1 text-[14px]" style="color: {{ $brandBlue }};">
        to help you go beyond the average agent
    </div>
    </div>

</div>