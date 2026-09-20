@include('public.layout.head')

<body data-section="admin" class="relative bg-white min-h-screen font-sans text-gray-800">

@include('admin.layout.nav')

@php
    $activeAgents  = $data['activeAgents'] ?? collect();
    $noStartAgents = $data['noStartAgents'] ?? collect();

    $noStartCreditAgents = $data['noStartCreditAgents'] ?? collect();
    $noPhotoAgents       = $data['noPhotoAgents'] ?? collect();
    $noLogoAgents        = $data['noLogoAgents'] ?? collect();

    $hasDuplicates = ($data['dupCount'] ?? 0) > 0;

    // which list is showing (each has its own page parameter)
    $currentTab = match (true) {
        // the tab only exists while some login email still has more than one account
        request()->has('duplicates') && $hasDuplicates => 'duplicates',
        request()->has('nologo_page')          => 'nologo',
        request()->has('nophoto_page')         => 'nophoto',
        request()->has('nostartcredits_page')  => 'nostartcredits',
        request()->has('nostart_page')         => 'nostart',
        default                                => 'active',
    };

    // admin Settings > "Confirm agent deletion": does Delete ask first?
    $confirmDelete = $data['confirmDelete'] ?? true;
@endphp

{{--
    Layout for this page is PLAIN CSS on purpose (the .ag-* rules below): it shows correctly
    whether or not the site stylesheet has been rebuilt since the last deploy, the same reason
    the dashboard's campaign rows use it. The rows / tables inside the tabs keep their existing
    classes; the rules here only tighten their spacing.
--}}
<style>
    .ag-wrap    { max-width: 1400px; margin: 0 auto; }

    /* title + search on one line */
    .ag-top     { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 14px; }
    .ag-title   { display: flex; align-items: baseline; gap: 10px; min-width: 0; }
    .ag-title h1 { margin: 0; font-size: 22px; line-height: 1.2; font-weight: 650; letter-spacing: -.01em; color: #0f172a; }
    .ag-title span { font-size: 13px; color: #64748b; white-space: nowrap; }

    .ag-search  { position: relative; flex: 1 1 320px; max-width: 480px; }
    .ag-search input { width: 100%; height: 40px; border: 1px solid #d5dbe6; border-radius: 12px; background: #fff;
                       padding: 0 14px 0 38px; font-size: 14px; color: #0f172a; outline: none;
                       box-shadow: 0 1px 2px rgba(15,23,42,.04); }
    .ag-search input:focus { border-color: #214e9b; box-shadow: 0 0 0 3px rgba(33,78,155,.12); }
    .ag-search svg { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px;
                     color: #94a3b8; pointer-events: none; }

    /* one calm card holds the tabs and the list */
    .ag-card    { background: #fff; border-radius: 18px; box-shadow: 0 8px 28px rgba(15,23,42,.06); overflow: hidden; }
    .ag-tabs    { display: flex; gap: 2px; padding: 0 10px; border-bottom: 1px solid #e8edf5; overflow-x: auto;
                  white-space: nowrap; scrollbar-width: none; }
    .ag-tabs::-webkit-scrollbar { display: none; }
    .ag-tab     { display: inline-flex; align-items: center; gap: 8px; padding: 13px 12px; margin-bottom: -1px;
                  font-size: 13px; font-weight: 600; color: #64748b; text-decoration: none;
                  border-bottom: 2px solid transparent; transition: color .12s, border-color .12s; }
    .ag-tab:hover { color: #0f172a; }
    .ag-tab.is-on { color: #214e9b; border-bottom-color: #214e9b; }
    .ag-count   { padding: 3px 7px; border-radius: 999px; background: #eef2f8; color: #475569;
                  font-size: 11px; font-weight: 700; line-height: 1; }
    .ag-tab.is-on .ag-count { background: #214e9b; color: #fff; }
    .ag-tab.is-warn .ag-count { background: #fef3c7; color: #92400e; }
    .ag-tab.is-warn.is-on { color: #b45309; border-bottom-color: #d97706; }
    .ag-tab.is-warn.is-on .ag-count { background: #d97706; color: #fff; }

    .ag-body    { padding: 14px 16px 16px; }
    .ag-group   { scroll-margin-top: 96px; }   /* a group scrolled to by its #anchor clears the fixed navbar */

    /* status / error messages: slim */
    .ag-alert   { margin-bottom: 12px; padding: 10px 14px; border-radius: 12px; font-size: 13px; font-weight: 600; border: 1px solid; }
    .ag-alert.ok  { background: #ecfdf5; border-color: #a7f3d0; color: #047857; }
    .ag-alert.bad { background: #fef2f2; border-color: #fecaca; color: #b91c1c; }

    /* the tab's own heading + help line, tightened */
    .ag-body > .mb-5 { margin-bottom: 10px; }
    /* the active tab already names the list, so its heading is kept for screen readers only
       and the one-line description under it is what shows */
    .ag-body h2 { position: absolute; width: 1px; height: 1px; margin: -1px; padding: 0; overflow: hidden;
                  clip: rect(0 0 0 0); white-space: nowrap; border: 0; }
    .ag-body h2 + p { margin-top: 0; font-size: 12.5px; color: #64748b; }

    /* denser tables (the markup inside keeps its classes) */
    .ag-body table thead th { padding: 9px 14px; font-size: 11px; }
    .ag-body table tbody td { padding: 9px 14px; }
    .ag-body > .overflow-x-auto { border-radius: 12px; }
    #bulkDeleteForm { padding: 8px 14px; border-radius: 12px; }

    @media (max-width: 640px) {
        .ag-body { padding: 10px; }
        .ag-search { max-width: none; flex-basis: 100%; }
    }
</style>

@php
    $pagerTotal = fn ($p) => method_exists($p, 'total') ? $p->total() : 0;

    // Every agent is in exactly one of these three lists, so together they are all of them.
    $totalAgents = $pagerTotal($activeAgents) + $pagerTotal($noStartAgents) + $pagerTotal($noStartCreditAgents);

    $tabs = [
        ['key' => 'active',         'label' => 'With Start Date',        'query' => '',                       'count' => $pagerTotal($activeAgents)],
        ['key' => 'nostart',        'label' => 'No Start Date',          'query' => '?nostart_page=1',        'count' => $pagerTotal($noStartAgents)],
        ['key' => 'nostartcredits', 'label' => 'No Start Date + Credits', 'query' => '?nostartcredits_page=1', 'count' => $pagerTotal($noStartCreditAgents)],
        ['key' => 'nophoto',        'label' => 'No Photo',               'query' => '?nophoto_page=1',        'count' => $pagerTotal($noPhotoAgents)],
        ['key' => 'nologo',         'label' => 'No Logo',                'query' => '?nologo_page=1',         'count' => $pagerTotal($noLogoAgents)],
    ];

    // Only while some login email still has more than one account.
    if ($hasDuplicates) {
        $tabs[] = ['key' => 'duplicates', 'label' => 'Duplicate Logins', 'query' => '?duplicates=1', 'count' => $data['dupCount'] ?? 0, 'warn' => true];
    }
@endphp

{{-- MAIN --}}
<main class="min-h-screen bg-[#f4f7fb] pt-24">
    <div class="ag-wrap px-4 py-4 sm:px-6 lg:px-8">

        {{-- TITLE + SEARCH: one slim line --}}
        <div class="ag-top">
            <div class="ag-title">
                <h1>Agents</h1>
                <span>{{ number_format($totalAgents) }} accounts</span>
            </div>

            <div class="ag-search">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>
                </svg>

                <input
                    id="agentSearch"
                    type="text"
                    placeholder="Search by name, email, username or ID"
                    aria-label="Search agents"
                    autocomplete="off"
                >

                <div
                    id="agentSearchResults"
                    class="absolute z-50 mt-2 hidden w-full overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg"
                ></div>
            </div>
        </div>

        @if(session('status'))
            <div class="ag-alert ok">{{ session('status') }}</div>
        @endif

        @if($errors->any())
            <div class="ag-alert bad">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        {{-- TABS + LIST --}}
        <div class="ag-card">

            <nav class="ag-tabs" aria-label="Agent lists">
                @foreach($tabs as $tab)
                    <a href="{{ request()->url() }}{{ $tab['query'] }}"
                       class="ag-tab {{ $currentTab === $tab['key'] ? 'is-on' : '' }} {{ !empty($tab['warn']) ? 'is-warn' : '' }}"
                       @if($currentTab === $tab['key']) aria-current="page" @endif>
                        {{ $tab['label'] }}
                        <span class="ag-count">{{ number_format($tab['count']) }}</span>
                    </a>
                @endforeach
            </nav>

            {{-- TAB CONTENT --}}
            <div class="ag-body">

                @if($currentTab === 'active')

                    {{-- ACTIVE AGENTS HEADER --}}
                    <div class="mb-5 flex flex-wrap items-center justify-between gap-2">
                        <div>
                            <h2 class="text-xl font-semibold text-slate-900">
                                Agents With Start Date
                            </h2>

                            <p class="mt-1 text-sm text-slate-500">
                                Agent accounts that currently have a start date.
                            </p>
                        </div>
                    </div>

                    {{-- ACTIVE AGENTS: MOBILE CARDS --}}
                    <div class="space-y-3 xl:hidden">

                        @forelse($activeAgents as $agent)

                            @php
                                $displayName = trim(($agent->agtFirst ?? '') . ' ' . ($agent->agtLast ?? ''));

                                if ($displayName === '') {
                                    $displayName = $agent->agtFullName
                                        ?: $agent->agtUname
                                        ?: $agent->agtEmail
                                        ?: 'No Name';
                                }
                            @endphp

                            <div class="rounded-2xl border border-slate-200 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <a href="/admin/agentView/{{ $agent->id }}" class="block truncate text-sm font-semibold text-slate-900 hover:underline">
                                            {{ $displayName }}
                                        </a>
                                        <div class="truncate text-xs text-slate-500">
                                            {{ $agent->agtEmail ?: '—' }}
                                        </div>
                                    </div>
                                    <a href="/admin/agentLogin/{{ $agent->id }}" class="shrink-0 text-xs font-semibold text-[#214e9b] hover:underline">
                                        ID {{ $agent->id }}
                                    </a>
                                </div>

                                <div class="mt-3 grid grid-cols-3 gap-2 text-xs">
                                    <div>
                                        <div class="text-slate-400">Credits</div>
                                        <div class="font-semibold text-slate-700">{{ $agent->remCreds ?? 0 }}</div>
                                    </div>
                                    <div>
                                        <div class="text-slate-400">Start</div>
                                        <div class="font-semibold text-slate-700">
                                            {{ $agent->startDate ? \Carbon\Carbon::parse($agent->startDate)->format('m/d/Y') : '—' }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-slate-400">Expires</div>
                                        <div class="font-semibold text-slate-700">
                                            {{ $agent->expireDate ? \Carbon\Carbon::parse($agent->expireDate)->format('m/d/Y') : '—' }}
                                        </div>
                                    </div>
                                </div>

                            </div>

                        @empty

                            <div class="rounded-2xl border border-dashed border-slate-200 px-4 py-10 text-center text-sm text-slate-500">
                                No agents with start dates found.
                            </div>

                        @endforelse

                    </div>

                    {{-- ACTIVE AGENTS: TABLE (md and up) --}}
                    <div class="hidden overflow-x-auto rounded-2xl border border-slate-200 xl:block">

                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        ID
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        Agent
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        Email
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        Credits
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        Start Date
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        Expire Date
                                    </th>

                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-100 bg-white">

                                @forelse($activeAgents as $agent)

                                    @php
                                        $displayName = trim(($agent->agtFirst ?? '') . ' ' . ($agent->agtLast ?? ''));

                                        if ($displayName === '') {
                                            $displayName = $agent->agtFullName
                                                ?: $agent->agtUname
                                                ?: $agent->agtEmail
                                                ?: 'No Name';
                                        }
                                    @endphp

                                    <tr class="hover:bg-slate-50">

                                        <td class="whitespace-nowrap px-6 py-3 text-sm">
                                            <a href="/admin/agentLogin/{{ $agent->id }}" class="font-semibold text-[#214e9b] hover:underline">
                                                {{ $agent->id }}
                                            </a>
                                        </td>

                                        <td class="whitespace-nowrap px-6 py-3 text-sm font-semibold text-slate-900">
                                            <a href="/admin/agentView/{{ $agent->id }}" class="hover:underline">
                                                {{ $displayName }}
                                            </a>
                                        </td>

                                        <td class="whitespace-nowrap px-6 py-3 text-sm text-slate-700">
                                            {{ $agent->agtEmail ?: '—' }}
                                        </td>

                                        <td class="whitespace-nowrap px-6 py-3 text-sm text-slate-700">
                                            {{ $agent->remCreds ?? 0 }}
                                        </td>

                                        <td class="whitespace-nowrap px-6 py-3 text-sm text-slate-700">
                                            {{ $agent->startDate
                                                ? \Carbon\Carbon::parse($agent->startDate)->format('m/d/Y')
                                                : '—' }}
                                        </td>

                                        <td class="whitespace-nowrap px-6 py-3 text-sm text-slate-700">
                                            {{ $agent->expireDate
                                                ? \Carbon\Carbon::parse($agent->expireDate)->format('m/d/Y')
                                                : '—' }}
                                        </td>

                                    </tr>

                                @empty

                                    <tr>
                                        <td colspan="6" class="px-6 py-10 text-center text-sm text-slate-500">
                                            No agents with start dates found.
                                        </td>
                                    </tr>

                                @endforelse

                            </tbody>
                        </table>

                    </div>

                    @if(method_exists($activeAgents, 'links'))
                        <div class="mt-5">
                            {{ $activeAgents->links() }}
                        </div>
                    @endif

                @elseif($currentTab === 'nostartcredits')

                    @include('admin.agents.agentReviewPanel', [
                        'agents'        => $noStartCreditAgents,
                        'title'         => 'No Start Date, With Credits',
                        'description'   => 'Agents without a start date who still have credits. Open one to set a start date or adjust the credits.',
                        'emptyText'     => 'No agents without a start date have credits.',
                        'showStartDate' => false,
                        'showCredits'   => true,
                    ])

                @elseif($currentTab === 'nophoto')

                    @include('admin.agents.agentReviewPanel', [
                        'agents'        => $noPhotoAgents,
                        'title'         => 'Agents Without a Photo',
                        'description'   => 'Agents with a start date who have no photo on file.',
                        'emptyText'     => 'Every agent with a start date has a photo.',
                        'showStartDate' => true,
                        'showCredits'   => true,
                    ])

                @elseif($currentTab === 'nologo')

                    @include('admin.agents.agentReviewPanel', [
                        'agents'        => $noLogoAgents,
                        'title'         => 'Agents Without a Logo',
                        'description'   => 'Agents with a start date who have no logo on file.',
                        'emptyText'     => 'Every agent with a start date has a logo.',
                        'showStartDate' => true,
                        'showCredits'   => true,
                    ])

                @elseif($currentTab === 'duplicates')

                    @include('admin.agents.duplicateLogins', [
                        'groups' => $data['dupGroups'] ?? collect(),
                        'total'  => $data['dupCount'] ?? 0,
                        'confirmDelete' => $confirmDelete,
                    ])

                @else

                    {{-- NO START DATE HEADER --}}
                    <div class="mb-5 flex flex-wrap items-center justify-between gap-2">
                        <div>
                            <h2 class="text-xl font-semibold text-slate-900">
                                Agents With No Start Date
                            </h2>

                            <p class="mt-1 text-sm text-slate-500">
                                Agent records without a start date and without credits. These can be reviewed or deleted.
                                (Agents without a start date who do have credits are on the "No Start Date + Credits" tab.)
                            </p>
                        </div>
                    </div>

                    {{-- BULK DELETE BAR. The checkboxes in the phone cards and the table below
                         are attached to this form with form="bulkDeleteForm", so they can live
                         in either layout. Selection covers the agents on THIS page only. --}}
                    @if(method_exists($noStartAgents, 'count') && $noStartAgents->count() > 0)
                        <form id="bulkDeleteForm"
                              method="POST"
                              action="{{ route('admin.agentsDeleteMany') }}"
                              data-confirm="{{ $confirmDelete ? '1' : '0' }}"
                              class="mb-3 flex flex-wrap items-center justify-between gap-3 rounded-2xl bg-slate-50 px-4 py-3">
                            @csrf

                            <label class="flex cursor-pointer items-center gap-2 text-sm font-semibold text-slate-700">
                                <input type="checkbox" id="selectAllNoStart" class="h-5 w-5 rounded border-slate-300">
                                Select all on this page
                            </label>

                            <div class="flex items-center gap-3">
                                <span id="selectedCount" class="text-sm text-slate-500">0 selected</span>

                                <button type="submit"
                                        id="bulkDeleteBtn"
                                        disabled
                                        class="rounded-lg !bg-red-600 px-4 py-2 text-xs font-semibold !text-white shadow-sm hover:!bg-red-700 disabled:cursor-not-allowed disabled:opacity-40">
                                    Delete selected
                                </button>
                            </div>
                        </form>
                    @endif

                    {{-- NO START DATE: MOBILE CARDS --}}
                    <div class="space-y-3 xl:hidden">

                        @forelse($noStartAgents as $agent)

                            @php
                                $displayName = trim(($agent->agtFirst ?? '') . ' ' . ($agent->agtLast ?? ''));

                                if ($displayName === '') {
                                    $displayName = $agent->agtFullName
                                        ?: $agent->agtUname
                                        ?: $agent->agtEmail
                                        ?: 'No Name';
                                }
                            @endphp

                            <div class="rounded-2xl border border-slate-200 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <a href="/admin/agentView/{{ $agent->id }}" class="block truncate text-sm font-semibold text-slate-900 hover:underline">
                                            {{ $displayName }}
                                        </a>
                                        <div class="truncate text-xs text-slate-500">
                                            {{ $agent->agtEmail ?: '—' }}
                                        </div>
                                    </div>
                                    <a href="/admin/agentLogin/{{ $agent->id }}" class="shrink-0 text-xs font-semibold text-[#214e9b] hover:underline">
                                        ID {{ $agent->id }}
                                    </a>
                                </div>

                                @php
                                    $b       = ($data['noStartBlockers'] ?? [])[(int) $agent->id] ?? ['flyers' => 0, 'orders' => 0, 'campaigns' => 0, 'reasons' => []];
                                    $canDel  = \App\Support\DuplicateAccounts::canDelete($b);
                                @endphp

                                @if($canDel)
                                    <label class="mt-4 flex cursor-pointer items-center gap-2 border-t border-slate-100 pt-3 text-sm font-semibold text-slate-700">
                                        <input type="checkbox" name="ids[]" value="{{ $agent->id }}" form="bulkDeleteForm"
                                               class="agent-check h-5 w-5 rounded border-slate-300">
                                        Select for deletion
                                    </label>
                                @else
                                    <div class="mt-4 border-t border-slate-100 pt-3 text-xs font-semibold text-amber-700">
                                        Can't be deleted: it has {{ \App\Support\DuplicateAccounts::describe($b) }}.
                                    </div>
                                @endif
                            </div>

                        @empty

                            <div class="rounded-2xl border border-dashed border-slate-200 px-4 py-10 text-center text-sm text-slate-500">
                                No agents without start dates found.
                            </div>

                        @endforelse

                    </div>

                    {{-- NO START DATE: TABLE (md and up) --}}
                    <div class="hidden overflow-x-auto rounded-2xl border border-slate-200 xl:block">

                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        ID
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        Agent
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        Email
                                    </th>

                                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        Select
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-100 bg-white">

                                @forelse($noStartAgents as $agent)

                                    @php
                                        $displayName = trim(($agent->agtFirst ?? '') . ' ' . ($agent->agtLast ?? ''));

                                        if ($displayName === '') {
                                            $displayName = $agent->agtFullName
                                                ?: $agent->agtUname
                                                ?: $agent->agtEmail
                                                ?: 'No Name';
                                        }
                                    @endphp

                                    <tr class="hover:bg-slate-50">

                                        <td class="whitespace-nowrap px-6 py-3 text-sm">
                                            <a href="/admin/agentLogin/{{ $agent->id }}" class="font-semibold text-[#214e9b] hover:underline">
                                                {{ $agent->id }}
                                            </a>
                                        </td>

                                        <td class="whitespace-nowrap px-6 py-3 text-sm font-semibold text-slate-900">
                                            <a href="/admin/agentView/{{ $agent->id }}" class="hover:underline">
                                                {{ $displayName }}
                                            </a>
                                        </td>

                                        <td class="whitespace-nowrap px-6 py-3 text-sm text-slate-700">
                                            {{ $agent->agtEmail ?: '—' }}
                                        </td>

                                        @php
                                            $b      = ($data['noStartBlockers'] ?? [])[(int) $agent->id] ?? ['flyers' => 0, 'orders' => 0, 'campaigns' => 0, 'reasons' => []];
                                            $canDel = \App\Support\DuplicateAccounts::canDelete($b);
                                        @endphp

                                        <td class="whitespace-nowrap px-6 py-3 text-right">
                                            @if($canDel)
                                                <input type="checkbox" name="ids[]" value="{{ $agent->id }}" form="bulkDeleteForm"
                                                       aria-label="Select {{ $displayName }} for deletion"
                                                       class="agent-check h-5 w-5 rounded border-slate-300">
                                            @else
                                                <span class="text-xs font-semibold text-amber-700">Has {{ \App\Support\DuplicateAccounts::describe($b) }}</span>
                                            @endif
                                        </td>

                                    </tr>

                                @empty

                                    <tr>
                                        <td colspan="4" class="px-6 py-10 text-center text-sm text-slate-500">
                                            No agents without start dates found.
                                        </td>
                                    </tr>

                                @endforelse

                            </tbody>
                        </table>

                    </div>

                    @if(method_exists($noStartAgents, 'links'))
                        <div class="mt-5">
                            {{ $noStartAgents->links() }}
                        </div>
                    @endif

                @endif

            </div>

        </div>

    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('agentSearch');
    const results = document.getElementById('agentSearchResults');

    let timer = null;
    let latest = 0;   // only the newest search may draw its results

    // Agent names and emails are typed by the agents themselves, so they must
    // never be put into the page as raw HTML.
    const esc = value => String(value ?? '').replace(/[&<>"']/g, c => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    }[c]));

    input.addEventListener('input', function () {
        clearTimeout(timer);

        const q = input.value.trim();

        if (q.length < 2) {
            latest++;   // discard any search still in flight
            results.innerHTML = '';
            results.classList.add('hidden');
            return;
        }

        timer = setTimeout(() => {
            const mine = ++latest;

            fetch(`/admin/agent/search?q=${encodeURIComponent(q)}`)
                .then(response => response.json())
                .then(data => {
                    // a slower, older search finishing late must not overwrite a newer one
                    if (mine !== latest) return;

                    const agents = data.agents || [];

                    if (!agents.length) {
                        results.innerHTML = `
                            <div class="px-4 py-3 text-sm text-slate-500">
                                No agents found.
                            </div>
                        `;
                        results.classList.remove('hidden');
                        return;
                    }

                    results.innerHTML = agents.map(agent => `
                        <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-4 py-3 hover:bg-slate-50">
                            <div class="min-w-0">
                                <a href="/admin/agentView/${esc(agent.id)}" class="text-sm font-semibold text-slate-900 hover:underline">
                                    ${esc(agent.name)}
                                </a>
                                <div class="break-words text-xs text-slate-500">
                                    ${esc(agent.email || 'No email')}
                                    ${agent.username && agent.username !== agent.email ? ' &middot; login ' + esc(agent.username) : ''}
                                    &mdash; ID:
                                    <a href="/admin/agentLogin/${esc(agent.id)}" class="font-semibold text-[#214e9b] hover:underline">${esc(agent.id)}</a>
                                </div>
                            </div>

                            ${agent.startDate
                                ? `<span class="shrink-0 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700">Started ${esc(agent.startDate)}</span>`
                                : `<span class="shrink-0 rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-semibold text-amber-700">No start date</span>`}
                        </div>
                    `).join('');

                    if (data.more) {
                        results.innerHTML += `
                            <div class="px-4 py-3 text-xs font-semibold text-slate-500">
                                Showing the first ${agents.length} matches - type more of the name to narrow it down.
                            </div>
                        `;
                    }

                    results.classList.remove('hidden');
                });
        }, 250);
    });

    document.addEventListener('click', function (e) {
        if (!input.contains(e.target) && !results.contains(e.target)) {
            results.classList.add('hidden');
        }
    });
});
</script>

{{-- No Start Date: select several agents and delete them together. --}}
<script>
(function () {
    var form = document.getElementById('bulkDeleteForm');
    if (!form) return;

    var boxes   = Array.prototype.slice.call(document.querySelectorAll('.agent-check'));
    var all     = document.getElementById('selectAllNoStart');
    var countEl = document.getElementById('selectedCount');
    var btn     = document.getElementById('bulkDeleteBtn');

    // Every agent is rendered twice (phone cards + desktop table, only one is
    // visible at a time), so count each agent once by its id.
    function selectedIds() {
        var seen = {};
        boxes.forEach(function (b) { if (b.checked) seen[b.value] = true; });
        return Object.keys(seen);
    }

    function totalAgents() {
        var seen = {};
        boxes.forEach(function (b) { seen[b.value] = true; });
        return Object.keys(seen).length;
    }

    function refresh() {
        var n = selectedIds().length;

        countEl.textContent = n + ' selected';
        btn.disabled = n === 0;
        all.checked = n > 0 && n === totalAgents();
        all.indeterminate = n > 0 && n < totalAgents();
    }

    // Keep an agent's two copies in step, so resizing the window can't leave
    // one ticked and the other not.
    boxes.forEach(function (b) {
        b.addEventListener('change', function () {
            boxes.forEach(function (other) {
                if (other.value === b.value) other.checked = b.checked;
            });
            refresh();
        });
    });

    all.addEventListener('change', function () {
        boxes.forEach(function (b) { b.checked = all.checked; });
        refresh();
    });

    form.addEventListener('submit', function (e) {
        var n = selectedIds().length;

        if (n === 0) {
            e.preventDefault();
            return;
        }

        // admin Settings > "Confirm agent deletion" (data-confirm = 1 / 0)
        if (form.dataset.confirm === '1' &&
            !confirm('Delete ' + n + (n === 1 ? ' agent' : ' agents') + '? This cannot be undone.')) {
            e.preventDefault();
        }
    });

    // Back/forward can restore ticked boxes without firing change events.
    window.addEventListener('pageshow', refresh);

    refresh();
})();
</script>

@include('public.layout.footer')

</body>
</html>
