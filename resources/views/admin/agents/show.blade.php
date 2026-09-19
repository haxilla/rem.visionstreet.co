@include('public.layout.head')

<body data-section="admin" class="relative bg-white min-h-screen font-sans text-gray-800">

@include('admin.layout.nav')

@php
    $displayName = trim(($agent->agtFirst ?? '') . ' ' . ($agent->agtLast ?? ''));
    if ($displayName === '') {
        $displayName = $agent->agtFullName ?: $agent->agtUname ?: $agent->agtEmail ?: 'No Name';
    }

    $showExpireDate = in_array((int) $agent->accountType, [2, 3], true);
@endphp

<main class="min-h-screen bg-[#f4f7fb] pt-24">
<div class="px-4 py-6 sm:px-6 lg:px-10 lg:py-8">

    {{-- HEADER --}}
    <div class="rounded-[24px] bg-white px-5 py-6 shadow-[0_12px_35px_rgba(15,23,42,0.06)] sm:px-8 sm:py-7">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <div class="text-[12px] font-semibold uppercase tracking-[0.22em] text-[#214e9b]/70">
                    Admin / Agents
                </div>

                <h1 class="mt-2 text-2xl font-semibold text-slate-900 sm:text-[32px]">
                    {{ $displayName }}
                </h1>

                <p class="mt-2 text-[14px] text-slate-600">
                    Agent ID {{ $agent->id }}
                </p>
            </div>

            <div class="flex gap-2">
                <a href="/admin/agents" class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                    Back to Agents
                </a>
                <a href="/admin/agentFlyerCreate/{{ $agent->id }}" class="rounded-lg border border-[#214e9b] px-4 py-2.5 text-sm font-semibold text-[#214e9b] hover:bg-[#214e9b]/5">
                    Create Flyer
                </a>
                <a href="/admin/agentLogin/{{ $agent->id }}" class="rounded-lg bg-[#16213e] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#22315a]">
                    Log in as this agent
                </a>
            </div>
        </div>
    </div>

    {{-- HISTORY STAT TILES --}}
    <div class="mt-6 grid grid-cols-2 gap-3">
        <div class="rounded-2xl bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                Flyers Created
            </div>
            <div class="mt-2 text-3xl font-semibold text-slate-900">
                {{ $flyerCount }}
            </div>
        </div>

        <div class="rounded-2xl bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                Campaigns Sent
            </div>
            <div class="mt-2 text-3xl font-semibold text-slate-900">
                {{ $campaignCount }}
            </div>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">

        {{-- AGENT INFO --}}
        <div class="rounded-[24px] bg-white p-5 shadow-[0_12px_35px_rgba(15,23,42,0.06)] sm:p-6">
            <h2 class="text-lg font-semibold text-slate-900">Agent Info</h2>

            <dl class="mt-4 divide-y divide-slate-100 text-sm">
                <div class="flex justify-between gap-4 py-2.5">
                    <dt class="text-slate-500">Username</dt>
                    <dd class="text-right font-medium text-slate-900">{{ $agent->agtUname ?: '—' }}</dd>
                </div>
                <div class="flex justify-between gap-4 py-2.5">
                    <dt class="text-slate-500">Email</dt>
                    <dd class="text-right font-medium text-slate-900">{{ $agent->agtEmail ?: '—' }}</dd>
                </div>
                <div class="flex justify-between gap-4 py-2.5">
                    <dt class="text-slate-500">Main Phone</dt>
                    <dd class="text-right font-medium text-slate-900">{{ $agent->agtMainPhone ?: '—' }}</dd>
                </div>
                <div class="flex justify-between gap-4 py-2.5">
                    <dt class="text-slate-500">Mobile</dt>
                    <dd class="text-right font-medium text-slate-900">{{ $agent->agtMobile ?: '—' }}</dd>
                </div>
                <div class="flex justify-between gap-4 py-2.5">
                    <dt class="text-slate-500">Home Phone</dt>
                    <dd class="text-right font-medium text-slate-900">{{ $agent->agtHomePhone ?: '—' }}</dd>
                </div>
                <div class="flex justify-between gap-4 py-2.5">
                    <dt class="text-slate-500">Secondary Phone</dt>
                    <dd class="text-right font-medium text-slate-900">{{ $agent->agtPhone2 ?: '—' }}</dd>
                </div>
                <div class="flex justify-between gap-4 py-2.5">
                    <dt class="text-slate-500">Website</dt>
                    <dd class="text-right font-medium text-slate-900">{{ $agent->agtWebsite ?: '—' }}</dd>
                </div>
                <div class="flex justify-between gap-4 py-2.5">
                    <dt class="text-slate-500">Address</dt>
                    <dd class="text-right font-medium text-slate-900">
                        @if($agent->agtAddress1 || $agent->agtCity)
                            {{ $agent->agtAddress1 }}
                            @if($agent->agtAddress2) {{ $agent->agtAddress2 }} @endif
                            <br>{{ $agent->agtCity }}@if($agent->agtCity && $agent->agtState), @endif{{ $agent->agtState }} {{ $agent->agtZip }}
                        @else
                            —
                        @endif
                    </dd>
                </div>
                <div class="flex justify-between gap-4 py-2.5">
                    <dt class="text-slate-500">County</dt>
                    <dd class="text-right font-medium text-slate-900">{{ $agent->agtCounty ?: '—' }}</dd>
                </div>
                <div class="flex justify-between gap-4 py-2.5">
                    <dt class="text-slate-500">MLS ID</dt>
                    <dd class="text-right font-medium text-slate-900">{{ $agent->agtMlsID ?: '—' }}</dd>
                </div>
                <div class="flex justify-between gap-4 py-2.5">
                    <dt class="text-slate-500">Board</dt>
                    <dd class="text-right font-medium text-slate-900">{{ $agent->agtBoard ?: '—' }}</dd>
                </div>
                <div class="flex justify-between gap-4 py-2.5">
                    <dt class="text-slate-500">Designations</dt>
                    <dd class="text-right font-medium text-slate-900">{{ $agent->agtDesigs ?: '—' }}</dd>
                </div>
                <div class="flex justify-between gap-4 py-2.5">
                    <dt class="text-slate-500">Office / Brokerage</dt>
                    <dd class="text-right font-medium text-slate-900">{{ $agent->theAgtOffice->officeName ?? '—' }}</dd>
                </div>
            </dl>
        </div>

        {{-- ACCOUNT INFO --}}
        <div class="rounded-[24px] bg-white p-5 shadow-[0_12px_35px_rgba(15,23,42,0.06)] sm:p-6">
            <h2 class="text-lg font-semibold text-slate-900">Account Info</h2>

            <dl class="mt-4 divide-y divide-slate-100 text-sm">
                <div class="flex justify-between gap-4 py-2.5">
                    <dt class="text-slate-500">Account Type</dt>
                    <dd class="text-right font-medium text-slate-900">{{ $agent->accountType ?? '—' }}</dd>
                </div>
                <div class="flex justify-between gap-4 py-2.5">
                    <dt class="text-slate-500">Start Date</dt>
                    <dd class="text-right font-medium text-slate-900">
                        {{ $agent->startDate ? \Carbon\Carbon::parse($agent->startDate)->format('m/d/Y') : '—' }}
                    </dd>
                </div>
                @if($showExpireDate)
                    <div class="flex justify-between gap-4 py-2.5">
                        <dt class="text-slate-500">Expire Date</dt>
                        <dd class="text-right font-medium text-slate-900">
                            {{ $agent->expireDate ? \Carbon\Carbon::parse($agent->expireDate)->format('m/d/Y') : '—' }}
                        </dd>
                    </div>
                @endif
                <div class="flex justify-between gap-4 py-2.5">
                    <dt class="text-slate-500">Remaining Credits</dt>
                    <dd class="text-right font-medium text-slate-900">{{ $agent->remCreds ?? 0 }}</dd>
                </div>
                <div class="flex justify-between gap-4 py-2.5">
                    <dt class="text-slate-500">Purchased Credits</dt>
                    <dd class="text-right font-medium text-slate-900">{{ $agent->pCreds ?? 0 }}</dd>
                </div>
                <div class="flex justify-between gap-4 py-2.5">
                    <dt class="text-slate-500">Last Login</dt>
                    <dd class="text-right font-medium text-slate-900">
                        {{ $agent->lastLogin ? \Carbon\Carbon::parse($agent->lastLogin)->format('m/d/Y g:ia') : '—' }}
                    </dd>
                </div>
                <div class="flex justify-between gap-4 py-2.5">
                    <dt class="text-slate-500">Last Known IP</dt>
                    <dd class="text-right font-medium text-slate-900">{{ $agent->IP ?: '—' }}</dd>
                </div>
                @if($agent->moved || $agent->removalDate)
                    <div class="flex justify-between gap-4 py-2.5">
                        <dt class="text-slate-500">Moved / Removal Date</dt>
                        <dd class="text-right font-medium text-red-600">
                            {{ $agent->removalDate ? \Carbon\Carbon::parse($agent->removalDate)->format('m/d/Y') : 'Flagged as moved' }}
                        </dd>
                    </div>
                @endif
                @if($agent->agtReview || $agent->agtReviewDate)
                    <div class="flex justify-between gap-4 py-2.5">
                        <dt class="text-slate-500">Admin Review</dt>
                        <dd class="text-right font-medium text-slate-900">
                            {{ $agent->agtReview ?: '—' }}
                            @if($agent->agtReviewDate)
                                ({{ \Carbon\Carbon::parse($agent->agtReviewDate)->format('m/d/Y') }})
                            @endif
                        </dd>
                    </div>
                @endif
            </dl>
        </div>

    </div>

    {{-- PURCHASES --}}
    <div class="mt-6 rounded-[24px] bg-white p-5 shadow-[0_12px_35px_rgba(15,23,42,0.06)] sm:p-6">
        <h2 class="text-lg font-semibold text-slate-900">Purchases</h2>

        @if($orders->isEmpty())
            <div class="mt-4 rounded-2xl border border-dashed border-slate-200 px-4 py-10 text-center text-sm text-slate-500">
                No purchase records found for this agent.
            </div>
        @else
            <div class="mt-4 overflow-x-auto rounded-2xl border border-slate-200">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Item</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach($orders as $order)
                            <tr class="hover:bg-slate-50">
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-700">{{ $order->item_name ?: '—' }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-700">
                                    {{ $order->payment_date ? \Carbon\Carbon::parse($order->payment_date)->format('m/d/Y') : '—' }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-700">
                                    @if($order->payment_gross !== null)
                                        ${{ number_format($order->payment_gross, 2) }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-700">{{ $order->payment_status ?: '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
</main>

@include('public.layout.footer')

</body>
</html>
