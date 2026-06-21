@include('public.layout.head')

<body data-section="admin" class="relative bg-white min-h-screen font-sans text-gray-800">

@include('admin.layout.nav')

<main
    class="transition-all duration-300 min-h-screen pt-24 relative"
    :class="collapsed ? 'ml-20' : 'ml-64'"
>
    <div class="mx-2 sm:mx-4 lg:mx-10">
        <div class="pageswap p-2 sm:p-4 lg:p-6 w-full">

            @php
                /*
                    This page now displays individual propdelivnow campaigns.

                    Status rules:
                    - Waiting: emRequest <= now AND emStart is empty
                    - In Progress: emStart filled AND emFinished empty
                    - Complete: emRequest, emStart, emFinished all filled

                    Authorization:
                    Update the field list inside campaignAuthorized() if your actual authorization
                    column is named differently.
                */

                $waitingFlyerCamps    = $data['waitingFlyerCamps'] ?? collect();
                $inProgressFlyerCamps = $data['inProgressFlyerCamps'] ?? collect();
                $completeFlyerCamps   = $data['completeFlyerCamps'] ?? collect();

                $directCampaigns      = $data['campaigns'] ?? collect();

                $campaignValue = function ($campaign, $keys, $default = null) {
                    foreach ((array) $keys as $key) {
                        if (is_array($campaign) && array_key_exists($key, $campaign)) {
                            return $campaign[$key];
                        }

                        if (is_object($campaign) && isset($campaign->{$key})) {
                            return $campaign->{$key};
                        }
                    }

                    return $default;
                };

                $campaignFlyer = function ($campaign) use ($campaignValue) {
                    return $campaignValue($campaign, ['flyer', 'theFlyer'], null);
                };

                $campaignAuthorized = function ($campaign) use ($campaignValue) {
                    $value = $campaignValue($campaign, [
                        'emAuthorized',
                        'campAuthorized',
                        'authorized',
                        'isAuthorized',
                        'xAuthorized',
                        'emAuth',
                        'auth'
                    ], null);

                    return in_array($value, [1, '1', true, 'true', 'TRUE', 'yes', 'YES', 'Y', 'y'], true);
                };

                $campaignDate = function ($campaign, $keys) use ($campaignValue) {
                    return $campaignValue($campaign, $keys, null);
                };

                $formatDate = function ($date) {
                    if (!$date) {
                        return 'N/A';
                    }

                    try {
                        return \Carbon\Carbon::parse($date)->format('M j, Y g:i A');
                    } catch (\Exception $e) {
                        return $date;
                    }
                };

                $isEmptyDate = function ($date) {
                    return empty($date) || $date === '0000-00-00' || $date === '0000-00-00 00:00:00';
                };

                $allCampaigns = collect();

                $addCampaigns = function ($items) use (&$allCampaigns) {
                    if (!$items) {
                        return;
                    }

                    foreach (collect($items) as $key => $item) {
                        if ($item instanceof \Illuminate\Support\Collection) {
                            foreach ($item as $campaign) {
                                $allCampaigns->push($campaign);
                            }
                        } elseif (is_array($item) && isset($item[0])) {
                            foreach ($item as $campaign) {
                                $allCampaigns->push($campaign);
                            }
                        } else {
                            $allCampaigns->push($item);
                        }
                    }
                };

                $addCampaigns($directCampaigns);
                $addCampaigns($waitingFlyerCamps);
                $addCampaigns($inProgressFlyerCamps);
                $addCampaigns($completeFlyerCamps);

                $now = now();

                $waitingCampaigns = $allCampaigns->filter(function ($campaign) use ($campaignDate, $isEmptyDate, $now) {
                    $emRequest = $campaignDate($campaign, ['emRequest']);
                    $emStart   = $campaignDate($campaign, ['emStart']);

                    if ($isEmptyDate($emRequest) || !$isEmptyDate($emStart)) {
                        return false;
                    }

                    try {
                        return \Carbon\Carbon::parse($emRequest)->lte($now);
                    } catch (\Exception $e) {
                        return false;
                    }
                })->values();

                $waitingAuthorized = $waitingCampaigns->filter(function ($campaign) use ($campaignAuthorized) {
                    return $campaignAuthorized($campaign);
                })->values();

                $waitingUnauthorized = $waitingCampaigns->filter(function ($campaign) use ($campaignAuthorized) {
                    return !$campaignAuthorized($campaign);
                })->values();

                $inProgressCampaigns = $allCampaigns->filter(function ($campaign) use ($campaignDate, $isEmptyDate) {
                    $emStart    = $campaignDate($campaign, ['emStart']);
                    $emFinished = $campaignDate($campaign, ['emFinished', 'emComplete']);

                    return !$isEmptyDate($emStart) && $isEmptyDate($emFinished);
                })->values();

                $completedCampaigns = $allCampaigns->filter(function ($campaign) use ($campaignDate, $isEmptyDate) {
                    $emRequest  = $campaignDate($campaign, ['emRequest']);
                    $emStart    = $campaignDate($campaign, ['emStart']);
                    $emFinished = $campaignDate($campaign, ['emFinished', 'emComplete']);

                    return !$isEmptyDate($emRequest) && !$isEmptyDate($emStart) && !$isEmptyDate($emFinished);
                })->sortByDesc(function ($campaign) use ($campaignDate) {
                    return $campaignDate($campaign, ['emFinished', 'emComplete']);
                })->take(10)->values();

                $getThumbUrl = function ($campaign) use ($campaignFlyer) {
                    $flyer = $campaignFlyer($campaign);

                    if (!$flyer) {
                        return null;
                    }

                    $photo = $flyer?->thePhotos?->first();
                    $meta  = $flyer?->theMeta;

                    if ($photo && $meta && $meta->zipDir && $meta->mlsDir && $photo->photoName) {
                        return "https://realtyrepublic.com/hqphotos/{$meta->zipDir}/{$meta->mlsDir}/{$photo->photoName}";
                    }

                    return null;
                };

                $getAddress = function ($campaign) use ($campaignValue, $campaignFlyer) {
                    $directAddress = $campaignValue($campaign, ['address', 'xFullStreet', 'fullAddress'], null);

                    if ($directAddress) {
                        return $directAddress;
                    }

                    $flyer = $campaignFlyer($campaign);

                    return $flyer?->xFullStreet ?? 'No Address';
                };

                $getFlyerId = function ($campaign) use ($campaignValue, $campaignFlyer) {
                    $directId = $campaignValue($campaign, ['flyer_id', 'propflyer_id', 'ufid', 'flyerId'], null);

                    if ($directId) {
                        return $directId;
                    }

                    $flyer = $campaignFlyer($campaign);

                    return $flyer?->id ?? null;
                };

                $getAgent = function ($campaign) use ($campaignFlyer) {
                    $flyer = $campaignFlyer($campaign);

                    return $flyer?->theAgent ?? null;
                };

                $renderCampaignCard = function ($campaign, $status) use (
                    $data,
                    $campaignValue,
                    $campaignDate,
                    $campaignAuthorized,
                    $formatDate,
                    $getThumbUrl,
                    $getAddress,
                    $getFlyerId,
                    $getAgent
                ) {
                    $thumbUrl   = $getThumbUrl($campaign);
                    $address    = $getAddress($campaign);
                    $flyerId    = $getFlyerId($campaign);
                    $agent      = $getAgent($campaign);

                    $subject    = $campaignValue($campaign, ['emSubject'], 'N/A');
                    $label      = $campaignValue($campaign, ['campLabel'], 'N/A');
                    $emails     = $campaignValue($campaign, ['emailCount', 'emCount', 'totalEmails', 'countEmails'], null);
                    $area       = $campaignValue($campaign, ['emArea'], 'N/A');
                    $areaKey    = strtolower(trim($area));
                    $emailCount = $data['emailCounts'][$areaKey] ?? 0;

                    $emRequest  = $campaignDate($campaign, ['emRequest']);
                    $emStart    = $campaignDate($campaign, ['emStart']);
                    $emFinished = $campaignDate($campaign, ['emFinished', 'emComplete']);

                    $authorized = $campaignAuthorized($campaign);
                @endphp

                <div class="border-b border-slate-200 px-2 py-2 hover:bg-slate-50">

                    <div class="hidden lg:flex lg:items-center lg:gap-4 text-sm">

                        <div class="w-32 shrink-0 truncate">
                            {{ $area }}
                        </div>

                        <div class="flex-1 truncate">
                            {{ $address }}
                        </div>

                        <div class="w-48 shrink-0 truncate">
                            {{ $agent?->agtFullName ?? 'N/A' }}
                        </div>

                        <div class="w-24 shrink-0 text-right">
                            {{ number_format($emailCount) }}
                        </div>

                        <div class="w-32 shrink-0 text-right">
                            @if($status === 'progress')
                                {{ $formatDate($emStart) }}
                            @elseif($status === 'completed')
                                {{ $formatDate($emFinished) }}
                            @else
                                {{ $formatDate($emRequest) }}
                            @endif
                        </div>

                    </div>

                    <div class="lg:hidden text-sm">

                        <div class="flex-1 truncate pl-3">
                            {{ $address }}
                        </div>

                        <div class="text-xs text-slate-500 mt-1">
                            Area: {{ $area }}
                            · Agent: {{ $agent?->agtFullName ?? 'N/A' }}
                            · Emails: {{ $emails ? number_format($emails) : '-' }}
                        </div>

                        <div class="text-xs text-slate-400 mt-1">
                            {{ $formatDate($emRequest) }}
                        </div>

                    </div>

                </div>

                @php
                };
            @endphp

            <div class="min-h-screen bg-[#f4f7fb]">
                <div class="flex min-h-screen">

                    {{-- DESKTOP SIDEBAR --}}
                    <div class="hidden lg:block">
                        @include('admin.includes.sidebar')
                    </div>

                    {{-- MOBILE SIDEBAR OVERLAY --}}
                    <div id="adminMobileMenuOverlay" class="fixed inset-0 z-[60] hidden lg:hidden">
                        <div class="absolute inset-0 bg-slate-900/50" id="adminMobileMenuBackdrop"></div>

                        <div class="relative h-full w-72 bg-white shadow-2xl p-6">
                            <div class="mb-6 flex items-center justify-between">
                                <div class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">
                                    Admin Menu
                                </div>

                                <button
                                    type="button"
                                    id="adminMobileMenuClose"
                                    class="rounded-lg bg-slate-100 px-3 py-2 text-sm font-semibold text-slate-600"
                                >
                                    Close
                                </button>
                            </div>

                            <nav class="space-y-2">
                                <a href="/admin" class="block rounded-xl px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                    Dashboard
                                </a>

                                <a href="/admin/flyers" class="block rounded-xl px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                    Flyers
                                </a>

                                <a href="/admin/campaigns" class="block rounded-xl bg-[#214e9b] px-4 py-3 text-sm font-semibold text-white">
                                    Campaigns
                                </a>

                                <a href="/admin/agents" class="block rounded-xl px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                    Agents
                                </a>

                                <a href="/admin/logout" class="block rounded-xl px-4 py-3 text-sm font-semibold text-red-600 hover:bg-red-50">
                                    Log Out
                                </a>
                            </nav>
                        </div>
                    </div>

                    {{-- MAIN --}}
                    <main class="flex-1 px-2 py-4 sm:px-4 lg:px-10 lg:py-8">

                        {{-- MOBILE MENU BAR --}}
                        <div class="mb-4 flex items-center justify-between rounded-2xl bg-white px-4 py-3 shadow-sm lg:hidden">
                            <div>
                                <div class="text-xs font-semibold uppercase tracking-[0.18em] text-[#214e9b]/70">
                                    Admin
                                </div>
                                <div class="text-sm font-semibold text-slate-900">
                                    Campaigns
                                </div>
                            </div>

                            <button
                                type="button"
                                id="adminMobileMenuButton"
                                class="rounded-xl bg-[#214e9b] px-4 py-2 text-sm font-semibold text-white shadow"
                            >
                                Menu
                            </button>
                        </div>

                        {{-- HEADER --}}
                        <div class="rounded-[20px] bg-white px-4 py-5 sm:px-6 sm:py-6 lg:px-8 lg:py-7 shadow-[0_12px_35px_rgba(15,23,42,0.06)]">
                            <div class="text-[12px] font-semibold uppercase tracking-[0.22em] text-[#214e9b]/70">
                                Campaign Operations
                            </div>

                            <h1 class="mt-2 text-[26px] font-semibold text-slate-900 sm:text-[32px]">
                                Campaign Overview
                            </h1>

                            <p class="mt-2 text-[14px] text-slate-600">
                                Individual campaign status by authorization, delivery progress, and recent completions.
                            </p>
                        </div>

                        {{-- SUMMARY CARDS --}}
                        <div class="mt-6 grid grid-cols-2 gap-3 lg:grid-cols-4">
                            <div class="rounded-2xl bg-white p-4 shadow-sm">
                                <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Waiting
                                </div>
                                <div class="mt-2 text-2xl font-semibold text-slate-900">
                                    {{ $waitingCampaigns->count() }}
                                </div>
                            </div>

                            <div class="rounded-2xl bg-white p-4 shadow-sm">
                                <div class="text-xs font-semibold uppercase tracking-wide text-emerald-600">
                                    Authorized
                                </div>
                                <div class="mt-2 text-2xl font-semibold text-slate-900">
                                    {{ $waitingAuthorized->count() }}
                                </div>
                            </div>

                            <div class="rounded-2xl bg-white p-4 shadow-sm">
                                <div class="text-xs font-semibold uppercase tracking-wide text-red-600">
                                    Unauthorized
                                </div>
                                <div class="mt-2 text-2xl font-semibold text-slate-900">
                                    {{ $waitingUnauthorized->count() }}
                                </div>
                            </div>

                            <div class="rounded-2xl bg-white p-4 shadow-sm">
                                <div class="text-xs font-semibold uppercase tracking-wide text-blue-600">
                                    In Progress
                                </div>
                                <div class="mt-2 text-2xl font-semibold text-slate-900">
                                    {{ $inProgressCampaigns->count() }}
                                </div>
                            </div>
                        </div>

                        {{-- CAMPAIGN DASHBOARD --}}
                        <div class="mt-6 rounded-[24px] bg-white shadow-[0_12px_35px_rgba(15,23,42,0.06)] overflow-hidden">

                            {{-- MAIN TAB BUTTONS --}}
                            <div class="border-b border-slate-200 bg-slate-50 px-3 py-3 sm:px-6 sm:pt-5 sm:pb-0">
                                <div class="grid grid-cols-1 gap-2 sm:flex sm:flex-wrap">

                                    <button
                                        type="button"
                                        class="campaign-tab-btn bg-[#214e9b] text-white rounded-xl sm:rounded-b-none sm:rounded-t-xl px-5 py-3 text-sm font-semibold shadow"
                                        data-tab="waiting"
                                    >
                                        Waiting
                                        <span class="ml-2 rounded-full bg-white/20 px-2 py-0.5 text-xs">
                                            {{ $waitingCampaigns->count() }}
                                        </span>
                                    </button>

                                    <button
                                        type="button"
                                        class="campaign-tab-btn bg-slate-200 text-slate-700 rounded-xl sm:rounded-b-none sm:rounded-t-xl px-5 py-3 text-sm font-semibold"
                                        data-tab="progress"
                                    >
                                        In Progress
                                        <span class="ml-2 rounded-full bg-white/50 px-2 py-0.5 text-xs">
                                            {{ $inProgressCampaigns->count() }}
                                        </span>
                                    </button>

                                    <button
                                        type="button"
                                        class="campaign-tab-btn bg-slate-200 text-slate-700 rounded-xl sm:rounded-b-none sm:rounded-t-xl px-5 py-3 text-sm font-semibold"
                                        data-tab="completed"
                                    >
                                        Completed
                                        <span class="ml-2 rounded-full bg-white/50 px-2 py-0.5 text-xs">
                                            {{ $completedCampaigns->count() }}
                                        </span>
                                    </button>

                                </div>
                            </div>

                            {{-- TAB CONTENT --}}
                            <div class="p-3 sm:p-6">

                                {{-- WAITING --}}
                                <div class="campaign-panel" id="tab-waiting">

                                    <div class="mb-5 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                                        <div>
                                            <h2 class="text-xl font-semibold text-slate-900">
                                                Waiting Campaigns
                                            </h2>

                                            <p class="mt-1 text-sm text-slate-500">
                                                Requested campaigns where the request time has passed and delivery has not started.
                                            </p>
                                        </div>

                                        <span class="w-fit rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                            {{ $waitingCampaigns->count() }} waiting
                                        </span>
                                    </div>

                                    {{-- WAITING SUB MENU --}}
                                    <div class="mb-5 grid grid-cols-1 gap-2 sm:flex sm:flex-wrap">
                                        <button
                                            type="button"
                                            class="waiting-tab-btn bg-slate-200 text-slate-700 rounded-xl px-4 py-2 text-sm font-semibold"
                                            data-waiting-tab="authorized"
                                        >
                                            Authorized
                                            <span class="ml-2 rounded-full bg-white/20 px-2 py-0.5 text-xs">
                                                {{ $waitingAuthorized->count() }}
                                            </span>
                                        </button>

                                        <button
                                            type="button"
                                            class="waiting-tab-btn bg-emerald-600 text-white rounded-xl px-4 py-2 text-sm font-semibold shadow"
                                            data-waiting-tab="unauthorized"
                                        >
                                            Unauthorized
                                            <span class="ml-2 rounded-full bg-white/50 px-2 py-0.5 text-xs">
                                                {{ $waitingUnauthorized->count() }}
                                            </span>
                                        </button>
                                    </div>

                                    {{-- UNAUTHORIZED WAITING --}}
                                    <div class="waiting-panel" id="waiting-unauthorized">

                                        <div class="hidden lg:flex lg:items-center lg:gap-4 text-xs font-semibold uppercase tracking-wide text-slate-500 border-b border-slate-300 px-2 py-2">
                                            <div class="w-32 shrink-0">Area</div>
                                            <div class="flex-1">Address</div>
                                            <div class="w-48 shrink-0">Agent</div>
                                            <div class="w-24 shrink-0 text-right">Emails</div>
                                            <div class="w-32 shrink-0 text-right">Requested</div>
                                        </div>

                                        <div>
                                            @forelse($waitingUnauthorized as $campaign)
                                                @php $renderCampaignCard($campaign, 'waiting'); @endphp
                                            @empty
                                                <div class="rounded-2xl border border-dashed border-slate-300 p-8 text-center text-sm text-slate-500">
                                                    No unauthorized waiting campaigns found.
                                                </div>
                                            @endforelse
                                        </div>

                                    </div>

                                    {{-- AUTHORIZED WAITING --}}
                                    <div class="waiting-panel hidden" id="waiting-authorized">

                                        <div class="hidden lg:flex lg:items-center lg:gap-4 text-xs font-semibold uppercase tracking-wide text-slate-500 border-b border-slate-300 px-2 py-2">
                                            <div class="w-32 shrink-0">Area</div>
                                            <div class="flex-1">Address</div>
                                            <div class="w-48 shrink-0">Agent</div>
                                            <div class="w-24 shrink-0 text-right">Emails</div>
                                            <div class="w-32 shrink-0 text-right">Requested</div>
                                        </div>

                                        <div>
                                            @forelse($waitingAuthorized as $campaign)
                                                @php $renderCampaignCard($campaign, 'waiting'); @endphp
                                            @empty
                                                <div class="rounded-2xl border border-dashed border-slate-300 p-8 text-center text-sm text-slate-500">
                                                    No authorized waiting campaigns found.
                                                </div>
                                            @endforelse
                                        </div>

                                    </div>

                                </div>

                                {{-- IN PROGRESS --}}
                                <div class="campaign-panel hidden" id="tab-progress">

                                    <div class="mb-5 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                                        <div>
                                            <h2 class="text-xl font-semibold text-slate-900">
                                                In Progress Campaigns
                                            </h2>

                                            <p class="mt-1 text-sm text-slate-500">
                                                Campaigns that have started delivery but have not finished.
                                            </p>
                                        </div>

                                        <span class="w-fit rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
                                            {{ $inProgressCampaigns->count() }} in progress
                                        </span>
                                    </div>

                                    <div class="hidden lg:flex lg:items-center lg:gap-4 text-xs font-semibold uppercase tracking-wide text-slate-500 border-b border-slate-300 px-2 py-2">

                                        <div class="w-32 shrink-0">
                                            Area
                                        </div>

                                        <div class="flex-1">
                                            Address
                                        </div>

                                        <div class="w-48 shrink-0">
                                            Agent
                                        </div>

                                        <div class="w-24 shrink-0 text-right">
                                            Emails
                                        </div>

                                        <div class="w-32 shrink-0 text-right">
                                            Started
                                        </div>

                                    </div>

                                    <div>

                                        @forelse($inProgressCampaigns as $campaign)
                                            @php $renderCampaignCard($campaign, 'progress'); @endphp
                                        @empty
                                            <div class="rounded-2xl border border-dashed border-slate-300 p-8 text-center text-sm text-slate-500">
                                                No in progress campaigns found.
                                            </div>
                                        @endforelse

                                    </div>

                                </div>

                                {{-- COMPLETED --}}
                                <div class="campaign-panel hidden" id="tab-completed">

                                    <div class="mb-5 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                                        <div>
                                            <h2 class="text-xl font-semibold text-slate-900">
                                                Recently Completed Campaigns
                                            </h2>

                                            <p class="mt-1 text-sm text-slate-500">
                                                Last 10 completed campaigns, sorted by finish time.
                                            </p>
                                        </div>

                                        <span class="w-fit rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                                            Last {{ $completedCampaigns->count() }}
                                        </span>
                                    </div>

                                    <div class="hidden lg:flex lg:items-center lg:gap-4 text-xs font-semibold uppercase tracking-wide text-slate-500 border-b border-slate-300 px-2 py-2">

                                        <div class="w-32 shrink-0">
                                            Area
                                        </div>

                                        <div class="flex-1">
                                            Address
                                        </div>

                                        <div class="w-48 shrink-0">
                                            Agent
                                        </div>

                                        <div class="w-24 shrink-0 text-right">
                                            Emails
                                        </div>

                                        <div class="w-32 shrink-0 text-right">
                                            Finished
                                        </div>

                                    </div>

                                    <div>

                                        @forelse($completedCampaigns as $campaign)
                                            @php $renderCampaignCard($campaign, 'completed'); @endphp
                                        @empty
                                            <div class="rounded-2xl border border-dashed border-slate-300 p-8 text-center text-sm text-slate-500">
                                                No completed campaigns found.
                                            </div>
                                        @endforelse

                                    </div>

                                </div>

                            </div>
                        </div>

                    </main>

                </div>
            </div>

        </div>
    </div>
