<div class="p-6">

    <h1 class="text-2xl font-bold mb-4">
        Bouncebox
    </h1>

    <div class="mb-4 text-sm text-gray-500">
        {{ number_format($data['count']) }} messages
    </div>

    <div class="border rounded overflow-hidden bg-white">

        @foreach(($data['messages'] ?? []) as $message)

            <a href="/admin/bounces/view/{{ $message['messageNumber'] }}"
               class="block border-b px-4 py-3 hover:bg-gray-50">

                <div class="flex justify-between gap-4">

                    <div class="truncate">

                        <span class="font-semibold">
                            {{ $message['from'] }}
                        </span>

                        <span class="text-gray-400">
                            —
                        </span>

                        <span>
                            {{ $message['subject'] }}
                        </span>

                    </div>

                    <div class="text-xs text-gray-500 whitespace-nowrap">
                        {{ $message['date'] }}
                    </div>

                </div>

            </a>

        @endforeach

    </div>

    <div class="flex gap-3 mt-5">

        @if($data['hasNewer'])
            <a href="?page={{ $data['page'] - 1 }}"
               class="px-4 py-2 border rounded">
                Newer
            </a>
        @endif

        @if($data['hasOlder'])
            <a href="?page={{ $data['page'] + 1 }}"
               class="px-4 py-2 border rounded">
                Older
            </a>
        @endif

    </div>

</div>