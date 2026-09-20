@include('public.layout.head')

<body data-section="admin" class="relative bg-white min-h-screen font-sans text-gray-800">

@include('admin.layout.nav')

{{--
    Merge duplicate accounts: tick flyers, choose the account that receives them.
    Only reachable for accounts that share a login email (adminController::agentMerge).
    Needs: $agent (the account this was opened from), $accounts (built in the controller).
    The few layout rules below are plain CSS so the page never depends on a stylesheet rebuild.
--}}
<style>
    .mg-thumb   { width: 64px; height: 44px; object-fit: cover; border-radius: 6px; background: #e2e8f0; display: block; }
    .mg-nothumb { width: 64px; height: 44px; border-radius: 6px; background: #f1f5f9; }
    .mg-row td  { vertical-align: middle; }
    .mg-row.is-off { opacity: .45; }
</style>

@php
    $mdy = fn ($d) => $d ? \Carbon\Carbon::parse($d)->format('m/d/Y') : '—';
    $email = $agent->xxAgtUname;
    $card  = 'rounded-[24px] bg-white shadow-[0_12px_35px_rgba(15,23,42,0.06)]';
@endphp

<main class="min-h-screen bg-[#f4f7fb] pt-24">
<div class="mx-auto max-w-[1400px] px-4 py-6 sm:px-6 lg:px-10 lg:py-8">

    {{-- HEADER --}}
    <div class="{{ $card }} px-5 py-6 sm:px-8 sm:py-7">
        <div class="text-[12px] font-semibold uppercase tracking-[0.22em] text-[#214e9b]/70">Admin / Agents</div>

        <h1 class="mt-2 text-2xl font-semibold text-slate-900 sm:text-[30px]">Merge duplicate accounts</h1>

        <p class="mt-2 break-all text-sm text-slate-600">
            Accounts using <span class="font-semibold text-slate-900">{{ $email }}</span>
        </p>

        <p class="mt-2 max-w-3xl text-sm text-slate-500">
            Tick the flyers to move and choose the account that should receive them. Each flyer's photos, style,
            remarks, map and campaign history go with it. Credits, start date, photo, logo and office stay with
            the account they belong to.
        </p>

        <div class="mt-4 flex flex-wrap gap-2">
            <a href="/admin/agents?duplicates=1" class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                Back to Duplicate Logins
            </a>
        </div>
    </div>

    @if(session('status'))
        <div class="mt-6 rounded-2xl border border-emerald-300 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-semibold text-red-700">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('admin.agentMergeSave', $agent->id) }}" id="mergeForm" class="mt-6 space-y-6">
        @csrf

        {{-- DESTINATION --}}
        <div class="{{ $card }} px-5 py-5 sm:px-8">
            <label for="destination" class="block text-sm font-semibold text-slate-800">
                Move the ticked flyers into
            </label>

            <div class="mt-2 flex flex-wrap items-center gap-3">
                <select id="destination" name="destination" required
                        class="min-w-[18rem] rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-[#214e9b] focus:outline-none focus:ring-2 focus:ring-[#214e9b]/20">
                    <option value="">Choose an account...</option>
                    @foreach($accounts as $acct)
                        <option value="{{ $acct['id'] }}" data-name="{{ $acct['name'] }} (#{{ $acct['id'] }})" @selected(old('destination') == $acct['id'])>
                            #{{ $acct['id'] }} &mdash; {{ $acct['name'] }} ({{ count($acct['flyers']) }} {{ count($acct['flyers']) === 1 ? 'flyer' : 'flyers' }})
                        </option>
                    @endforeach
                </select>

                <button type="submit" id="moveBtn" disabled
                        class="rounded-lg bg-[#214e9b] px-4 py-2 text-sm font-semibold text-white hover:bg-[#1b3f80] disabled:cursor-not-allowed disabled:opacity-40">
                    Move flyers
                </button>

                <span id="selectedCount" class="text-sm text-slate-500">0 flyers ticked</span>
            </div>
        </div>

        {{-- ONE BLOCK PER ACCOUNT --}}
        @foreach($accounts as $acct)
            <div class="{{ $card }} overflow-hidden" data-block="{{ $acct['id'] }}">

                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 sm:px-6">
                    <div class="min-w-0">
                        <h2 class="text-base font-semibold text-slate-900 sm:text-lg">
                            <a href="/admin/agentView/{{ $acct['id'] }}" class="hover:underline">{{ $acct['name'] }}</a>
                            <span class="ml-1 text-sm font-normal text-slate-400">#{{ $acct['id'] }}</span>
                        </h2>
                        <p class="mt-0.5 text-sm text-slate-500">
                            {{ $acct['office'] ?: 'No office' }}
                            &middot; {{ count($acct['flyers']) }} {{ count($acct['flyers']) === 1 ? 'flyer' : 'flyers' }}
                            &middot; {{ $acct['credits'] }} credits
                            &middot; start {{ $mdy($acct['start']) }}
                        </p>
                    </div>

                    {{-- Delete only shows once the account has no flyers left (deleted ones count) --}}
                    @if(count($acct['flyers']) === 0)
                        @if($acct['can_delete'])
                            <button type="submit"
                                    formaction="{{ route('admin.agentDeleteDuplicate', $acct['id']) }}"
                                    formnovalidate
                                    name="from" value="merge"
                                    @if($confirmDelete ?? true) onclick="return confirm({{ \Illuminate\Support\Js::from('Delete account #' . $acct['id'] . ' ' . $acct['name'] . '? This cannot be undone.') }})" @endif
                                    class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50">
                                Delete account
                            </button>
                        @else
                            <div class="max-w-md text-xs text-slate-500">
                                <div>Can't delete yet: it has {{ implode(', ', $acct['reasons']) }}.</div>

                                @if($acct['orders'] > 0 || $acct['campaigns'] > 0)
                                    {{-- orders + campaign records can be moved into the account chosen above --}}
                                    <button type="submit"
                                            formaction="{{ route('admin.agentMoveRecords', $acct['id']) }}"
                                            formnovalidate
                                            data-needs-dest="1"
                                            @if($confirmDelete ?? true) data-confirm-records="1" @endif
                                            class="mt-1.5 rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-40">
                                        Move its {{ $acct['orders'] }} {{ $acct['orders'] === 1 ? 'order' : 'orders' }} and
                                        {{ $acct['campaigns'] }} campaign {{ $acct['campaigns'] === 1 ? 'record' : 'records' }} into the account chosen above
                                    </button>
                                @endif

                                @if(in_array('credits', $acct['reasons'], true))
                                    <div class="mt-1">Credits: set them on the agent's page.</div>
                                @endif
                            </div>
                        @endif
                    @endif

                    @if(count($acct['flyers']) > 0)
                        <div class="flex gap-2">
                            <button type="button" data-select-all="{{ $acct['id'] }}"
                                    class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                                Tick all
                            </button>
                            <button type="button" data-select-none="{{ $acct['id'] }}"
                                    class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                                Clear
                            </button>
                        </div>
                    @endif
                </div>

                @if(count($acct['flyers']) === 0)
                    <div class="px-5 py-8 text-center text-sm text-slate-500 sm:px-6">This account has no flyers.</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="w-12 px-4 py-2"></th>
                                    <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Flyer</th>
                                    <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Created</th>
                                    <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Last sent</th>
                                    <th class="px-4 py-2 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Hits</th>
                                    <th class="px-4 py-2"></th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-100">
                                @foreach($acct['flyers'] as $flyer)
                                    <tr class="mg-row">
                                        <td class="px-4 py-3">
                                            <input type="checkbox" name="flyers[]" value="{{ $flyer['id'] }}"
                                                   data-account="{{ $acct['id'] }}"
                                                   aria-label="Move {{ $flyer['title'] }}"
                                                   class="h-5 w-5 rounded border-slate-300"
                                                   @if($flyer['busy']) data-busy="1" disabled @endif>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-3">
                                                @if($flyer['thumb'])
                                                    <img src="{{ $flyer['thumb'] }}" alt="" loading="lazy" class="mg-thumb">
                                                @else
                                                    <div class="mg-nothumb"></div>
                                                @endif
                                                <div class="min-w-0">
                                                    <div class="text-sm font-semibold text-slate-900">{{ $flyer['title'] }}</div>
                                                    <div class="text-xs text-slate-500">{{ $flyer['place'] ?: '—' }} &middot; ID {{ $flyer['id'] }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-600">{{ $mdy($flyer['created']) }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-600">{{ $mdy($flyer['last_sent']) }}</td>
                                        <td class="px-4 py-3 text-right text-sm text-slate-600">{{ number_format($flyer['hits']) }}</td>
                                        <td class="px-4 py-3 text-xs font-semibold">
                                            @if($flyer['deleted'])
                                                <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-slate-600">Deleted</span>
                                            @endif
                                            @if($flyer['busy'])
                                                <span class="rounded-full bg-amber-50 px-2.5 py-0.5 text-amber-700 ring-1 ring-amber-200">Delivering now</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @endforeach

    </form>
</div>
</main>

<script>
(function () {
    var form   = document.getElementById('mergeForm');
    var dest   = document.getElementById('destination');
    var btn    = document.getElementById('moveBtn');
    var count  = document.getElementById('selectedCount');
    var boxes  = Array.prototype.slice.call(form.querySelectorAll('input[name="flyers[]"]'));

    function ticked() {
        return boxes.filter(function (b) { return b.checked && !b.disabled; });
    }

    function refresh() {
        // flyers already in the chosen account can't be moved into it
        boxes.forEach(function (b) {
            var here = dest.value !== '' && b.getAttribute('data-account') === dest.value;
            var row  = b.closest('tr');

            if (here) { b.checked = false; }

            // "Delivering now" boxes are disabled by the server and stay that way
            b.disabled = here || b.hasAttribute('data-busy');
            if (row) { row.classList.toggle('is-off', b.disabled); }
        });

        var n = ticked().length;
        count.textContent = n + (n === 1 ? ' flyer ticked' : ' flyers ticked');
        btn.disabled = (n === 0 || dest.value === '');
    }

    // "Move its orders and campaign records" buttons need a destination (and not itself)
    var recordBtns = Array.prototype.slice.call(form.querySelectorAll('[data-needs-dest]'));

    function refreshRecordButtons() {
        recordBtns.forEach(function (rb) {
            var own = rb.closest('[data-block]');
            var self = own ? own.getAttribute('data-block') : null;
            rb.disabled = (dest.value === '' || dest.value === self);
        });
    }

    recordBtns.forEach(function (rb) {
        rb.addEventListener('click', function (e) {
            if (rb.hasAttribute('data-confirm-records')) {
                var name = dest.options[dest.selectedIndex].getAttribute('data-name');
                if (!confirm("Move this account's orders and campaign records into " + name + "?")) { e.preventDefault(); }
            }
        });
    });

    dest.addEventListener('change', refreshRecordButtons);
    refreshRecordButtons();

    dest.addEventListener('change', refresh);
    boxes.forEach(function (b) { b.addEventListener('change', refresh); });

    function setAccount(id, on) {
        boxes.forEach(function (b) {
            if (b.getAttribute('data-account') === id && !b.disabled) { b.checked = on; }
        });
        refresh();
    }

    Array.prototype.forEach.call(form.querySelectorAll('[data-select-all]'), function (el) {
        el.addEventListener('click', function () { setAccount(el.getAttribute('data-select-all'), true); });
    });
    Array.prototype.forEach.call(form.querySelectorAll('[data-select-none]'), function (el) {
        el.addEventListener('click', function () { setAccount(el.getAttribute('data-select-none'), false); });
    });

    form.addEventListener('submit', function (e) {
        // a "Delete account" button submits this same form to its own address - not a move
        if (e.submitter && e.submitter.hasAttribute('formaction')) { return; }

        var n    = ticked().length;
        var name = dest.options[dest.selectedIndex].getAttribute('data-name');

        if (!confirm('Move ' + n + (n === 1 ? ' flyer' : ' flyers') + ' into ' + name + '?')) {
            e.preventDefault();
        }
    });

    refresh();
})();
</script>

</body>
</html>
