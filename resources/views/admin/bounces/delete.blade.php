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
                $azRow = $data['azRow'] ?? null;
                $arizonaRow = $data['arizonaRow'] ?? null;
            @endphp

            <div class="min-h-screen bg-[#f4f7fb]">
                <div class="flex min-h-screen">

                    @include('admin.includes.sidebar')

                    <main class="flex-1 px-6 py-6 lg:px-10 lg:py-8">

                        <div class="mb-5">
                            <a
                                href="/admin/bounces"
                                class="inline-flex items-center rounded-full border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 shadow-sm transition hover:border-[#214e9b]/40 hover:text-[#214e9b]"
                            >
                                ← Back to Bouncebox
                            </a>
                        </div>

                        <div class="rounded-[24px] border border-amber-200 bg-amber-50 px-8 py-7 shadow-[0_12px_35px_rgba(15,23,42,0.04)]">
                            <div class="text-[12px] font-semibold uppercase tracking-[0.22em] text-amber-700/80">
                                Delete Preview
                            </div>

                            <h1 class="mt-2 text-[32px] font-semibold text-amber-950">
                                Confirm Recipient Delete
                            </h1>

                            <p class="mt-2 text-[14px] text-amber-800">
                                No records have been deleted yet. Review the records below before continuing.
                            </p>
                        </div>

                        <div class="mt-8 overflow-hidden rounded-[24px] bg-white shadow-[0_12px_35px_rgba(15,23,42,0.06)]">
                            <div class="border-b border-slate-200 bg-slate-50 px-6 py-5">
                                <h2 class="text-sm font-semibold text-slate-700">
                                    Records That Would Be Deleted
                                </h2>
                            </div>

                            <div class="grid gap-5 p-8 text-sm text-slate-700 md:grid-cols-2">

                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                                    <div class="mb-4 flex items-center justify-between">
                                        <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                            AZEmails
                                        </div>

                                        @if($azRow)
                                            <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                                                Found
                                            </span>
                                        @else
                                            <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                                Not Found
                                            </span>
                                        @endif
                                    </div>

                                    <div class="space-y-2">
                                        <div><strong>Connection:</strong> remote_emailgroups_azemails</div>
                                        <div><strong>Table:</strong> {{ $azTable }}</div>
                                        <div><strong>EID:</strong> {{ $eid }}</div>
                                        <div><strong>Email:</strong> {{ $azRow->email ?? '' }}</div>
                                        <div><strong>First Name:</strong> {{ $azRow->FirstName ?? '' }}</div>
                                        <div><strong>Last Name:</strong> {{ $azRow->LastName ?? '' }}</div>
                                        <div><strong>Full Name:</strong> {{ $azRow->FullName ?? '' }}</div>
                                        <div><strong>Office:</strong> {{ $azRow->Officename ?? '' }}</div>
                                        <div><strong>License #:</strong> {{ $azRow->agentLicenseNum ?? '' }}</div>
                                    </div>
                                </div>

                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                                    <div class="mb-4 flex items-center justify-between">
                                        <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                            ArizonaEmails
                                        </div>

                                        @if($arizonaRow)
                                            <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                                                Found
                                            </span>
                                        @else
                                            <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                                Not Found
                                            </span>
                                        @endif
                                    </div>

                                    <div class="space-y-2">
                                        <div><strong>Connection:</strong> remote_emailgroups_arizonaemails</div>
                                        <div><strong>Table:</strong> {{ $arizonaTable }}</div>
                                        <div><strong>EID:</strong> {{ $eid }}</div>
                                        <div><strong>Email:</strong> {{ $arizonaRow->email ?? '' }}</div>
                                        <div><strong>First Name:</strong> {{ $arizonaRow->FirstName ?? '' }}</div>
                                        <div><strong>Last Name:</strong> {{ $arizonaRow->LastName ?? '' }}</div>
                                        <div><strong>Full Name:</strong> {{ $arizonaRow->FullName ?? '' }}</div>
                                        <div><strong>Office:</strong> {{ $arizonaRow->Officename ?? '' }}</div>
                                        <div><strong>License #:</strong> {{ $arizonaRow->agentLicenseNum ?? '' }}</div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="mt-8 rounded-[24px] bg-white px-8 py-6 shadow-[0_12px_35px_rgba(15,23,42,0.06)]">
                            <div class="text-sm font-semibold text-slate-700">
                                Bouncebox Message
                            </div>

                            <p class="mt-2 text-sm text-slate-600">
                                <span class="font-semibold text-slate-400">Message Number:</span>
                                <span class="ml-1 font-semibold text-[#214e9b]">
                                    {{ $messageNumber ?: 'Not provided' }}
                                </span>
                            </p>
                        </div>

                        <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <a
                                href="/admin/bounces"
                                class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-6 py-3 text-sm font-semibold text-slate-600 shadow-sm transition hover:border-[#214e9b]/40 hover:text-[#214e9b]"
                            >
                                Cancel / Return to Bouncebox
                            </a>

                            @if($azRow && $arizonaRow)
                                <form
                                    method="POST"
                                    action="/admin/bounces/delete-recipient-confirmed"
                                    onsubmit="return confirm('FINAL CONFIRMATION: Delete these recipient records from both remote databases and continue with bounce message deletion?');"
                                >
                                    @csrf

                                    <input type="hidden" name="az_table" value="{{ $azTable }}">
                                    <input type="hidden" name="arizona_table" value="{{ $arizonaTable }}">
                                    <input type="hidden" name="eid" value="{{ $eid }}">
                                    <input type="hidden" name="messageNumber" value="{{ $messageNumber }}">

                                    <button
                                        type="submit"
                                        class="inline-flex cursor-pointer items-center justify-center rounded-full bg-red-600 px-7 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700"
                                    >
                                        Confirm Delete
                                    </button>
                                </form>
                            @endif
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