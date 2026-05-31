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
                $email = $data['email'] ?? '';
                $foundByDb = $data['foundByDb'] ?? [];
                $hasProblem = $data['hasProblem'] ?? true;
                $azMatch = $data['azMatch'] ?? null;
                $arizonaMatch = $data['arizonaMatch'] ?? null;
                $row = $data['row'] ?? null;
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

                        {{-- HEADER --}}
                        <div class="rounded-[24px] bg-white px-8 py-7 shadow-[0_12px_35px_rgba(15,23,42,0.06)]">
                            <div class="text-[12px] font-semibold uppercase tracking-[0.22em] text-[#214e9b]/70">
                                Recipient Review
                            </div>

                            <h1 class="mt-2 text-[32px] font-semibold text-slate-900">
                                Email Lookup Result
                            </h1>

                            <p class="mt-2 text-[14px] text-slate-600">
                                <span class="font-semibold text-slate-400">Searched Email:</span>
                                <span class="ml-1 font-semibold text-[#214e9b]">{{ $email }}</span>
                            </p>
                        </div>

                        @if($hasProblem)

                            {{-- NEEDS REVIEW --}}
                            <div class="mt-8 rounded-[24px] border border-amber-200 bg-amber-50 px-8 py-6 shadow-[0_12px_35px_rgba(15,23,42,0.04)]">
                                <div class="text-lg font-semibold text-amber-900">
                                    Needs Review
                                </div>

                                <p class="mt-1 text-sm text-amber-800">
                                    Each remote database should contain exactly one matching record.
                                </p>
                            </div>

                            <div class="mt-8 space-y-6">

                                @foreach($foundByDb as $dbLabel => $matches)

                                    <div class="overflow-hidden rounded-[24px] bg-white shadow-[0_12px_35px_rgba(15,23,42,0.06)]">

                                        <div class="border-b border-slate-200 bg-slate-50 px-6 py-5">
                                            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">

                                                <h2 class="text-sm font-semibold text-slate-700">
                                                    {{ $dbLabel }}
                                                </h2>

                                                <span class="rounded-full {{ count($matches) === 1 ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }} px-3 py-1 text-xs font-semibold">
                                                    {{ count($matches) }} match(es)
                                                </span>

                                            </div>
                                        </div>

                                        <div class="p-6">

                                            @if(count($matches) === 0)
                                                <div class="rounded-2xl border border-red-200 bg-red-50 p-5 text-sm font-semibold text-red-700">
                                                    No matching record found.
                                                </div>
                                            @endif

                                            <div class="space-y-4">

                                                @foreach($matches as $match)

                                                    @php $r = $match['row']; @endphp

                                                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">

                                                        <div class="mb-4">
                                                            <span class="rounded-full bg-[#214e9b]/10 px-3 py-1 text-xs font-semibold text-[#214e9b]">
                                                                Table: {{ $match['table'] }}
                                                            </span>
                                                        </div>

                                                        <div class="grid gap-3 text-sm text-slate-700 md:grid-cols-2">

                                                            <div>
                                                                <span class="font-semibold text-slate-400">EID:</span>
                                                                <span class="ml-1">{{ $r->eid ?? '' }}</span>
                                                            </div>

                                                            <div>
                                                                <span class="font-semibold text-slate-400">Email:</span>
                                                                <span class="ml-1">{{ $r->email ?? '' }}</span>
                                                            </div>

                                                            <div>
                                                                <span class="font-semibold text-slate-400">Name:</span>
                                                                <span class="ml-1">{{ $r->FullName ?? '' }}</span>
                                                            </div>

                                                            <div>
                                                                <span class="font-semibold text-slate-400">Office:</span>
                                                                <span class="ml-1">{{ $r->Officename ?? '' }}</span>
                                                            </div>

                                                            <div>
                                                                <span class="font-semibold text-slate-400">License #:</span>
                                                                <span class="ml-1">{{ $r->agentLicenseNum ?? '' }}</span>
                                                            </div>

                                                            <div>
                                                                <span class="font-semibold text-slate-400">License Checked:</span>
                                                                <span class="ml-1">{{ $r->checkLicDate ?? '' }}</span>
                                                            </div>

                                                        </div>

                                                    </div>

                                                @endforeach

                                            </div>

                                        </div>

                                    </div>

                                @endforeach

                            </div>

                        @else

                            {{-- MIRROR MATCH --}}
                            <div class="mt-8 rounded-[24px] border border-emerald-200 bg-emerald-50 px-8 py-6 shadow-[0_12px_35px_rgba(15,23,42,0.04)]">
                                <div class="text-lg font-semibold text-emerald-900">
                                    Mirror Match Found
                                </div>

                                <p class="mt-1 text-sm text-emerald-800">
                                    One record exists in each remote database.
                                </p>

                                <div class="mt-4 grid gap-3 text-sm text-emerald-900 md:grid-cols-2">
                                    <div>
                                        <span class="font-semibold">AZEmails:</span>
                                        {{ $azMatch['table'] }} / EID {{ $azMatch['row']->eid ?? '' }}
                                    </div>

                                    <div>
                                        <span class="font-semibold">ArizonaEmails:</span>
                                        {{ $arizonaMatch['table'] }} / EID {{ $arizonaMatch['row']->eid ?? '' }}
                                    </div>
                                </div>
                            </div>

                            {{-- EDIT FORM --}}
                            <form method="POST" action="/admin/bounces/update-recipient" class="mt-8">
                                @csrf

                                <input type="hidden" name="az_table" value="{{ $azMatch['table'] }}">
                                <input type="hidden" name="az_eid" value="{{ $azMatch['row']->eid ?? '' }}">

                                <input type="hidden" name="arizona_table" value="{{ $arizonaMatch['table'] }}">
                                <input type="hidden" name="arizona_eid" value="{{ $arizonaMatch['row']->eid ?? '' }}">

                                <div class="overflow-hidden rounded-[24px] bg-white shadow-[0_12px_35px_rgba(15,23,42,0.06)]">

                                    <div class="border-b border-slate-200 bg-slate-50 px-6 py-5">
                                        <h2 class="text-sm font-semibold text-slate-700">
                                            Editable Recipient Details
                                        </h2>
                                    </div>

                                    <div class="space-y-5 p-8">

                                        <div>
                                            <label class="mb-2 block text-sm font-semibold text-slate-700">
                                                Email
                                            </label>
                                            <input
                                                type="email"
                                                name="email"
                                                value="{{ $row->email ?? '' }}"
                                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#214e9b]/50 focus:bg-white focus:ring-4 focus:ring-[#214e9b]/10"
                                            >
                                        </div>

                                        <div>
                                            <label class="mb-2 block text-sm font-semibold text-slate-700">
                                                Office Name
                                            </label>
                                            <input
                                                type="text"
                                                name="Officename"
                                                value="{{ $row->Officename ?? '' }}"
                                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#214e9b]/50 focus:bg-white focus:ring-4 focus:ring-[#214e9b]/10"
                                            >
                                        </div>

                                        <div>
                                            <label class="mb-2 block text-sm font-semibold text-slate-700">
                                                Office Address 1
                                            </label>
                                            <input
                                                type="text"
                                                name="officeaddress1"
                                                value="{{ $row->officeaddress1 ?? '' }}"
                                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#214e9b]/50 focus:bg-white focus:ring-4 focus:ring-[#214e9b]/10"
                                            >
                                        </div>

                                        <div>
                                            <label class="mb-2 block text-sm font-semibold text-slate-700">
                                                Office Address 2
                                            </label>
                                            <input
                                                type="text"
                                                name="officeaddress2"
                                                value="{{ $row->officeaddress2 ?? '' }}"
                                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#214e9b]/50 focus:bg-white focus:ring-4 focus:ring-[#214e9b]/10"
                                            >
                                        </div>

                                        <div class="grid gap-5 md:grid-cols-3">

                                            <div>
                                                <label class="mb-2 block text-sm font-semibold text-slate-700">
                                                    Office City
                                                </label>
                                                <input
                                                    type="text"
                                                    name="officecity"
                                                    value="{{ $row->officecity ?? '' }}"
                                                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#214e9b]/50 focus:bg-white focus:ring-4 focus:ring-[#214e9b]/10"
                                                >
                                            </div>

                                            <div>
                                                <label class="mb-2 block text-sm font-semibold text-slate-700">
                                                    Office State
                                                </label>
                                                <input
                                                    type="text"
                                                    name="officestate"
                                                    value="{{ $row->officestate ?? '' }}"
                                                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#214e9b]/50 focus:bg-white focus:ring-4 focus:ring-[#214e9b]/10"
                                                >
                                            </div>

                                            <div>
                                                <label class="mb-2 block text-sm font-semibold text-slate-700">
                                                    Office Zip
                                                </label>
                                                <input
                                                    type="text"
                                                    name="officezip"
                                                    value="{{ $row->officezip ?? '' }}"
                                                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#214e9b]/50 focus:bg-white focus:ring-4 focus:ring-[#214e9b]/10"
                                                >
                                            </div>

                                        </div>

                                        <div>
                                            <label class="mb-2 block text-sm font-semibold text-slate-700">
                                                Office Phone
                                            </label>
                                            <input
                                                type="text"
                                                name="officephone"
                                                value="{{ $row->officephone ?? '' }}"
                                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#214e9b]/50 focus:bg-white focus:ring-4 focus:ring-[#214e9b]/10"
                                            >
                                        </div>

                                    </div>

                                </div>

                                {{-- READ ONLY DETAILS --}}
                                <div class="mt-8 overflow-hidden rounded-[24px] bg-white shadow-[0_12px_35px_rgba(15,23,42,0.06)]">

                                    <div class="border-b border-slate-200 bg-slate-50 px-6 py-5">
                                        <h2 class="text-sm font-semibold text-slate-700">
                                            Read Only Recipient Status
                                        </h2>
                                    </div>

                                    <div class="grid gap-4 p-8 text-sm text-slate-700 md:grid-cols-2 lg:grid-cols-3">

                                        <div>
                                            <span class="block text-xs font-semibold uppercase tracking-wide text-slate-400">
                                                Full Name
                                            </span>
                                            <span class="mt-1 block font-semibold text-slate-800">
                                                {{ $row->FullName ?? '' }}
                                            </span>
                                        </div>

                                        <div>
                                            <span class="block text-xs font-semibold uppercase tracking-wide text-slate-400">
                                                License #
                                            </span>
                                            <span class="mt-1 block font-semibold text-slate-800">
                                                {{ $row->agentLicenseNum ?? '' }}
                                            </span>
                                        </div>

                                        <div>
                                            <span class="block text-xs font-semibold uppercase tracking-wide text-slate-400">
                                                License Checked
                                            </span>
                                            <span class="mt-1 block font-semibold text-slate-800">
                                                {{ $row->checkLicDate ?? '' }}
                                            </span>
                                        </div>

                                        <div>
                                            <span class="block text-xs font-semibold uppercase tracking-wide text-slate-400">
                                                Agent Status
                                            </span>
                                            <span class="mt-1 block font-semibold text-slate-800">
                                                {{ $row->agentLicStatus ?? '' }}
                                            </span>
                                        </div>

                                        <div>
                                            <span class="block text-xs font-semibold uppercase tracking-wide text-slate-400">
                                                Send OK
                                            </span>
                                            <span class="mt-1 block font-semibold text-slate-800">
                                                {{ $row->sendOK ?? '' }}
                                            </span>
                                        </div>

                                        <div>
                                            <span class="block text-xs font-semibold uppercase tracking-wide text-slate-400">
                                                Bounce Count
                                            </span>
                                            <span class="mt-1 block font-semibold text-slate-800">
                                                {{ $row->bounceCount ?? '' }}
                                            </span>
                                        </div>

                                        <div>
                                            <span class="block text-xs font-semibold uppercase tracking-wide text-slate-400">
                                                Suspend Count
                                            </span>
                                            <span class="mt-1 block font-semibold text-slate-800">
                                                {{ $row->suspendCount ?? '' }}
                                            </span>
                                        </div>

                                    </div>

                                </div>

                                <div class="mt-6 flex justify-end">
                                    <button
                                        type="submit"
                                        class="rounded-full bg-[#214e9b] px-7 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#1b4386]"
                                    >
                                        Save Changes to Both Databases
                                    </button>
                                </div>

                            </form>

                        @endif

                    </main>

                </div>
            </div>

        </div>
    </div>
</main>

@include('public.layout.footer')

</body>
</html>