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

    $oldAreas = old('areas', []);
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
        Sending isn't wired up yet — saving below stores your open house,
        bonus, subject, and area choices, but nothing will go out until
        that's finished.
    </div>

    <form method="POST" action="/member/flyer/save_sendsetup" class="flex flex-col gap-8">

        @csrf

        <input type="hidden" name="flyerId" value="{{ $flyer->id }}">

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
                Choose up to 2 areas to send this flyer to.
            </p>

            <div id="area-badges" class="flex flex-wrap gap-2">

                @foreach($areas as $value => $label)
                    <label class="area-badge cursor-pointer select-none rounded-full border-2 border-slate-200 px-4 py-2 text-sm font-bold text-slate-600 transition has-[:checked]:border-[#123f91] has-[:checked]:bg-[#123f91] has-[:checked]:text-white has-[:disabled]:cursor-not-allowed has-[:disabled]:opacity-40">
                        <input type="checkbox" name="areas[]" value="{{ $value }}"
                            class="hidden"
                            @checked(in_array($value, $oldAreas))>
                        {{ $label }}
                    </label>
                @endforeach

            </div>

        </div>

        {{-- OPEN HOUSES --}}
        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">

            <label class="mb-1 block text-sm font-black text-slate-900">
                Open Houses
            </label>

            <p class="mb-4 text-sm text-slate-500">
                Up to two open house sessions for this listing.
            </p>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                <div class="grid grid-cols-2 gap-3">
                    <input type="date" name="openHouseDate1"
                        value="{{ old('openHouseDate1', $flyer->openHouseDate1) }}"
                        class="rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-[#123f91] focus:outline-none focus:ring-4 focus:ring-[#123f91]/10">
                    <input type="time" name="openHouseTime1"
                        value="{{ old('openHouseTime1', $flyer->openHouseTime1) }}"
                        class="rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-[#123f91] focus:outline-none focus:ring-4 focus:ring-[#123f91]/10">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <input type="date" name="openHouseDate2"
                        value="{{ old('openHouseDate2', $flyer->openHouseDate2) }}"
                        class="rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-[#123f91] focus:outline-none focus:ring-4 focus:ring-[#123f91]/10">
                    <input type="time" name="openHouseTime2"
                        value="{{ old('openHouseTime2', $flyer->openHouseTime2) }}"
                        class="rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-[#123f91] focus:outline-none focus:ring-4 focus:ring-[#123f91]/10">
                </div>

            </div>

        </div>

        {{-- AGENT BONUS --}}
        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">

            <label class="mb-1 block text-sm font-black text-slate-900">
                Agent Bonus
            </label>

            <p class="mb-4 text-sm text-slate-500">
                Optional incentive to the buyer's agent.
            </p>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                <input type="text" name="agentBonusAmount"
                    value="{{ old('agentBonusAmount', $flyer->agentBonusAmount) }}"
                    placeholder="e.g. $1,000 or 0.5%"
                    class="rounded-xl border border-slate-300 px-4 py-3 text-sm shadow-inner focus:border-[#123f91] focus:outline-none focus:ring-4 focus:ring-[#123f91]/10">

                <input type="text" name="agentBonusComment"
                    value="{{ old('agentBonusComment', $flyer->agentBonusComment) }}"
                    placeholder="Optional note shown with the bonus"
                    class="rounded-xl border border-slate-300 px-4 py-3 text-sm shadow-inner focus:border-[#123f91] focus:outline-none focus:ring-4 focus:ring-[#123f91]/10">

            </div>

            @if($flyer->reducedAmount)

                <div class="mt-4 rounded-xl bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800">
                    Price reduced by ${{ number_format($flyer->reducedAmount) }}{{ $flyer->reducedDate ? ' on '.\Carbon\Carbon::parse($flyer->reducedDate)->format('m/d/Y') : '' }} — calculated automatically from the original list price.
                </div>

            @endif

        </div>

        <div class="flex justify-end">
            <button type="submit"
                class="rounded-xl bg-[#123f91] px-8 py-4 text-lg font-black text-white hover:bg-[#0d2f6e]">
                Save
            </button>
        </div>

    </form>

</div>

</main>

@include('public.layout.footer')

<script>
(function () {
    var boxes = document.querySelectorAll('#area-badges input[type="checkbox"]');

    function syncMax() {
        var checkedCount = Array.prototype.filter.call(boxes, function (b) { return b.checked; }).length;
        boxes.forEach(function (b) {
            b.disabled = !b.checked && checkedCount >= 2;
        });
    }

    boxes.forEach(function (b) { b.addEventListener('change', syncMax); });
    syncMax();
})();
</script>

</body>
</html>
