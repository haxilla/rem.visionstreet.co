@include('public.layout.head')

@php
    $paginator   = $data['searchAll'];
    $pageItems   = collect($paginator->items());
    $featured    = $pageItems->first();
    $listings    = $pageItems->slice(1);
    $searchValue = request('q', '');
@endphp

<body data-section="admin" class="linkcheck relative min-h-screen bg-[#eef2f7] font-sans text-slate-800 postgres">

@include('public.layout.nav')

<main class="transition-all duration-300 min-h-screen pt-24 relative" :class="collapsed ? 'ml-20' : 'ml-64'">

<div class="mx-5 lg:mx-10">
<div class="mx-auto max-w-[1380px]">

{{-- HERO --}}
<section class="relative overflow-hidden rounded-[34px] bg-gradient-to-br from-[#1b3875] via-[#234893] to-[#3a66c7] shadow-[0_30px_80px_rgba(25,48,109,.22)]">

<div class="absolute inset-0
bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,.16),transparent_30%),
radial-gradient(circle_at_bottom_left,rgba(255,255,255,.10),transparent_24%)]"></div>

<div class="relative grid grid-cols-1 xl:grid-cols-[1.05fr_.95fr] gap-0">

{{-- LEFT HERO --}}
<div class="px-7 py-8 sm:px-10 sm:py-10 lg:px-12 lg:py-12 xl:py-14 text-white">

<div class="inline-flex items-center rounded-full border border-white/20 bg-white/10 px-4 py-2 text-[11px] font-semibold uppercase tracking-[0.26em] backdrop-blur-sm">
Slide Show Gallery
</div>

<h1 class="mt-6 font-serif text-[38px] leading-[0.95] sm:text-[50px] lg:text-[62px]">
Discover Featured<br>
Homes In Style
</h1>

<p class="mt-6 max-w-[660px] text-[16px] leading-8 text-white/84">
Browse recent Realty Emails listings in a more visual, high-impact gallery experience.
</p>


{{-- SEARCH --}}
<div class="mt-8 max-w-[760px]">

<form method="GET" action="" class="relative">

<div class="relative overflow-hidden rounded-[22px]
border border-white/20
bg-white/96
shadow-[0_18px_44px_rgba(0,0,0,.16)]">

<div class="flex flex-col md:flex-row md:items-center">

<div class="flex min-w-0 flex-1 items-center px-5 py-4">

<input
type="text"
name="q"
value="{{ $searchValue }}"
placeholder="Search address, city, zip, agent..."
class="w-full border-0 bg-transparent p-0 text-[15px] text-slate-800 focus:outline-none"
/>

</div>

<div class="px-4 pb-4 md:px-4 md:pb-0">

<button
type="submit"
class="inline-flex w-full items-center justify-center
rounded-full
bg-[#244a98]
px-6 py-3
text-[14px]
font-semibold
text-white
shadow-[0_12px_26px_rgba(36,74,152,.28)]
hover:bg-[#1b3f88]"
>
Search
</button>

</div>

</div>
</div>
</form>

</div>

</div>