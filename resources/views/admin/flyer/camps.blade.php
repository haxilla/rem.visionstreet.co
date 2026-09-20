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
$approvedWaiting  = $pendingRequests->filter(fn ($c) => (int) $c->authorized === 1);
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

    @php
        $inProgressCampaigns = $data['inProgressFlyerCamps'][$propInfo->id] ?? collect();
        $completedCampaigns  = $data['completeFlyerCamps'][$propInfo->id] ?? collect();

        // legacy rows store the area code in mixed case (AzPhxSE)
        $areaName = fn ($code) => $data['areaLabels'][strtolower((string) $code)] ?? $code;

        $place = trim(($propInfo->xCity ?? '') . ', ' . ($propInfo->xState ?: ($propInfo->state ?? '')) . ' ' . ($propInfo->xZip ?? ''), ' ,');

        $card      = 'bg-white rounded-2xl shadow-sm ring-1 ring-slate-200/70';
        $cardHead  = 'flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-5 py-4';
        $eyebrow   = 'text-xs font-semibold uppercase tracking-wide text-slate-500';
    @endphp

    <div class="max-w-6xl lg:max-w-[1400px] mx-auto px-3 sm:px-4 lg:px-6 py-6">

        {{-- TRIAL MODE / STATUS / ERRORS --}}
        @if($data['trialMode'] ?? false)
            <div class="bg-amber-50 border border-amber-300 text-amber-800 rounded-2xl px-5 py-4 mb-6 text-sm font-semibold">
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
            <div class="bg-emerald-50 border border-emerald-300 text-emerald-800 rounded-2xl px-5 py-4 mb-6 text-sm font-semibold">
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

        {{-- HEADER: which property this is, who it belongs to, and where it stands --}}
        <div class="{{ $card }} p-5 sm:p-6 mb-6">

            <a href="/admin/flyers" class="text-sm font-semibold text-[#214e9b] hover:underline">← Back to Flyers</a>

            <div class="mt-3 flex flex-wrap items-start justify-between gap-4">

                <div class="min-w-0">
                    <div class="{{ $eyebrow }}">Campaigns</div>

                    <h1 class="mt-1 break-words text-2xl font-semibold leading-tight text-slate-900 sm:text-[28px]">
                        {{ $propInfo->xFullStreet ?: 'Flyer #' . $propInfo->id }}
                    </h1>

                    <p class="mt-1 text-sm text-slate-600">
                        @if($place !== '')
                            {{ $place }}
                            <span class="mx-1.5 text-slate-300">&middot;</span>
                        @endif
                        Flyer #{{ $propInfo->id }}
                        @if($agent?->agtFullName)
                            <span class="mx-1.5 text-slate-300">&middot;</span>
                            <a href="/admin/agentView/{{ $propInfo->propagent_id }}" class="font-semibold text-[#214e9b] hover:underline">{{ $agent->agtFullName }}</a>
                        @endif
                    </p>
                </div>

                <a href="/admin/flyerEdit/{{ $propInfo->id }}"
                   class="rounded-lg bg-[#214e9b] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#1b3f80]">
                    Edit Flyer
                </a>

            </div>

            <div class="mt-4 flex flex-wrap gap-2 text-xs font-semibold">
                @if($awaitingApproval->isNotEmpty())
                    <span class="rounded-full bg-amber-100 px-3 py-1 text-amber-800">{{ $awaitingApproval->count() }} awaiting approval</span>
                @endif
                @if($approvedWaiting->isNotEmpty())
                    <span class="rounded-full bg-indigo-100 px-3 py-1 text-indigo-700">{{ $approvedWaiting->count() }} approved, waiting to send</span>
                @endif
                @if($inProgressCampaigns->isNotEmpty())
                    <span class="rounded-full bg-blue-100 px-3 py-1 text-blue-700">{{ $inProgressCampaigns->count() }} in progress</span>
                @endif
                <span class="rounded-full bg-emerald-100 px-3 py-1 text-emerald-700">{{ $completedCampaigns->count() }} completed</span>

                <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-600">
                    {{ number_format(optional($propInfo->theStats)->xWebViews ?? 0) }} flyer views
                </span>
                @if($createdDate)
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-600">Created {{ $createdDate }}</span>
                @endif
            </div>

        </div>

        {{-- TWO COLUMNS on large screens: the send request and campaign lists on the
             left, the flyer beside them in a sticky right column so it stays in view
             while you work. Below lg everything stacks in the order the sections
             appear here (request, subject, flyer, campaigns). Each section pins itself
             to a column with lg:col-start-*; card bottom margins (mb-6) give the
             vertical spacing, so only a column gap is needed. --}}
        <div class="lg:grid lg:grid-cols-[minmax(0,1fr)_minmax(0,460px)] xl:grid-cols-[minmax(0,1fr)_minmax(0,600px)] lg:gap-x-6">

        {{-- SEND REQUEST --}}
        <div class="{{ $card }} overflow-hidden mb-6 lg:col-start-1">

            <div class="{{ $cardHead }}">
                <h2 class="font-semibold text-slate-900">Send Request</h2>

                @if($awaitingApproval->isNotEmpty())
                    <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800">Awaiting approval</span>
                @elseif($pendingRequests->isNotEmpty())
                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">Approved</span>
                @else
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">No pending request</span>
                @endif
            </div>

            {{-- AGENT + SEND DETAILS --}}
            <div class="grid gap-x-8 gap-y-5 px-5 py-5 md:grid-cols-2 text-sm">

                <div>
                    <div class="{{ $eyebrow }} mb-1">Agent</div>
                    <div class="font-semibold text-slate-900">{{ $agent->agtFullName ?? 'Unknown agent' }}</div>
                    @if($agent?->agtEmail)
                        <div class="text-slate-600">{{ $agent->agtEmail }}</div>
                    @endif
                    @if($agent?->agtMainPhone)
                        <div class="text-slate-600">{{ $agent->agtMainPhone }}</div>
                    @endif
                    <div class="mt-1 text-slate-500">Credits remaining: {{ number_format($agent->remCreds ?? 0) }}</div>
                </div>

                <div>
                    <div class="{{ $eyebrow }} mb-1">Requested</div>
                    <div class="text-slate-900">
                        {{ $pendingRequests->isNotEmpty() ? \Carbon\Carbon::parse($pendingRequests->first()->emRequest)->format('M j, Y g:i A') : '-' }}
                    </div>

                    <div class="{{ $eyebrow }} mt-3 mb-1">Email Subject</div>
                    <div class="text-slate-900">{{ $pendingRequests->first()->emSubject ?? $subject ?: 'No subject' }}</div>
                </div>

                <div>
                    <div class="{{ $eyebrow }} mb-1">Open Houses</div>
                    @forelse($openHouses as $line)
                        <div class="text-slate-900">{{ $line }}</div>
                    @empty
                        <div class="text-slate-500">None</div>
                    @endforelse
                </div>

                <div>
                    <div class="{{ $eyebrow }} mb-1">Agent Bonus / Price Reduced</div>

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

            {{-- REQUESTED AREAS: waiting to be approved, or approved and waiting to send --}}
            <div class="border-t border-slate-200">
                <div class="{{ $eyebrow }} px-5 pt-4">Requested Areas ({{ $pendingRequests->count() }})</div>

                <div class="divide-y divide-slate-100">
                    @forelse($pendingRequests as $req)
                        @include('admin.flyer.campRow', [
                            'stage'      => (int) $req->authorized === 1 ? 'approved' : 'awaiting',
                            'area'       => $areaLabel($req),
                            'adminAdded' => $req->isAdminAdded(),
                            'subject'    => null,
                            'emails'     => $req->totalEmails ?? ($data['emailCounts'][$req->emArea] ?? null),
                            'cid'        => $req->cid,
                            'requested'  => $req->emRequest,
                            'started'    => $req->emStart,
                            'completed'  => $req->emComplete,
                        ])
                    @empty
                        <div class="px-5 py-6 text-sm text-slate-500">This flyer has no pending send request.</div>
                    @endforelse
                </div>
            </div>

            {{-- ACTIONS --}}
            <div class="space-y-5 border-t border-slate-200 bg-slate-50 px-5 py-5">

                {{-- ADD FREE AREA: right under the requested areas it adds to --}}
                <div>
                    <div class="{{ $eyebrow }}">Add a Free Area</div>

                    <p class="mb-3 mt-1 text-sm text-slate-500">
                        No credit is charged to the agent. The area is added as unapproved and is approved together with this flyer's other waiting areas.
                    </p>

                    <form method="POST"
                          action="{{ route('admin.campaignAddArea', $propInfo->id) }}"
                          class="flex flex-col gap-3 md:flex-row">
                        @csrf

                        <select name="area" required class="flex-1 rounded-xl border border-slate-300 bg-white px-4 py-3">
                            <option value="">Select Area</option>
                            @foreach($addableAreas as $areaKey => $count)
                                <option value="{{ $areaKey }}">
                                    {{ $data['areaLabels'][$areaKey] ?? strtoupper($areaKey) }} ({{ number_format($count) }} contacts)
                                </option>
                            @endforeach
                        </select>

                        <button type="submit"
                                class="rounded-xl bg-[#214e9b] px-5 py-3 font-semibold text-white hover:bg-[#1b3f80]">
                            Add Free Area
                        </button>
                    </form>

                    @if($addableAreas->isEmpty())
                        <p class="mt-3 text-sm text-slate-500">
                            Every area is already waiting or in progress for this flyer.
                        </p>
                    @endif
                </div>

                {{-- APPROVE / UNAPPROVE --}}
                @if($awaitingApproval->isNotEmpty() || $approvedWaiting->isNotEmpty())
                    <div class="border-t border-slate-200 pt-5">
                        <div class="{{ $eyebrow }}">Approval</div>

                        <p class="mb-3 mt-1 text-sm text-slate-500">
                            Approving marks every waiting area ready for the mail system to send (add any free areas first).
                            Unapproving takes approved areas that have not started back to awaiting approval; areas already in progress are not affected.
                        </p>

                        <div class="flex flex-wrap gap-3">

                            @if($awaitingApproval->isNotEmpty())
                                <form method="POST"
                                      action="{{ route('admin.campaignApprove', $propInfo->id) }}"
                                      onsubmit="return confirm('Approve {{ $awaitingApproval->count() }} area(s) for {{ addslashes($propInfo->xFullStreet ?? 'this flyer') }}?');">
                                    @csrf
                                    <button type="submit"
                                            class="rounded-xl bg-emerald-600 px-5 py-3 font-semibold text-white hover:bg-emerald-700">
                                        Approve {{ $awaitingApproval->count() }} {{ $awaitingApproval->count() === 1 ? 'Area' : 'Areas' }}
                                    </button>
                                </form>
                            @endif

                            @if($approvedWaiting->isNotEmpty())
                                <form method="POST"
                                      action="{{ route('admin.campaignUnapprove', $propInfo->id) }}"
                                      onsubmit="return confirm('Unapprove {{ $approvedWaiting->count() }} area(s) for {{ addslashes($propInfo->xFullStreet ?? 'this flyer') }}? They will not be sent until approved again.');">
                                    @csrf
                                    <button type="submit"
                                            class="rounded-xl border border-red-300 bg-white px-5 py-3 font-semibold text-red-600 hover:bg-red-50">
                                        Unapprove {{ $approvedWaiting->count() }} {{ $approvedWaiting->count() === 1 ? 'Area' : 'Areas' }}
                                    </button>
                                </form>
                            @endif

                        </div>
                    </div>
                @endif

            </div>

        </div>

        {{-- SUBJECT --}}
        <div class="{{ $card }} p-5 mb-6 lg:col-start-1">

            <label class="{{ $eyebrow }} mb-2 block">Email Subject</label>

            <div class="flex flex-col gap-3 md:flex-row">

                <input
                    type="text"
                    value="{{ $subject }}"
                    class="flex-1 rounded-xl border border-slate-300 px-4 py-3"
                >

                <button
                    class="rounded-xl bg-[#214e9b] px-5 py-3 font-semibold text-white hover:bg-[#1b3f80]">
                    Save Subject
                </button>

            </div>

        </div>

        {{-- FLYER: right-hand column spanning the left-hand sections on large screens
             (sticky, and scrollable within itself if it's taller than the window); a
             normal full-width card in the stack on small screens. The flyer scales
             itself to whatever width it's given (see scaleFlyer). --}}
        <div class="mb-6 lg:col-start-2 lg:row-start-1 lg:row-span-4 lg:sticky lg:top-[88px] lg:self-start lg:max-h-[calc(100vh-104px)] lg:overflow-y-auto">

            <div class="{{ $card }} p-4">

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

        </div>

        {{-- IN PROGRESS: campaigns that have started but not finished. Campaigns still
             WAITING to start are listed once, under "Requested Areas" in the Send Request
             card (that is where they are approved), so they are not repeated here. --}}
        <div class="{{ $card }} overflow-hidden mb-6 lg:col-start-1">

            <div class="{{ $cardHead }}">
                <h2 class="font-semibold text-slate-900">In Progress ({{ $inProgressCampaigns->count() }})</h2>
            </div>

            <div class="divide-y divide-slate-100">

                @forelse($inProgressCampaigns as $camp)
                    @include('admin.flyer.campRow', [
                        'stage'      => 'progress',
                        'area'       => $areaName($camp['emArea']),
                        'adminAdded' => $camp['admin_added'] ?? false,
                        'subject'    => $camp['emSubject'],
                        'cid'        => $camp['cid'],
                        'requested'  => $camp['emRequest'],
                        'started'    => $camp['emStart'],
                        'completed'  => null,
                    ])
                @empty
                    <div class="px-5 py-8 text-center text-sm text-slate-500">No campaigns are in progress.</div>
                @endforelse

            </div>

        </div>

        {{-- COMPLETED --}}
        <div class="{{ $card }} overflow-hidden lg:col-start-1">

            <div class="{{ $cardHead }}">
                <h2 class="font-semibold text-slate-900">Completed Campaigns ({{ $completedCampaigns->count() }})</h2>

                <span class="text-sm text-slate-500">{{ number_format($completedCampaigns->sum('totalEmails')) }} emails sent</span>
            </div>

            <div class="divide-y divide-slate-100">

                @forelse($completedCampaigns as $camp)
                    @include('admin.flyer.campRow', [
                        'stage'      => 'complete',
                        'area'       => $areaName($camp['emArea']),
                        'adminAdded' => $camp['admin_added'] ?? false,
                        'subject'    => $camp['emSubject'],
                        'emails'     => $camp['totalEmails'],
                        'cid'        => $camp['cid'],
                        'requested'  => $camp['emRequest'],
                        'started'    => $camp['emStart'],
                        'completed'  => $camp['emComplete'],
                    ])
                @empty
                    <div class="px-5 py-8 text-center text-sm text-slate-500">No completed campaigns found.</div>
                @endforelse

            </div>

        </div>

        </div>{{-- /two-column grid --}}

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

    // The flyer's height changes after this first pass as its photos and logos
    // load (a longer flyer was clipped until a reload, when they were cached),
    // so measure again whenever the flyer or its column changes size.
    window.addEventListener('load', scaleFlyer);

    if ('ResizeObserver' in window) {
        const observer = new ResizeObserver(scaleFlyer);
        const flyerPanel = document.querySelector('#flyer-scale-wrapper .flyer-panel.active');
        const stage = document.querySelector('.flyer-stage');

        if (flyerPanel) observer.observe(flyerPanel);
        if (stage) observer.observe(stage);
    }

});
</script>

</body>
</html>