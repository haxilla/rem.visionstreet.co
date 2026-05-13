@include('public.layout.head')

<body data-section="admin" class="relative bg-white min-h-screen font-sans text-gray-800">

@include('public.layout.nav')

@php
    $activeAgents = $data['activeAgents'] ?? collect();
    $noStartAgents = $data['noStartAgents'] ?? collect();

    $currentTab = request()->has('nostart_page') ? 'nostart' : 'active';

    $agents = $currentTab === 'nostart'
        ? $noStartAgents
        : $activeAgents;
@endphp

<main
    class="transition-all duration-300 min-h-screen pt-24 relative"
    :class="collapsed ? 'ml-20' : 'ml-64'"
>
    <div class="ml-8 mr-8 lg:ml-10 lg:mr-10">
        <div class="pageswap p-6 w-full">

            <div class="min-h-screen bg-[#f4f7fb]">
                <div class="flex min-h-screen">

                    @include('admin.includes.sidebar')

                    {{-- MAIN --}}
                    <main class="flex-1 px-6 py-6 lg:px-10 lg:py-8">

                        {{-- HEADER --}}
                        <div class="rounded-[24px] bg-white px-8 py-7 shadow-[0_12px_35px_rgba(15,23,42,0.06)]">
                            <div class="text-[12px] font-semibold uppercase tracking-[0.22em] text-[#214e9b]/70">
                                Admin
                            </div>

                            <h1 class="mt-2 text-[32px] font-semibold text-slate-900">
                                Agents
                            </h1>

                            <p class="mt-2 text-[14px] text-slate-600">
                                View and manage agent accounts in the Realty Emails system.
                            </p>
                        </div>

                        {{-- AGENTS TABS --}}
                        <div class="mt-10 rounded-[24px] bg-white shadow-[0_12px_35px_rgba(15,23,42,0.06)] overflow-hidden">

                            {{-- TAB BUTTONS --}}
                            <div class="border-b border-slate-200 bg-slate-50 px-6 pt-5">
                                <div class="flex flex-wrap gap-2">

                                    <a
                                        href="{{ request()->url() }}"
                                        class="{{ $currentTab === 'active' ? 'bg-[#214e9b] text-white shadow' : 'bg-slate-200 text-slate-700' }} rounded-t-xl px-5 py-3 text-sm font-semibold"
                                    >
                                        With Start Date

                                        <span class="ml-2 rounded-full bg-white/20 px-2 py-0.5 text-xs">
                                            {{ method_exists($activeAgents, 'total') ? $activeAgents->total() : 0 }}
                                        </span>
                                    </a>

                                    <a
                                        href="{{ request()->url() }}?nostart_page=1"
                                        class="{{ $currentTab === 'nostart' ? 'bg-[#214e9b] text-white shadow' : 'bg-slate-200 text-slate-700' }} rounded-t-xl px-5 py-3 text-sm font-semibold"
                                    >
                                        No Start Date

                                        <span class="ml-2 rounded-full bg-white/20 px-2 py-0.5 text-xs">
                                            {{ method_exists($noStartAgents, 'total') ? $noStartAgents->total() : 0 }}
                                        </span>
                                    </a>

                                </div>
                            </div>

                            {{-- TAB CONTENT --}}
                            <div class="p-6">

                                <div class="mb-5 flex items-center justify-between">
                                    <div>
                                        <h2 class="text-xl font-semibold text-slate-900">
                                            {{ $currentTab === 'nostart' ? 'Agents With No Start Date' : 'Agents With Start Date' }}
                                        </h2>

                                        <p class="mt-1 text-sm text-slate-500">
                                            {{ $currentTab === 'nostart'
                                                ? 'Agent accounts that do not currently have a start date.'
                                                : 'Agent accounts that currently have a start date.' }}
                                        </p>
                                    </div>

                                    @if(method_exists($agents, 'total'))
                                        <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
                                            {{ $agents->total() }} agents
                                        </span>
                                    @endif
                                </div>

                                <div class="overflow-x-auto rounded-2xl border border-slate-200">

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

                                            @forelse($agents as $agent)

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
                                                                href="/admin/agents/{{ $agent->id }}"
                                                                class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100"
                                                            >
                                                                View
                                                            </a>

                                                            <a
                                                                href="/admin/agents/{{ $agent->id }}/login-as"
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
                                                        No agents found.
                                                    </td>
                                                </tr>

                                            @endforelse

                                        </tbody>
                                    </table>

                                </div>

                                @if(method_exists($agents, 'links'))
                                    <div class="mt-5">
                                        {{ $agents->links() }}
                                    </div>
                                @endif

                            </div>

                        </div>

                    </main>

                </div>
            </div>

        </div>
    </div>
</main>

@include('public.layout.footer')

</body>
</html>