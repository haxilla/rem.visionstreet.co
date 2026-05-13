@include('public.layout.head')

<body data-section="admin" class="relative bg-white min-h-screen font-sans text-gray-800">

@include('public.layout.nav')
@php
    $agents = $data['agents'] ?? collect();
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

                        {{-- AGENTS LIST --}}
                        <div class="mt-10 rounded-[24px] bg-white shadow-[0_12px_35px_rgba(15,23,42,0.06)] overflow-hidden">

                            <div class="border-b border-slate-200 bg-slate-50 px-6 py-5">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <h2 class="text-xl font-semibold text-slate-900">
                                            All Agents
                                        </h2>

                                        <p class="mt-1 text-sm text-slate-500">
                                            Paginated list of agent accounts.
                                        </p>
                                    </div>

                                    <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
                                        {{ $agents->total() }} agents
                                    </span>
                                </div>
                            </div>

                            <div class="overflow-x-auto">

                                {{-- TABLE HEADER --}}
                                <div class="grid min-w-[920px] grid-cols-[1.4fr_1.4fr_110px_130px_130px_150px] gap-4 border-b border-slate-200 bg-white px-6 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    <div>First Name</div>
                                    <div>Last Name</div>
                                    <div>Credits</div>
                                    <div>Start Date</div>
                                    <div>Expire Date</div>
                                    <div class="text-right">Actions</div>
                                </div>

                                {{-- TABLE ROWS --}}
                                @forelse($agents as $agent)

                                    @php
                                        $nameParts = preg_split('/\s+/', trim($agent->agtFullName ?? ''), 2);

                                        $firstName = $agent->agtFirstName
                                            ?? $agent->firstName
                                            ?? $nameParts[0]
                                            ?? '';

                                        $lastName = $agent->agtLastName
                                            ?? $agent->lastName
                                            ?? $nameParts[1]
                                            ?? '';
                                    @endphp

                                    <div class="grid min-w-[920px] grid-cols-[1.4fr_1.4fr_110px_130px_130px_150px] gap-4 border-b border-slate-100 px-6 py-4 text-sm text-slate-700 transition hover:bg-slate-50">

                                        <div class="truncate font-semibold text-slate-900">
                                            {{ $firstName ?: '—' }}
                                        </div>

                                        <div class="truncate font-semibold text-slate-900">
                                            {{ $lastName ?: '—' }}
                                        </div>

                                        <div>
                                            {{ $agent->remCreds ?? 0 }}
                                        </div>

                                        <div>
                                            {{ $agent->startDate
                                                ? \Carbon\Carbon::parse($agent->startDate)->format('m/d/Y')
                                                : '—' }}
                                        </div>

                                        <div>
                                            {{ $agent->expireDate
                                                ? \Carbon\Carbon::parse($agent->expireDate)->format('m/d/Y')
                                                : '—' }}
                                        </div>

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

                                    </div>

                                @empty

                                    <div class="p-10 text-center text-sm text-slate-500">
                                        No agents found.
                                    </div>

                                @endforelse

                            </div>

                            {{-- PAGINATION --}}
                            <div class="border-t border-slate-200 bg-slate-50 px-6 py-5">
                                {{ $agents->links() }}
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