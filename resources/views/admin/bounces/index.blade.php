@include('public.layout.head')

<body data-section="admin" class="relative bg-white min-h-screen font-sans text-gray-800">

@include('public.layout.nav')

<main
    class="transition-all duration-300 min-h-screen pt-24 relative"
    :class="collapsed ? 'ml-20' : 'ml-64'"
>
    <div class="ml-8 mr-8 lg:ml-10 lg:mr-10">
        <div class="pageswap p-6 w-full">

            <div class="min-h-screen bg-[#f4f7fb]">
                <div class="flex min-h-screen">

                    @include('admin.includes.sidebar')

                    <main class="flex-1 px-6 py-6 lg:px-10 lg:py-8">

                        <form method="POST" action="/admin/bounces/group-delete">
                            @csrf

                            {{-- HEADER --}}
                            <div class="rounded-[24px] bg-white px-8 py-7 shadow-[0_12px_35px_rgba(15,23,42,0.06)]">
                                <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">

                                    <div>
                                        <div class="text-[12px] font-semibold uppercase tracking-[0.22em] text-[#214e9b]/70">
                                            Admin Mail
                                        </div>

                                        <h1 class="mt-2 text-[32px] font-semibold text-slate-900">
                                            Bouncebox
                                        </h1>

                                        <p class="mt-2 text-[14px] text-slate-600">
                                            {{ number_format($data['count'] ?? 0) }} messages
                                        </p>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <div class="rounded-full bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-600">
                                            Page {{ $data['page'] ?? 1 }}
                                        </div>

                                        <button
                                            type="submit"
                                            onclick="return confirm('Delete selected messages?')"
                                            class="rounded-full bg-red-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700"
                                        >
                                            Delete Selected
                                        </button>
                                    </div>

                                </div>
                            </div>

                            {{-- TABLE CARD --}}
                            <div class="mt-10 overflow-hidden rounded-[24px] bg-white shadow-[0_12px_35px_rgba(15,23,42,0.06)]">

                                <div class="border-b border-slate-200 bg-slate-50 px-6 py-5">
                                    <div class="text-sm font-semibold text-slate-700">
                                        Message List
                                    </div>
                                </div>

                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm">

                                        <thead class="border-b border-slate-200 bg-white text-xs uppercase tracking-wide text-slate-400">
                                            <tr>
                                                <th class="px-5 py-4 text-left w-12">
                                                    <input
                                                        type="checkbox"
                                                        class="rounded border-slate-300 text-[#214e9b]"
                                                        onclick="document.querySelectorAll('.msg-check').forEach(cb => cb.checked = this.checked)"
                                                    >
                                                </th>

                                                <th class="px-5 py-4 text-left w-24">
                                                    Msg #
                                                </th>

                                                <th class="px-5 py-4 text-left w-56">
                                                    Date
                                                </th>

                                                <th class="px-5 py-4 text-left w-80">
                                                    From
                                                </th>

                                                <th class="px-5 py-4 text-left">
                                                    Subject
                                                </th>
                                            </tr>
                                        </thead>

                                        <tbody class="divide-y divide-slate-100">

                                            @forelse(($data['messages'] ?? []) as $message)

                                                <tr class="transition hover:bg-[#214e9b]/5">

                                                    <td class="px-5 py-4">
                                                        <input
                                                            type="checkbox"
                                                            name="messages[]"
                                                            value="{{ $message['messageNumber'] }}"
                                                            class="msg-check rounded border-slate-300 text-[#214e9b]"
                                                        >
                                                    </td>

                                                    <td class="px-5 py-4 whitespace-nowrap text-slate-500">
                                                        #{{ $message['messageNumber'] }}
                                                    </td>

                                                    <td class="px-5 py-4 whitespace-nowrap text-slate-600">
                                                        {{ $message['date'] }}
                                                    </td>

                                                    <td class="px-5 py-4 max-w-xs truncate text-slate-700">
                                                        {{ $message['from'] }}
                                                    </td>

                                                    <td class="px-5 py-4">
                                                        <a
                                                            href="/admin/bounces/{{ $message['messageNumber'] }}"
                                                            class="font-semibold text-[#214e9b] hover:underline"
                                                        >
                                                            {{ $message['subject'] }}
                                                        </a>
                                                    </td>

                                                </tr>

                                            @empty

                                                <tr>
                                                    <td colspan="5" class="px-5 py-12 text-center text-sm text-slate-500">
                                                        No messages found.
                                                    </td>
                                                </tr>

                                            @endforelse

                                        </tbody>

                                    </table>
                                </div>

                            </div>

                            {{-- PAGINATION --}}
                            <div class="mt-6 flex items-center justify-between">

                                <div>
                                    @if(!empty($data['hasNewer']))
                                        <a
                                            href="/admin/bounces?page={{ $data['page'] - 1 }}&perPage={{ $data['perPage'] ?? 50 }}"
                                            class="inline-flex items-center rounded-full border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 shadow-sm transition hover:border-[#214e9b]/40 hover:text-[#214e9b]"
                                        >
                                            ← Newer
                                        </a>
                                    @endif
                                </div>

                                <div>
                                    @if(!empty($data['hasOlder']))
                                        <a
                                            href="/admin/bounces?page={{ $data['page'] + 1 }}&perPage={{ $data['perPage'] ?? 50 }}"
                                            class="inline-flex items-center rounded-full border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 shadow-sm transition hover:border-[#214e9b]/40 hover:text-[#214e9b]"
                                        >
                                            Older →
                                        </a>
                                    @endif
                                </div>

                            </div>

                        </form>

                    </main>

                </div>
            </div>

        </div>
    </div>
</main>

@include('public.layout.footer')

</body>
</html>