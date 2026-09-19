@include('public.layout.flyerhead')

<body data-section="admin" class="bg-[#f4f7fb] min-h-screen">

@include('admin.layout.nav')

@php


$propInfo = $data['propInfo'];

include(app_path().'/flyers/variables.php');

$template = strtolower($propInfo->theStyle->template ?? '');
$templateView = 'flyers.s'.$template;

$subject = '';

$campaigns =
    $data['waitingFlyerCamps'][$propInfo->id]
    ?? $data['inProgressFlyerCamps'][$propInfo->id]
    ?? $data['completeFlyerCamps'][$propInfo->id]
    ?? collect();

$subject = $campaigns->first()['emSubject'] ?? '';

// ---- approval screen ----
$pendingRequests = $data['pendingRequests'] ?? collect();
$awaitingApproval = $pendingRequests->filter(fn ($c) => (int) $c->authorized !== 1);
$agent       = $data['agent'] ?? null;
$sendDetails = $data['sendDetails'] ?? null;

$areaLabel = function ($camp) use ($data) {
    return $camp->emArea_display ?: ($data['areaLabels'][$camp->emArea] ?? $camp->emArea);
};

$openHouseLine = function ($n) use ($sendDetails) {
    $date = $sendDetails->{'openHouseDate'.$n} ?? null;
    if (!$date) {
        return null;
    }
    $line  = \Carbon\Carbon::parse($date)->format('D, M j, Y');
    $start = $sendDetails->{'openHouseTime'.$n} ?? null;
    $end   = $sendDetails->{'openHouseEndTime'.$n} ?? null;
    if ($start) {
        $line .= ', ' . \Carbon\Carbon::parse($start)->format('g:i A');
    }
    if ($end) {
        $line .= ' - ' . \Carbon\Carbon::parse($end)->format('g:i A');
    }
    return $line;
};

$openHouses = array_values(array_filter([$openHouseLine(1), $openHouseLine(2)]));

// Areas the admin can still add (not already waiting or running)
$addableAreas = collect($data['emailCounts'] ?? [])
    ->reject(fn ($count, $areaKey) => in_array($areaKey, $data['busyAreas'] ?? [], true));

// created_at is only reliably populated for flyers created through this
// app - anything imported from the legacy pre-Laravel system has it
// null, with the real date only in the legacy creationDate column.
$createdDate = null;
if ($propInfo->created_at) {
    $createdDate = $propInfo->created_at->format('n/j/Y');
} elseif ($propInfo->creationDate) {
    $createdDate = \Carbon\Carbon::parse($propInfo->creationDate)->format('n/j/Y');
}
@endphp