</main>

@include('public.layout.footer')

<script>
document.addEventListener('DOMContentLoaded', function () {

    const buttons = document.querySelectorAll('.campaign-tab-btn');
    const panels = document.querySelectorAll('.campaign-panel');

    buttons.forEach(function(button) {

        button.addEventListener('click', function() {

            const target = button.dataset.tab;
            const targetPanel = document.getElementById('tab-' + target);

            panels.forEach(function(panel) {
                panel.classList.add('hidden');
            });

            if (targetPanel) {
                targetPanel.classList.remove('hidden');
            }

            buttons.forEach(function(btn) {
                btn.classList.remove(
                    'bg-[#214e9b]',
                    'text-white',
                    'shadow'
                );

                btn.classList.add(
                    'bg-slate-200',
                    'text-slate-700'
                );
            });

            button.classList.remove(
                'bg-slate-200',
                'text-slate-700'
            );

            button.classList.add(
                'bg-[#214e9b]',
                'text-white',
                'shadow'
            );

        });

    });

    const waitingButtons = document.querySelectorAll('.waiting-tab-btn');
    const waitingPanels = document.querySelectorAll('.waiting-panel');

    waitingButtons.forEach(function(button) {

        button.addEventListener('click', function() {

            const target = button.dataset.waitingTab;
            const targetPanel = document.getElementById('waiting-' + target);

            waitingPanels.forEach(function(panel) {
                panel.classList.add('hidden');
            });

            if (targetPanel) {
                targetPanel.classList.remove('hidden');
            }

            waitingButtons.forEach(function(btn) {
                btn.classList.remove(
                    'bg-emerald-600',
                    'text-white',
                    'shadow'
                );

                btn.classList.add(
                    'bg-slate-200',
                    'text-slate-700'
                );
            });

            button.classList.remove(
                'bg-slate-200',
                'text-slate-700'
            );

            button.classList.add(
                'bg-emerald-600',
                'text-white',
                'shadow'
            );

        });

    });

    // MOBILE MENU

    const mobileMenuButton = document.getElementById('adminMobileMenuButton');
    const mobileMenuOverlay = document.getElementById('adminMobileMenuOverlay');
    const mobileMenuBackdrop = document.getElementById('adminMobileMenuBackdrop');
    const mobileMenuClose = document.getElementById('adminMobileMenuClose');

    if (mobileMenuButton && mobileMenuOverlay) {

        mobileMenuButton.addEventListener('click', function () {
            mobileMenuOverlay.classList.remove('hidden');
        });

        if (mobileMenuBackdrop) {
            mobileMenuBackdrop.addEventListener('click', function () {
                mobileMenuOverlay.classList.add('hidden');
            });
        }

        if (mobileMenuClose) {
            mobileMenuClose.addEventListener('click', function () {
                mobileMenuOverlay.classList.add('hidden');
            });
        }

    }

});
</script>

</body>
</html>