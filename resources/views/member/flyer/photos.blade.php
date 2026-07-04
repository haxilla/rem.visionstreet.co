@include('member.layout.head')

<body data-section="member" class="relative min-h-screen bg-[#f0f2f7] font-sans text-slate-800">

@include('member.layout.nav')

@php
$flyer = $data['flyer'] ?? null;
@endphp

<main class="min-h-screen bg-[#f0f2f7] pt-24">

<div class="mx-auto flex w-full max-w-[1400px] gap-8 px-4 pb-16 sm:px-6 lg:px-8">

    <section class="min-w-0 flex-1">

        {{-- HEADER --}}
        <div class="mb-8">

            <div class="text-sm font-bold uppercase tracking-wider text-[#123f91]">
                Step 3 of 5
            </div>

            <h1 class="mt-2 text-4xl font-black text-slate-900">
                Property Photos
            </h1>

            <p class="mt-2 text-slate-500">
                Add photos to your flyer.
            </p>

        </div>

        {{-- PROGRESS --}}
        <div class="mb-8 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">

            <div class="flex flex-wrap items-center gap-3 text-sm font-bold">

                <span class="text-emerald-600">✓ Property</span>
                <span class="text-slate-300">→</span>

                <span class="text-emerald-600">✓ Details</span>
                <span class="text-slate-300">→</span>

                <span class="text-[#123f91]">● Photos</span>
                <span class="text-slate-300">→</span>

                <span class="text-slate-400">Text</span>
                <span class="text-slate-300">→</span>

                <span class="text-slate-400">Design</span>

            </div>

        </div>

        {{-- PROPERTY SUMMARY --}}
        <div class="mb-8 rounded-3xl bg-white p-5 shadow-sm ring-1 ring-black/5">

            <div class="flex items-start justify-between">

                <div>

                    <div class="text-xl font-black text-slate-900">
                        {{ $flyer->xFullStreet }}
                    </div>

                    <div class="text-slate-600">
                        {{ $flyer->xCity }},
                        {{ $flyer->xState }}
                        {{ $flyer->xZip }}
                    </div>

                    @if($flyer->xMlsNum)

                        <div class="mt-1 text-sm text-slate-500">
                            MLS #{{ $flyer->xMlsNum }}
                        </div>

                    @endif

                </div>

            </div>

        </div>

        <div
            id="photoUploader"
            data-flyer-id="{{ $flyer->id }}"
        >

            {{-- PHOTO SECTION --}}
            <div class="rounded-3xl bg-white p-8 shadow-sm ring-1 ring-black/5">

                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">

                    <div>

                        <h2 class="text-2xl font-black text-slate-900">
                            Property Photos
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            Drop photos here or click to browse. Uploads begin automatically.
                        </p>

                    </div>

                    <div
                        id="photoCount"
                        class="hidden text-sm font-bold text-slate-600"
                    >
                    </div>

                </div>

                {{-- DROPZONE --}}
                <div
                    id="dropZone"
                    class="mt-6 cursor-pointer rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 px-6 py-10 text-center transition hover:border-[#123f91] hover:bg-white"
                >

                    <div class="text-lg font-black text-slate-800">
                        Drop Photos Here
                    </div>

                    <div class="mt-2 text-sm font-semibold text-slate-500">
                        or click anywhere in this box to browse
                    </div>

                    <div class="mt-1 text-xs text-slate-400">
                        You can select multiple photos at once.
                    </div>

                </div>

                <input
                    id="photos"
                    type="file"
                    multiple
                    accept="image/*"
                    class="hidden"
                >

                {{-- PREVIEW GRID --}}
                <div
                    id="photoPreviewGrid"
                    class="mt-8 grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5"
                >
                </div>

                {{-- UPLOAD STATUS --}}
                <div id="uploadedPhotosSection"
                    class="mt-10 hidden">

                    <div id="uploadedPhotosFooter" class="mt-8 hidden text-center">

                        <div id="uploadedPhotoMessage"
                        class="text-xl font-black text-emerald-600">
                        </div>

                        <div class="mt-6 flex justify-center gap-4">

                            <button id="uploadMoreButton"
                            type="button"
                            class="cursor-pointer rounded-xl bg-white px-5 py-3 font-bold text-slate-700 shadow-sm ring-1 ring-black/5">
                                Upload More Photos
                            </button>

                            <a href="/member/flyer/text?flyerId={{ $flyer->id }}"
                                class="rounded-xl bg-[#123f91] px-6 py-3 font-bold text-white hover:bg-[#0f3274]">
                                Continue →
                            </a>

                        </div>

                    </div>

                    <div id="uploadedPhotosGrid"
                        class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5">
                    </div>

                </div>
            </div>

        </div>

    </section>

