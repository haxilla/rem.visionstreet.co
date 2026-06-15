@include('member.layout.head')

<body class="min-h-screen bg-slate-100 text-slate-950">

@include('member.layout.nav')

@php

    $zipValue = $propInfo->xxZip ?: $propInfo->xZip;

    $featuredPhoto = $propInfo->thePhotos
        ? $propInfo->thePhotos->where('def', 1)->first()
        : null;

    if (!$featuredPhoto && $propInfo->thePhotos->count()) {
        $featuredPhoto = $propInfo->thePhotos->first();
    }

    $featuredPhotoUrl = null;

    if ($featuredPhoto && $propInfo->theMeta) {
        $featuredPhotoUrl =
            'https://realtyrepublic.com/hqphotos/'
            . $propInfo->theMeta->zipDir . '/'
            . $propInfo->theMeta->mlsDir . '/'
            . $featuredPhoto->photoName;
    }

@endphp

<main class="mx-auto max-w-6xl px-4 pt-12 pb-10">

    {{-- Sticky Action Bar --}}
    <div class="sticky top-[72px] z-40 -mx-4 mb-8 border-b border-slate-300 bg-slate-100/95 px-4 py-3 backdrop-blur">

        <div class="mx-auto flex max-w-6xl items-center justify-between gap-3">

            <a href="/member/flyerEdit/{{ $propInfo->id }}"
               class="inline-flex items-center justify-center rounded-md border border-slate-300 bg-white px-4 py-2.5 text-sm font-extrabold text-slate-700 shadow-sm hover:bg-slate-50">

                <span class="mr-2 text-lg leading-none">←</span>
                Back to Flyer

            </a>

            <button type="button"
                    class="inline-flex items-center justify-center rounded-md bg-emerald-700 px-5 py-2.5 text-sm font-extrabold text-white shadow-sm hover:bg-emerald-800">

                Save Photos
                <span class="ml-2 text-lg leading-none">→</span>

            </button>

        </div>

    </div>

    {{-- Header --}}
    <div class="mb-6 rounded-xl border border-slate-300 bg-white p-4 shadow-sm">

        <div class="flex items-start gap-4">

            @if($featuredPhotoUrl)
                <div class="shrink-0">
                    <img src="{{ $featuredPhotoUrl }}"
                         alt="Property Photo"
                         class="h-20 w-28 rounded-md border border-slate-300 bg-white object-cover shadow-sm sm:h-24 sm:w-36">
                </div>
            @endif

            <div class="min-w-0 flex-1">

                <div class="mt-1 text-xs font-bold text-slate-500 sm:text-sm">
                    MLS #{{ $propInfo->xMlsNum }}
                </div>

                <h1 class="mt-1 text-xl font-extrabold leading-snug text-slate-950 sm:text-3xl">
                    {{ $propInfo->xFullStreet }}
                </h1>

                <div class="mt-1 text-sm text-slate-600 sm:text-base">
                    {{ $propInfo->xCity }}, {{ $propInfo->xState }} {{ $zipValue }}
                </div>

            </div>

        </div>

    </div>

    {{-- Featured Photo --}}
    <section class="mb-6 overflow-hidden rounded-xl border border-slate-300 bg-white shadow-sm">

        <div class="bg-gradient-to-r from-[#1b2f63] to-[#2a4486] px-6 py-5">

            <h2 class="text-xl font-extrabold text-white">
                Featured Photo
            </h2>

            <p class="mt-1 text-sm text-blue-100">
                This is the primary photo used throughout the flyer.
            </p>

        </div>

        <div class="p-6">

            @if($featuredPhotoUrl)

                <img src="{{ $featuredPhotoUrl }}"
                     alt="Featured Photo"
                     class="w-full rounded-lg border border-slate-300 shadow-sm">

            @else

                <div class="rounded-lg border border-dashed border-slate-300 p-10 text-center text-slate-500">
                    No featured photo available.
                </div>

            @endif

            <div class="mt-4 rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm font-medium text-blue-900">
                Click any photo below to make it the featured photo.
            </div>

        </div>

    </section>

    {{-- Photo Gallery --}}
    <section class="overflow-hidden rounded-xl border border-slate-300 bg-white shadow-sm">

        <div class="border-b border-slate-200 bg-slate-50 px-6 py-5">

            <h2 class="text-xl font-extrabold text-slate-950">
                Property Photos
            </h2>

            <p class="mt-1 text-sm text-slate-600">
                Select a photo below to use as the featured image.
            </p>

        </div>

        <div class="p-6">

            <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4">

                @foreach($propInfo->thePhotos as $photo)

                    @continue($featuredPhoto && $photo->photoID == $featuredPhoto->photoID)

                    @php

                        $photoUrl =
                            'https://realtyrepublic.com/hqphotos/'
                            . $propInfo->theMeta->zipDir . '/'
                            . $propInfo->theMeta->mlsDir . '/'
                            . $photo->photoName;

                    @endphp

                    <div class="group cursor-pointer overflow-hidden rounded-lg border border-slate-300 bg-white shadow-sm transition hover:border-blue-400 hover:shadow-md">

                        <img src="{{ $photoUrl }}"
                             alt="Property Photo"
                             class="aspect-[4/3] w-full object-cover">

                    </div>

                @endforeach

            </div>

        </div>

    </section>

</main>

</body>
</html>