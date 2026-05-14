@include('public.layout.head')

<body
    data-section="member"
    class="relative min-h-screen bg-slate-50 font-sans text-slate-800"
>

@include('public.layout.nav')

@php
    $member = Auth::guard('member')->user();
@endphp

<main
    class="transition-all duration-300 min-h-screen pt-24 relative"
    :class="collapsed ? 'ml-20' : 'ml-64'"
>
    <div class="ml-8 mr-8 lg:ml-10 lg:mr-10">

        {{-- ADMIN IMPERSONATION BAR --}}
        @if(session()->has('impersonator_admin_id'))
            <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <div class="text-sm font-bold uppercase tracking-wide text-amber-800">
                            Admin Impersonation Active
                        </div>

                        <div class="mt-1 text-sm text-amber-700">
                            You are currently viewing this account as
                            <span class="font-semibold">{{ $member->agtFullName }}</span>.
                        </div>
                    </div>

                    <a
                        href="/admin"
                        class="inline-flex items-center rounded-xl bg-amber-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-amber-700"
                    >
                        Return to Admin
                    </a>
                </div>
            </div>
        @endif

        <div class="overflow-hidden rounded-3xl bg-white shadow-xl ring-1 ring-slate-200">

            {{-- TOP HEADER --}}
            <div class="relative overflow-hidden bg-gradient-to-r from-[#1b2f63] via-[#223a75] to-[#2a4486] px-8 py-8">

                <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.25),transparent_35%)]"></div>

                <div class="relative flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">

                    <div>
                        <div class="text-3xl font-black tracking-tight text-white">
                            RealtyEmails<span class="text-blue-200">.com</span>
                        </div>

                        <p class="mt-2 text-sm text-blue-100">
                            Member Dashboard
                        </p>

                        <h1 class="mt-4 text-4xl font-bold tracking-tight text-white">
                            Welcome Back
                        </h1>

                        <p class="mt-2 text-lg text-blue-100">
                            {{ $member->agtFullName }}
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">

                        <div class="rounded-2xl bg-white/10 px-6 py-5 backdrop-blur-sm ring-1 ring-white/20">
                            <div class="text-sm uppercase tracking-wide text-blue-100">
                                Active Flyers
                            </div>

                            <div class="mt-2 text-3xl font-bold text-white">
                                {{ $activeFlyers ?? 0 }}
                            </div>
                        </div>

                        <div class="rounded-2xl bg-white/10 px-6 py-5 backdrop-blur-sm ring-1 ring-white/20">
                            <div class="text-sm uppercase tracking-wide text-blue-100">
                                Credits
                            </div>

                            <div class="mt-2 text-3xl font-bold text-white">
                                {{ $credits ?? 0 }}
                            </div>
                        </div>

                    </div>

                </div>
            </div>

            {{-- MAIN CONTENT --}}
            <div class="grid grid-cols-1 gap-8 p-8 xl:grid-cols-[320px_1fr]">

                {{-- SIDEBAR --}}
                <aside>

                    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-4">

                        <div class="mb-4 px-3">
                            <div class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">
                                Navigation
                            </div>
                        </div>

                        <nav class="space-y-2">

                            <a href="#"
                            class="group flex items-center justify-between rounded-2xl bg-white px-4 py-4 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-0.5 hover:shadow-md">

                                <span class="font-semibold text-slate-700 group-hover:text-[#214e9b]">
                                    Create New Flyer
                                </span>

                                <span class="text-slate-300 group-hover:text-[#214e9b]">
                                    →
                                </span>
                            </a>

                            <a href="#"
                            class="group flex items-center justify-between rounded-2xl bg-white px-4 py-4 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-0.5 hover:shadow-md">

                                <span class="font-semibold text-slate-700 group-hover:text-[#214e9b]">
                                    Existing Flyers
                                </span>

                                <span class="text-slate-300 group-hover:text-[#214e9b]">
                                    →
                                </span>
                            </a>

                            <a href="#"
                            class="group flex items-center justify-between rounded-2xl bg-white px-4 py-4 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-0.5 hover:shadow-md">

                                <span class="font-semibold text-slate-700 group-hover:text-[#214e9b]">
                                    Campaign History
                                </span>

                                <span class="text-slate-300 group-hover:text-[#214e9b]">
                                    →
                                </span>
                            </a>

                            <a href="#"
                            class="group flex items-center justify-between rounded-2xl bg-white px-4 py-4 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-0.5 hover:shadow-md">

                                <span class="font-semibold text-slate-700 group-hover:text-[#214e9b]">
                                    Account Information
                                </span>

                                <span class="text-slate-300 group-hover:text-[#214e9b]">
                                    →
                                </span>
                            </a>

                            <a href="#"
                            class="group flex items-center justify-between rounded-2xl bg-white px-4 py-4 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-0.5 hover:shadow-md">

                                <span class="font-semibold text-slate-700 group-hover:text-[#214e9b]">
                                    Purchase Credits
                                </span>

                                <span class="text-slate-300 group-hover:text-[#214e9b]">
                                    →
                                </span>
                            </a>

                        </nav>

                    </div>

                </aside>

                {{-- DASHBOARD CONTENT --}}
                <section>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">

                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">

                            <div class="text-sm font-semibold uppercase tracking-wide text-slate-400">
                                New Flyers
                            </div>

                            <div class="mt-4 text-5xl font-black tracking-tight text-slate-900">
                                {{ $newFlyers ?? 0 }}
                            </div>

                            <p class="mt-3 text-sm text-slate-500">
                                Ready for setup and delivery.
                            </p>

                        </div>

                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">

                            <div class="text-sm font-semibold uppercase tracking-wide text-slate-400">
                                Pending Campaigns
                            </div>

                            <div class="mt-4 text-5xl font-black tracking-tight text-slate-900">
                                {{ $pendingCampaigns ?? 0 }}
                            </div>

                            <p class="mt-3 text-sm text-slate-500">
                                Waiting for delivery processing.
                            </p>

                        </div>

                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">

                            <div class="text-sm font-semibold uppercase tracking-wide text-slate-400">
                                Completed Deliveries
                            </div>

                            <div class="mt-4 text-5xl font-black tracking-tight text-slate-900">
                                {{ $completedCampaigns ?? 0 }}
                            </div>

                            <p class="mt-3 text-sm text-slate-500">
                                Successfully delivered campaigns.
                            </p>

                        </div>

                    </div>

                    {{-- LARGE CONTENT PANEL --}}
                    <div class="mt-8 rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">

                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-2xl font-bold tracking-tight text-slate-900">
                                    Recent Activity
                                </h2>

                                <p class="mt-1 text-sm text-slate-500">
                                    Latest flyer and campaign updates.
                                </p>
                            </div>
                        </div>

                        <div class="mt-8">

                            <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-12 text-center">
                                <div class="text-lg font-semibold text-slate-600">
                                    Activity Feed Placeholder
                                </div>

                                <p class="mt-2 text-sm text-slate-500">
                                    Replace this section with your campaign table, recent flyers, stats, or charts.
                                </p>
                            </div>

                        </div>

                    </div>

                </section>

            </div>

        </div>

    </div>
</main>

@include('public.layout.footer')

</body>
</html>