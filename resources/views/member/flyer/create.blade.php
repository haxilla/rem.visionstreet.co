@include('member.layout.head')

<body data-section="member" class="relative min-h-screen bg-[#f0f2f7] font-sans text-slate-800">

@include('member.layout.nav')

@php
$flyer = $data['flyer'] ?? null;
@endphp

<main class="min-h-screen bg-[#f0f2f7] pt-[88px]">

<div class="mx-auto w-full max-w-[900px] px-4 pb-10 sm:px-6 lg:px-8">

    <section>

        {{-- HEADER --}}
        <div class="mb-3">

            <div class="flex flex-wrap items-baseline gap-x-3 gap-y-0.5">

                <h1 class="text-2xl font-black leading-tight text-slate-900">
                    {{ $flyer ? 'Edit Flyer' : 'Create New Flyer' }}
                </h1>

                <span class="text-xs font-bold uppercase tracking-wider text-[#123f91]">
                    Step 1 of 5
                </span>

            </div>

            <p class="text-sm text-slate-500">
                {{ $flyer ? 'Update the property information below.' : 'Start by entering the MLS# and property address.' }}
            </p>

        </div>

        {{-- PROGRESS --}}
        @if ($flyer)
            @include('member.flyer.wizard', [
                'flyer' => $flyer
            ])
        @endif

        @if ($errors->any())

            <div class="mb-3 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm">

                <div class="mb-1 font-bold text-red-700">
                    Please correct the following:
                </div>

                <ul class="list-disc pl-5 text-red-600">

                    @foreach ($errors->all() as $error)

                        <li>{{ $error }}</li>

                    @endforeach

                </ul>

            </div>

        @endif

        <form action="/member/flyer/save" method="post">

            @csrf

            @if($flyer)
                <input
                    type="hidden"
                    name="flyerId"
                    value="{{ $flyer->id }}"
                >
            @endif

            <input type="hidden" name="return" value="{{ request('return') }}">

            {{-- PROPERTY CARD (MLS number shares the first row with the
                 address; MLS stays first so it's still first in tab order) --}}
            <div class="wz-card">

                <div class="grid gap-3 md:grid-cols-3">

                    <div>

                        <label for="xMlsNum" class="wz-label">
                            MLS Number
                            <span class="wz-label-hint">(blank if not listed)</span>
                        </label>

                        <input
                            type="text"
                            id="xMlsNum"
                            name="xMlsNum"
                            value="{{ old('xMlsNum', $flyer->xMlsNum ?? '') }}"
                            class="wz-input"
                        >

                    </div>

                    <div class="md:col-span-2">

                        <label for="xFullStreet" class="wz-label">
                            Property Address
                        </label>

                        <input
                            type="text"
                            id="xFullStreet"
                            name="xFullStreet"
                            value="{{ old('xFullStreet', $flyer->xFullStreet ?? '') }}"
                            class="wz-input"
                            placeholder="123 Main Street"
                            required
                        >

                    </div>

                </div>

                <div class="mt-3 grid gap-3 md:grid-cols-3">

                    <div>

                        <label for="xCity" class="wz-label">
                            City
                        </label>

                        <input
                            type="text"
                            id="xCity"
                            name="xCity"
                            value="{{ old('xCity', $flyer->xCity ?? '') }}"
                            class="wz-input"
                            required
                        >

                    </div>

                    <div>

                        <label for="xState" class="wz-label">
                            State
                        </label>

                        <select
                            id="xState"
                            name="xState"
                            class="wz-input"
                            required
                        >
                            <option value="">Select state</option>
                            @php $selectedState = old('xState', $flyer->state ?? ''); @endphp
                            @foreach(config('usstates') as $abbr => $name)
                                <option value="{{ $abbr }}" @selected($selectedState === $abbr)>{{ $name }}</option>
                            @endforeach
                        </select>

                    </div>

                    <div>

                        <label for="xZip" class="wz-label">
                            ZIP Code
                        </label>

                        <input
                            type="text"
                            id="xZip"
                            name="xZip"
                            value="{{ old('xZip', $flyer?->xZip ?: $flyer?->xxZip ?: '') }}"
                            class="wz-input"
                            required
                        >

                    </div>

                </div>

            </div>

            {{-- ACTIONS --}}
            <div class="mt-4 flex items-center justify-between">

                <a href="/member/dashboard" class="wz-btn wz-btn-secondary">
                    Cancel
                </a>

                <button type="submit" class="wz-btn wz-btn-primary">
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
