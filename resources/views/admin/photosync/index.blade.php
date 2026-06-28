@include('admin.layout.head')

<body data-section="admin" class="relative bg-white min-h-screen font-sans text-gray-800">

@include('admin.layout.nav')

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

                        {{-- HEADER --}}
                        <div class="rounded-[24px] bg-white px-8 py-7 shadow-[0_12px_35px_rgba(15,23,42,0.06)]">

                            <div class="flex justify-between items-center">

                                <div>

                                    <div class="text-[12px] font-semibold uppercase tracking-[0.22em] text-[#214e9b]/70">
                                        Photo Maintenance
                                    </div>

                                    <h1 class="mt-2 text-[32px] font-semibold text-slate-900">
                                        Photo Synchronization
                                    </h1>

                                </div>

                                <button
                                    id="startSync"
                                    type="button"
                                    onclick="runSync()"
                                    class="cursor-pointer rounded-full bg-[#214e9b] px-6 py-3 text-white font-semibold hover:bg-[#1a3f7f]">

                                    Start Sync

                                </button>

                            </div>

                        </div>
                        <div id="syncResults">
                            <div class="text-[12px] font-semibold uppercase tracking-[0.22em] text-[#214e9b]/70 mt-4">
                                Download Photos
                            </div>
                            {{-- SUMMARY --}}
                            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">

                                <div class="rounded-xl bg-white p-6 shadow">
                                    <div class="text-sm text-slate-500">Processed</div>
                                    <div id="completed" class="text-3xl font-bold">
                                        {{ $data['completed'] ?? 0 }}
                                    </div>
                                </div>

                                <div class="rounded-xl bg-white p-6 shadow">
                                    <div class="text-sm text-slate-500">Remaining</div>
                                    <div id="remaining" class="text-3xl font-bold">
                                        {{ $data['remaining'] ?? 0 }}
                                    </div>
                                </div>

                                <div class="rounded-xl bg-white p-6 shadow">
                                    <div class="text-sm text-slate-500">Uploaded</div>
                                    <div id="uploaded" class="text-3xl font-bold">
                                        {{ $data['uploaded'] ?? 0 }}
                                    </div>
                                </div>

                                <div class="rounded-xl bg-white p-6 shadow">
                                    <div class="text-sm text-slate-500">Downloaded</div>
                                    <div id="downloaded" class="text-3xl font-bold">
                                        {{ $data['downloaded'] ?? 0 }}
                                    </div>
                                </div>

                            </div>

                            {{-- RESIZE PHOTOS --}}
                            <div class="mt-10">

                                <div class="text-[12px] font-semibold uppercase tracking-[0.22em] text-[#214e9b]/70">
                                    Resize Photos
                                </div>

                                <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">

                                    <div class="rounded-xl bg-white p-6 shadow">
                                        <div class="text-sm text-slate-500">Total</div>
                                        <div id="resizeTotal" class="text-3xl font-bold">
                                            0
                                        </div>
                                    </div>

                                    <div class="rounded-xl bg-white p-6 shadow">
                                        <div class="text-sm text-slate-500">Processed</div>
                                        <div id="resizeProcessed" class="text-3xl font-bold">
                                            0
                                        </div>
                                    </div>

                                    <div class="rounded-xl bg-white p-6 shadow">
                                        <div class="text-sm text-slate-500">Remaining</div>
                                        <div id="resizeRemaining" class="text-3xl font-bold">
                                            0
                                        </div>
                                    </div>

                                </div>

                            </div>

                            {{-- LOG --}}
                            <div class="mt-8 rounded-[24px] bg-white shadow overflow-hidden">

                                <div class="border-b border-slate-200 px-6 py-5">

                                    <div class="text-sm font-semibold text-slate-700">

                                        Synchronization Log

                                    </div>

                                </div>

                                <div class="overflow-y-auto max-h-[600px]">

                                    <table class="w-full text-sm">

                                        <thead class="border-b bg-slate-50">

                                            <tr>

                                                <th class="px-5 py-4 text-left">
                                                    Date
                                                </th>

                                                <th class="px-5 py-4 text-left">
                                                    Flyer
                                                </th>

                                                <th class="px-5 py-4 text-left">
                                                    Photo
                                                </th>

                                                <th class="px-5 py-4 text-left">
                                                    Status
                                                </th>

                                            </tr>

                                        </thead>

                                        <tbody id="syncLog">

                                            @forelse($data['results'] ?? [] as $result)

                                                <tr>

                                                    <td class="px-5 py-4">

                                                        {{ $result['photoDate'] }}

                                                    </td>

                                                    <td class="px-5 py-4">

                                                        {{ $result['propflyer_id'] }}

                                                    </td>

                                                    <td class="px-5 py-4">

                                                        {{ $result['photoName'] }}

                                                    </td>

                                                    <td class="px-5 py-4">

                                                        {{ $result['status'] }}

                                                    </td>

                                                </tr>

                                            @empty

                                                <tr>

                                                    <td colspan="4"
                                                        class="text-center text-slate-400 py-12">

                                                        Click "Start Sync" to begin.

                                                    </td>

                                                </tr>

                                            @endforelse

                                        </tbody>

                                    </table>

                                </div>

                            </div>
                        </div>

                    </main>

                </div>

            </div>

        </div>

    </div>

</main>

<script>
function runSync() {

    const button = document.getElementById('startSync');

    button.disabled = true;
    button.style.cursor = 'default';
    button.classList.add('opacity-50', 'cursor-not-allowed', 'animate-pulse');

    button.innerHTML = '⟳ Synchronizing...';

    fetch('/admin/photosync/run')

        .then(response => response.json())

        .then(data => {

            document.getElementById('completed').textContent = data.completed;
            document.getElementById('remaining').textContent = data.remaining;
            document.getElementById('uploaded').textContent =
                Number(document.getElementById('uploaded').textContent) + data.uploaded;

            document.getElementById('downloaded').textContent =
                Number(document.getElementById('downloaded').textContent) + data.downloaded;

            let rows = '';

            data.results.forEach(function(result){

                rows += `
                    <tr>
                        <td class="px-5 py-4">${result.photoDate}</td>
                        <td class="px-5 py-4">${result.propflyer_id}</td>
                        <td class="px-5 py-4">${result.photoName}</td>
                        <td class="px-5 py-4">${result.status}</td>
                    </tr>
                `;

            });

            if (document.getElementById('syncLog').innerText.includes('Click "Start Sync"')) {
                document.getElementById('syncLog').innerHTML = '';}

            document.getElementById('syncLog').insertAdjacentHTML('beforeend', rows);

            if (data.remaining > 0) {

                setTimeout(runSync, 100);

            } else {

                runResize();

            }

        })

        .catch(error => {

            console.error(error);

        });

}

function runResize() {

    fetch('/admin/photosync/resize')

        .then(response => response.json())

        .then(data => {

            document.getElementById('resizeTotal').textContent = data.total;
            document.getElementById('resizeProcessed').textContent = data.processed;
            document.getElementById('resizeRemaining').textContent = data.remaining;

        })

        .catch(error => {

            console.error(error);

        });
    /*
    const button = document.getElementById('startSync');

    button.disabled = false;
    button.style.cursor = 'pointer';
    button.classList.remove('opacity-50', 'cursor-not-allowed', 'animate-pulse');
    button.innerHTML = 'Start Sync';
    */
}

</script>

@include('admin.layout.footer')

</body>
</html>