<main class="pt-[72px]">

    <div class="max-w-6xl mx-auto px-3 sm:px-4 lg:px-6 py-6">

        {{-- TRIAL MODE / STATUS / ERRORS --}}
        @if($data['trialMode'] ?? false)
            <div class="bg-amber-50 border border-amber-300 text-amber-700 rounded-2xl px-5 py-4 mb-6 text-sm font-semibold">
                Trial mode is ON. No credits are used, and the agent's copy goes to
                @if(($data['trialEmail'] ?? '') !== '')
                    {{ $data['trialEmail'] }}
                @else
                    the test email (not set yet - add one in Settings)
                @endif
                instead of the agent.
                <a href="/admin/settings" class="underline">Change in Settings</a>
            </div>
        @endif

        @if(session('status'))
            <div class="bg-emerald-50 border border-emerald-300 text-emerald-700 rounded-2xl px-5 py-4 mb-6 text-sm font-semibold">
                {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-2xl px-5 py-4 mb-6 text-sm font-semibold">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        {{-- HEADER --}}
        <div class="bg-white rounded-2xl shadow-sm p-5 mb-6">

            <div class="flex flex-wrap items-center justify-between gap-2">

                <a href="/admin/flyers"
                class="text-sm text-[#214e9b] font-semibold">
                    ← Back to Flyers
                </a>

                <div class="flex flex-wrap items-center gap-3">
                    <a href="/admin/flyerEdit/{{ $propInfo->id }}"
                    class="rounded-lg bg-[#214e9b] px-4 py-2 text-sm font-semibold text-white hover:bg-[#1b3f80]">
                        Edit Flyer
                    </a>

                    <div class="flex flex-wrap items-center gap-2">
                    @if($createdDate)
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                            Created {{ $createdDate }}
                        </span>
                    @endif

                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                        {{ number_format(optional($propInfo->theStats)->xWebViews ?? 0) }} Flyer Views
                    </span>
                    </div>
                </div>

            </div>

        </div>

        {{-- SEND REQUEST REVIEW --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-6">

            <div class="px-5 py-4 border-b border-slate-200 flex flex-wrap items-center justify-between gap-3">
                <h2 class="font-semibold text-slate-900">Send Request</h2>

                @if($awaitingApproval->isNotEmpty())
                    <span class="bg-amber-100 text-amber-700 px-3 py-1 rounded-full text-xs font-semibold">
                        Awaiting approval
                    </span>
                @elseif($pendingRequests->isNotEmpty())
                    <span class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-xs font-semibold">
                        Approved
                    </span>
                @else
                    <span class="bg-slate-100 text-slate-600 px-3 py-1 rounded-full text-xs font-semibold">
                        No pending request
                    </span>
                @endif
            </div>

            {{-- AGENT + SEND DETAILS --}}
            <div class="grid gap-x-8 gap-y-4 px-5 py-5 md:grid-cols-2 text-sm">

                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500 mb-1">Agent</div>
                    <div class="font-semibold text-slate-900">{{ $agent->agtFullName ?? 'Unknown agent' }}</div>
                    @if($agent?->agtEmail)
                        <div class="text-slate-600">{{ $agent->agtEmail }}</div>
                    @endif
                    @if($agent?->agtMainPhone)
                        <div class="text-slate-600">{{ $agent->agtMainPhone }}</div>
                    @endif
                    <div class="text-slate-500 mt-1">Credits remaining: {{ number_format($agent->remCreds ?? 0) }}</div>
                </div>

                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500 mb-1">Requested</div>
                    <div class="text-slate-900">
                        {{ $pendingRequests->isNotEmpty() ? \Carbon\Carbon::parse($pendingRequests->first()->emRequest)->format('M j, Y g:i A') : '-' }}
                    </div>

                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500 mt-3 mb-1">Email Subject</div>
                    <div class="text-slate-900">{{ $pendingRequests->first()->emSubject ?? $subject ?: 'No subject' }}</div>
                </div>

                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500 mb-1">Open Houses</div>
                    @forelse($openHouses as $line)
                        <div class="text-slate-900">{{ $line }}</div>
                    @empty
                        <div class="text-slate-500">None</div>
                    @endforelse
                </div>

                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500 mb-1">Agent Bonus / Price Reduced</div>

                    @if($sendDetails?->agentBonusAmount)
                        <div class="text-slate-900">
                            Bonus: {{ $sendDetails->agentBonusAmount }}
                            @if($sendDetails->agentBonusComment)
                                <span class="text-slate-500">({{ $sendDetails->agentBonusComment }})</span>
                            @endif
                        </div>
                    @endif

                    @if($sendDetails?->reducedAmount)
                        <div class="text-slate-900">
                            Reduced ${{ number_format($sendDetails->reducedAmount) }}
                            @if($sendDetails->reducedDate)
                                <span class="text-slate-500">on {{ \Carbon\Carbon::parse($sendDetails->reducedDate)->format('M j, Y') }}</span>
                            @endif
                        </div>
                    @endif

                    @if(!$sendDetails?->agentBonusAmount && !$sendDetails?->reducedAmount)
                        <div class="text-slate-500">None</div>
                    @endif
                </div>

            </div>

            {{-- REQUESTED AREAS --}}
            <div class="border-t border-slate-200">
                <div class="px-5 pt-4 text-xs font-semibold uppercase tracking-wide text-slate-500">
                    Requested Areas
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($pendingRequests as $req)
                        <div class="px-5 py-3 flex items-center justify-between gap-3">
                            <div>
                                <div class="font-semibold">
                                    {{ $areaLabel($req) }}
                                    @include('admin.flyer.campSource', ['adminAdded' => $req->isAdminAdded()])
                                </div>
                                <div class="text-sm text-slate-500">{{ number_format($req->totalEmails ?? ($data['emailCounts'][$req->emArea] ?? 0)) }} contacts</div>
                            </div>

                            @if((int) $req->authorized === 1)
                                <span class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-xs font-semibold">Approved</span>
                            @else
                                <span class="bg-amber-100 text-amber-700 px-3 py-1 rounded-full text-xs font-semibold">Awaiting approval</span>
                            @endif
                        </div>
                    @empty
                        <div class="px-5 py-6 text-sm text-slate-500">
                            This flyer has no pending send request.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- ADD FREE AREA: directly under the requested areas it adds to --}}
            <div class="border-t border-slate-200 px-5 py-4">

                <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                    Add a Free Area
                </div>

                <p class="mb-3 mt-1 text-sm text-slate-500">
                    No credit is charged to the agent. The area is added as unapproved and is approved together with this flyer's other waiting areas.
                </p>

                <form method="POST"
                      action="{{ route('admin.campaignAddArea', $propInfo->id) }}"
                      class="flex flex-col md:flex-row gap-3">
                    @csrf

                    <select name="area" required class="flex-1 border border-slate-300 rounded-xl px-4 py-3">
                        <option value="">Select Area</option>
                        @foreach($addableAreas as $areaKey => $count)
                            <option value="{{ $areaKey }}">
                                {{ $data['areaLabels'][$areaKey] ?? strtoupper($areaKey) }} ({{ number_format($count) }} contacts)
                            </option>
                        @endforeach
                    </select>

                    <button type="submit"
                        class="bg-[#214e9b] text-white px-5 py-3 rounded-xl font-semibold">
                        Add Free Area
                    </button>

                </form>

                @if($addableAreas->isEmpty())
                    <p class="text-sm text-slate-500 mt-3">
                        Every area is already waiting or in progress for this flyer.
                    </p>
                @endif

            </div>

            {{-- APPROVE --}}
            @if($awaitingApproval->isNotEmpty())
                <form method="POST"
                      action="{{ route('admin.campaignApprove', $propInfo->id) }}"
                      onsubmit="return confirm('Approve {{ $awaitingApproval->count() }} area(s) for {{ addslashes($propInfo->xFullStreet ?? 'this flyer') }}?');"
                      class="border-t border-slate-200 px-5 py-4 flex flex-wrap items-center justify-between gap-3">
                    @csrf

                    <p class="text-sm text-slate-600">
                        Approving marks every waiting area ready for the mail system to send.
                        Add any free areas above first.
                    </p>

                    <button type="submit"
                            class="bg-emerald-600 text-white px-5 py-3 rounded-xl font-semibold hover:bg-emerald-700">
                        Approve {{ $awaitingApproval->count() }} {{ $awaitingApproval->count() === 1 ? 'Area' : 'Areas' }}
                    </button>
                </form>
            @endif

        </div>

        {{-- SUBJECT --}}
        <div class="bg-white rounded-2xl shadow-sm p-5 mb-6">

            <label class="block text-sm font-semibold text-slate-700 mb-2">
                Email Subject
            </label>

            <div class="flex flex-col md:flex-row gap-3">

                <input
                    type="text"
                    value="{{ $subject }}"
                    class="flex-1 border border-slate-300 rounded-xl px-4 py-3"
                >

                <button
                    class="bg-[#214e9b] text-white px-5 py-3 rounded-xl font-semibold">
                    Save Subject
                </button>

            </div>

        </div>

        {{-- FLYER --}}
        <div class="bg-white rounded-2xl shadow-sm p-4 mb-6">

            <div class="flyer-stage">

                <div id="flyer-scale-wrapper">

                    <div class="flyer-panel active">

                        @if(View::exists($templateView))
                            @include($templateView)
                        @endif

                    </div>

                </div>

            </div>

        </div>

        @php

        $waitingCampaigns =
            $data['waitingFlyerCamps'][$propInfo->id] ?? collect();

        $inProgressCampaigns =
            $data['inProgressFlyerCamps'][$propInfo->id] ?? collect();

        $completedCampaigns =
            $data['completeFlyerCamps'][$propInfo->id] ?? collect();

        @endphp

        {{-- ACTIVE CAMPAIGNS --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-6">

            <div class="px-5 py-4 border-b border-slate-200">
                <h2 class="font-semibold text-slate-900">
                    Active Campaigns
                </h2>
            </div>

            <div class="divide-y divide-slate-100">

                @foreach($waitingCampaigns as $camp)

                    <div class="px-5 py-4 flex items-center justify-between">

                        <div>
                            <div class="font-semibold">
                                {{ $camp['emArea'] }}
                                @include('admin.flyer.campSource', ['adminAdded' => $camp['admin_added'] ?? false])
                            </div>

                            <div class="text-sm text-slate-500">
                                Requested {{ $camp['emRequest'] }}
                            </div>
                        </div>

                        @if((int) ($camp['authorized'] ?? 0) === 1)
                            <span class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-xs font-semibold">
                                Approved - waiting to send
                            </span>
                        @else
                            <span class="bg-amber-100 text-amber-700 px-3 py-1 rounded-full text-xs font-semibold">
                                Awaiting approval
                            </span>
                        @endif

                    </div>

                @endforeach

                @foreach($inProgressCampaigns as $camp)

                    <div class="px-5 py-4 flex items-center justify-between">

                        <div>
                            <div class="font-semibold">
                                {{ $camp['emArea'] }}
                                @include('admin.flyer.campSource', ['adminAdded' => $camp['admin_added'] ?? false])
                            </div>

                            <div class="text-sm text-slate-500">
                                Started {{ $camp['emStart'] }}
                            </div>
                        </div>

                        <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-semibold">
                            In Progress
                        </span>

                    </div>

                @endforeach

                @if($waitingCampaigns->isEmpty() && $inProgressCampaigns->isEmpty())

                    <div class="px-5 py-8 text-center text-slate-500">
                        No active campaigns found.
                    </div>

                @endif

            </div>

        </div>

        {{-- COMPLETED --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">

            <div class="px-5 py-4 border-b border-slate-200">
                <h2 class="font-semibold text-slate-900">
                    Completed Campaigns ({{ number_format($completedCampaigns->sum('totalEmails')) }})
                </h2>
            </div>

            <div class="divide-y divide-slate-100">

                @forelse($completedCampaigns as $camp)

                    <div class="px-5 py-4 flex items-center justify-between">

                        <div>
                            <div class="font-semibold">
                                {{ $camp['emArea'] }} ({{ number_format($camp['totalEmails'] ?? 0) }})
                                @include('admin.flyer.campSource', ['adminAdded' => $camp['admin_added'] ?? false])
                            </div>

                            <div class="text-sm text-slate-500">
                                Completed {{ $camp['emComplete'] }}
                            </div>

                            @if($camp['emSubject'])
                                <div class="text-sm text-slate-500">
                                    Subject: {{ $camp['emSubject'] }}
                                </div>
                            @endif
                        </div>

                        <span class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-xs font-semibold">
                            Completed
                        </span>

                    </div>

                @empty

                    <div class="px-5 py-8 text-center text-slate-500">
                        No completed campaigns found.
                    </div>

                @endforelse

            </div>

        </div>

    </div>

</main>

@include('public.layout.footer')


<style>
.flyer-stage{
    width:100%;
    overflow:hidden;
}

#flyer-scale-wrapper{
    width:600px;
    transform-origin:top left;
    margin:0 auto;
}

.flyer-panel.active{
    display:block;
}
</style>

<script src="/my/js/flyers/photoSwap.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {

    function scaleFlyer() {
 
        const stage = document.querySelector('.flyer-stage');
        const wrapper = document.getElementById('flyer-scale-wrapper');

        if (!stage || !wrapper) return;

        const activeFlyer =
            wrapper.querySelector('.flyer-panel.active');

        if (!activeFlyer) return;

        const availableWidth = stage.clientWidth;
        const scale = Math.min(availableWidth / 600, 1);

        wrapper.style.transformOrigin = 'top left';
        wrapper.style.transform = `scale(${scale})`;

        wrapper.style.height =
            (activeFlyer.offsetHeight * scale) + 'px';
    }

    scaleFlyer();
    window.addEventListener('resize', scaleFlyer);

});
</script>

</body>
</html>