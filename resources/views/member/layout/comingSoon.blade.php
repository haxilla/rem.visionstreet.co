<div class="mx-auto max-w-2xl px-4 pt-32 pb-20 text-center sm:px-6 lg:px-8">
    <div class="rounded-3xl bg-white p-12 shadow-sm ring-1 ring-black/5">
        <div class="mx-auto mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-[#123f91]/10 text-2xl">
            🚧
        </div>
        <h1 class="text-3xl font-black text-slate-900">{{ $title ?? 'Coming Soon' }}</h1>
        <p class="mt-3 text-slate-500">{{ $description ?? "This page hasn't been built yet - check back soon." }}</p>
        <a href="/member/dashboard" class="mt-8 inline-block rounded-xl bg-[#123f91] px-6 py-3 text-sm font-bold text-white hover:bg-[#0f3274]">
            Back to Dashboard
        </a>
    </div>
</div>
