@include('public.layout.head')

<body data-section="admin" class="relative bg-white min-h-screen font-sans text-gray-800">

@include('admin.layout.nav')

@php
    $activeAgents  = $data['activeAgents'] ?? collect();
    $noStartAgents = $data['noStartAgents'] ?? collect();

    $currentTab = request()->has('nostart_page') ? 'nostart' : 'active';
@endphp

<div class="flex min-h-screen bg-[#f4f7fb] pt-[72px]">

    @include('admin.includes.sidebar')

    {{-- MAIN --}}
    <main class="min-w-0 flex-1 px-4 py-6 sm:px-6 lg:px-10 lg:py-8">

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
                    <div class="space-y-3 md:hidden">

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
                                        <div class="truncate text-sm font-semibold text-slate-900">
                                            {{ $displayName }}
                                        </div>
                                        <div class="truncate text-xs text-slate-500">
                                            {{ $agent->agtEmail ?: '—' }}
                                        </div>
                                    </div>
                                    <div class="shrink-0 text-xs text-slate-400">
                                        ID {{ $agent->id }}
                                    </div>
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

                                <div class="mt-4 flex gap-2">
                                    <a
                                        href="/admin/agentView/{{ $agent->id }}"
                                        class="flex-1 rounded-lg border border-slate-200 px-3 py-2 text-center text-xs font-semibold text-slate-700 hover:bg-slate-100"
                                    >
                                        View
                                    </a>

                                    <a
                                        href="/admin/agentLogin/{{ $agent->id }}"
                                        class="flex-1 rounded-lg bg-[#16213e] px-3 py-2 text-center text-xs font-semibold text-white hover:bg-[#22315a]"
                                    >
                                        Login
                                    </a>
                                </div>
                            </div>

                        @empty

                            <div class="rounded-2xl border border-dashed border-slate-200 px-4 py-10 text-center text-sm text-slate-500">
                                No agents with start dates found.
                            </div>

                        @endforelse

                    </div>

                    {{-- ACTIVE AGENTS: TABLE (md and up) --}}
                    <div class="hidden overflow-x-auto rounded-2xl border border-slate-200 md:block">

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

                                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        Actions
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

                                        <td class="whitespace-nowrap px-6 py-3 text-sm text-slate-500">
                                            {{ $agent->id }}
                                        </td>

                                        <td class="whitespace-nowrap px-6 py-3 text-sm font-semibold text-slate-900">
                                            {{ $displayName }}
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

                                        <td class="whitespace-nowrap px-6 py-3 text-right">
                                            <div class="flex justify-end gap-2">

                                                <a
                                                    href="/admin/agentView/{{ $agent->id }}"
                                                    class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100"
                                                >
                                                    View
                                                </a>

                                                <a
                                                    href="/admin/agentLogin/{{ $agent->id }}"
                                                    class="rounded-lg bg-[#16213e] px-3 py-1.5 text-xs font-semibold text-white hover:bg-[#22315a]"
                                                >
                                                    Login
                                                </a>

                                            </div>
                                        </td>

                                    </tr>

                                @empty

                                    <tr>
                                        <td colspan="7" class="px-6 py-10 text-center text-sm text-slate-500">
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

                @else

                    {{-- NO START DATE HEADER --}}
                    <div class="mb-5 flex flex-wrap items-center justify-between gap-2">
                        <div>
                            <h2 class="text-xl font-semibold text-slate-900">
                                Agents With No Start Date
                            </h2>

                            <p class="mt-1 text-sm text-slate-500">
                                Agent records without a start date. These can be reviewed or deleted.
                            </p>
                        </div>

                        @if(method_exists($noStartAgents, 'total'))
                            <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                {{ $noStartAgents->total() }} records
                            </span>
                        @endif
                    </div>

                    {{-- NO START DATE: MOBILE CARDS --}}
                    <div class="space-y-3 md:hidden">

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
                                        <div class="truncate text-sm font-semibold text-slate-900">
                                            {{ $displayName }}
                                        </div>
                                        <div class="truncate text-xs text-slate-500">
                                            {{ $agent->agtEmail ?: '—' }}
                                        </div>
                                    </div>
                                    <div class="shrink-0 text-xs text-slate-400">
                                        ID {{ $agent->id }}
                                    </div>
                                </div>

                                <div class="mt-4 flex gap-2">
                                    <a
                                        href="/admin/agents/{{ $agent->id }}"
                                        class="flex-1 rounded-lg border border-slate-200 px-3 py-2 text-center text-xs font-semibold text-slate-700 hover:bg-slate-100"
                                    >
                                        View
                                    </a>

                                    <a
                                        href="/admin/agentDelete/{{ $agent->id }}"
                                        class="flex-1 rounded-lg !bg-red-600 px-3 py-2 text-center text-xs font-semibold !text-white shadow-sm hover:!bg-red-700"
                                    >
                                        Delete
                                    </a>
                                </div>
                            </div>

                        @empty

                            <div class="rounded-2xl border border-dashed border-slate-200 px-4 py-10 text-center text-sm text-slate-500">
                                No agents without start dates found.
                            </div>

                        @endforelse

                    </div>

                    {{-- NO START DATE: TABLE (md and up) --}}
                    <div class="hidden overflow-x-auto rounded-2xl border border-slate-200 md:block">

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
                                        Actions
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

                                        <td class="whitespace-nowrap px-6 py-3 text-sm text-slate-500">
                                            {{ $agent->id }}
                                        </td>

                                        <td class="whitespace-nowrap px-6 py-3 text-sm font-semibold text-slate-900">
                                            {{ $displayName }}
                                        </td>

                                        <td class="whitespace-nowrap px-6 py-3 text-sm text-slate-700">
                                            {{ $agent->agtEmail ?: '—' }}
                                        </td>

                                        <td class="whitespace-nowrap px-6 py-3 text-right">
                                            <div class="flex justify-end gap-2">

                                                <a
                                                    href="/admin/agents/{{ $agent->id }}"
                                                    class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100"
                                                >
                                                    View
                                                </a>

                                                <a
                                                    href="/admin/agentDelete/{{ $agent->id }}"
                                                    class="inline-flex items-center rounded-lg !bg-red-600 px-3 py-1.5 text-xs font-semibold !text-white shadow-sm hover:!bg-red-700"
                                                >
                                                    Delete
                                                </a>

                                            </div>
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

    </main>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('agentSearch');
    const results = document.getElementById('agentSearchResults');

    let timer = null;

    input.addEventListener('input', function () {
        clearTimeout(timer);

        const q = input.value.trim();

        if (q.length < 2) {
            results.innerHTML = '';
            results.classList.add('hidden');
            return;
        }

        timer = setTimeout(() => {
            fetch(`/admin/agent/search?q=${encodeURIComponent(q)}`)
                .then(response => response.json())
                .then(data => {
                    results.innerHTML = '';

                    if (!data.length) {
                        results.innerHTML = `
                            <div class="px-4 py-3 text-sm text-slate-500">
                                No agents found.
                            </div>
                        `;
                        results.classList.remove('hidden');
                        return;
                    }

                    data.forEach(agent => {
                        results.innerHTML += `
                            <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3 hover:bg-slate-50">
                                <div>
                                    <div class="text-sm font-semibold text-slate-900">
                                        ${agent.name}
                                    </div>
                                    <div class="text-xs text-slate-500">
                                        ${agent.email ?? 'No email'} — ID: ${agent.id}
                                    </div>
                                </div>

                                <div class="flex gap-2">
                                    <a href="/admin/agentView/${agent.id}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                                        View
                                    </a>

                                    <a href="/admin/agentLogin/${agent.id}" class="rounded-lg bg-[#16213e] px-3 py-1.5 text-xs font-semibold text-white hover:bg-[#22315a]">
                                        Login
                                    </a>
                                </div>
                            </div>
                        `;
                    });

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

@include('public.layout.footer')

</body>
</html>
