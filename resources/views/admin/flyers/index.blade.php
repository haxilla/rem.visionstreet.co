@include('public.layout.head')

<body data-section="admin" class="relative bg-white min-h-screen font-sans text-gray-800">

@include('admin.layout.nav')

@php
    $flyers = $data['flyers'] ?? collect();
@endphp

{{-- MAIN --}}
<main class="min-h-screen bg-[#f4f7fb] pt-24">
    <div class="px-4 py-6 sm:px-6 lg:px-10 lg:py-8">

        {{-- HEADER --}}
        <div class="rounded-[24px] bg-white px-5 py-6 shadow-[0_12px_35px_rgba(15,23,42,0.06)] sm:px-8 sm:py-7">
            <div class="text-[12px] font-semibold uppercase tracking-[0.22em] text-[#214e9b]/70">
                Admin
            </div>

            <h1 class="mt-2 text-2xl font-semibold text-slate-900 sm:text-[32px]">
                Flyers
            </h1>

            <p class="mt-2 text-[14px] text-slate-600">
                All flyers in the Realty Emails system, sorted by most recently sent.
            </p>
        </div>

        {{-- SEARCH FLYERS --}}
        <div class="mt-6 rounded-[24px] bg-white p-4 shadow-[0_12px_35px_rgba(15,23,42,0.06)] sm:mt-8 sm:p-6">
            <label class="block text-sm font-semibold text-slate-700">
                Search Flyers
            </label>

            <div class="relative mt-2">
                <input
                    id="flyerSearch"
                    type="text"
                    placeholder="Search by address, MLS#, or ID..."
                    autocomplete="off"
                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-[#214e9b] focus:outline-none focus:ring-2 focus:ring-[#214e9b]/20"
                >

                <div
                    id="flyerSearchResults"
                    class="absolute z-50 mt-2 hidden w-full overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg"
                ></div>
            </div>
        </div>

        {{-- FLYERS LIST --}}
        <div class="mt-6 rounded-[24px] bg-white shadow-[0_12px_35px_rgba(15,23,42,0.06)] overflow-hidden sm:mt-10">

            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-200 bg-slate-50 px-4 py-5 sm:px-6">
                <div>
                    <h2 class="text-xl font-semibold text-slate-900">
                        All Flyers
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Sorted by last sent date, most recent first.
                    </p>
                </div>

                @if(method_exists($flyers, 'total'))
                    <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
                        {{ $flyers->total() }} flyers
                    </span>
                @endif
            </div>

            <div class="p-3 sm:p-6">

                {{-- MOBILE CARDS --}}
                <div class="space-y-3 xl:hidden">

                    @forelse($flyers as $flyer)

                        @php
                            $agentName = optional($flyer->theAgent)->agtFullName ?: '—';
                            $lastSent = $flyer->xLastDeliveryDate ?? null;
                        @endphp

                        <div class="rounded-2xl border border-slate-200 p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <a href="/admin/flyerCamps/{{ $flyer->id }}" class="block truncate text-sm font-semibold text-slate-900 hover:underline">
                                        {{ $flyer->xFullStreet ?: 'No Address' }}
                                    </a>
                                    <div class="truncate text-xs text-slate-500">
                                        {{ $agentName }}
                                    </div>
                                </div>
                                <div class="shrink-0 text-xs text-slate-400">
                                    ID {{ $flyer->id }}
                                </div>
                            </div>

                            <div class="mt-3 grid grid-cols-3 gap-2 text-xs">
                                <div>
                                    <div class="text-slate-400">MLS#</div>
                                    <div class="font-semibold text-slate-700">{{ $flyer->xMlsNum ?: '—' }}</div>
                                </div>
                                <div>
                                    <div class="text-slate-400">List Price</div>
                                    <div class="font-semibold text-slate-700">
                                        {{ $flyer->xListPrice ? '$' . number_format($flyer->xListPrice) : '—' }}
                                    </div>
                                </div>
                                <div>
                                    <div class="text-slate-400">Last Sent</div>
                                    <div class="font-semibold text-slate-700">
                                        {{ $lastSent ? \Carbon\Carbon::parse($lastSent)->format('m/d/Y') : 'Never' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                    @empty

                        <div class="rounded-2xl border border-dashed border-slate-200 px-4 py-10 text-center text-sm text-slate-500">
                            No flyers found.
                        </div>

                    @endforelse

                </div>

                {{-- TABLE (md and up) --}}
                <div class="hidden overflow-x-auto rounded-2xl border border-slate-200 xl:block">

                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Address</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Agent</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">MLS#</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">List Price</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Last Sent</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100 bg-white">

                            @forelse($flyers as $flyer)

                                @php
                                    $agentName = optional($flyer->theAgent)->agtFullName ?: '—';
                                    $lastSent = $flyer->xLastDeliveryDate ?? null;
                                @endphp

                                <tr class="hover:bg-slate-50">

                                    <td class="whitespace-nowrap px-6 py-3 text-sm text-slate-500">
                                        {{ $flyer->id }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-3 text-sm font-semibold text-slate-900">
                                        <a href="/admin/flyerCamps/{{ $flyer->id }}" class="hover:underline">
                                            {{ $flyer->xFullStreet ?: 'No Address' }}
                                        </a>
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-3 text-sm text-slate-700">
                                        {{ $agentName }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-3 text-sm text-slate-700">
                                        {{ $flyer->xMlsNum ?: '—' }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-3 text-sm text-slate-700">
                                        {{ $flyer->xListPrice ? '$' . number_format($flyer->xListPrice) : '—' }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-3 text-sm text-slate-700">
                                        {{ $lastSent ? \Carbon\Carbon::parse($lastSent)->format('m/d/Y') : 'Never' }}
                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-sm text-slate-500">
                                        No flyers found.
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>
                    </table>

                </div>

                @if(method_exists($flyers, 'links'))
                    <div class="mt-5">
                        {{ $flyers->links() }}
                    </div>
                @endif

            </div>
        </div>

    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('flyerSearch');
    const results = document.getElementById('flyerSearchResults');

    let timer = null;

    input.addEventListener('input', function () {
        clearTimeout(timer);

        const q = input.value.trim();

        if (q.length < 2) {
            results.innerHTML = '';
            results.classList.add('hidden');
            return;
        }

        timer = setTimeout(() => {
            fetch(`/admin/flyer/search?q=${encodeURIComponent(q)}`)
                .then(response => response.json())
                .then(data => {
                    results.innerHTML = '';

                    if (!data.length) {
                        results.innerHTML = `
                            <div class="px-4 py-3 text-sm text-slate-500">
                                No flyers found.
                            </div>
                        `;
                        results.classList.remove('hidden');
                        return;
                    }

                    data.forEach(flyer => {
                        results.innerHTML += `
                            <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3 hover:bg-slate-50">
                                <div>
                                    <a href="/admin/flyerCamps/${flyer.id}" class="text-sm font-semibold text-slate-900 hover:underline">
                                        ${flyer.address}
                                    </a>
                                    <div class="text-xs text-slate-500">
                                        MLS# ${flyer.mls ?? '—'} — ID: ${flyer.id}
                                    </div>
                                </div>
                            </div>
                        `;
                    });

                    results.classList.remove('hidden');
                });
        }, 250);
    });

    document.addEventListener('click', function (e) {
        if (!input.contains(e.target) && !results.contains(e.target)) {
            results.classList.add('hidden');
        }
    });
});
</script>

@include('public.layout.footer')

</body>
</html>
