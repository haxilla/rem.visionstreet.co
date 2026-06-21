@include('public.layout.head')

<body data-section="admin" class="relative bg-white min-h-screen font-sans text-gray-800">

@include('admin.layout.nav')

<main
    class="transition-all duration-300 min-h-screen pt-24 relative"
    :class="collapsed ? 'ml-20' : 'ml-64'"
>
    <div class="mx-2 sm:mx-4 lg:mx-10">
        <div class="pageswap p-2 sm:p-4 lg:p-6 w-full">

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

                    {{-- DESKTOP SIDEBAR --}}
                    <div class="hidden lg:block">
                        @include('admin.includes.sidebar')
                    </div>

                    {{-- MOBILE SIDEBAR OVERLAY --}}
                    <div id="adminMobileMenuOverlay" class="fixed inset-0 z-[60] hidden lg:hidden">
                        <div class="absolute inset-0 bg-slate-900/50" id="adminMobileMenuBackdrop"></div>

                        <div class="relative h-full w-72 bg-white shadow-2xl p-6">
                            <div class="mb-6 text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">
                                Admin Menu
                            </div>

                            <nav class="space-y-2">
                                <a href="/admin" class="block rounded-xl px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                    Dashboard
                                </a>

                                <a href="/admin/flyers" class="block rounded-xl px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                    Flyers
                                </a>

                                <a href="/admin/campaigns" class="block rounded-xl px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                    Campaigns
                                </a>

                                <a href="/admin/agents" class="block rounded-xl px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                    Agents
                                </a>

                                <a href="/admin/logout" class="block rounded-xl px-4 py-3 text-sm font-semibold text-red-600 hover:bg-red-50">
                                    Log Out
                                </a>
                            </nav>
                        </div>
                    </div>

                    {{-- MAIN --}}
                    <main class="flex-1 px-2 py-4 sm:px-4 lg:px-10 lg:py-8">

                        {{-- HEADER --}}
                        <div class="rounded-[20px] bg-white px-4 py-5 sm:px-6 sm:py-6 lg:px-8 lg:py-7 shadow-[0_12px_35px_rgba(15,23,42,0.06)]">
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

                        {{-- TABBED GROUP PREVIEW --}}
                        <div class="mt-10 rounded-[24px] bg-white shadow-[0_12px_35px_rgba(15,23,42,0.06)] overflow-hidden">

                            {{-- TAB BUTTONS --}}
                            <div class="border-b border-slate-200 bg-slate-50 px-6 pt-5">
                                <div class="flex flex-wrap gap-2">

                                    <button
                                        type="button"
                                        class="campaign-tab-btn bg-[#214e9b] text-white rounded-t-xl px-5 py-3 text-sm font-semibold shadow"
                                        data-tab="waiting"
                                    >
                                        Waiting
                                        <span class="ml-2 rounded-full bg-white/20 px-2 py-0.5 text-xs">
                                            {{ $waitingFlyerCamps->count() }}
                                        </span>
                                    </button>

                                    <button
                                        type="button"
                                        class="campaign-tab-btn bg-slate-200 text-slate-700 rounded-t-xl px-5 py-3 text-sm font-semibold"
                                        data-tab="progress"
                                    >
                                        In Progress
                                        <span class="ml-2 rounded-full bg-white/50 px-2 py-0.5 text-xs">
                                            {{ $inProgressFlyerCamps->count() }}
                                        </span>
                                    </button>

                                    <button
                                        type="button"
                                        class="campaign-tab-btn bg-slate-200 text-slate-700 rounded-t-xl px-5 py-3 text-sm font-semibold"
                                        data-tab="completed"
                                    >
                                        Completed
                                        <span class="ml-2 rounded-full bg-white/50 px-2 py-0.5 text-xs">
                                            {{ $completeFlyerCamps->count() }}
                                        </span>
                                    </button>

                                </div>
                            </div>

                            {{-- TAB CONTENT --}}
                            <div class="p-6">

                                {{-- WAITING --}}
                                <div class="campaign-panel" id="tab-waiting">

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

                                                $flyer = $first['flyer'] ?? null;
                                                $photo = $flyer?->thePhotos?->first();
                                                $meta  = $flyer?->theMeta;
                                                $agent = $flyer?->theAgent;

                                                $thumbUrl = null;

                                                if ($photo && $meta && $meta->zipDir && $meta->mlsDir && $photo->photoName) {
                                                    $thumbUrl = "https://realtyrepublic.com/hqphotos/{$meta->zipDir}/{$meta->mlsDir}/{$photo->photoName}";
                                                }
                                            @endphp

                                            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-[#214e9b]/40 hover:shadow-md">

                                                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                                                    <div class="flex items-center gap-4">
                                                        <div class="h-20 w-28 shrink-0 overflow-hidden rounded-xl border border-slate-200 bg-slate-100">
                                                            @if($thumbUrl)
                                                                <img
                                                                    src="{{ $thumbUrl }}"
                                                                    alt="{{ $first['address'] ?? 'Property photo' }}"
                                                                    class="h-full w-full object-cover"
                                                                >
                                                            @else
                                                                <div class="flex h-full w-full items-center justify-center text-xs text-slate-400">
                                                                    No Photo
                                                                </div>
                                                            @endif
                                                        </div>

                                                        <div>
                                                            <a
                                                                href="/flyer/{{ $flyerId }}"
                                                                class="text-[15px] font-semibold text-[#214e9b] hover:underline"
                                                            >
                                                                ID#: {{ $flyerId }}
                                                            </a>

                                                            <div class="mt-1 text-sm text-slate-700">
                                                                {{ $first['address'] ?? 'No Address' }}
                                                            </div>
                                                            <div class="mt-2">

                                                                @if($agent)
                                                                    <div class="text-xs text-slate-500">

                                                                        <span class="font-medium text-slate-400">
                                                                            Agent:
                                                                        </span>

                                                                        <a
                                                                            href="#"
                                                                            class="ml-1 font-medium text-[#214e9b] hover:underline"
                                                                        >
                                                                            {{ $agent->agtFullName }}
                                                                        </a>

                                                                    </div>
                                                                @endif

                                                            </div>
                                                        </div>
                                                    </div>

                                                    <button
                                                        type="button"
                                                        class="campaign-toggle flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold text-slate-600 hover:bg-slate-200"
                                                    >
                                                        {{ $campaigns->count() }} campaigns
                                                        <span class="campaign-arrow transition-transform">▼</span>
                                                    </button>

                                                </div>

                                                <div class="campaign-details hidden mt-4 border-t border-slate-200 pt-4">
                                                    <div class="space-y-2">

                                                        @foreach($campaigns as $campaign)

                                                            <div class="rounded-xl bg-slate-50 p-3 text-sm text-slate-700">

                                                                <div>
                                                                    <strong>Subject:</strong>
                                                                    {{ $campaign['emSubject'] ?? 'N/A' }}
                                                                </div>

                                                                <div>
                                                                    <strong>Requested:</strong>
                                                                    {{ $campaign['emRequest'] ?? 'N/A' }}
                                                                </div>

                                                                <div>
                                                                    <strong>Started:</strong>
                                                                    {{ $campaign['emStart'] ?? 'N/A' }}
                                                                </div>

                                                                <div>
                                                                    <strong>Completed:</strong>
                                                                    {{ $campaign['emComplete'] ?? 'N/A' }}
                                                                </div>

                                                                <div>
                                                                    <strong>Label:</strong>
                                                                    {{ $campaign['campLabel'] ?? 'N/A' }}
                                                                </div>

                                                            </div>

                                                        @endforeach

                                                    </div>
                                                </div>

                                            </div>

                                        @empty

                                            <div class="rounded-2xl border border-dashed border-slate-300 p-10 text-center text-sm text-slate-500">
                                                No waiting groups found.
                                            </div>

                                        @endforelse

                                    </div>

                                </div>

                                {{-- IN PROGRESS --}}
                                <div class="campaign-panel hidden" id="tab-progress">

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

                                                $flyer = $first['flyer'] ?? null;
                                                $photo = $flyer?->thePhotos?->first();
                                                $meta  = $flyer?->theMeta;
                                                $agent = $flyer?->theAgent;

                                                $thumbUrl = null;

                                                if ($photo && $meta && $meta->zipDir && $meta->mlsDir && $photo->photoName) {
                                                    $thumbUrl = "https://realtyrepublic.com/hqphotos/{$meta->zipDir}/{$meta->mlsDir}/{$photo->photoName}";
                                                }
                                            @endphp

                                            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-[#214e9b]/40 hover:shadow-md">

                                                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                                                    <div class="flex items-center gap-4">
                                                        <div class="h-20 w-28 shrink-0 overflow-hidden rounded-xl border border-slate-200 bg-slate-100">
                                                            @if($thumbUrl)
                                                                <img
                                                                    src="{{ $thumbUrl }}"
                                                                    alt="{{ $first['address'] ?? 'Property photo' }}"
                                                                    class="h-full w-full object-cover"
                                                                >
                                                            @else
                                                                <div class="flex h-full w-full items-center justify-center text-xs text-slate-400">
                                                                    No Photo
                                                                </div>
                                                            @endif
                                                        </div>

                                                        <div>
                                                            <a
                                                                href="/flyer/{{ $flyerId }}"
                                                                class="text-[15px] font-semibold text-[#214e9b] hover:underline"
                                                            >
                                                                ID#: {{ $flyerId }}
                                                            </a>

                                                            <div class="mt-1 text-sm text-slate-700">
                                                                {{ $first['address'] ?? 'No Address' }}
                                                            </div>
                                                            <div class="mt-2">

                                                                @if($agent)
                                                                    <div class="text-xs text-slate-500">

                                                                        <span class="font-medium text-slate-400">
                                                                            Agent:
                                                                        </span>

                                                                        <a
                                                                            href="#"
                                                                            class="ml-1 font-medium text-[#214e9b] hover:underline"
                                                                        >
                                                                            {{ $agent->agtFullName }}
                                                                        </a>

                                                                    </div>
                                                                @endif

                                                            </div>
                                                        </div>
                                                    </div>

                                                    <button
                                                        type="button"
                                                        class="campaign-toggle flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold text-slate-600 hover:bg-slate-200"
                                                    >
                                                        {{ $campaigns->count() }} campaigns
                                                        <span class="campaign-arrow transition-transform">▼</span>
                                                    </button>

                                                </div>

                                                <div class="campaign-details hidden mt-4 border-t border-slate-200 pt-4">
                                                    <div class="space-y-2">

                                                        @foreach($campaigns as $campaign)

                                                            <div class="rounded-xl bg-slate-50 p-3 text-sm text-slate-700">

                                                                <div>
                                                                    <strong>Subject:</strong>
                                                                    {{ $campaign['emSubject'] ?? 'N/A' }}
                                                                </div>

                                                                <div>
                                                                    <strong>Requested:</strong>
                                                                    {{ $campaign['emRequest'] ?? 'N/A' }}
                                                                </div>

                                                                <div>
                                                                    <strong>Started:</strong>
                                                                    {{ $campaign['emStart'] ?? 'N/A' }}
                                                                </div>

                                                                <div>
                                                                    <strong>Completed:</strong>
                                                                    {{ $campaign['emComplete'] ?? 'N/A' }}
                                                                </div>

                                                                <div>
                                                                    <strong>Label:</strong>
                                                                    {{ $campaign['campLabel'] ?? 'N/A' }}
                                                                </div>

                                                            </div>

                                                        @endforeach

                                                    </div>
                                                </div>

                                            </div>

                                        @empty

                                            <div class="rounded-2xl border border-dashed border-slate-300 p-10 text-center text-sm text-slate-500">
                                                No in progress groups found.
                                            </div>

                                        @endforelse

                                    </div>

                                </div>

                                {{-- COMPLETED --}}
                                <div class="campaign-panel hidden" id="tab-completed">

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

                                                $flyer = $first['flyer'] ?? null;
                                                $photo = $flyer?->thePhotos?->first();
                                                $meta  = $flyer?->theMeta;
                                                $agent = $flyer?->theAgent;

                                                $thumbUrl = null;

                                                if ($photo && $meta && $meta->zipDir && $meta->mlsDir && $photo->photoName) {
                                                    $thumbUrl = "https://realtyrepublic.com/hqphotos/{$meta->zipDir}/{$meta->mlsDir}/{$photo->photoName}";
                                                }
                                            @endphp

                                            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-[#214e9b]/40 hover:shadow-md">

                                                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                                                    <div class="flex items-center gap-4">
                                                        <div class="h-20 w-28 shrink-0 overflow-hidden rounded-xl border border-slate-200 bg-slate-100">
                                                            @if($thumbUrl)
                                                                <img
                                                                    src="{{ $thumbUrl }}"
                                                                    alt="{{ $first['address'] ?? 'Property photo' }}"
                                                                    class="h-full w-full object-cover"
                                                                >
                                                            @else
                                                                <div class="flex h-full w-full items-center justify-center text-xs text-slate-400">
                                                                    No Photo
                                                                </div>
                                                            @endif
                                                        </div>

                                                        <div>
                                                            <a
                                                                href="/flyer/{{ $flyerId }}"
                                                                class="text-[15px] font-semibold text-[#214e9b] hover:underline"
                                                            >
                                                                ID#: {{ $flyerId }}
                                                            </a>

                                                            <div class="mt-1 text-sm text-slate-700">
                                                                {{ $first['address'] ?? 'No Address' }}
                                                            </div>
                                                            <div class="mt-2">

                                                                @if($agent)
                                                                    <div class="text-xs text-slate-500">

                                                                        <span class="font-medium text-slate-400">
                                                                            Agent:
                                                                        </span>

                                                                        <a
                                                                            href="#"
                                                                            class="ml-1 font-medium text-[#214e9b] hover:underline"
                                                                        >
                                                                            {{ $agent->agtFullName }}
                                                                        </a>

                                                                    </div>
                                                                @endif

                                                            </div>
                                                        </div>
                                                    </div>

                                                    <button
                                                        type="button"
                                                        class="campaign-toggle flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold text-slate-600 hover:bg-slate-200"
                                                    >
                                                        {{ $campaigns->count() }} campaigns
                                                        <span class="campaign-arrow transition-transform">▼</span>
                                                    </button>

                                                </div>

                                                <div class="campaign-details hidden mt-4 border-t border-slate-200 pt-4">
                                                    <div class="space-y-2">

                                                        @foreach($campaigns as $campaign)

                                                            <div class="rounded-xl bg-slate-50 p-3 text-sm text-slate-700">

                                                                <div>
                                                                    <strong>Subject:</strong>
                                                                    {{ $campaign['emSubject'] ?? 'N/A' }}
                                                                </div>

                                                                <div>
                                                                    <strong>Requested:</strong>
                                                                    {{ $campaign['emRequest'] ?? 'N/A' }}
                                                                </div>

                                                                <div>
                                                                    <strong>Started:</strong>
                                                                    {{ $campaign['emStart'] ?? 'N/A' }}
                                                                </div>

                                                                <div>
                                                                    <strong>Completed:</strong>
                                                                    {{ $campaign['emComplete'] ?? 'N/A' }}
                                                                </div>

                                                                <div>
                                                                    <strong>Label:</strong>
                                                                    {{ $campaign['campLabel'] ?? 'N/A' }}
                                                                </div>

                                                            </div>

                                                        @endforeach

                                                    </div>
                                                </div>

                                            </div>

                                        @empty

                                            <div class="rounded-2xl border border-dashed border-slate-300 p-10 text-center text-sm text-slate-500">
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

<script>
document.addEventListener('DOMContentLoaded', function () {

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

    const campaignToggles = document.querySelectorAll('.campaign-toggle');

    campaignToggles.forEach(function(toggle) {

        toggle.addEventListener('click', function() {

            const card = toggle.closest('.rounded-2xl');
            const details = card.querySelector('.campaign-details');
            const arrow = toggle.querySelector('.campaign-arrow');

            if (!details) {
                return;
            }

            details.classList.toggle('hidden');

            if (arrow) {
                arrow.classList.toggle('rotate-180');
            }

        });

    });

    // MOBILE MENU

    const mobileMenuButton = document.getElementById('adminMobileMenuButton');
    const mobileMenuOverlay = document.getElementById('adminMobileMenuOverlay');
    const mobileMenuBackdrop = document.getElementById('adminMobileMenuBackdrop');

    if (mobileMenuButton && mobileMenuOverlay) {

        mobileMenuButton.addEventListener('click', function () {
            mobileMenuOverlay.classList.remove('hidden');
        });

        if (mobileMenuBackdrop) {
            mobileMenuBackdrop.addEventListener('click', function () {
                mobileMenuOverlay.classList.add('hidden');
            });
        }

    }

});



</script>

</body>
</html>