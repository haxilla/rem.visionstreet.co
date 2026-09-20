@include('member.layout.head')

<body data-section="member" class="relative min-h-screen bg-[#f0f2f7] font-sans text-slate-800">

@include('member.layout.nav')

@php
    $flyer = $data['flyer'] ?? null;
    $lastSubject = $data['lastSubject'] ?? null;
    $remCredits = $data['remCredits'] ?? 0;

    // Trial mode (admin Settings): no credits are needed or charged.
    $trialMode = $data['trialMode'] ?? false;
    $creditsBlocked = !$trialMode && $remCredits <= 0;

    $areas = [
        'phoenix_metro'    => 'Phoenix Metro',
        'northeast_valley' => 'Northeast Valley',
        'southeast_valley' => 'Southeast Valley',
        'west_valley'      => 'West Valley',
        'northern_az'      => 'Northern AZ',
        'southern_az'      => 'Southern AZ',
    ];

    $oldAreas = old('areas', []);

    // Which of these areas already have a pending (requested, not yet
    // started/completed) campaign for this flyer, so the agent can see
    // what's already awaiting admin approval instead of the page looking
    // like nothing happened after saving.
    $campaignAreaMap = include app_path('flyers/campaignAreas.php');
    $dbToMemberKey = [];
    foreach ($campaignAreaMap as $memberKey => $areaInfo) {
        $dbToMemberKey[$areaInfo['db']] = $memberKey;
    }

    $pendingAreaKeys = [];
    if ($flyer) {
        $pendingDbAreas = \App\Models\Core\Propdelivnow::where('propflyer_id', $flyer->id)
            ->whereNull('emStart')
            ->whereNull('emComplete')
            ->pluck('emArea');

        foreach ($pendingDbAreas as $dbArea) {
            if (isset($dbToMemberKey[$dbArea])) {
                $pendingAreaKeys[] = $dbToMemberKey[$dbArea];
            }
        }
    }

    // A flyer that already has a request waiting for delivery can't be
    // requested again until delivery has started (also enforced server-side).
    $hasPending = !empty($pendingAreaKeys);
    $areasLocked = $creditsBlocked || $hasPending;

    $timeOptions = [];
    for ($h = 6; $h <= 21; $h++) {
        foreach ([0, 30] as $m) {
            if ($h === 21 && $m === 30) {
                continue;
            }
            $value = sprintf('%02d:%02d', $h, $m);
            $timeOptions[$value] = \Carbon\Carbon::createFromTime($h, $m)->format('g:i A');
        }
    }
@endphp

<main class="min-h-screen bg-[#f0f2f7] pt-[88px]">

