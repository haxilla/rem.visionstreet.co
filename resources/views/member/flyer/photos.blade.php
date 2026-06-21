@include('member.layout.head')

<body data-section="member" class="relative min-h-screen bg-[#f0f2f7] font-sans text-slate-800">

@include('member.layout.nav')

@php
$flyer = $data['flyer'] ?? null;
@endphp

<main class="min-h-screen bg-[#f0f2f7] pt-24">

<div class="mx-auto flex w-full max-w-[1400px] gap-8 px-4 pb-16 sm:px-6 lg:px-8">

    {{-- SIDEBAR --}}

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

                <a
                    href="/member/flyer/create?flyerId={{ $flyer->id }}"
                    class="rounded-xl bg-slate-100 px-4 py-2 text-sm font-bold text-slate-700">
                    Edit
                </a>

            </div>

        </div>

        <form action="/member/flyer/savePhotos" method="post">

            @csrf

            <input
                type="hidden"
                name="flyerId"
                value="{{ $flyer->id }}"
            >

            {{-- PHOTO SECTION --}}
            <div class="rounded-3xl bg-white p-8 shadow-sm ring-1 ring-black/5">

                <h2 class="text-2xl font-black text-slate-900">
                    Property Photos
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Photos will appear here once photo uploads are enabled.
                </p>

                <div class="mt-8 rounded-2xl border-2 border-dashed border-slate-300 p-12">

                    <div class="text-center">

                        <div class="text-lg font-bold text-slate-700">
                            No Photos Added Yet
                        </div>

                        <div class="mt-2 text-sm text-slate-500">
                            Photo uploads coming soon.
                        </div>

                    </div>

                </div>

            </div>

            {{-- ACTIONS --}}
            <div class="mt-8 flex items-center justify-between">

                <a
                    href="/member/flyer/details?flyerId={{ $flyer->id }}"
                    class="rounded-xl bg-white px-5 py-3 font-bold text-slate-700 shadow-sm ring-1 ring-black/5">
                    ← Back
                </a>

                <button
                    type="submit"
                    class="rounded-xl bg-[#123f91] px-6 py-3 font-bold text-white hover:bg-[#0f3274]">
                    Save & Continue →
                </button>

            </div>

        </form>

    </section>

</div>

</main>

@include('public.layout.footer')

</body>
</html>