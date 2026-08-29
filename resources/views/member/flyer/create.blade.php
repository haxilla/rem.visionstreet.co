@include('member.layout.head')

<body data-section="member" class="relative min-h-screen bg-[#f0f2f7] font-sans text-slate-800">

@include('member.layout.nav')

<main class="min-h-screen bg-[#f0f2f7] pt-24">

<div class="mx-auto w-full max-w-[700px] px-4 pb-16 sm:px-6 lg:px-8">

    <section>

        {{-- HEADER --}}
        <div class="mb-8 text-center">

            <h1 class="text-4xl font-black text-slate-900">
                Let's Start Your Flyer
            </h1>

            <p class="mt-2 text-slate-500">
                Is this property already listed in the MLS?
            </p>

        </div>

        <form action="/member/flyer/property" method="get"
            class="rounded-3xl bg-white p-8 shadow-sm ring-1 ring-black/5">

            <label class="mb-2 block text-sm font-bold text-slate-700">
                If your property is already listed in the MLS, enter the MLS# below to continue.
            </label>

            <input
                type="text"
                name="xMlsNum"
                class="w-full rounded-2xl border border-slate-300 px-5 py-4 text-lg focus:border-[#123f91] focus:outline-none"
                placeholder="MLS Number"
            >

            <div class="mt-6 flex items-center justify-between gap-3">

                <a href="/member/flyer/property"
                   class="rounded-xl bg-white px-5 py-3 font-bold text-slate-600 shadow-sm ring-1 ring-black/5 hover:bg-slate-50">
                    Skip →
                </a>

                <button type="submit"
                    class="rounded-xl bg-[#123f91] px-6 py-3 font-bold text-white hover:bg-[#0f3274]">
                    Continue →
                </button>

            </div>

        </form>

    </section>

</div>

</main>

@include('public.layout.footer')

</body>
</html>
