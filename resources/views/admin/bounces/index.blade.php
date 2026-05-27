<div class="p-6 max-w-7xl mx-auto">

    <form method="POST" action="/admin/bouncebox/group-delete">
        @csrf

        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold">
                    Bouncebox
                </h1>

                <div class="text-sm text-gray-500 mt-1">
                    {{ number_format($data['count'] ?? 0) }} messages
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="text-sm text-gray-500">
                    Page {{ $data['page'] ?? 1 }}
                </div>

                <button type="submit"
                        onclick="return confirm('Delete selected messages?')"
                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                    Delete Selected
                </button>
            </div>
        </div>

        <div class="overflow-hidden border border-gray-200 rounded-lg bg-white shadow-sm">

            <table class="w-full text-sm">

                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left w-12">
                            <input type="checkbox"
                                   onclick="document.querySelectorAll('.msg-check').forEach(cb => cb.checked = this.checked)">
                        </th>

                        <th class="px-4 py-3 text-left w-24">
                            Msg #
                        </th>

                        <th class="px-4 py-3 text-left w-56">
                            Date
                        </th>

                        <th class="px-4 py-3 text-left w-80">
                            From
                        </th>

                        <th class="px-4 py-3 text-left">
                            Subject
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">

                    @forelse(($data['messages'] ?? []) as $message)

                        <tr class="hover:bg-blue-50">

                            <td class="px-4 py-3">
                                <input type="checkbox"
                                       name="messages[]"
                                       value="{{ $message['messageNumber'] }}"
                                       class="msg-check">
                            </td>

                            <td class="px-4 py-3 text-gray-500 whitespace-nowrap">
                                #{{ $message['messageNumber'] }}
                            </td>

                            <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                                {{ $message['date'] }}
                            </td>

                            <td class="px-4 py-3 text-gray-700 truncate max-w-xs">
                                {{ $message['from'] }}
                            </td>

                            <td class="px-4 py-3">
                                <a href="/admin/bouncebox/{{ $message['messageNumber'] }}"
                                   class="text-blue-700 hover:underline">
                                    {{ $message['subject'] }}
                                </a>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="5"
                                class="px-4 py-8 text-center text-gray-500">
                                No messages found.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        <div class="flex items-center justify-between mt-6">

            <div>
                @if(!empty($data['hasNewer']))
                    <a href="/admin/bounces?page={{ $data['page'] - 1 }}&perPage={{ $data['perPage'] ?? 50 }}"
                       class="inline-flex items-center px-4 py-2 border rounded bg-white hover:bg-gray-50">
                        ← Newer
                    </a>
                @endif
            </div>

            <div>
                @if(!empty($data['hasOlder']))
                    <a href="/admin/bounces?page={{ $data['page'] + 1 }}&perPage={{ $data['perPage'] ?? 50 }}"
                       class="inline-flex items-center px-4 py-2 border rounded bg-white hover:bg-gray-50">
                        Older →
                    </a>
                @endif
            </div>

        </div>

    </form>

</div>