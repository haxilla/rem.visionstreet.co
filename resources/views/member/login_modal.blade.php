<div class="overflow-hidden rounded-[22px] bg-white relative">

    {{-- Top band --}}
    <div class="relative bg-gradient-to-r from-[#1b2f63] via-[#223a75] to-[#2a4486] px-6 py-5 sm:px-8">

        {{-- Close button --}}
        <button
            type="button"
            onclick="closeModal()"
            class="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded-full bg-white/10 text-white transition hover:bg-white/20"
            aria-label="Close modal"
        >
            <svg xmlns="http://www.w3.org/2000/svg"
                 class="h-5 w-5"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor"
                 stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        <div class="flex items-start justify-between gap-4 pr-12">
            <div>
                <div class="text-[11px] font-semibold uppercase tracking-[0.22em] text-white/65">
                    Member Access
                </div>

                <h2 class="mt-1 font-serif text-[34px] leading-none text-white sm:text-[38px]">
                    Welcome Back
                </h2>

                <p class="mt-3 max-w-[360px] text-[14px] leading-6 text-white/78">
                    Log in to manage your account.
                </p>
            </div>
        </div>
    </div>

    {{-- Form body --}}
    <div class="px-6 py-6 sm:px-8 sm:py-7">

        @if ($errors->any())
            <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-[13px] text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('member.login.submit') }}" class="space-y-5">
            @csrf

            <div>
                <label for="member_username" class="mb-2 block text-[12px] font-semibold uppercase tracking-[0.14em] text-[#1d2f5f]/70">
                    Email Address
                </label>

                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-[#4966b1]/60">
                        <i class="ti-email text-[16px]"></i>
                    </span>

                    <input
                        id="member_username"
                        type="email"
                        name="username"
                        value="{{ old('username') }}"
                        required
                        autofocus
                        autocomplete="email"
                        placeholder="you@example.com"
                        class="h-[54px] w-full rounded-[16px] border border-[#d7def0] bg-[#f8faff] pl-11 pr-4 text-[15px] text-[#1c2440] outline-none transition placeholder:text-[#90a0c6] focus:border-[#5f7ed1] focus:bg-white focus:ring-4 focus:ring-[#5f7ed1]/10"
                    >
                </div>
            </div>

            <div>
                <div class="mb-2 flex items-center justify-between gap-3">
                    <label for="member_password" class="block text-[12px] font-semibold uppercase tracking-[0.14em] text-[#1d2f5f]/70">
                        Password
                    </label>

                    <a href="/forgot-password" class="text-[12px] font-semibold text-[#3559b6] transition hover:text-[#1f3f8f]">
                        Forgot Password?
                    </a>
                </div>

                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-[#4966b1]/60">
                        <i class="ti-lock text-[16px]"></i>
                    </span>

                    <input
                        id="member_password"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="Enter your password"
                        class="h-[54px] w-full rounded-[16px] border border-[#d7def0] bg-[#f8faff] pl-11 pr-4 text-[15px] text-[#1c2440] outline-none transition placeholder:text-[#90a0c6] focus:border-[#5f7ed1] focus:bg-white focus:ring-4 focus:ring-[#5f7ed1]/10"
                    >
                </div>
            </div>

            <div class="flex items-center justify-between gap-4 pt-1">
                <label class="inline-flex cursor-pointer items-center gap-2 text-[13px] text-[#42506f]">
                    <input
                        type="checkbox"
                        name="remember"
                        value="1"
                        class="h-4 w-4 rounded border-[#b7c4e5] text-[#3153aa] focus:ring-[#3153aa]/20"
                    >
                    <span>Keep me signed in</span>
                </label>
            </div>

            <div class="pt-2">
                <button
                    type="submit"
                    class="inline-flex h-[54px] w-full items-center justify-center rounded-full bg-gradient-to-r from-[#284892] via-[#3358ab] to-[#4668bf] px-6 text-[15px] font-semibold text-white shadow-[0_14px_30px_rgba(41,72,146,.22)] transition hover:scale-[1.01] hover:shadow-[0_18px_36px_rgba(41,72,146,.28)]"
                >
                    Log In
                </button>
            </div>
        </form>

        <div class="mt-6 border-t border-[#e7ebf5] pt-5 text-center">
            <p class="text-[13px] text-[#64708c]">
                Need an account?
                <a href="/signup" class="font-semibold text-[#3559b6] transition hover:text-[#1f3f8f]">
                    Sign up here
                </a>
            </p>
        </div>

    </div>

</div>