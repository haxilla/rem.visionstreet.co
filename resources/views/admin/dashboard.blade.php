@php
    $waitingFlyerCamps    = $data['waitingFlyerCamps'] ?? collect();
    $inProgressFlyerCamps = $data['inProgressFlyerCamps'] ?? collect();
    $completeFlyerCamps   = $data['completeFlyerCamps'] ?? collect();

    $campaignsWaiting     = $data['campaignsWaiting'] ?? 0;
    $campaignsInProgress  = $data['campaignsInProgress'] ?? 0;
    $campaignsCompleted   = $data['campaignsCompleted'] ?? 0;
@endphp

{{-- TABBED GROUP PREVIEW --}}
<div class="mt-10 rounded-[24px] bg-white shadow-[0_12px_35px_rgba(15,23,42,0.06)]">

    {{-- TAB BUTTONS --}}
    <div class="border-b border-slate-200 px-6 pt-5">
        <div class="flex flex-wrap gap-2">

            <button
                type="button"
                class="campaign-tab-btn bg-[#214e9b] text-white rounded-t-xl px-5 py-3 text-sm font-semibold transition shadow"
                data-tab="waiting"
            >
                Waiting
                <span class="ml-2 rounded-full bg-white/20 px-2 py-0.5 text-xs">
                    {{ $waitingFlyerCamps->count() }}
                </span>
            </button>

            <button
                type="button"
                class="campaign-tab-btn bg-slate-100 text-slate-600 hover:bg-slate-200 rounded-t-xl px-5 py-3 text-sm font-semibold transition"
                data-tab="inProgress"
            >
                In Progress
                <span class="ml-2 rounded-full bg-slate-200 px-2 py-0.5 text-xs">
                    {{ $inProgressFlyerCamps->count() }}
                </span>
            </button>

            <button
                type="button"
                class="campaign-tab-btn bg-slate-100 text-slate-600 hover:bg-slate-200 rounded-t-xl px-5 py-3 text-sm font-semibold transition"
                data-tab="completed"
            >
                Completed
                <span class="ml-2 rounded-full bg-slate-200 px-2 py-0.5 text-xs">
                    {{ $completeFlyerCamps->count() }}
                </span>
            </button>

        </div>
    </div>

    <div class="p-6">

        {{-- WAITING --}}
        <div class="campaign-tab-panel" id="campaign-tab-waiting">
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

        {{-- IN PROGRESS --}}
        <div class="campaign-tab-panel hidden" id="campaign-tab-inProgress">
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

        {{-- COMPLETED --}}
        <div class="campaign-tab-panel hidden" id="campaign-tab-completed">
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const buttons = document.querySelectorAll('.campaign-tab-btn');
    const panels = document.querySelectorAll('.campaign-tab-panel');

    buttons.forEach(function (button) {
        button.addEventListener('click', function () {
            const tab = button.dataset.tab;

            panels.forEach(function (panel) {
                panel.classList.add('hidden');
            });

            document
                .getElementById('campaign-tab-' + tab)
                .classList.remove('hidden');

            buttons.forEach(function (btn) {
                btn.classList.remove(
                    'bg-[#214e9b]',
                    'text-white',
                    'shadow'
                );

                btn.classList.add(
                    'bg-slate-100',
                    'text-slate-600',
                    'hover:bg-slate-200'
                );

                const badge = btn.querySelector('span');

                if (badge) {
                    badge.classList.remove('bg-white/20');
                    badge.classList.add('bg-slate-200');
                }
            });

            button.classList.remove(
                'bg-slate-100',
                'text-slate-600',
                'hover:bg-slate-200'
            );

            button.classList.add(
                'bg-[#214e9b]',
                'text-white',
                'shadow'
            );

            const activeBadge = button.querySelector('span');

            if (activeBadge) {
                activeBadge.classList.remove('bg-slate-200');
                activeBadge.classList.add('bg-white/20');
            }
        });
    });
});
</script>