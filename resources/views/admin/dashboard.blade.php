@include('public.layout.head')

<body
    data-section="admin"
    class="relative bg-white min-h-screen font-sans text-gray-800"
>

@include('public.layout.nav')

<main
    class="transition-all duration-300 min-h-screen pt-24 relative"
    :class="collapsed ? 'ml-20' : 'ml-64'"
>
    <div class="ml-8 mr-8 lg:ml-10 lg:mr-10">
        <div class="pageswap p-6 w-full">

            @php
                $waitingFlyerCamps    = $data['waitingFlyerCamps'] ?? collect();
                $inProgressFlyerCamps = $data['inProgressFlyerCamps'] ?? collect();
                $completeFlyerCamps   = $data['completeFlyerCamps'] ?? collect();

                $campaignsWaiting     = $data['campaignsWaiting'] ?? 0;
                $campaignsInProgress  = $data['campaignsInProgress'] ?? 0;
                $campaignsCompleted   = $data['campaignsCompleted'] ?? 0;
            @endphp

            <div class="min-h-screen bg-[#f4f7fb]">
                <div class="flex min-h-screen">

                    @include('admin.includes.sidebar')

                    {{-- MAIN --}}
                    <main class="flex-1 px-6 py-6 lg:px-10 lg:py-8">

                        {{-- HEADER --}}
                        <div class="rounded-[24px] bg-white px-8 py-7 shadow-[0_12px_35px_rgba(15,23,42,0.06)]">
                            <div class="text-[12px] font-semibold uppercase tracking-[0.22em] text-[#214e9b]/70">
                                Dashboard
                            </div>

                            <h1 class="mt-2 text-[32px] font-semibold text-slate-900">
                                Campaign Overview
                            </h1>

                            <p class="mt-2 text-[14px] text-slate-600">
                                Campaign status summary
                            </p>
                        </div>

                        {{-- STATUS CARDS --}}
                        <div class="mt-8 grid grid-cols-1 gap-6 xl:grid-cols-3">

                            {{-- WAITING --}}
                            <div class="rounded-[24px] bg-white p-6 shadow">
                                <div class="flex justify-between">
                                    <div>
                                        <div class="text-xs uppercase tracking-wider text-amber-600">
                                            Campaign Status
                                        </div>

                                        <div class="text-lg font-semibold">
                                            Waiting
                                        </div>
                                    </div>

                                    <span class="bg-amber-100 text-amber-700 text-xs px-3 py-1 rounded-full">
                                        Waiting
                                    </span>
                                </div>

                                <div class="mt-6 text-4xl font-semibold">
                                    {{ $campaignsWaiting }}
                                </div>

                                <div class="mt-4 text-sm text-slate-500">
                                    Flyer groups:
                                    {{ $waitingFlyerCamps->count() }}
                                </div>
                            </div>

                            {{-- IN PROGRESS --}}
                            <div class="rounded-[24px] bg-white p-6 shadow">
                                <div class="flex justify-between">
                                    <div>
                                        <div class="text-xs uppercase tracking-wider text-blue-700">
                                            Campaign Status
                                        </div>

                                        <div class="text-lg font-semibold">
                                            In Progress
                                        </div>
                                    </div>

                                    <span class="bg-blue-100 text-blue-700 text-xs px-3 py-1 rounded-full">
                                        Active
                                    </span>
                                </div>

                                <div class="mt-6 text-4xl font-semibold">
                                    {{ $campaignsInProgress }}
                                </div>

                                <div class="mt-4 text-sm text-slate-500">
                                    Flyer groups:
                                    {{ $inProgressFlyerCamps->count() }}
                                </div>
                            </div>

                            {{-- COMPLETED --}}
                            <div class="rounded-[24px] bg-white p-6 shadow">
                                <div class="flex justify-between">
                                    <div>
                                        <div class="text-xs uppercase tracking-wider text-emerald-700">
                                            Campaign Status
                                        </div>

                                        <div class="text-lg font-semibold">
                                            Completed
                                        </div>
                                    </div>

                                    <span class="bg-emerald-100 text-emerald-700 text-xs px-3 py-1 rounded-full">
                                        Done
                                    </span>
                                </div>

                                <div class="mt-6 text-4xl font-semibold">
                                    {{ $campaignsCompleted }}
                                </div>

                                <div class="mt-4 text-sm text-slate-500">
                                    Flyer groups:
                                    {{ $completeFlyerCamps->count() }}
                                </div>
                            </div>

                        </div>

                        {{-- TABBED GROUP PREVIEW --}}
                        <div
                            x-data="{ activeTab: 'waiting' }"
                            class="mt-10 rounded-[24px] bg-white shadow-[0_12px_35px_rgba(15,23,42,0.06)]"
                        >

                            {{-- TAB BUTTONS --}}
                            <div class="border-b border-slate-200 px-6 pt-5">
                                <div class="flex flex-wrap gap-2">

                                    <button
                                        type="button"
                                        @click="activeTab = 'waiting'"
                                        class="rounded-t-xl px-5 py-3 text-sm font-semibold transition"
                                        :class="activeTab === 'waiting'
                                            ? 'bg-[#214e9b] text-white shadow'
                                            : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                                    >
                                        Waiting
                                        <span class="ml-2 rounded-full bg-white/20 px-2 py-0.5 text-xs">
                                            {{ $waitingFlyerCamps->count() }}
                                        </span>
                                    </button>

                                    <button
                                        type="button"
                                        @click="activeTab = 'inProgress'"
                                        class="rounded-t-xl px-5 py-3 text-sm font-semibold transition"
                                        :class="activeTab === 'inProgress'
                                            ? 'bg-[#214e9b] text-white shadow'
                                            : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                                    >
                                        In Progress
                                        <span class="ml-2 rounded-full bg-white/20 px-2 py-0.5 text-xs">
                                            {{ $inProgressFlyerCamps->count() }}
                                        </span>
                                    </button>

                                    <button
                                        type="button"
                                        @click="activeTab = 'completed'"
                                        class="rounded-t-xl px-5 py-3 text-sm font-semibold transition"
                                        :class="activeTab === 'completed'
                                            ? 'bg-[#214e9b] text-white shadow'
                                            : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                                    >
                                        Completed
                                        <span class="ml-2 rounded-full bg-white/20 px-2 py-0.5 text-xs">
                                            {{ $completeFlyerCamps->count() }}
                                        </span>
                                    </button>

                                </div>
                            </div>

                            {{-- TAB CONTENT --}}
                            <div class="p-6">

                                {{-- WAITING TAB --}}
                                <div x-show="activeTab === 'waiting'" x-cloak>
                                    <div class="mb-5 flex items-center justify-between">
                                        <div>
                                            <h2 class="text-xl font-semibold text-slate-900">
                                                Waiting Groups
                                            </h2>
                                            <p class="mt-1 text-sm text-slate-500">
                                                Campaign groups that have been requested but not started.
                                            </p>
                                        </div>

                                        <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                            {{ $waitingFlyerCamps->count() }} groups
                                        </span>
                                    </div>

                                    <div class="space-y-3">
                                        @forelse($waitingFlyerCamps as $flyerId => $campaigns)
                                            @php
                                                $first = $campaigns->first();
                                            @endphp

                                            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm hover:border-[#214e9b]/40 hover:shadow-md transition">
                                                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                                    <div>
                                                        <a
                                                            href="/flyer/{{ $flyerId }}"
                                                            class="font-semibold text-[#214e9b] hover:underline"
                                                        >
                                                            ID#: {{ $flyerId }}
                                                        </a>

                                                        <div class="mt-1 text-sm font-medium text-slate-800">
                                                            {{ $first['address'] ?? 'No Address' }}
                                                        </div>
                                                    </div>

                                                    <div class="text-sm text-slate-500">
                                                        {{ $campaigns->count() }} campaigns
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="rounded-xl border border-dashed border-slate-300 p-8 text-center text-sm text-slate-500">
                                                No waiting groups found.
                                            </div>
                                        @endforelse
                                    </div>
                                </div>

                                {{-- IN PROGRESS TAB --}}
                                <div x-show="activeTab === 'inProgress'" x-cloak>
                                    <div class="mb-5 flex items-center justify-between">
                                        <div>
                                            <h2 class="text-xl font-semibold text-slate-900">
                                                In Progress Groups
                                            </h2>
                                            <p class="mt-1 text-sm text-slate-500">
                                                Campaign groups currently being processed.
                                            </p>
                                        </div>

                                        <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
                                            {{ $inProgressFlyerCamps->count() }} groups
                                        </span>
                                    </div>

                                    <div class="space-y-3">
                                        @forelse($inProgressFlyerCamps as $flyerId => $campaigns)
                                            @php
                                                $first = $campaigns->first();
                                            @endphp

                                            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm hover:border-[#214e9b]/40 hover:shadow-md transition">
                                                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                                    <div>
                                                        <a
                                                            href="/flyer/{{ $flyerId }}"
                                                            class="font-semibold text-[#214e9b] hover:underline"
                                                        >
                                                            ID#: {{ $flyerId }}
                                                        </a>

                                                        <div class="mt-1 text-sm font-medium text-slate-800">
                                                            {{ $first['address'] ?? 'No Address' }}
                                                        </div>
                                                    </div>

                                                    <div class="text-sm text-slate-500">
                                                        {{ $campaigns->count() }} campaigns
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="rounded-xl border border-dashed border-slate-300 p-8 text-center text-sm text-slate-500">
                                                No in progress groups found.
                                            </div>
                                        @endforelse
                                    </div>
                                </div>

                                {{-- COMPLETED TAB --}}
                                <div x-show="activeTab === 'completed'" x-cloak>
                                    <div class="mb-5 flex items-center justify-between">
                                        <div>
                                            <h2 class="text-xl font-semibold text-slate-900">
                                                Completed Groups
                                            </h2>
                                            <p class="mt-1 text-sm text-slate-500">
                                                Campaign groups that have finished processing.
                                            </p>
                                        </div>

                                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                                            {{ $completeFlyerCamps->count() }} groups
                                        </span>
                                    </div>

                                    <div class="space-y-3">
                                        @forelse($completeFlyerCamps as $flyerId => $campaigns)
                                            @php
                                                $first = $campaigns->first();
                                            @endphp

                                            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm hover:border-[#214e9b]/40 hover:shadow-md transition">
                                                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                                    <div>
                                                        <a
                                                            href="/flyer/{{ $flyerId }}"
                                                            class="font-semibold text-[#214e9b] hover:underline"
                                                        >
                                                            ID#: {{ $flyerId }}
                                                        </a>

                                                        <div class="mt-1 text-sm font-medium text-slate-800">
                                                            {{ $first['address'] ?? 'No Address' }}
                                                        </div>
                                                    </div>

                                                    <div class="text-sm text-slate-500">
                                                        {{ $campaigns->count() }} campaigns
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="rounded-xl border border-dashed border-slate-300 p-8 text-center text-sm text-slate-500">
                                                No completed groups found.
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

</body>
</html>