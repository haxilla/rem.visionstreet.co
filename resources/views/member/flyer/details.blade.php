@include('member.layout.head')

<body data-section="member" class="relative min-h-screen bg-[#f0f2f7] font-sans text-slate-800">

@include('member.layout.nav')

<main class="min-h-screen bg-[#f0f2f7] pt-24">

<div class="mx-auto w-full max-w-4xl px-4 pb-16 sm:px-6 lg:px-8">

    {{-- HEADER --}}
    <div class="mb-8">

        <div class="text-sm font-bold uppercase tracking-wider text-[#123f91]">
            Step 2 of 5
        </div>

        <h1 class="mt-2 text-4xl font-black text-slate-900">
            Property Details
        </h1>

        <div class="mt-4 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-black/5">

            <div class="flex items-start justify-between gap-4">

                <div>

                    <div class="text-xl font-black text-slate-900">
                        {{ $data['flyer']->xFullStreet }}
                    </div>

                    <div class="text-slate-600">
                        {{ $data['flyer']->xCity }},
                        {{ $data['flyer']->xState }}
                        {{ $data['flyer']->xZip }}
                    </div>

                    @if($data['flyer']->xMlsNum)

                        <div class="mt-2 text-sm font-semibold text-slate-500">
                            MLS #{{ $data['flyer']->xMlsNum }}
                        </div>

                    @endif

                </div>

                <a
                    href="/member/flyer/create?flyerId={{ $data['flyer']->id }}"
                    class="rounded-xl bg-slate-100 px-4 py-2 text-sm font-bold text-slate-700 hover:bg-slate-200">
                    Edit
                </a>

            </div>

        </div>

    </div>

    {{-- PROGRESS --}}
    @include('member.flyer.wizard', [
        'flyer' => $data['flyer']
    ])

    @if ($errors->any())

        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4">

            <div class="mb-2 font-bold text-red-700">
                Please correct the following:
            </div>

            <ul class="list-disc pl-5 text-red-600">

                @foreach ($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif

    <form method="post" action="/member/flyer/save_details">

        @csrf

        <input
            type="hidden"
            name="flyerId"
            value="{{ $data['flyer']->id }}"
        >

        <div class="rounded-3xl bg-white p-8 shadow-sm ring-1 ring-black/5">

            <div class="grid gap-6 md:grid-cols-2">

                <div>

                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        List Price
                    </label>

                    <input
                        type="text"
                        name="xListPrice"
                        value="{{ old('xListPrice', $data['flyer']->xListPrice ?? '') }}"
                        class="w-full rounded-2xl border border-slate-300 px-4 py-3"
                    >

                </div>

                <div>

                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Year Built
                    </label>

                    <input
                        type="text"
                        name="xYrBuilt"
                        value="{{ old('xYrBuilt', $data['flyer']->xYrBuilt ?? '') }}"
                        class="w-full rounded-2xl border border-slate-300 px-4 py-3"
                    >

                </div>

                <div>

                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Bedrooms
                    </label>

                    <input
                        type="text"
                        name="xBeds"
                        value="{{ old('xBeds', $data['flyer']->xBeds ?? '') }}"
                        class="w-full rounded-2xl border border-slate-300 px-4 py-3"
                    >

                </div>

                <div>

                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Bathrooms
                    </label>

                    <input
                        type="text"
                        name="xBaths"
                        value="{{ old('xBaths', $data['flyer']->xBaths ?? '') }}"
                        class="w-full rounded-2xl border border-slate-300 px-4 py-3"
                    >

                </div>

                <div>

                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Square Feet
                    </label>

                    <input
                        type="text"
                        name="xSqft"
                        value="{{ old('xSqft', $data['flyer']->xSqft ?? '') }}"
                        class="w-full rounded-2xl border border-slate-300 px-4 py-3"
                    >

                </div>

                <div>

                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Pool
                    </label>

                    <select
                        name="xPool"
                        class="w-full rounded-2xl border border-slate-300 px-4 py-3">

                        <option value="">Select Pool Type</option>

                        <option value="Private Pool"
                            @selected(old('xPool', $data['flyer']->xPoolPvt ?? '') == 'Private Pool')>
                            Private Pool
                        </option>

                        <option value="Community Pool"
                            @selected(old('xPool', $data['flyer']->xPoolPvt ?? '') == 'Community Pool')>
                            Community Pool
                        </option>

                        <option value="No Pool"
                            @selected(old('xPool', $data['flyer']->xPoolPvt ?? '') == 'No Pool')>
                            No Pool
                        </option>

                    </select>

                </div>

                <div class="md:col-span-2">

                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Parking
                    </label>

                    <select
                        name="xParking"
                        class="w-full rounded-2xl border border-slate-300 px-4 py-3">

                        <option value="">Select Parking Type</option>

                        <option value="Slab Parking"
                            @selected(old('xParking', $data['flyer']->xParking ?? '') == 'Slab Parking')>
                            Slab Parking
                        </option>

                        <option value="Carport"
                            @selected(old('xParking', $data['flyer']->xParking ?? '') == 'Carport')>
                            Carport
                        </option>

                        <option value="1 Car Garage"
                            @selected(old('xParking', $data['flyer']->xParking ?? '') == '1 Car Garage')>
                            1 Car Garage
                        </option>

                        <option value="2 Car Garage"
                            @selected(old('xParking', $data['flyer']->xParking ?? '') == '2 Car Garage')>
                            2 Car Garage
                        </option>

                        <option value="3 Car Garage"
                            @selected(old('xParking', $data['flyer']->xParking ?? '') == '3 Car Garage')>
                            3 Car Garage
                        </option>

                        <option value="4+ Car Garage"
                            @selected(old('xParking', $data['flyer']->xParking ?? '') == '4+ Car Garage')>
                            4+ Car Garage
                        </option>

                    </select>

                </div>

            </div>

        </div>

        <div class="mt-8 flex items-center justify-between">

            <a
                href="/member/dashboard"
                class="rounded-xl bg-white px-5 py-3 font-bold text-slate-700 shadow-sm ring-1 ring-black/5">
                Cancel
            </a>

            <button
                type="submit"
                class="rounded-xl bg-[#123f91] px-6 py-3 font-bold text-white hover:bg-[#0f3274]">
                Save & Continue →
            </button>

        </div>

    </form>

</div>

</main>

@include('public.layout.footer')

</body>
</html>
