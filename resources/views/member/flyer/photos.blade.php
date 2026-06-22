@include('member.layout.head')

<body data-section="member" class="relative min-h-screen bg-[#f0f2f7] font-sans text-slate-800">

@include('member.layout.nav')

@php
$flyer = $data['flyer'] ?? null;
@endphp

<main class="min-h-screen bg-[#f0f2f7] pt-24">

<div class="mx-auto flex w-full max-w-[1400px] gap-8 px-4 pb-16 sm:px-6 lg:px-8">

    <section class="min-w-0 flex-1">

        {{-- HEADER --}}
        <div class="mb-8">

            <div class="text-sm font-bold uppercase tracking-wider text-[#123f91]">
                Step 3 of 5
            </div>

            <h1 class="mt-2 text-4xl font-black text-slate-900">
                Property Photos
            </h1>

            <p class="mt-2 text-slate-500">
                Add photos to your flyer.
            </p>

        </div>

        {{-- PROGRESS --}}
        <div class="mb-8 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">

            <div class="flex flex-wrap items-center gap-3 text-sm font-bold">

                <span class="text-emerald-600">✓ Property</span>
                <span class="text-slate-300">→</span>

                <span class="text-emerald-600">✓ Details</span>
                <span class="text-slate-300">→</span>

                <span class="text-[#123f91]">● Photos</span>
                <span class="text-slate-300">→</span>

                <span class="text-slate-400">Text</span>
                <span class="text-slate-300">→</span>

                <span class="text-slate-400">Design</span>

            </div>

        </div>

        {{-- PROPERTY SUMMARY --}}
        <div class="mb-8 rounded-3xl bg-white p-5 shadow-sm ring-1 ring-black/5">

            <div class="flex items-start justify-between">

                <div>

                    <div class="text-xl font-black text-slate-900">
                        {{ $flyer->xFullStreet }}
                    </div>

                    <div class="text-slate-600">
                        {{ $flyer->xCity }},
                        {{ $flyer->xState }}
                        {{ $flyer->xZip }}
                    </div>

                    @if($flyer->xMlsNum)
                        <div class="mt-1 text-sm text-slate-500">
                            MLS #{{ $flyer->xMlsNum }}
                        </div>
                    @endif

                </div>

            </div>

        </div>

        <form
            action="/member/flyer/savePhotos"
            method="post"
            enctype="multipart/form-data"
        >

            @csrf

            <input
                type="hidden"
                name="flyerId"
                value="{{ $flyer->id }}"
            >

            {{-- PHOTO SECTION --}}
            <div class="rounded-3xl bg-white p-8 shadow-sm ring-1 ring-black/5">

                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                    <div>

                        <h2 class="text-2xl font-black text-slate-900">
                            Property Photos
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            Select photos for your flyer.
                        </p>

                    </div>

                    <div>

                        <label
                            for="photos"
                            class="inline-flex cursor-pointer items-center rounded-xl bg-[#123f91] px-5 py-3 font-bold text-white hover:bg-[#0f3274]"
                        >
                            + Add Photos
                        </label>

                        <input
                            id="photos"
                            name="photos[]"
                            type="file"
                            multiple
                            accept="image/*"
                            class="hidden"
                        >

                    </div>

                </div>

                {{-- EMPTY STATE --}}
                <div
                    id="emptyState"
                    class="mt-8 rounded-2xl border border-slate-200 bg-slate-50 p-12 text-center"
                >

                    <div class="text-lg font-bold text-slate-700">
                        No Photos Selected
                    </div>

                    <div class="mt-2 text-sm text-slate-500">
                        Click "Add Photos" to begin building your flyer gallery.
                    </div>

                </div>

                {{-- PREVIEW GRID --}}
                <div
                    id="photoPreviewGrid"
                    class="mt-8 hidden grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5"
                >
                </div>

                {{-- FOOTER --}}
                <div
                    id="photoSummary"
                    class="mt-8 hidden items-center justify-between border-t border-slate-200 pt-6"
                >

                    <div
                        id="photoCount"
                        class="font-bold text-slate-700"
                    >
                    </div>

                    <button
                        type="submit"
                        class="rounded-xl bg-[#123f91] px-6 py-3 font-bold text-white hover:bg-[#0f3274]"
                    >
                        Upload Photos
                    </button>

                </div>

            </div>

            {{-- ACTIONS --}}
            <div class="mt-8 flex items-center justify-between">

                <a
                    href="/member/flyer/details?flyerId={{ $flyer->id }}"
                    class="rounded-xl bg-white px-5 py-3 font-bold text-slate-700 shadow-sm ring-1 ring-black/5"
                >
                    ← Back
                </a>

            </div>

        </form>

    </section>

</div>

</main>

@include('public.layout.footer')

<script>

document.addEventListener('DOMContentLoaded', () => {

    const input = document.getElementById('photos');
    const previewGrid = document.getElementById('photoPreviewGrid');
    const emptyState = document.getElementById('emptyState');
    const photoSummary = document.getElementById('photoSummary');
    const photoCount = document.getElementById('photoCount');

    input.addEventListener('change', function () {

        previewGrid.innerHTML = '';

        const files = Array.from(this.files);

        if (!files.length) {

            emptyState.classList.remove('hidden');
            previewGrid.classList.add('hidden');
            photoSummary.classList.add('hidden');

            return;
        }

        emptyState.classList.add('hidden');

        previewGrid.classList.remove('hidden');

        photoSummary.classList.remove('hidden');
        photoSummary.classList.add('flex');

        photoCount.textContent =
            files.length +
            (files.length === 1
                ? ' Photo Selected'
                : ' Photos Selected');

        files.forEach((file) => {

            if (!file.type.startsWith('image/')) {
                return;
            }

            const reader = new FileReader();

            reader.onload = function (e) {

                const card = document.createElement('div');

                card.className =
                    'overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm';

                card.innerHTML = `
                    <div class="aspect-square bg-slate-100">
                        <img
                            src="${e.target.result}"
                            class="h-full w-full object-cover"
                        >
                    </div>
                `;

                previewGrid.appendChild(card);

            };

            reader.readAsDataURL(file);

        });

    });

});

</script>

</body>
</html>