{{--
    RealtyEmails - Agent Login

    RealtyEmails is where agents turn a listing into an e-flyer, email it to local agents,
    share / print it and track how many times it's viewed. The people signing in here are
    those agents, so the page speaks to them: no "saved searches" or "market reports".

    One compact card under the fixed navbar: the sign-in form, with "What's new on RealtyEmails"
    (the most recently sent flyers) beside it. Tailwind comes from the CDN like the rest of the
    login flow, so nothing here depends on the site stylesheet being rebuilt.
    The form field names (xxAgtUname / password / recaptcha_token) are what
    guestController::memberLogin reads - keep them.
--}}
@php
    // "What's new": the most recently SENT flyers (app/member/login.php) - 4 of them, and each
    // needs a cover photo to show. With none, the panel is left out and the form stands alone.
    $cards = [];

    foreach (($data['slides'] ?? collect()) as $the) {
        if (count($cards) >= 4) {
            break;
        }

        $photo = $the->thePhotos->where('def', '=', '1')->first()?->photoName;

        if (!$photo || empty($the->theMeta?->zipDir) || empty($the->theMeta?->mlsDir)) {
            continue;
        }

        $agentImg = null;
        if (!empty($the->theAgent?->agtPhoto) && !empty($the->theAgent?->theAgentCleanup?->newRemID)) {
            $agentImg = "/agentPhotos/{$the->theAgent->theAgentCleanup->newRemID}/{$the->theAgent->agtPhoto}";
        } elseif (!empty($the->theAgent?->agtPhoto) && !empty($the->theOffice?->officeID)) {
            $agentImg = "https://realtyemails.com/HQoffice/{$the->theOffice->officeID}/{$the->theAgent->agtPhoto}";
        }

        $cards[] = [
            'img'    => "/hqphotos/{$the->theMeta->zipDir}/{$the->theMeta->mlsDir}/{$photo}",
            'agent'  => $agentImg,
            'name'   => $the->theAgent->agtFullName ?? '',
            'street' => $the->xFullStreet ?? '',
            'city'   => trim(($the->xCity ?? '') . ', ' . ($the->state ?? ''), ', '),
            'sent'   => $the->xLastDeliveryDate ? \Carbon\Carbon::parse($the->xLastDeliveryDate)->format('M j') : null,
        ];
    }
@endphp
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

        <div class="w-full {{ count($cards) ? 'max-w-[860px]' : 'max-w-[400px]' }}">

            <div class="overflow-hidden rounded-2xl bg-white shadow-[0_10px_40px_rgba(13,27,62,.08)] ring-1 ring-slate-200/70 {{ count($cards) ? 'md:grid md:grid-cols-[1fr_1.05fr]' : '' }}">

                {{-- ── What's new: the most recently sent flyers ── --}}
                @if(count($cards))
                    <aside class="order-2 bg-brand-bluedark px-7 py-8 sm:px-9 md:order-1">

                        <p class="text-[11px] font-semibold uppercase tracking-[.16em] text-brand-gold">Recently sent</p>
                        <h2 class="mt-1.5 font-display text-[26px] font-semibold leading-[1.1] text-white">
                            What's new on RealtyEmails
                        </h2>

                        <div class="mt-6 space-y-4">
                            @foreach($cards as $card)
                                <div class="flex items-center gap-3.5">
                                    <div class="h-[54px] w-[82px] flex-shrink-0 overflow-hidden rounded-lg bg-brand-bluemid">
                                        <img src="{{ $card['img'] }}" alt="{{ $card['street'] }}" loading="lazy" class="h-full w-full object-cover">
                                    </div>

                                    <div class="min-w-0">
                                        <p class="truncate text-[14px] font-semibold leading-snug text-white">{{ $card['street'] }}</p>
                                        <p class="truncate text-[12px] text-white/50">{{ $card['city'] }}</p>

                                        <div class="mt-1 flex items-center gap-1.5 text-[11px] text-white/55">
                                            @if($card['agent'])
                                                <img src="{{ $card['agent'] }}" alt="" class="h-4 w-4 rounded object-cover ring-1 ring-white/20">
                                            @endif
                                            <span class="truncate">{{ $card['name'] }}</span>
                                            @if($card['sent'])
                                                <span class="flex-shrink-0 text-white/35">&middot; Sent {{ $card['sent'] }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </aside>
                @endif

                {{-- ── Sign in ── --}}
                <div class="order-1 px-7 py-8 sm:px-9 sm:py-9 md:order-2">

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

                    {{-- Agents from the old site: old passwords aren't used, they make a new one by email --}}
                    <div class="mt-5 rounded-xl bg-brand-blue/5 px-4 py-3 text-[13px] leading-relaxed text-slate-600">
                        <span class="font-semibold text-brand-bluedark">Used RealtyEmails before?</span>
                        We've moved to a new site, and your old password won't work here.
                        <a href="{{ route('member.password.forgot') }}" class="font-semibold text-brand-blue hover:underline">Create your password</a>
                        &mdash; we'll email you a link.
                    </div>

                    {{-- New agents start from the free flyer on the home page --}}
                    <p class="mt-5 border-t border-slate-100 pt-5 text-center text-[13px] text-slate-500">
                        New to RealtyEmails?
                        <a href="/" class="font-semibold text-brand-blue hover:underline">Start with a free flyer</a>
                    </p>

                </div>

            </div>

            <p class="mt-6 text-center text-[11px] text-slate-400">&copy; {{ date('Y') }} RealtyEmails</p>

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
