@include('member.layout.head')

<body data-section="member" class="relative min-h-screen bg-[#f0f2f7] font-sans text-slate-800">

@include('member.layout.nav')

@php
    $flyer = $data['flyer'] ?? null;
    $lastSubject = $data['lastSubject'] ?? null;

    $areas = [
        'phoenix_metro'    => 'Phoenix Metro',
        'northeast_valley' => 'Northeast Valley',
        'southeast_valley' => 'Southeast Valley',
        'west_valley'      => 'West Valley',
        'northern_az'      => 'Northern AZ',
        'southern_az'      => 'Southern AZ',
    ];
@endphp

<main class="min-h-screen bg-[#f0f2f7] pt-24">

<div class="mx-auto flex w-full max-w-[900px] flex-col gap-8 px-4 pb-16 sm:px-6 lg:px-8">

    {{-- HEADER --}}
    <div>

        <a href="/member/flyer/preview?flyerId={{ $flyer->id }}"
            class="text-sm font-bold text-[#123f91]">
            ← Back to Preview
        </a>

        <h1 class="mt-2 text-4xl font-black text-slate-900">
            Send This Flyer
        </h1>

        <p class="mt-2 text-slate-500">
            {{ $flyer->xFullStreet }},
            {{ $flyer->xCity }},
            {{ $flyer->xState }}
            {{ $flyer->xZip }}
        </p>

    </div>

    <div class="rounded-xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm font-bold text-amber-800">
        Sending isn't wired up yet — you can fill this out, but nothing will
        go out until that's finished.
    </div>

    <form onsubmit="return false;" class="flex flex-col gap-8">

        {{-- SUBJECT --}}
        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">

            <label class="mb-2 block text-sm font-black text-slate-900">
                Email Subject
            </label>

            <input
                type="text"
                name="emSubject"
                value="{{ old('emSubject', $lastSubject) }}"
                placeholder="e.g. Just Listed - {{ $flyer->xFullStreet }}"
                class="w-full rounded-xl border border-slate-300 px-4 py-3 text-base shadow-inner focus:border-[#123f91] focus:outline-none focus:ring-4 focus:ring-[#123f91]/10"
            >

        </div>

        {{-- AREAS --}}
        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">

            <label class="mb-1 block text-sm font-black text-slate-900">
                Areas
            </label>

            <p class="mb-4 text-sm text-slate-500">
                Choose one or more areas to send this flyer to.
            </p>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">

                @foreach($areas as $value => $label)
                    <label class="flex items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 font-bold text-slate-700 hover:bg-slate-50">
                        <input type="checkbox" name="areas[]" value="{{ $value }}" class="h-5 w-5 rounded border-slate-300 text-[#123f91] focus:ring-[#123f91]">
                        {{ $label }}
                    </label>
                @endforeach

            </div>

        </div>

        {{-- FLAGS --}}
        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">

            <label class="mb-1 block text-sm font-black text-slate-900">
                Highlight
            </label>

            <p class="mb-4 text-sm text-slate-500">
                Optional callouts for this send.
            </p>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">

                <label class="flex items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 font-bold text-slate-700 hover:bg-slate-50">
                    <input type="checkbox" name="priceReduced" value="1" class="h-5 w-5 rounded border-slate-300 text-[#123f91] focus:ring-[#123f91]">
                    Price Reduced
                </label>

                <label class="flex items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 font-bold text-slate-700 hover:bg-slate-50">
                    <input type="checkbox" name="openHouse" value="1" class="h-5 w-5 rounded border-slate-300 text-[#123f91] focus:ring-[#123f91]">
                    Open House
                </label>

                <label class="flex items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 font-bold text-slate-700 hover:bg-slate-50">
                    <input type="checkbox" name="agentBonus" value="1" class="h-5 w-5 rounded border-slate-300 text-[#123f91] focus:ring-[#123f91]">
                    Commission to Buyer's Agent
                </label>

            </div>

        </div>

        <div class="flex justify-end">

            <button type="submit" disabled
                title="Sending isn't wired up yet"
                class="cursor-not-allowed rounded-xl bg-slate-300 px-8 py-4 text-lg font-black text-slate-500">
                Send Flyer (Coming Soon)
            </button>

        </div>

    </form>

</div>

</main>

@include('public.layout.footer')

</body>
</html>
