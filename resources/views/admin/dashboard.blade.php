@include('public.layout.head')

<body data-section="admin" class="relative bg-white min-h-screen font-sans text-gray-800">

@include('admin.layout.nav')

{{-- Layout of a dashboard campaign card (admin/dashRow), as PLAIN CSS on purpose: where
     things sit must not depend on Tailwind classes that only exist after a stylesheet
     rebuild. Wide: a grid of thumbnail | address + agent + subject (takes the rest) |
     dates | areas button, all vertically centred, so nothing can wrap onto a row of its
     own. Narrow: the thumbnail and details on top, the dates and button underneath. --}}
<style>

    .dash-card {
        display: grid;
        grid-template-columns: 5rem minmax(0, 1fr);
        gap: 0.6rem 0.9rem;
        align-items: start;
        padding: 0.75rem;
    }

    .dc-thumb   { width: 5rem; height: 3.75rem; }
    .dc-main    { min-width: 0; }
    .dc-dates   { grid-column: 1 / -1; }
    .dc-toggle  { grid-column: 1 / -1; }

    /* the address line and the agent line wrap their items; the subject stays on one line */
    .dc-line    { display: flex; flex-wrap: wrap; align-items: center; column-gap: 0.5rem; row-gap: 0.25rem; }
    .dc-subject { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

    .dc-pair    { display: flex; gap: 0.5rem; }
    .dc-label   { width: 5rem; flex: none; font-size: 0.6875rem; font-weight: 700; letter-spacing: 0.04em; text-transform: uppercase; color: #64748b; }

    @media (min-width: 900px) {
        .dash-card  { grid-template-columns: 6rem minmax(0, 1fr) 16.5rem auto; align-items: center; }
        .dc-thumb   { width: 6rem; height: 4.5rem; }
        .dc-dates,
        .dc-toggle  { grid-column: auto; }
    }
</style>

@php
    /*
        One card per flyer per stage (its requested areas open in a dropdown); the
        grouping and everything on the cards is built in app/admin/dashboard.php.

        Stages:
        - Waiting: requested, the request time has passed, delivery not started
          (Unauthorized = at least one area still to authorize; Authorized = all are)
        - In Progress: started, not finished
        - Completed: finished - the 10 most recently finished flyers
    */

    $waitingUnauthorized = $data['waitingUnauthorized'] ?? collect();
    $waitingAuthorized   = $data['waitingAuthorized'] ?? collect();
    $inProgress          = $data['inProgress'] ?? collect();
    $completed           = $data['completed'] ?? collect();

    $waitingAll = $waitingUnauthorized->concat($waitingAuthorized);

    // "12 flyers · 34 areas · 152,300 contacts" for a list of flyer groups
    $summary = function ($groups) {
        $areas    = $groups->sum(fn ($g) => $g['areas']->count());
        $contacts = $groups->sum('contacts');

        return number_format($groups->count()) . ' ' . ($groups->count() === 1 ? 'flyer' : 'flyers')
            . ' · ' . number_format($areas) . ' ' . ($areas === 1 ? 'area' : 'areas')
            . ($contacts > 0 ? ' · ' . number_format($contacts) . ' contacts' : '');
    };

    $oldestWaiting = $waitingAll->pluck('requested')->filter()->sort()->first();

    $empty = 'rounded-2xl border border-dashed border-slate-300 p-8 text-center text-sm text-slate-500';
@endphp

<main class="min-h-screen bg-[#f4f7fb] pt-[72px]">
    <div class="px-4 py-4 sm:px-6 lg:px-10 lg:py-5">

            <div>

                        {{-- CAMPAIGN DASHBOARD --}}
                        <div class="rounded-2xl bg-white shadow-[0_12px_35px_rgba(15,23,42,0.06)] overflow-hidden">

                            {{-- TITLE + MAIN TAB BUTTONS: one slim bar, the title on the left and the
                                 tabs on the right, evenly padded all round --}}
                            <div class="flex flex-col gap-3 border-b border-slate-200 bg-slate-50 px-4 py-3 sm:flex-row sm:items-center sm:justify-between sm:gap-6 sm:px-6">

                                <h1 class="text-lg font-semibold leading-tight text-slate-900">Campaign Overview</h1>

                                <div class="grid grid-cols-1 gap-2 sm:flex sm:flex-wrap">

                                    <button
                                        type="button"
                                        class="campaign-tab-btn bg-[#214e9b] text-white rounded-xl px-5 py-2 text-sm font-semibold shadow"
                                        data-tab="waiting"
                                    >
                                        Waiting
                                        <span class="ml-2 rounded-full bg-white/20 px-2 py-0.5 text-xs">
                                            {{ $waitingAll->count() }}
                                        </span>
                                    </button>

                                    <button
                                        type="button"
                                        class="campaign-tab-btn bg-slate-200 text-slate-700 rounded-xl px-5 py-2 text-sm font-semibold"
                                        data-tab="progress"
                                    >
                                        In Progress
                                        <span class="ml-2 rounded-full bg-white/50 px-2 py-0.5 text-xs">
                                            {{ $inProgress->count() }}
                                        </span>
                                    </button>

                                    <button
                                        type="button"
                                        class="campaign-tab-btn bg-slate-200 text-slate-700 rounded-xl px-5 py-2 text-sm font-semibold"
                                        data-tab="completed"
                                    >
                                        Completed
                                        <span class="ml-2 rounded-full bg-white/50 px-2 py-0.5 text-xs">
                                            {{ $completed->count() }}
                                        </span>
                                    </button>

                                </div>
                            </div>

                            {{-- TAB CONTENT --}}
                            <div class="p-3 sm:p-5">

                                {{-- WAITING --}}
                                <div class="campaign-panel" id="tab-waiting">

                                    {{-- WAITING SUB MENU, with the tab's summary on the same row --}}
                                    <div class="mb-4 flex flex-wrap items-center justify-between gap-x-6 gap-y-2">

                                    <div class="grid grid-cols-1 gap-2 sm:flex sm:flex-wrap">
                                        <button type="button"
                                        class="waiting-tab-btn bg-emerald-600 text-white rounded-xl px-4 py-2 text-sm font-semibold shadow"
                                        data-waiting-tab="unauthorized">
                                            Unauthorized
                                            <span class="ml-2 rounded-full bg-white/50 px-2 py-0.5 text-xs">
                                                {{ $waitingUnauthorized->count() }}
                                            </span>
                                        </button>

                                        <button type="button"
                                        class="waiting-tab-btn bg-slate-200 text-slate-700 rounded-xl px-4 py-2 text-sm font-semibold"
                                        data-waiting-tab="authorized">
                                            Authorized
                                            <span class="ml-2 rounded-full bg-white/20 px-2 py-0.5 text-xs">
                                                {{ $waitingAuthorized->count() }}
                                            </span>
                                        </button>

                                    </div>

                                    <p class="text-sm text-slate-500">
                                        {{ $summary($waitingAll) }}
                                        @if($oldestWaiting)
                                            &middot; oldest request {{ $oldestWaiting->diffForHumans() }}
                                        @endif
                                    </p>

                                    </div>

                                    {{-- UNAUTHORIZED WAITING --}}
                                    <div class="waiting-panel" id="waiting-unauthorized">
                                        <div class="space-y-3">
                                            @forelse($waitingUnauthorized as $group)
                                                @include('admin.dashRow', ['group' => $group, 'stage' => 'waiting', 'tone' => 'amber'])
                                            @empty
                                                <div class="{{ $empty }}">No unauthorized waiting campaigns found.</div>
                                            @endforelse
                                        </div>
                                    </div>

                                    {{-- AUTHORIZED WAITING --}}
                                    <div class="waiting-panel hidden" id="waiting-authorized">
                                        <div class="space-y-3">
                                            @forelse($waitingAuthorized as $group)
                                                @include('admin.dashRow', ['group' => $group, 'stage' => 'waiting', 'tone' => 'indigo'])
                                            @empty
                                                <div class="{{ $empty }}">No authorized waiting campaigns found.</div>
                                            @endforelse
                                        </div>
                                    </div>

                                </div>

                                {{-- IN PROGRESS --}}
                                <div class="campaign-panel hidden" id="tab-progress">

                                    <p class="mb-4 text-sm text-slate-500">{{ $summary($inProgress) }}</p>

                                    <div class="space-y-3">
                                        @forelse($inProgress as $group)
                                            @include('admin.dashRow', ['group' => $group, 'stage' => 'progress', 'tone' => 'blue'])
                                        @empty
                                            <div class="{{ $empty }}">No in progress campaigns found.</div>
                                        @endforelse
                                    </div>

                                </div>

                                {{-- COMPLETED --}}
                                <div class="campaign-panel hidden" id="tab-completed">

                                    <p class="mb-4 text-sm text-slate-500">Last {{ $completed->count() }} flyers to finish &middot; {{ $summary($completed) }}</p>

                                    <div class="space-y-3">
                                        @forelse($completed as $group)
                                            @include('admin.dashRow', ['group' => $group, 'stage' => 'completed', 'tone' => 'emerald'])
                                        @empty
                                            <div class="{{ $empty }}">No completed campaigns found.</div>
                                        @endforelse
                                    </div>

                                </div>

                            </div>
                        </div>

            </div>

        </div>
</main>

@include('public.layout.footer')

<script>
document.addEventListener('DOMContentLoaded', function () {

    // The "N areas" button on a campaign card opens / closes that card's list of areas.
    document.addEventListener('click', function (event) {
        const button = event.target.closest('[data-areas-toggle]');

        if (!button) return;

        const card = button.closest('[data-dash-card]');
        const panel = card ? card.querySelector('[data-areas-panel]') : null;

        if (!panel) return;

        const opening = panel.hidden;

        panel.hidden = !opening;
        button.setAttribute('aria-expanded', opening ? 'true' : 'false');

        const chevron = button.querySelector('[data-areas-chevron]');

        if (chevron) chevron.style.transform = opening ? 'rotate(180deg)' : '';
    });

    const buttons = document.querySelectorAll('.campaign-tab-btn');
    const panels = document.querySelectorAll('.campaign-panel');

    buttons.forEach(function(button) {

        button.addEventListener('click', function() {

            const target = button.dataset.tab;
            const targetPanel = document.getElementById('tab-' + target);

            panels.forEach(function(panel) {
                panel.classList.add('hidden');
            });

            if (targetPanel) {
                targetPanel.classList.remove('hidden');
            }

            buttons.forEach(function(btn) {
                btn.classList.remove(
                    'bg-[#214e9b]',
                    'text-white',
                    'shadow'
                );

                btn.classList.add(
                    'bg-slate-200',
                    'text-slate-700'
                );
            });

            button.classList.remove(
                'bg-slate-200',
                'text-slate-700'
            );

            button.classList.add(
                'bg-[#214e9b]',
                'text-white',
                'shadow'
            );

        });

    });

    const waitingButtons = document.querySelectorAll('.waiting-tab-btn');
    const waitingPanels = document.querySelectorAll('.waiting-panel');

    waitingButtons.forEach(function(button) {

        button.addEventListener('click', function() {

            const target = button.dataset.waitingTab;
            const targetPanel = document.getElementById('waiting-' + target);

            waitingPanels.forEach(function(panel) {
                panel.classList.add('hidden');
            });

            if (targetPanel) {
                targetPanel.classList.remove('hidden');
            }

            waitingButtons.forEach(function(btn) {
                btn.classList.remove(
                    'bg-emerald-600',
                    'text-white',
                    'shadow'
                );

                btn.classList.add(
                    'bg-slate-200',
                    'text-slate-700'
                );
            });

            button.classList.remove(
                'bg-slate-200',
                'text-slate-700'
            );

            button.classList.add(
                'bg-emerald-600',
                'text-white',
                'shadow'
            );

        });

    });

    // MOBILE MENU

    const mobileMenuButton = document.getElementById('adminMobileMenuButton');
    const mobileMenuOverlay = document.getElementById('adminMobileMenuOverlay');
    const mobileMenuBackdrop = document.getElementById('adminMobileMenuBackdrop');
    const mobileMenuClose = document.getElementById('adminMobileMenuClose');

    if (mobileMenuButton && mobileMenuOverlay) {

        mobileMenuButton.addEventListener('click', function () {
            mobileMenuOverlay.classList.remove('hidden');
        });

        if (mobileMenuBackdrop) {
            mobileMenuBackdrop.addEventListener('click', function () {
                mobileMenuOverlay.classList.add('hidden');
            });
        }

        if (mobileMenuClose) {
            mobileMenuClose.addEventListener('click', function () {
                mobileMenuOverlay.classList.add('hidden');
            });
        }

    }

});
</script>

</body>
</html>