<div class="mx-auto flex w-full max-w-[900px] flex-col gap-3 px-4 pb-10 sm:px-6 lg:px-8">

    {{-- HEADER --}}
    <div class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-1">

        <h1 class="text-2xl font-black leading-tight text-slate-900">
            Send This Flyer
        </h1>

        <a href="/member/flyer/preview?flyerId={{ $flyer->id }}"
            class="text-sm font-bold text-[#123f91] hover:underline">
            ← Back to Preview
        </a>

    </div>

    @if($hasPending)
        <div class="rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm font-semibold text-blue-800">
            This flyer is already in the delivery queue. You can request another send once delivery has started.
        </div>
    @endif

    @if(session('sendsetup_status'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
            {{ session('sendsetup_status') }}
        </div>
    @endif

    @if(session('sendsetup_error'))
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
            {{ session('sendsetup_error') }}
        </div>
    @endif

    {{-- PROPERTY SNAPSHOT --}}
    @php $coverPhoto = $flyer->thePhotos->first(); @endphp
    <div class="wz-card flex items-center gap-3 p-3">

        @if($coverPhoto)
            <img src="/hqphotos/{{ $flyer->theMeta->zipDir }}/{{ $flyer->theMeta->mlsDir }}/{{ $coverPhoto->photoName }}"
                class="h-14 w-14 shrink-0 rounded-lg object-cover">
        @else
            <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-300">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 9.75L12 3l9 6.75V21a.75.75 0 01-.75.75H3.75A.75.75 0 013 21V9.75z" />
                </svg>
            </div>
        @endif

        <div class="min-w-0">

            <div class="truncate text-base font-black text-slate-900">
                {{ $flyer->xFullStreet }}
            </div>

            <div class="text-sm text-slate-500">
                {{ $flyer->xCity }}, {{ $flyer->state }} {{ $flyer->xZip }}
            </div>

            <div class="flex flex-wrap gap-x-3 text-sm font-semibold text-slate-600">
                @if($flyer->xListPrice)<span>${{ number_format($flyer->xListPrice) }}</span>@endif
                @if($flyer->xBeds)<span>{{ $flyer->xBeds }} bd</span>@endif
                @if($flyer->xBaths)<span>{{ $flyer->xBaths }} ba</span>@endif
                @if($flyer->xSqft)<span>{{ number_format($flyer->xSqft) }} sqft</span>@endif
            </div>

        </div>

    </div>

    <form method="POST" action="/member/flyer/save_sendsetup" class="flex flex-col gap-3">

        @csrf

        <input type="hidden" name="flyerId" value="{{ $flyer->id }}">

        {{-- SUBJECT --}}
        <div class="wz-card">

            <label for="emSubject" class="wz-label">
                Email Subject
            </label>

            <input
                type="text"
                id="emSubject"
                name="emSubject"
                value="{{ old('emSubject', $lastSubject) }}"
                placeholder="e.g. Just Listed - {{ $flyer->xFullStreet }}"
                class="wz-input"
            >

        </div>

        {{-- AREAS --}}
        <div class="wz-card" id="areas-card">

            <div class="wz-label">
                Areas
                <span class="wz-label-hint">(choose 2 areas to send this flyer to)</span>
            </div>

            {{-- The credit rule, stated up front - and again in the popup if they try to go on without 2 --}}
            <div class="mb-3 rounded-lg bg-blue-50 px-3 py-2 text-sm text-blue-900 ring-1 ring-blue-200">
                <span class="font-black">You must choose 2 areas.</span>
                Two areas cost only <span class="font-black">ONE credit</span> &mdash; you do not save a credit by choosing just one.
                The area you click first is Area 1 and the second is Area 2.
            </div>

            @if($creditsBlocked)
                <div class="mb-3 flex flex-wrap items-center gap-3 rounded-lg bg-amber-50 px-3 py-2 text-amber-800 ring-1 ring-amber-200">
                    <div class="text-sm font-bold">
                        You need credits to request a send.
                    </div>
                    <a href="/member/buy-credits" class="shrink-0 rounded-md bg-amber-600 px-3 py-1.5 text-xs font-black text-white hover:bg-amber-700">
                        Buy Credits
                    </a>
                </div>
            @endif

            <div id="area-badges" class="flex flex-wrap gap-2">

                @foreach($areas as $value => $label)
                    @if(in_array($value, $pendingAreaKeys, true))
                        <span class="rounded-full border-2 border-amber-300 bg-amber-50 px-3 py-1.5 text-sm font-bold text-amber-700">
                            {{ $label }} — Pending Approval
                        </span>
                    @else
                        <label class="area-badge cursor-pointer select-none rounded-full border-2 border-slate-200 px-3 py-1.5 text-sm font-bold text-slate-600 transition has-[:checked]:border-[#123f91] has-[:checked]:bg-[#123f91] has-[:checked]:text-white has-[:disabled]:cursor-not-allowed has-[:disabled]:opacity-40">
                            <input type="checkbox" name="areas[]" value="{{ $value }}"
                                class="hidden"
                                @checked(in_array($value, $oldAreas))
                                @if($areasLocked) disabled data-locked="1" @endif>
                            {{ $label }}
                            {{-- 1 or 2: the order this area was picked in (filled in by the script below) --}}
                            <span class="area-order ml-1.5 inline-flex h-5 w-5 items-center justify-center rounded-full bg-white text-xs font-black text-[#123f91]" hidden></span>
                        </label>
                    @endif
                @endforeach

            </div>

            {{-- the order the areas were clicked in, sent with the form (see the script below) --}}
            <input type="hidden" name="areaOrder" id="areaOrder" value="{{ old('areaOrder') }}">

        </div>

        {{-- MARKETING HIGHLIGHTS --}}
        <div class="wz-card">

            <h2 class="text-base font-black text-slate-900">Marketing Highlights</h2>
            <p class="mb-3 text-xs text-slate-500">Optional extras for this listing.</p>

            {{-- OPEN HOUSES --}}
            <div class="wz-label">
                Open Houses
                <span class="wz-label-hint">(up to two sessions)</span>
            </div>

            <div class="flex flex-col gap-2">

                @foreach([1, 2] as $n)

                    <div class="grid grid-cols-1 gap-2 sm:grid-cols-3">

                        <input type="date" name="openHouseDate{{ $n }}"
                            aria-label="Open house {{ $n }} date"
                            value="{{ old('openHouseDate'.$n, $flyer->{'openHouseDate'.$n}) }}"
                            class="wz-input">

                        @php $selStart = old('openHouseTime'.$n, $flyer->{'openHouseTime'.$n}); @endphp
                        <select name="openHouseTime{{ $n }}" aria-label="Open house {{ $n }} start time"
                            class="wz-input">
                            <option value="">Start</option>
                            @foreach($timeOptions as $value => $label)
                                <option value="{{ $value }}" @selected($selStart && substr($selStart,0,5) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>

                        @php $selEnd = old('openHouseEndTime'.$n, $flyer->{'openHouseEndTime'.$n}); @endphp
                        <select name="openHouseEndTime{{ $n }}" aria-label="Open house {{ $n }} end time"
                            class="wz-input">
                            <option value="">End</option>
                            @foreach($timeOptions as $value => $label)
                                <option value="{{ $value }}" @selected($selEnd && substr($selEnd,0,5) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>

                    </div>

                @endforeach

            </div>

            <div class="mt-4 grid grid-cols-1 gap-4 border-t border-slate-200 pt-3 md:grid-cols-2">

                {{-- AGENT BONUS --}}
                <div>

                    <div class="wz-label">
                        Agent Bonus
                        <span class="wz-label-hint">(optional incentive to the buyer's agent)</span>
                    </div>

                    <div class="flex flex-col gap-2">

                        <input type="text" name="agentBonusAmount"
                            aria-label="Agent bonus amount"
                            value="{{ old('agentBonusAmount', $flyer->agentBonusAmount) }}"
                            placeholder="e.g. $1,000 or 0.5%"
                            class="wz-input">

                        <input type="text" name="agentBonusComment"
                            aria-label="Agent bonus note"
                            value="{{ old('agentBonusComment', $flyer->agentBonusComment) }}"
                            placeholder="Optional note shown with the bonus"
                            class="wz-input">

                    </div>

                </div>

                {{-- PRICE REDUCTION --}}
                <div>

                    <div class="wz-label">
                        Price Reduction
                        <span class="wz-label-hint">(amount and date, if any)</span>
                    </div>

                    <div class="flex flex-col gap-2">

                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-sm font-semibold text-slate-400">$</span>
                            <input type="text" name="reducedAmount"
                                aria-label="Price reduction amount"
                                value="{{ old('reducedAmount', $flyer->reducedAmount) }}"
                                placeholder="e.g. 10000"
                                class="wz-input pl-7">
                        </div>

                        <input type="date" name="reducedDate"
                            aria-label="Price reduction date"
                            value="{{ old('reducedDate', $flyer->reducedDate) }}"
                            class="wz-input">

                    </div>

                </div>

            </div>

        </div>

        <div class="flex justify-end">
            <button type="submit" id="submitBtn" @disabled($hasPending) class="wz-btn wz-btn-primary disabled:cursor-not-allowed disabled:opacity-60">
                Submit for Delivery
            </button>
        </div>

    </form>

</div>

</main>

@include('public.layout.footer')

{{-- "Choose 2 areas" popup: shown when they try to submit without exactly 2 areas (checked
     by the script below, and again by save_sendsetup.php, which flashes
     sendsetup_need_two_areas if the form was posted without them). Same look as the
     "added to the delivery queue" popup on the dashboard. --}}
<div id="twoAreasModal"
     class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/50 p-4"
     role="dialog" aria-modal="true" aria-labelledby="twoAreasModalTitle" hidden>

    <div class="w-full max-w-md rounded-2xl bg-white p-6 text-center shadow-xl">

        <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-amber-100 text-2xl font-black text-amber-600">
            !
        </div>

        <h2 id="twoAreasModalTitle" class="text-xl font-black text-slate-900">
            Please choose 2 areas
        </h2>

        <p class="mt-2 text-sm text-slate-600">
            You must choose <span class="font-black text-slate-900">2 areas</span> before you can submit this flyer for delivery.
            <span id="twoAreasCount" class="block pt-1 font-semibold text-slate-800"></span>
        </p>

        <div class="mt-3 rounded-lg bg-blue-50 px-3 py-2 text-sm font-semibold text-blue-900 ring-1 ring-blue-200">
            Two areas cost only <span class="font-black">ONE credit</span>.
            You do <span class="font-black">not</span> save a credit by choosing only one area.
        </div>

        <button type="button" id="twoAreasClose" class="wz-btn wz-btn-primary mt-5">
            OK, I'll choose my areas
        </button>

    </div>
</div>

<script>
(function () {
    var boxes = document.querySelectorAll('#area-badges input[type="checkbox"]');
    var orderField = document.getElementById('areaOrder');

    // The order the areas were clicked in: the first is Area 1, the second Area 2.
    // Restored from the form after a bounce (e.g. the "choose 2 areas" popup).
    var picked = [];

    (orderField.value || '').split(',').forEach(function (key) {
        var box = document.querySelector('#area-badges input[value="' + key + '"]');
        if (box && box.checked && picked.indexOf(key) === -1) picked.push(key);
    });

    boxes.forEach(function (b) {
        if (b.checked && picked.indexOf(b.value) === -1) picked.push(b.value);
    });

    function syncOrder() {
        // drop anything no longer ticked
        picked = picked.filter(function (key) {
            var box = document.querySelector('#area-badges input[value="' + key + '"]');
            return box && box.checked;
        });

        orderField.value = picked.join(',');

        boxes.forEach(function (b) {
            var chip = b.parentNode.querySelector('.area-order');
            var n = picked.indexOf(b.value) + 1;

            if (!chip) return;

            chip.hidden = n === 0;
            chip.textContent = n === 0 ? '' : String(n);
        });
    }

    function syncMax() {
        var checkedCount = Array.prototype.filter.call(boxes, function (b) { return b.checked; }).length;
        boxes.forEach(function (b) {
            // boxes the server locked (no credits / already queued) stay locked
            b.disabled = b.dataset.locked === '1' || (!b.checked && checkedCount >= 2);
        });
    }

    boxes.forEach(function (b) {
        b.addEventListener('change', function () {
            if (b.checked && picked.indexOf(b.value) === -1) picked.push(b.value);

            syncOrder();
            syncMax();
        });
    });

    syncOrder();
    syncMax();

    // ---- the "choose 2 areas" popup ----
    var modal = document.getElementById('twoAreasModal');
    var modalClose = document.getElementById('twoAreasClose');
    var modalCount = document.getElementById('twoAreasCount');
    var areasLocked = {{ $areasLocked ? 'true' : 'false' }};   // no credits / already queued: there are no areas to pick

    function showTwoAreasModal() {
        var n = picked.length;

        modalCount.textContent = n === 0
            ? 'You have not chosen any areas yet.'
            : 'You have chosen ' + n + ' of 2 areas.';

        modal.hidden = false;
        document.body.style.overflow = 'hidden';
        modalClose.focus();
    }

    function hideTwoAreasModal() {
        modal.hidden = true;
        document.body.style.overflow = '';

        var card = document.getElementById('areas-card');
        if (card) card.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    modalClose.addEventListener('click', hideTwoAreasModal);
    modal.addEventListener('click', function (e) { if (e.target === modal) hideTwoAreasModal(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && !modal.hidden) hideTwoAreasModal(); });

    @if(session('sendsetup_need_two_areas'))
        showTwoAreasModal();
    @endif

    // Lock the form after the first submit. A second click / Enter press used
    // to send a second identical request, which created duplicate areas.
    var form = document.querySelector('form[action="/member/flyer/save_sendsetup"]');
    var submitBtn = document.getElementById('submitBtn');
    var submitted = false;

    form.addEventListener('submit', function (e) {
        if (submitted) {
            e.preventDefault();
            return;
        }

        // exactly 2 areas or no send - say why, and that 2 areas cost the same as 1
        if (!areasLocked && picked.length !== 2) {
            e.preventDefault();
            showTwoAreasModal();
            return;
        }

        submitted = true;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Submitting…';
    });

    // Coming back with the browser's Back button restores the page from
    // memory with the button still locked - unlock it.
    window.addEventListener('pageshow', function (e) {
        if (e.persisted) {
            submitted = false;
            submitBtn.disabled = false;
            submitBtn.textContent = 'Submit for Delivery';
        }
    });
})();
</script>

</body>
</html>
