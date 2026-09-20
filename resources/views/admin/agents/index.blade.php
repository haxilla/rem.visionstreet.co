@include('public.layout.head')

<body data-section="admin" class="relative bg-white min-h-screen font-sans text-gray-800">

@include('admin.layout.nav')

@php
    $activeAgents  = $data['activeAgents'] ?? collect();
    $noStartAgents = $data['noStartAgents'] ?? collect();

    $noStartCreditAgents = $data['noStartCreditAgents'] ?? collect();
    $noPhotoAgents       = $data['noPhotoAgents'] ?? collect();
    $noLogoAgents        = $data['noLogoAgents'] ?? collect();

    // which list is showing (each has its own page parameter)
    $currentTab = match (true) {
        request()->has('duplicates')           => 'duplicates',
        request()->has('nologo_page')          => 'nologo',
        request()->has('nophoto_page')         => 'nophoto',
        request()->has('nostartcredits_page')  => 'nostartcredits',
        request()->has('nostart_page')         => 'nostart',
        default                                => 'active',
    };

    // admin Settings > "Confirm agent deletion": does Delete ask first?
    $confirmDelete = $data['confirmDelete'] ?? true;
@endphp

{{-- MAIN --}}
<main class="min-h-screen bg-[#f4f7fb] pt-24">
    <div class="px-4 py-6 sm:px-6 lg:px-10 lg:py-8">

        {{-- HEADER --}}
        <div class="rounded-[24px] bg-white px-5 py-6 shadow-[0_12px_35px_rgba(15,23,42,0.06)] sm:px-8 sm:py-7">
            <div class="text-[12px] font-semibold uppercase tracking-[0.22em] text-[#214e9b]/70">
                Admin
            </div>

            <h1 class="mt-2 text-2xl font-semibold text-slate-900 sm:text-[32px]">
                Agents
            </h1>

            <p class="mt-2 text-[14px] text-slate-600">
                View and manage agent accounts in the Realty Emails system.
            </p>
        </div>

        @if(session('status'))
            <div class="mt-6 rounded-2xl border border-emerald-300 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-semibold text-red-700">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        {{-- SEARCH AGENTS --}}
        <div class="mt-6 rounded-[24px] bg-white p-4 shadow-[0_12px_35px_rgba(15,23,42,0.06)] sm:mt-8 sm:p-6">
            <label class="block text-sm font-semibold text-slate-700">
                Search Agents
            </label>

            <div class="relative mt-2">
                <input
                    id="agentSearch"
                    type="text"
                    placeholder="Search by name, email, username, or ID..."
                    autocomplete="off"
                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-[#214e9b] focus:outline-none focus:ring-2 focus:ring-[#214e9b]/20"
                >

                <div
                    id="agentSearchResults"
                    class="absolute z-50 mt-2 hidden w-full overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg"
                ></div>
            </div>
        </div>

        {{-- AGENTS TABS --}}
        <div class="mt-6 rounded-[24px] bg-white shadow-[0_12px_35px_rgba(15,23,42,0.06)] overflow-hidden sm:mt-10">

            {{-- TAB BUTTONS --}}
            <div class="border-b border-slate-200 bg-slate-50 px-4 pt-5 sm:px-6">
                <div class="flex flex-wrap gap-2">

                    <a
                        href="{{ request()->url() }}"
                        class="{{ $currentTab === 'active' ? 'bg-[#214e9b] text-white shadow' : 'bg-slate-200 text-slate-700' }} rounded-t-xl px-4 py-3 text-sm font-semibold sm:px-5"
                    >
                        With Start Date

                        <span class="ml-2 rounded-full bg-white/20 px-2 py-0.5 text-xs">
                            {{ method_exists($activeAgents, 'total') ? $activeAgents->total() : 0 }}
                        </span>
                    </a>

                    <a
                        href="{{ request()->url() }}?nostart_page=1"
                        class="{{ $currentTab === 'nostart' ? 'bg-[#214e9b] text-white shadow' : 'bg-slate-200 text-slate-700' }} rounded-t-xl px-4 py-3 text-sm font-semibold sm:px-5"
                    >
                        No Start Date

                        <span class="ml-2 rounded-full bg-white/20 px-2 py-0.5 text-xs">
                            {{ method_exists($noStartAgents, 'total') ? $noStartAgents->total() : 0 }}
                        </span>
                    </a>

                    <a
                        href="{{ request()->url() }}?nostartcredits_page=1"
                        class="{{ $currentTab === 'nostartcredits' ? 'bg-[#214e9b] text-white shadow' : 'bg-slate-200 text-slate-700' }} rounded-t-xl px-4 py-3 text-sm font-semibold sm:px-5"
                    >
                        No Start Date + Credits

                        <span class="ml-2 rounded-full bg-white/20 px-2 py-0.5 text-xs">
                            {{ method_exists($noStartCreditAgents, 'total') ? $noStartCreditAgents->total() : 0 }}
                        </span>
                    </a>

                    <a
                        href="{{ request()->url() }}?nophoto_page=1"
                        class="{{ $currentTab === 'nophoto' ? 'bg-[#214e9b] text-white shadow' : 'bg-slate-200 text-slate-700' }} rounded-t-xl px-4 py-3 text-sm font-semibold sm:px-5"
                    >
                        No Photo

                        <span class="ml-2 rounded-full bg-white/20 px-2 py-0.5 text-xs">
                            {{ method_exists($noPhotoAgents, 'total') ? $noPhotoAgents->total() : 0 }}
                        </span>
                    </a>

                    <a
                        href="{{ request()->url() }}?nologo_page=1"
                        class="{{ $currentTab === 'nologo' ? 'bg-[#214e9b] text-white shadow' : 'bg-slate-200 text-slate-700' }} rounded-t-xl px-4 py-3 text-sm font-semibold sm:px-5"
                    >
                        No Logo

                        <span class="ml-2 rounded-full bg-white/20 px-2 py-0.5 text-xs">
                            {{ method_exists($noLogoAgents, 'total') ? $noLogoAgents->total() : 0 }}
                        </span>
                    </a>

                    <a
                        href="{{ request()->url() }}?duplicates=1"
                        class="{{ $currentTab === 'duplicates' ? 'bg-[#214e9b] text-white shadow' : 'bg-slate-200 text-slate-700' }} rounded-t-xl px-4 py-3 text-sm font-semibold sm:px-5"
                    >
                        Duplicate Logins

                        <span class="ml-2 rounded-full bg-white/20 px-2 py-0.5 text-xs">
                            {{ $data['dupCount'] ?? 0 }}
                        </span>
                    </a>

                </div>
            </div>

            {{-- TAB CONTENT --}}
            <div class="p-4 sm:p-6">

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

                        @if(method_exists($activeAgents, 'total'))
                            <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
                                {{ $activeAgents->total() }} agents
                            </span>
                        @endif
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

                        @if(method_exists($noStartAgents, 'total'))
                            <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                {{ $noStartAgents->total() }} records
                            </span>
                        @endif
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

                                <label class="mt-4 flex cursor-pointer items-center gap-2 border-t border-slate-100 pt-3 text-sm font-semibold text-slate-700">
                                    <input type="checkbox" name="ids[]" value="{{ $agent->id }}" form="bulkDeleteForm"
                                           class="agent-check h-5 w-5 rounded border-slate-300">
                                    Select for deletion
                                </label>
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

                                        <td class="whitespace-nowrap px-6 py-3 text-right">
                                            <input type="checkbox" name="ids[]" value="{{ $agent->id }}" form="bulkDeleteForm"
                                                   aria-label="Select {{ $displayName }} for deletion"
                                                   class="agent-check h-5 w-5 rounded border-slate-300">
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