</div>

</main>

@include('public.layout.footer')

<script>

document.addEventListener('DOMContentLoaded', () => {

    const uploader = document.getElementById('photoUploader');
    const dropZone = document.getElementById('dropZone');
    const input = document.getElementById('photos');
    const previewGrid = document.getElementById('photoPreviewGrid');
    const photoCount = document.getElementById('photoCount');

    const flyerId = uploader.dataset.flyerId;

    const csrfMeta = document.querySelector('meta[name="csrf-token"]');

    const csrfToken = csrfMeta
        ? csrfMeta.content
        : '{{ csrf_token() }}';

    let totalPhotos = 0;
    let uploadsStarted = 0;
    let uploadsFinished = 0;
    let uploadedPhotos = [];

    dropZone.addEventListener('click', () => {
        input.click();
    });

    dropZone.addEventListener('dragover', (e) => {

        e.preventDefault();

        dropZone.classList.add(
            'border-[#123f91]',
            'bg-white'
        );

    });

    dropZone.addEventListener('dragleave', () => {

        dropZone.classList.remove(
            'border-[#123f91]',
            'bg-white'
        );

    });

    dropZone.addEventListener('drop', (e) => {

        e.preventDefault();

        dropZone.classList.remove(
            'border-[#123f91]',
            'bg-white'
        );

        handleFiles(
            Array.from(e.dataTransfer.files)
        );

    });

    input.addEventListener('change', function () {

        handleFiles(
            Array.from(this.files)
        );

        this.value = '';

    });

    function handleFiles(files)
    {

        uploadsStarted += files.length;
        files.forEach((file) => {

            if (!file.type.startsWith('image/')) {
                return;
            }

            totalPhotos++;

            updatePhotoCount();

            createPreviewAndUpload(file);

        });
    }

    function updatePhotoCount()
    {
        photoCount.classList.remove('hidden');

        if (uploadsFinished < uploadsStarted) {

            photoCount.textContent =
                totalPhotos +
                (totalPhotos === 1
                    ? ' Photo Uploading...'
                    : ' Photos Uploading...');

        }

    }

    function createPreviewAndUpload(file)
    {
        const reader = new FileReader();

        reader.onload = function (e) {

            const card = document.createElement('div');

            card.className =
                'group overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm';

            card.innerHTML = `
                <div class="relative">

                    <img
                        src="${e.target.result}"
                        class="aspect-square w-full object-cover"
                    >

                    <button
                        type="button"
                        class="remove-preview absolute right-2 top-2 flex h-7 w-7 items-center justify-center rounded-full bg-white text-lg font-black leading-none text-slate-700 opacity-90 shadow hover:bg-red-600 hover:text-white"
                        title="Remove"
                    >
                        ×
                    </button>

                    <div
                        class="upload-badge absolute bottom-2 left-2 rounded-full bg-white/95 px-2 py-1 text-xs font-bold text-slate-700 shadow"
                    >
                        Waiting
                    </div>

                </div>

                <div class="h-2 bg-slate-200">

                    <div
                        class="upload-progress h-2 bg-[#123f91] transition-all"
                        style="width:0%"
                    ></div>

                </div>
            `;

            previewGrid.appendChild(card);

            const removeButton = card.querySelector('.remove-preview');

            removeButton.addEventListener('click', () => {

                card.remove();

                totalPhotos = Math.max(0, totalPhotos - 1);

                if (totalPhotos === 0) {
                    photoCount.classList.add('hidden');
                } else {
                    updatePhotoCount();
                }

            });

            uploadFile(file, card);

        };

        reader.readAsDataURL(file);
    }

    function uploadFile(file, card)
    {
        const progressBar = card.querySelector('.upload-progress');
        const badge = card.querySelector('.upload-badge');

        const xhr = new XMLHttpRequest();

        const formData = new FormData();

        formData.append('flyerId', flyerId);
        formData.append('photo', file);

        badge.textContent = 'Uploading';

        xhr.upload.addEventListener('progress', function (e) {

            if (!e.lengthComputable) {
                return;
            }

            const percent = Math.round(
                (e.loaded / e.total) * 100
            );

            progressBar.style.width = percent + '%';

            badge.textContent = percent + '%';

        });

        xhr.addEventListener('load', function () {

            let response = {};

            try {
                response = JSON.parse(xhr.responseText);
            } catch (e) {
                response.success = false;
            }

            if (response.photos) {
                uploadedPhotos = response.photos;
            }

            if (xhr.status >= 200 &&
                xhr.status < 300 &&
                response.success === true) {

                progressBar.style.width = '100%';

                progressBar.classList.remove('bg-[#123f91]');
                progressBar.classList.add('bg-emerald-500');

                badge.textContent = 'Uploaded ✓';
                badge.classList.remove('text-slate-700');
                badge.classList.add('text-emerald-700');

            } else {

                progressBar.classList.remove('bg-[#123f91]');
                progressBar.classList.add('bg-red-500');

                badge.textContent =
                    response.message || 'Failed';

                badge.classList.remove('text-slate-700');
                badge.classList.add('text-red-700');

            }

            uploadsFinished++;

            if (uploadsFinished === uploadsStarted) {
                showUploadComplete();
            }

        });

        xhr.addEventListener('error', function () {

            progressBar.classList.remove('bg-[#123f91]');
            progressBar.classList.add('bg-red-500');

            badge.textContent = 'Failed';
            badge.classList.remove('text-slate-700');
            badge.classList.add('text-red-700');

            uploadsFinished++;

            if (uploadsFinished === uploadsStarted) {
                showUploadComplete();
            }

        });

        xhr.open(
            'POST',
            '/member/flyer/uploadPhoto'
        );

        xhr.setRequestHeader(
            'X-CSRF-TOKEN',
            csrfToken
        );

        xhr.send(formData);
    }

    function showUploadComplete()
    {
        // Clear the upload queue
        previewGrid.innerHTML = '';

        // Hide the uploading message
        photoCount.classList.add('hidden');

        // Show uploaded section
        document
            .getElementById('uploadedPhotosSection')
            .classList.remove('hidden');

        // Update uploaded message
        document
            .getElementById('uploadedPhotoMessage')
            .textContent =
                uploadedPhotos.length +
                (uploadedPhotos.length === 1
                    ? ' Photo Uploaded'
                    : ' Photos Uploaded');

        // Show footer
        document
            .getElementById('uploadedPhotosFooter')
            .classList.remove('hidden');

        const grid = document.getElementById('uploadedPhotosGrid');

        grid.innerHTML = '';

        uploadedPhotos.forEach(function(photo){

            grid.innerHTML += `
                <div class="rounded-xl border border-slate-200 overflow-hidden bg-white shadow-sm">

                    <img
                        src="/hqphotos/{{ $flyer->theMeta->zipDir }}/{{ $flyer->theMeta->mlsDir }}/${photo.photoName}"
                        class="aspect-square w-full object-cover"
                    >

                </div>
            `;

        });

        document.getElementById('uploadMoreButton')
        .onclick = function () {
            input.click();
        };

    }

});

</script>

</body>
</html>