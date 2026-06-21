@include('member.layout.head')

<body data-section="member" class="relative min-h-screen bg-[#f0f2f7] font-sans text-slate-800">

@include('member.layout.nav')

<main class="min-h-screen bg-[#f0f2f7] pt-24">

```
<div class="mx-auto w-full max-w-4xl px-4 pb-16 sm:px-6 lg:px-8">

    {{-- HEADER --}}
    <div class="mb-8">

        <div class="text-sm font-bold uppercase tracking-wider text-[#123f91]">
            Step 2 of 5
        </div>

        <h1 class="mt-2 text-4xl font-black text-slate-900">
            Property Details
        </h1>

        <p class="mt-2 text-slate-500">
            Tell us about the property.
        </p>

    </div>

    {{-- PROGRESS --}}
    <div class="mb-8 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">

        <div class="flex flex-wrap items-center gap-3 text-sm font-bold">

            <span class="text-emerald-600">
                ✓ Property
            </span>

            <span class="text-slate-300">→</span>

            <span class="text-[#123f91]">
                ● Details
            </span>

            <span class="text-slate-300">→</span>

            <span class="text-slate-400">
                Photos
            </span>

            <span class="text-slate-300">→</span>

            <span class="text-slate-400">
                Text
            </span>

            <span class="text-slate-300">→</span>

            <span class="text-slate-400">
                Design
            </span>

        </div>

    </div>

    <form method="post" action="/member/flyer/saveDetails">

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
                        class="w-full rounded-2xl border border-slate-300 px-4 py-3"
                        placeholder="549900"
                    >

                </div>

                <div>

                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Year Built
                    </label>

                    <input
                        type="text"
                        name="xYrBuilt"
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
                        <option value="Private Pool">Private Pool</option>
                        <option value="Community Pool">Community Pool</option>
                        <option value="No Pool">No Pool</option>

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
                        <option value="Slab Parking">Slab Parking</option>
                        <option value="Carport">Carport</option>
                        <option value="1 Car Garage">1 Car Garage</option>
                        <option value="2 Car Garage">2 Car Garage</option>
                        <option value="3 Car Garage">3 Car Garage</option>
                        <option value="4+ Car Garage">4+ Car Garage</option>

                    </select>

                </div>

            </div>

        </div>

        <div class="mt-8 flex items-center justify-between">

            <button
                type="submit"
                class="rounded-xl bg-[#123f91] px-6 py-3 font-bold text-white hover:bg-[#0f3274]">
                Save & Continue →
            </button>

        </div>

    </form>

</div>
```

</main>

@include('public.layout.footer')

</body>
</html>
