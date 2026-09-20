{{--
    RealtyEmails - Agent Login

    RealtyEmails is where agents turn a listing into an e-flyer, email it to local agents,
    share / print it and track how many times it's viewed. The people signing in here are
    those agents, so the page speaks to them: no "saved searches" or "market reports".

    A single compact card under the fixed navbar. Tailwind comes from the CDN like the rest
    of the login flow, so nothing here depends on the site stylesheet being rebuilt.
    The form field names (xxAgtUname / password / recaptcha_token) are what
    guestController::memberLogin reads - keep them.
--}}
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agent Login — RealtyEmails</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            blue:     '#214e9b',
                            bluedark: '#0d1b3e',
                            bluemid:  '#1a3a70',
                            gold:     '#e79a63',
                            golddark: '#c77d40',
                        }
                    },
                    fontFamily: {
                        display: ['"Cormorant Garamond"', 'Georgia', 'serif'],
                        body:    ['"DM Sans"', 'sans-serif'],
                    },
                }
            }
        }
    </script>
</head>

<body class="min-h-full bg-[#f3f5f9] font-body text-slate-700 antialiased">

    @include('public.layout.nav')

    {{-- The navbar is fixed and 72px tall; the extra top padding keeps the card well clear of it. --}}
    <main class="flex min-h-screen flex-col items-center justify-center px-4 pb-14 pt-[120px]">

        <div class="w-full max-w-[400px]">

            <div class="rounded-2xl bg-white px-7 py-8 shadow-[0_10px_40px_rgba(13,27,62,.08)] ring-1 ring-slate-200/70 sm:px-9 sm:py-9">

                {{-- Heading --}}
                <p class="text-[11px] font-semibold uppercase tracking-[.16em] text-brand-golddark">Agent Login</p>
                <h1 class="mt-1.5 font-display text-[32px] font-bold leading-[1.05] tracking-tight text-brand-bluedark">
                    Sign in to your account
                </h1>
                <p class="mt-2.5 text-[14px] leading-relaxed text-slate-500">
                    Create and send your listing flyers, and see how they're doing.
                </p>

                {{-- Status (e.g. "we emailed you a link") and errors --}}
                @if (session('status'))
                    <div class="mt-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-[13px] leading-relaxed text-emerald-800">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mt-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-[13px] leading-relaxed text-red-700">
                        @foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach
                    </div>
                @endif

                {{-- Form --}}
                <form method="POST" action="{{ route('member.login') }}" novalidate class="mt-6 space-y-4">
                    @csrf

                    <div>
                        <label for="xxAgtUname" class="mb-1.5 block text-[12px] font-semibold text-slate-600">Email</label>
                        <input
                            type="email"
                            id="xxAgtUname"
                            name="xxAgtUname"
                            value="{{ old('xxAgtUname') }}"
                            placeholder="you@example.com"
                            autocomplete="email"
                            autofocus
                            required
                            class="block h-11 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 text-[15px] text-slate-800 placeholder-slate-300 outline-none transition focus:border-brand-blue focus:bg-white focus:ring-4 focus:ring-brand-blue/10"
                        >
                    </div>

                    <div>
                        <div class="mb-1.5 flex items-center justify-between">
                            <label for="password" class="block text-[12px] font-semibold text-slate-600">Password</label>
                            <a href="{{ route('member.password.forgot') }}" class="text-[12px] font-semibold text-brand-blue hover:underline">
                                Forgot password?
                            </a>
                        </div>
                        <div class="relative">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                autocomplete="current-password"
                                required
                                class="block h-11 w-full rounded-xl border border-slate-200 bg-slate-50 pl-3.5 pr-11 text-[15px] text-slate-800 outline-none transition focus:border-brand-blue focus:bg-white focus:ring-4 focus:ring-brand-blue/10"
                            >
                            <button
                                type="button"
                                onclick="togglePw()"
                                aria-label="Show or hide password"
                                class="absolute right-3 top-1/2 -translate-y-1/2 p-0.5 text-slate-400 transition-colors hover:text-brand-blue"
                            >
                                <svg id="eyeIcon" class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <input type="hidden" name="recaptcha_token" id="recaptchaToken">

                    <button
                        type="submit"
                        class="mt-1 h-11 w-full rounded-xl bg-brand-blue text-[15px] font-semibold text-white shadow-sm transition hover:bg-brand-bluemid focus:outline-none focus:ring-4 focus:ring-brand-blue/20"
                    >
                        Sign in
                    </button>
                </form>

                {{-- New agents start from the free flyer on the home page --}}
                <p class="mt-6 border-t border-slate-100 pt-5 text-center text-[13px] text-slate-500">
                    New to RealtyEmails?
                    <a href="/" class="font-semibold text-brand-blue hover:underline">Start with a free flyer</a>
                </p>

            </div>

            {{-- What the account is for, kept quiet --}}
            <ul class="mt-6 flex flex-wrap items-center justify-center gap-x-6 gap-y-1 text-[12px] text-slate-400">
                <li>Email local agents</li>
                <li>Share &amp; print</li>
                <li>Track every view</li>
            </ul>

            <p class="mt-5 text-center text-[11px] text-slate-400">&copy; {{ date('Y') }} RealtyEmails</p>

        </div>
    </main>

<script>
    function togglePw() {
        const input = document.getElementById('password');
        const icon  = document.getElementById('eyeIcon');
        const show  = input.type === 'password';
        input.type  = show ? 'text' : 'password';
        icon.innerHTML = show
            ? `<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
               <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
               <line x1="1" y1="1" x2="23" y2="23"/>`
            : `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
               <circle cx="12" cy="12" r="3"/>`;
    }
</script>

<script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>
<script>
    document.querySelector('form[action="{{ route('member.login') }}"]').addEventListener('submit', function (e) {
        e.preventDefault();
        var form = e.target;
        grecaptcha.ready(function () {
            grecaptcha.execute('{{ config('services.recaptcha.site_key') }}', {action: 'login'}).then(function (token) {
                document.getElementById('recaptchaToken').value = token;
                form.submit();
            });
        });
    });
</script>

</body>
</html>
