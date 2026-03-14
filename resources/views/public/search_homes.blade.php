@include('public.layout.head')

@php
$paginator = $data['searchAll'];

$pageItems = collect($paginator->items());

$featured = $pageItems->first();
$listings = $pageItems->slice(1);

$searchValue = request('q','');
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

<div class="relative grid grid-cols-1 xl:grid-cols-[1.05fr_.95fr]">

<div class="px-8 py-10 text-white">

<div class="inline-flex items-center rounded-full border border-white/20 bg-white/10 px-4 py-2 text-[11px] uppercase tracking-[0.25em] backdrop-blur-sm">
Slide Show Gallery
</div>

<h1 class="mt-6 font-serif text-[54px] leading-[0.95]">
Discover Homes<br>
In Style
</h1>

<p class="mt-5 max-w-[620px] text-[16px] leading-8 text-white/85">
Browse recent Realty Emails listings in a visual gallery experience.
</p>


<form method="GET" action="" class="mt-8 max-w-[700px]">

<div class="flex rounded-[22px] overflow-hidden bg-white shadow-lg">

<input
type="text"
name="q"
value="{{ $searchValue }}"
placeholder="Search address, city, zip, agent..."
class="flex-1 px-5 py-4 text-[15px] text-slate-800 outline-none"
/>

<button
type="submit"
class="bg-[#244a98] px-6 text-white font-semibold"
>
Search
</button>

</div>

</form>

</div>


@if($featured)

@php

$photoObj = $featured->thePhotos->where('def','=','1')->first();
$photo    = $photoObj?->photoName;

$listingImg=null;

if($photo && $featured->theMeta?->zipDir && $featured->theMeta?->mlsDir){
$listingImg="https://realtyrepublic.com/hqphotos/{$featured->theMeta->zipDir}/{$featured->theMeta->mlsDir}/{$photo}";
}

$listingURL="https://realtyrepublic.com/homedetails/{$featured->url_slug}";

$agentImg=null;

if(!empty($featured->theAgent?->agtPhoto) && !empty($featured->theAgent?->theAgentCleanup?->newRemID)){
$agentImg="https://realtyrepublic.com/agentPhotos/{$featured->theAgent->theAgentCleanup->newRemID}/{$featured->theAgent->agtPhoto}";
}

$street=$featured->xFullStreet;
$cityLine=trim(($featured->xCity).' '.($featured->xState).' '.($featured->xxZip));

$price=$featured->xPrice ?? $featured->xListPrice;
$priceLabel=$price ? '$'.number_format($price) : null;

@endphp

<div class="p-8">

<a href="{{ $listingURL }}" target="_blank">

<div class="rounded-[24px] overflow-hidden bg-white shadow-xl">

@if($listingImg)
<img src="{{ $listingImg }}" class="w-full h-[320px] object-cover">
@endif

<div class="p-6">

<div class="text-[26px] font-semibold text-[#214e9b]">
{{ $street }}
</div>

<div class="text-gray-600">
{{ $cityLine }}
</div>

@if($priceLabel)
<div class="mt-2 text-[20px] font-semibold">
{{ $priceLabel }}
</div>
@endif

<div class="mt-4 flex gap-3">

@if($agentImg)
<img src="{{ $agentImg }}" class="h-16 w-auto rounded">
@endif

<div>

<div class="font-medium">
{{ $featured->theAgent->agtFullName }}
</div>

<div class="text-sm text-gray-600">
{{ $featured->theOffice->officeName ?? '' }}
</div>

</div>

</div>

</div>

</div>

</a>

</div>

@endif

</div>
</section>



{{-- CONTENT --}}
<div class="mt-10 grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-10">


{{-- LIST --}}
<div class="space-y-6">

@foreach($listings as $the)

@php

$photoObj = $the->thePhotos->where('def','=','1')->first();
$photo    = $photoObj?->photoName;

$listingImg=null;

if($photo && $the->theMeta?->zipDir && $the->theMeta?->mlsDir){
$listingImg="https://realtyrepublic.com/hqphotos/{$the->theMeta->zipDir}/{$the->theMeta->mlsDir}/{$photo}";
}

$listingURL="https://realtyrepublic.com/homedetails/{$the->url_slug}";

$agentImg=null;

if(!empty($the->theAgent?->agtPhoto) && !empty($the->theAgent?->theAgentCleanup?->newRemID)){
$agentImg="https://realtyrepublic.com/agentPhotos/{$the->theAgent->theAgentCleanup->newRemID}/{$the->theAgent->agtPhoto}";
}

$street=$the->xFullStreet;
$cityLine=trim(($the->xCity).' '.($the->xState).' '.($the->xxZip));

$price=$the->xPrice ?? $the->xListPrice;
$priceLabel=$price ? '$'.number_format($price) : null;

@endphp


<a href="{{ $listingURL }}" target="_blank">

<div class="flex gap-6 bg-white rounded-[18px] shadow p-4">

@if($listingImg)
<img src="{{ $listingImg }}" class="w-[180px] h-[120px] object-cover rounded">
@endif

<div class="flex-1">

<div class="text-[18px] font-semibold text-[#214e9b]">
{{ $street }}
</div>

<div class="text-gray-600">
{{ $cityLine }}
</div>

@if($priceLabel)
<div class="mt-1 font-semibold">
{{ $priceLabel }}
</div>
@endif

<div class="mt-3 flex gap-3">

@if($agentImg)
<img src="{{ $agentImg }}" class="h-16 w-auto rounded">
@endif

<div>

<div>
{{ $the->theAgent->agtFullName }}
</div>

<div class="text-sm text-gray-600">
{{ $the->theOffice->officeName ?? '' }}
</div>

</div>

</div>

</div>

</div>

</a>

@endforeach


<div class="mt-6">
{{ $paginator->withQueryString()->links() }}
</div>


</div>



{{-- SIDEBAR --}}
<div class="space-y-6">

<div class="bg-white rounded-[20px] shadow p-6">

<div class="text-xl font-semibold">
Free Trial
</div>

<p class="text-sm mt-2 text-gray-600">
Create a flyer free. No credit card required.
</p>

<input class="mt-4 w-full border p-2 rounded" placeholder="Email">

<button class="mt-3 w-full bg-[#214e9b] text-white py-2 rounded">
Start Free
</button>

</div>


<div class="bg-white rounded-[20px] shadow p-6">

<div class="font-semibold mb-3">
Quick Search
</div>

<form>

<input class="w-full border p-2 rounded" placeholder="City / Zip">

<button class="mt-2 w-full bg-[#214e9b] text-white py-2 rounded">
Search
</button>

</form>

</div>


</div>



</div>


</div>
</div>

</main>

@include('public.layout.footer')