<div class="p-6 max-w-7xl mx-auto">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">
                Bouncebox
            </h1>

            <div class="text-sm text-gray-500 mt-1">
                {{ number_format($data['count']) }} messages
            </div>
        </div>

        <div class="text-sm text-gray-500">
            Page {{ $data['page'] }}
        </div>
    </div>

    <div class="overflow-hidden border border-gray-200 rounded-lg bg-white shadow-sm">

        <table class="w-full text-sm">

            <thead class="bg-gray-50 border-b border-gray-200">

                <tr>
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

                @forelse($data['messages'] as $message)

                    <tr class="hover:bg-blue-50">

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

                            <a href="/admin/bounces/view/{{ $message['messageNumber'] }}"
                               class="text-blue-700 hover:underline">

                                {{ $message['subject'] }}

                            </a>

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="4"
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

            @if($data['hasNewer'])

                <a href="/admin/bounces?page={{ $data['page'] - 1 }}"
                   class="inline-flex items-center px-4 py-2 border rounded bg-white hover:bg-gray-50">

                    ← Newer

                </a>

            @endif

        </div>

        <div>

            @if($data['hasOlder'])

                <a href="/admin/bounces?page={{ $data['page'] + 1 }}"
                   class="inline-flex items-center px-4 py-2 border rounded bg-white hover:bg-gray-50">

                    Older →

                </a>

            @endif

        </div>

    </div>

</div>