@include('member.layout.head', ['pageTitle' => 'Account Info | Realty Emails'])

<body data-section="member" class="relative min-h-screen bg-[#f0f2f7] font-sans text-slate-800">

@include('member.layout.nav')

@include('member.layout.profileStyles')

{{--
    The agent's own account page: plan (type, start date, expiry, credits), a little activity,
    the sign-in username with a "send me a password reset link" button, and order history.
    Data from memberController::accountInfo. Nothing on this page is editable except that
    the password link is emailed on request - name, address, photo etc. are on Agent Info.
--}}
<style>
    .ac-tiles   { display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 14px; }
    .ac-tile    { border: 1px solid #e6eaf2; border-radius: 16px; padding: 16px 18px; background: #fff; min-width: 0; }
    .ac-tile .k { font-size: 12px; font-weight: 800; letter-spacing: .05em; text-transform: uppercase; color: #64748b; }
    .ac-tile .v { margin-top: 6px; font-size: 24px; line-height: 1.15; font-weight: 800; color: #0f172a; word-break: break-word; }
    .ac-tile .v.sm { font-size: 18px; }
    .ac-tile .n { margin-top: 6px; font-size: 13px; color: #64748b; line-height: 1.4; }
    .ac-tile.warn { border-color: #fecaca; background: #fef8f8; }
    .ac-tile.warn .v { color: #b91c1c; }
    .ac-tile .ai-btn { margin-top: 10px; }

    .ac-row     { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 14px;
                  padding: 14px 0; border-bottom: 1px solid #eef1f6; }
    .ac-row:last-child { border-bottom: 0; padding-bottom: 0; }
    .ac-row:first-child { padding-top: 0; }
    .ac-row .k  { font-size: 14px; font-weight: 700; color: #475569; }
    .ac-row .v  { font-size: 15px; font-weight: 700; color: #0f172a; word-break: break-all; text-align: right; }
    .ac-help    { margin: 12px 0 0; font-size: 13px; color: #64748b; line-height: 1.5; }

    .ac-table   { width: 100%; border-collapse: collapse; font-size: 14px; }
    .ac-table th { padding: 10px 14px; text-align: left; font-size: 12px; font-weight: 800; letter-spacing: .05em; text-transform: uppercase;
                   color: #64748b; background: #f8fafd; border-bottom: 1px solid #e6eaf2; white-space: nowrap; }
    .ac-table td { padding: 12px 14px; border-bottom: 1px solid #eef1f6; color: #334155; vertical-align: middle; }
    .ac-table tr:last-child td { border-bottom: 0; }
    .ac-table .num { text-align: right; white-space: nowrap; font-weight: 700; }
    .ac-scroll  { overflow-x: auto; border: 1px solid #e6eaf2; border-radius: 14px; }
    .ac-badge   { display: inline-block; padding: 3px 10px; border-radius: 999px; font-size: 12px; font-weight: 800; background: #eef1f6; color: #475569; }
    .ac-badge.good { background: #dcfce7; color: #166534; }
    .ac-badge.wait { background: #fef3c7; color: #92400e; }
    .ac-badge.bad  { background: #fee2e2; color: #991b1b; }
    .ac-empty   { padding: 28px 16px; text-align: center; font-size: 14px; color: #64748b; border: 1px dashed #d5dbe6; border-radius: 14px; }

    @media (max-width: 720px) {
        .ac-row .v { text-align: left; }
    }
</style>

@php
    $type      = (int) ($agent->accountType ?? 0);
    $hasExpiry = \App\Support\AccountTypes::hasExpiry($type);
    $hasPrio   = \App\Support\AccountTypes::hasPriorityCredits($type);
    $hasCreds  = \App\Support\AccountTypes::hasCredits($type);

    $nice = fn ($d) => $d ? \Carbon\Carbon::parse($d)->format('M j, Y') : null;

    // "today" in the agent's own timezone, compared as plain dates
    $todayStr  = \Carbon\Carbon::now(\App\Support\AgentTime::timezoneFor($agent))->format('Y-m-d');
    $expireStr = $agent->expireDate ? \Carbon\Carbon::parse($agent->expireDate)->format('Y-m-d') : null;
    $expired   = $hasExpiry && $expireStr !== null && $expireStr < $todayStr;
    $daysLeft  = ($hasExpiry && $expireStr !== null && !$expired)
        ? (int) \Carbon\Carbon::parse($todayStr)->diffInDays(\Carbon\Carbon::parse($expireStr), false)
        : null;

    $lastSignIn  = $agent->lastLogin ? \Carbon\Carbon::parse($agent->lastLogin)->format('M j, Y \a\t g:i A') : null;
    $pwChanged   = array_key_exists('passwordResetAt', $agent->getAttributes()) && $agent->passwordResetAt
        ? \Carbon\Carbon::parse($agent->passwordResetAt)->format('M j, Y')
        : null;

    $orderBadge = function ($status) {
        $s = strtolower((string) $status);

        return match (true) {
            str_contains($s, 'complete'), str_contains($s, 'paid'), str_contains($s, 'success'), str_contains($s, 'processed') => 'good',
            str_contains($s, 'pending'), str_contains($s, 'wait'), str_contains($s, 'process')                                  => 'wait',
            str_contains($s, 'refund'), str_contains($s, 'revers'), str_contains($s, 'fail'), str_contains($s, 'den'),
            str_contains($s, 'cancel'), str_contains($s, 'void'), str_contains($s, 'expire')                                    => 'bad',
            default                                                                                                          => '',
        };
    };
@endphp

<main class="min-h-screen bg-[#f0f2f7] pt-24">
<div class="ai-wrap">

    <div class="ai-head">
        <h1>Account Info</h1>
        <p>Your plan, credits and sign-in details. Your name, address and photo are on <a href="/member/agent-info" style="color:#123f91;font-weight:700">Agent Info</a>.</p>
    </div>

    @if(session('status'))
        <div class="ai-alert ok">{{ session('status') }}</div>
    @endif

    @if($errors->any())
        <div class="ai-alert bad">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    {{-- ========== YOUR PLAN ========== --}}
    <section class="ai-card">
        <header class="ai-sec-h">
            <span class="ai-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/><path d="M6 15h4"/></svg>
            </span>
            <div>
                <h2>Your plan</h2>
                <p>What your account includes.</p>
            </div>
        </header>

        <div class="ai-card-b">
            <div class="ac-tiles">

                <div class="ac-tile">
                    <div class="k">Account type</div>
                    <div class="v sm">{{ \App\Support\AccountTypes::label($type) }}</div>
                    @if(\App\Support\AccountTypes::description($type) !== '')
                        <div class="n">{{ \App\Support\AccountTypes::description($type) }}</div>
                    @endif
                </div>

                <div class="ac-tile">
                    <div class="k">Start date</div>
                    <div class="v sm">{{ $nice($agent->startDate) ?? '—' }}</div>
                </div>

                @if($hasExpiry)
                    <div class="ac-tile {{ $expired ? 'warn' : '' }}">
                        <div class="k">Expire date</div>
                        <div class="v sm">{{ $nice($agent->expireDate) ?? '—' }}</div>
                        @if($expired)
                            <div class="n">This plan has expired.</div>
                            <a href="/member/buy-credits" class="ai-btn primary">Renew</a>
                        @elseif($daysLeft !== null)
                            <div class="n">{{ $daysLeft === 0 ? 'Expires today' : 'In ' . number_format($daysLeft) . ' ' . ($daysLeft === 1 ? 'day' : 'days') }}</div>
                        @endif
                    </div>
                @endif

                @if($hasPrio)
                    <div class="ac-tile">
                        <div class="k">Priority credits</div>
                        <div class="v">{{ number_format((int) ($agent->pCreds ?? 0)) }}</div>
                    </div>
                @endif

                @if($hasCreds)
                    <div class="ac-tile">
                        <div class="k">Credits</div>
                        <div class="v">{{ number_format((int) ($agent->remCreds ?? 0)) }}</div>
                        <div class="n">Each flyer you send uses one credit.</div>
                        <a href="/member/buy-credits" class="ai-btn primary">Buy credits</a>
                    </div>
                @endif

            </div>
        </div>
    </section>

    {{-- ========== ACTIVITY ========== --}}
    <section class="ai-card">
        <header class="ai-sec-h">
            <span class="ai-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="m7 15 4-4 3 3 5-6"/></svg>
            </span>
            <div>
                <h2>Your activity</h2>
                <p>A quick look at how you've used RealtyEmails.</p>
            </div>
        </header>

        <div class="ai-card-b">
            <div class="ac-tiles">
                <div class="ac-tile">
                    <div class="k">Flyers created</div>
                    <div class="v">{{ number_format($flyers) }}</div>
                </div>
                <div class="ac-tile">
                    <div class="k">Campaigns sent</div>
                    <div class="v">{{ number_format($sent) }}</div>
                </div>
                <div class="ac-tile">
                    <div class="k">Emails delivered</div>
                    <div class="v">{{ number_format($emails) }}</div>
                </div>
                <div class="ac-tile">
                    <div class="k">Last sign-in</div>
                    <div class="v sm">{{ $lastSignIn ?? '—' }}</div>
                </div>
                <div class="ac-tile">
                    <div class="k">Account number</div>
                    <div class="v sm">#{{ $agent->id }}</div>
                    <div class="n">Handy if you contact support.</div>
                </div>
            </div>
        </div>
    </section>

    {{-- ========== SIGN-IN & PASSWORD ========== --}}
    <section id="signin" class="ai-card">
        <header class="ai-sec-h">
            <span class="ai-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
            </span>
            <div>
                <h2>Sign-in &amp; password</h2>
                <p>What you use to sign in.</p>
            </div>
        </header>

        <div class="ai-card-b">
            <div class="ac-row">
                <span class="k">Username</span>
                <span class="v">{{ $agent->xxAgtUname ?: '—' }}</span>
            </div>

            <div class="ac-row">
                <span class="k">Password last changed</span>
                <span class="v">{{ $pwChanged ?? '—' }}</span>
            </div>

            <div class="ac-row">
                <span class="k">Reset your password</span>
                <form method="POST" action="{{ route('member.account.passwordLink') }}">
                    @csrf
                    <button type="submit" class="ai-btn primary">Email me a reset link</button>
                </form>
            </div>

            <p class="ac-help">
                We'll send a one-time link to <strong>{{ $maskedEmail }}</strong>. It works once and expires in
                {{ \App\Support\AgentPasswords::LINK_MINUTES }} minutes. Your username can't be changed here &mdash;
                contact support if you need it changed.
            </p>
        </div>
    </section>

    {{-- ========== ORDER HISTORY ========== --}}
    <section class="ai-card">
        <header class="ai-sec-h">
            <span class="ai-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2h12v20l-3-2-3 2-3-2-3 2z"/><path d="M9 7h6M9 11h6M9 15h4"/></svg>
            </span>
            <div>
                <h2>Order history</h2>
                <p>{{ $orders->isEmpty() ? 'Your purchases will appear here.' : number_format($orders->count()) . ' ' . ($orders->count() === 1 ? 'order' : 'orders') . ', newest first.' }}</p>
            </div>
        </header>

        <div class="ai-card-b">
            @if($orders->isEmpty())
                <div class="ac-empty">No orders yet.</div>
            @else
                <div class="ac-scroll">
                    <table class="ac-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Item</th>
                                <th style="text-align:right">Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                <tr>
                                    <td style="white-space:nowrap">{{ $nice($order->payment_date) ?? '—' }}</td>
                                    <td>{{ $order->item_name ?: '—' }}</td>
                                    <td class="num">
                                        @if($order->payment_gross !== null && $order->payment_gross !== '')
                                            ${{ number_format((float) $order->payment_gross, 2) }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>
                                        @if($order->payment_status)
                                            <span class="ac-badge {{ $orderBadge($order->payment_status) }}">{{ $order->payment_status }}</span>
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </section>

</div>
</main>

@include('public.layout.footer')

</body>
</html>
