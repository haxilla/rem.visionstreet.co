@include('member.layout.head')

<body data-section="member" class="relative min-h-screen bg-[#f0f2f7] font-sans text-slate-800">

@include('member.layout.nav')

<main class="min-h-screen bg-[#f0f2f7] pt-24">

```
<div class="mx-auto flex w-full max-w-[1400px] gap-8 px-4 pb-16 sm:px-6 lg:px-8">

    {{-- DESKTOP SIDEBAR --}}
    <aside class="sticky top-28 hidden h-fit w-[240px] shrink-0 rounded-3xl bg-white p-5 shadow-sm ring-1 ring-black/5 lg:block">

        <nav class="space-y-2 text-sm font-bold">

            <a href="/member/flyer/create"
               class="block rounded-xl bg-blue-50 px-4 py-3 text-[#123f91]">
                Create New Flyer
            </a>

            <a href="/member/resend-flyer"
               class="block rounded-xl px-4 py-3 text-slate-700 hover:bg-slate-100">
                Resend Flyer
            </a>

            <a href="/member/campaigns"
               class="block rounded-xl px-4 py-3 text-slate-700 hover:bg-slate-100">
                Campaigns
            </a>

            <a href="/member/agent-info"
               class="block rounded-xl px-4 py-3 text-slate-700 hover:bg-slate-100">
                Agent Info
            </a>

            <a href="/member/account"
               class="block rounded-xl px-4 py-3 text-slate-700 hover:bg-slate-100">
                Account Info
            </a>

            <a href="/logout"
               class="block rounded-xl px-4 py-3 text-red-600 hover:bg-red-50">
                Log Out
            </a>

        </nav>

    </aside>

    <section class="min-w-0 flex-1">

        {{-- HEADER --}}
        <div class="mb-8">

            <div class="text-sm font-bold uppercase tracking-wider text-[#123f91]">
                Step 1 of 5
            </div>

            <h1 class="mt-2 text-4xl font-black text-slate-900">
                Create New Flyer
            </h1>

            <p class="mt-2 text-slate-500">
                Start by entering the property's address.
            </p>

        </div>

        {{-- PROGRESS --}}
        <div class="mb-8 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">

            <div class="flex flex-wrap items-center gap-3 text-sm font-bold">

                <span class="text-[#123f91]">
                    ● Property
                </span>

                <span class="text-slate-300">→</span>

                <span class="text-slate-400">
                    Details
                </span>

                <span class="text-slate-300">→</span>

                <span class="text-slate-400">
                    Photos
                </span>

                <span class="text-slate-300">→</span>

                <span class="text-slate-400">
                    Text
                </span>

                <span class="text-slate-300">→</span>

                <span class="text-slate-400">
                    Design
                </span>

            </div>

        </div>

        <form action="/member/create-flyer" method="post">

            @csrf

            {{-- PROPERTY CARD --}}
            <div class="rounded-3xl bg-white p-8 shadow-sm ring-1 ring-black/5">

                <h2 class="text-2xl font-black text-slate-900">
                    Property Information
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Enter the property address to begin.
                </p>

                <div class="mt-8 space-y-6">

                    <div>

                        <label class="mb-2 block text-sm font-bold text-slate-700">
                            Property Address
                        </label>

                        <input
                            type="text"
                            name="xFullStreet"
                            class="w-full rounded-2xl border border-slate-300 px-5 py-4 text-lg focus:border-[#123f91] focus:outline-none"
                            placeholder="123 Main Street"
                            required
                        >

                    </div>

                    <div class="grid gap-6 md:grid-cols-3">

                        <div>

                            <label class="mb-2 block text-sm font-bold text-slate-700">
                                City
                            </label>

                            <input
                                type="text"
                                name="xCity"
                                class="w-full rounded-2xl border border-slate-300 px-4 py-3"
                                required
                            >

                        </div>

                        <div>

                            <label class="mb-2 block text-sm font-bold text-slate-700">
                                State
                            </label>

                            <input
                                type="text"
                                name="xState"
                                value="AZ"
                                class="w-full rounded-2xl border border-slate-300 px-4 py-3"
                                required
                            >

                        </div>

                        <div>

                            <label class="mb-2 block text-sm font-bold text-slate-700">
                                ZIP Code
                            </label>

                            <input
                                type="text"
                                name="xZip"
                                class="w-full rounded-2xl border border-slate-300 px-4 py-3"
                                required
                            >

                        </div>

                    </div>

                    <div>

                        <label class="mb-2 block text-sm font-bold text-slate-700">
                            MLS Number (Optional)
                        </label>

                        <input
                            type="text"
                            name="xMlsNum"
                            class="w-full rounded-2xl border border-slate-300 px-4 py-3"
                        >

                    </div>

                </div>

            </div>

            {{-- ACTIONS --}}
            <div class="mt-8 flex items-center justify-between">

                <a href="/member/dashboard"
                   class="rounded-xl bg-white px-5 py-3 font-bold text-slate-700 shadow-sm ring-1 ring-black/5">
                    Cancel
                </a>

                <button
                    type="submit"
                    class="rounded-xl bg-[#123f91] px-6 py-3 font-bold text-white hover:bg-[#0f3274]">
                    Save & Continue →
                </button>

            </div>

        </form>

    </section>

</div>
```

</main>

@include('public.layout.footer')

</body>
</html>
