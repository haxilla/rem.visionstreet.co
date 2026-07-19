@include('member.layout.head')

<body data-section="member" class="bg-slate-100">

@include('member.layout.nav')

<main class="pt-24 pb-16">

<div class="mx-auto max-w-6xl px-6">

    {{-- ========================================================= --}}
    {{-- PAGE HEADER --}}
    {{-- ========================================================= --}}

    <div class="mb-10">

        <div class="rounded-3xl bg-white p-8 shadow-sm">

            @if($flyer->xMlsNum)

                <div class="text-sm font-bold uppercase tracking-[.25em] text-blue-700">

                    MLS #{{ $flyer->xMlsNum }}

                </div>

            @endif

            <h1 class="mt-2 text-4xl font-black text-slate-900">

                {{ $flyer->xFullStreet }}

            </h1>

            <div class="mt-1 text-lg text-slate-500">

                {{ $flyer->xCity }},
                {{ $flyer->xState }}
                {{ $flyer->xZip }}

            </div>

            <div class="mt-6">

                <a
                    href="/member/flyer/create?flyerId={{ $flyer->id }}"
                    class="inline-flex items-center rounded-xl bg-slate-100 px-5 py-3 font-semibold text-slate-700 hover:bg-slate-200">

                    ← Edit Address

                </a>

            </div>

        </div>

    </div>

    @include('member.flyer.wizard',['flyer'=>$flyer])

    @if($errors->any())

        <div class="mt-8 rounded-2xl border border-red-200 bg-red-50 p-5">

            <div class="font-bold text-red-700">

                Please correct the following:

            </div>

            <ul class="mt-3 list-disc pl-5 text-red-600">

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif

    <form
        method="POST"
        action="/member/flyer/save_details"
        class="mt-8">

        @csrf

        <input
            type="hidden"
            name="flyerId"
            value="{{ $flyer->id }}">


        <div class="rounded-3xl bg-white shadow-sm overflow-hidden">

            <div class="p-10">

                <h2 class="text-2xl font-black text-slate-900">

                    Property Information

                </h2>

                <div class="mt-8 grid gap-x-8 gap-y-6 lg:grid-cols-4">

                    <div>

                        <label class="block text-sm font-semibold text-slate-600 mb-2">

                            List Price

                        </label>

                        <input
                            type="text"
                            name="xPrice"
                            value="{{ old('xPrice',$flyer->xPrice ?? '') }}"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3">

                    </div>

                    <div>

                        <label class="block text-sm font-semibold text-slate-600 mb-2">

                            Property Type

                        </label>

                        <input
                            type="text"
                            name="xPropertyType"
                            value="{{ old('xPropertyType',$flyer->xPropertyType ?? '') }}"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3">

                    </div>

                    <div>

                        <label class="block text-sm font-semibold text-slate-600 mb-2">

                            Bedrooms

                        </label>

                        <input
                            type="text"
                            name="xBeds"
                            value="{{ old('xBeds',$flyer->xBeds ?? '') }}"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3">

                    </div>

                    <div>

                        <label class="block text-sm font-semibold text-slate-600 mb-2">

                            Bathrooms

                        </label>

                        <input
                            type="text"
                            name="xBaths"
                            value="{{ old('xBaths',$flyer->xBaths ?? '') }}"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3">

                    </div>

                    <div>

                        <label class="block text-sm font-semibold text-slate-600 mb-2">

                            Square Feet

                        </label>

                        <input
                            type="text"
                            name="xSqFt"
                            value="{{ old('xSqFt',$flyer->xSqFt ?? '') }}"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3">

                    </div>

                    <div>

                        <label class="block text-sm font-semibold text-slate-600 mb-2">

                            Year Built

                        </label>

                        <input
                            type="text"
                            name="xYearBuilt"
                            value="{{ old('xYearBuilt',$flyer->xYearBuilt ?? '') }}"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3">

                    </div>

                    <div>

                        <label class="block text-sm font-semibold text-slate-600 mb-2">

                            Parking

                        </label>

                        <select
                            name="xParking"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3">

                            <option value="">Select</option>
                            <option>1 Car Garage</option>
                            <option>2 Car Garage</option>
                            <option>3 Car Garage</option>
                            <option>4 Car Garage</option>
                            <option>RV Parking</option>

                        </select>

                    </div>

                    <div>

                        <label class="block text-sm font-semibold text-slate-600 mb-2">

                            Pool

                        </label>

                        <select
                            name="xPool"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3">

                            <option value="">Select</option>
                            <option>Private Pool</option>
                            <option>Community Pool</option>
                            <option>No Pool</option>

                        </select>

                    </div>

                </div>

                <div class="mt-6">

                    <label class="block text-sm font-semibold text-slate-600 mb-2">

                        Cross Streets

                    </label>

                    <input
                        type="text"
                        name="xCrossStreets"
                        value="{{ old('xCrossStreets',$flyer->xCrossStreets ?? '') }}"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3">

                </div>

                <hr class="my-10">

                <h2 class="text-2xl font-black text-slate-900">

                    Property Highlights

                </h2>

                <div class="mt-8 space-y-4">
                    
                @for($i = 1; $i <= 7; $i++)

                    <div class="flex items-center gap-4">

                        <div class="text-2xl font-bold text-blue-700">

                            •

                        </div>

                        <input
                            type="text"
                            name="xBullet{{ $i }}"
                            value="{{ old('xBullet'.$i,$flyer->{'xBullet'.$i} ?? '') }}"
                            class="flex-1 rounded-xl border border-slate-300 px-4 py-3">

                    </div>

                @endfor

                <hr class="my-10">

                <h2 class="text-2xl font-black text-slate-900">

                    Additional Resources

                </h2>

                <div class="mt-8 grid gap-6 lg:grid-cols-2">

                    <div>

                        <label class="block text-sm font-semibold text-slate-600 mb-2">

                            Virtual Tour

                        </label>

                        <input
                            type="text"
                            name="xVirtualTour"
                            value="{{ old('xVirtualTour',$flyer->xVirtualTour ?? '') }}"
                            placeholder="https://"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3">

                    </div>

                    <div>

                        <label class="block text-sm font-semibold text-slate-600 mb-2">

                            MLS Listing Link

                        </label>

                        <input
                            type="text"
                            name="xMLSLink"
                            value="{{ old('xMLSLink',$flyer->xMLSLink ?? '') }}"
                            placeholder="https://"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3">

                    </div>

                </div>

                <hr class="my-10">

                <h2 class="text-2xl font-black text-slate-900">

                    Agent Remarks

                </h2>

                <textarea
                    name="xRemarks"
                    rows="8"
                    class="mt-6 w-full rounded-xl border border-slate-300 px-4 py-3">{{ old('xRemarks',$flyer->xRemarks ?? '') }}</textarea>

            </div>

            <div class="border-t bg-slate-50 px-10 py-6">

                <div class="flex items-center justify-between">

                    <a
                        href="/member/dashboard"
                        class="rounded-xl border border-slate-300 bg-white px-6 py-3 font-semibold text-slate-700 hover:bg-slate-100">

                        Cancel

                    </a>

                    <button
                        type="submit"
                        class="rounded-xl bg-blue-700 px-8 py-3 font-bold text-white hover:bg-blue-800">

                        Save &amp; Continue →

                    </button>

                </div>

            </div>

        </div>

    </form>

</div>

</main>

@include('member.layout.footer')

</body>

</html>
