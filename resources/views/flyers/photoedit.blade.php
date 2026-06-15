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

        </div>

    </div>

    {{-- Upload Photos --}}
    <section class="mb-6 overflow-hidden rounded-xl border border-slate-300 bg-white shadow-sm">

        <div class="border-b border-slate-200 bg-slate-50 px-6 py-5">

            <h2 class="text-xl font-extrabold text-slate-950">
                Add Photos
            </h2>

            <p class="mt-1 text-sm text-slate-600">
                Upload additional property photos. New photos will appear in the gallery below.
            </p>

        </div>

        <div class="p-6">

            <form method="POST"
                action=""
                enctype="multipart/form-data">

                @csrf

                <div class="rounded-lg border-2 border-dashed border-slate-300 bg-slate-50 p-10 text-center">

                    <input type="file"
                        id="photos"
                        name="photos[]"
                        multiple
                        accept="image/*"
                        class="hidden">

                    <label for="photos" class="block cursor-pointer">

                        <div class="text-4xl">
                            📷
                        </div>

                        <div class="mt-3 text-xl font-extrabold text-slate-800">
                            Add Photos
                        </div>

                        <div id="photoCount"
                            class="mt-2 text-sm text-slate-500">
                            Click here to browse for photos
                        </div>

                    </label>

                </div>

                <div id="selectedPhotos"
                    class="mt-6 hidden">
                </div>

                <div class="mt-6">
                    <button type="submit"
                            class="inline-flex items-center rounded-md bg-emerald-700 px-5 py-2.5 text-sm font-extrabold text-white shadow-sm hover:bg-emerald-800">

                        Upload Photos

                    </button>
                </div>

            </form>

        </div>

    </section>

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
                This is used as the main image for the flyer
            </p>

        </div>

        <div class="p-6">

            @if($featuredPhotoUrl)

                <div class="overflow-auto text-center">
                    <img src="{{ $featuredPhotoUrl }}"
                        alt="Featured Photo"
                        class="inline-block rounded-lg border border-slate-300 shadow-sm">
                </div>

            @else

                <div class="rounded-lg border border-dashed border-slate-300 p-10 text-center text-slate-500">
                    No featured photo available.
                </div>

            @endif

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
    <script>

document.addEventListener('DOMContentLoaded', function () {

    const input = document.getElementById('photos');

    if (!input) {
        return;
    }

    input.addEventListener('change', function () {

        const container = document.getElementById('selectedPhotos');
        const photoCount = document.getElementById('photoCount');

        container.innerHTML = '';

        if (!this.files.length) {

            photoCount.innerHTML = 'Click here to browse for photos';

            container.classList.add('hidden');

            return;
        }

        photoCount.innerHTML =
            this.files.length +
            (this.files.length === 1
                ? ' photo selected'
                : ' photos selected');

        const heading = document.createElement('div');

        heading.className =
            'mb-4 text-sm font-extrabold text-slate-700';

        heading.innerHTML = 'Photos Ready To Upload';

        const grid = document.createElement('div');

        grid.className =
            'grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4';

        container.appendChild(heading);
        container.appendChild(grid);

        Array.from(this.files).forEach(file => {

            const reader = new FileReader();

            reader.onload = function(e) {

                const card = document.createElement('div');

                card.className =
                    'overflow-hidden rounded-lg border border-slate-300 bg-white shadow-sm';

                card.innerHTML = `
                    <img
                        src="${e.target.result}"
                        class="aspect-[4/3] w-full object-cover"
                    >

                    <div class="border-t border-slate-200 p-2 text-xs text-slate-600 break-all">
                        ${file.name}
                    </div>
                `;

                grid.appendChild(card);

            };

            reader.readAsDataURL(file);

        });

        container.classList.remove('hidden');

    });

});

</script>
</body>
</html>