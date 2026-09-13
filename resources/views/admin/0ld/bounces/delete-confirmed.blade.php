@include('public.layout.head')

<body data-section="admin" class="relative bg-white min-h-screen font-sans text-gray-800">

@include('public.layout.nav')

<main
    class="transition-all duration-300 min-h-screen pt-24 relative"
    :class="collapsed ? 'ml-20' : 'ml-64'"
>
    <div class="ml-8 mr-8 lg:ml-10 lg:mr-10">
        <div class="pageswap p-6 w-full">

            @php
                $azTable = $data['azTable'] ?? '';
                $arizonaTable = $data['arizonaTable'] ?? '';
                $eid = $data['eid'] ?? '';
                $messageNumber = $data['messageNumber'] ?? '';
                $deletedAz = $data['deletedAz'] ?? 0;
                $deletedArizona = $data['deletedArizona'] ?? 0;
                $deletedBounceMessage = $data['deletedBounceMessage'] ?? false;
                $bounceDeleteError = $data['bounceDeleteError'] ?? null;
            @endphp

            <div class="min-h-screen bg-[#f4f7fb]">
                <div class="flex min-h-screen">

                    @include('admin.includes.sidebar')

                    <main class="flex-1 px-6 py-6 lg:px-10 lg:py-8">

                        {{-- HEADER --}}
                        <div class="rounded-[24px] bg-white px-8 py-7 shadow-[0_12px_35px_rgba(15,23,42,0.06)]">

                            <div class="text-[12px] font-semibold uppercase tracking-[0.22em] text-[#214e9b]/70">
                                Delete Complete
                            </div>

                            <h1 class="mt-2 text-[32px] font-semibold text-slate-900">
                                Recipient Delete Finished
                            </h1>

                            <p class="mt-2 text-[14px] text-slate-600">
                                Review the deletion results below.
                            </p>

                        </div>

                        {{-- RESULTS --}}
                        <div class="mt-8 overflow-hidden rounded-[24px] bg-white shadow-[0_12px_35px_rgba(15,23,42,0.06)]">

                            <div class="border-b border-slate-200 bg-slate-50 px-6 py-5">
                                <h2 class="text-sm font-semibold text-slate-700">
                                    Delete Results
                                </h2>
                            </div>

                            <div class="grid gap-5 p-8 text-sm text-slate-700 md:grid-cols-2 lg:grid-cols-4">

                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                                    <span class="block text-xs font-semibold uppercase tracking-wide text-slate-400">
                                        EID
                                    </span>
                                    <span class="mt-1 block font-semibold text-slate-800">
                                        {{ $eid }}
                                    </span>
                                </div>

                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                                    <span class="block text-xs font-semibold uppercase tracking-wide text-slate-400">
                                        AZEmails Deleted
                                    </span>
                                    <span class="mt-1 block text-2xl font-semibold text-slate-900">
                                        {{ $deletedAz }}
                                    </span>
                                    <span class="mt-1 block text-xs text-slate-500">
                                        {{ $azTable }}
                                    </span>
                                </div>

                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                                    <span class="block text-xs font-semibold uppercase tracking-wide text-slate-400">
                                        ArizonaEmails Deleted
                                    </span>
                                    <span class="mt-1 block text-2xl font-semibold text-slate-900">
                                        {{ $deletedArizona }}
                                    </span>
                                    <span class="mt-1 block text-xs text-slate-500">
                                        {{ $arizonaTable }}
                                    </span>
                                </div>

                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                                    <span class="block text-xs font-semibold uppercase tracking-wide text-slate-400">
                                        Bounce Message
                                    </span>

                                    <span class="mt-1 block font-semibold {{ $deletedBounceMessage ? 'text-emerald-700' : 'text-red-700' }}">
                                        {{ $deletedBounceMessage ? 'Deleted' : 'Not Deleted' }}
                                    </span>

                                    @if($messageNumber)
                                        <span class="mt-1 block text-xs text-slate-500">
                                            Message #{{ $messageNumber }}
                                        </span>
                                    @endif
                                </div>

                            </div>

                            @if($bounceDeleteError)
                                <div class="border-t border-slate-200 px-8 pb-8">
                                    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-sm text-amber-800">
                                        <strong>Bounce delete warning:</strong>
                                        {{ $bounceDeleteError }}
                                    </div>
                                </div>
                            @endif

                        </div>

                        {{-- ACTION --}}
                        <div class="mt-8">
                            <a
                                href="/admin/bounces"
                                class="inline-flex items-center rounded-full bg-[#214e9b] px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#1b4386]"
                            >
                                ← Return to Bouncebox
                            </a>
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