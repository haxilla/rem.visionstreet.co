{{--
    RealtyEmails — Member Login
    100% Tailwind CSS — zero custom CSS / style blocks
    -------------------------------------------------------
    Pass $slides — collection of listing models with:
        thePhotos, theMeta, theAgent, theAgentCleanup,
        theOffice, xFullStreet, xCity, xState, xxZip,
        xPrice / xListPrice
--}}

@php
    $slides = $data['slides'];
    $cards  = [];

    foreach ($slides->take(9) as $the) {
        $photoObj   = $the->thePhotos->where('def','=','1')->first();
        $photo      = $photoObj?->photoName;
        $listingImg = null;

        if ($photo && !empty($the->theMeta?->zipDir) && !empty($the->theMeta?->mlsDir)) {
            $listingImg = "https://realtyrepublic.com/hqphotos/{$the->theMeta->zipDir}/{$the->theMeta->mlsDir}/{$photo}";
        }

        $agentImg = null;
        if (!empty($the->theAgent?->agtPhoto) && !empty($the->theAgent?->theAgentCleanup?->newRemID)) {
            $agentImg = "https://realtyrepublic.com/agentPhotos/{$the->theAgent->theAgentCleanup->newRemID}/{$the->theAgent->agtPhoto}";
        } elseif (!empty($the->theAgent?->agtPhoto) && !empty($the->theOffice?->officeID)) {
            $agentImg = "https://realtyemails.com/HQoffice/{$the->theOffice->officeID}/{$the->theAgent->agtPhoto}";
        }

        $price      = $the->xPrice ?? $the->xListPrice ?? null;
        $priceLabel = $price ? '$' . number_format((float) $price) : null;

        if ($listingImg) {
            $cards[] = [
                'img'    => $listingImg,
                'agent'  => $agentImg,
                'name'   => $the->theAgent->agtFullName ?? '',
                'office' => $the->theOffice->officeName ?? '',
                'street' => $the->xFullStreet ?? '',
                'city'   => trim(($the->xCity ?? '') . ', ' . ($the->xState ?? '')),
                'price'  => $priceLabel,
            ];
        }
    }

    $tickerCards = array_merge($cards, $cards);
@endphp

<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Login — RealtyEmails</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&display=swap" rel="stylesheet">

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
                    keyframes: {
                        slowzoom: {
                            '0%':   { transform: 'scale(1.00)' },
                            '100%': { transform: 'scale(1.10)' },
                        },
                        ticker: {
                            '0%':   { transform: 'translateX(0)' },
                            '100%': { transform: 'translateX(-50%)' },
                        },
                        pulse2: {
                            '0%, 100%': { opacity: '1',  transform: 'scale(1)'   },
                            '50%':      { opacity: '.4', transform: 'scale(.65)' },
                        },
                        fadein: {
                            '0%':   { opacity: '0', transform: 'translateY(14px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)'    },
                        },
                    },
                    animation: {
                        slowzoom: 'slowzoom 18s ease infinite alternate',
                        ticker:   'ticker 32s linear infinite',
                        pulse2:   'pulse2 1.8s ease infinite',
                        fadein:   'fadein .55s ease both',
                    },
                }
            }
        }
    </script>
</head>

<body class="h-full font-body bg-brand-bluedark">

<div class="flex min-h-screen">

    {{-- ══════════════════════════════════════
         LEFT — Visual Mosaic Panel
    ══════════════════════════════════════ --}}
    <div class="relative hidden lg:flex lg:flex-1 overflow-hidden bg-brand-bluedark">

        {{-- 3×3 photo mosaic --}}
        <div class="absolute inset-0 grid grid-cols-3 grid-rows-3 gap-1.5 p-1.5 z-0">
            @if(count($cards))
                @foreach($cards as $i => $card)
                    <div class="relative overflow-hidden rounded-xl bg-brand-bluemid {{ $i === 0 ? 'row-span-2' : '' }}">
                        <img
                            src="{{ $card['img'] }}"
                            alt="{{ $card['street'] }}"
                            loading="lazy"
                            class="w-full h-full object-cover animate-slowzoom"
                            style="animation-delay: -{{ $i * 3 }}s"
                        >
                        @if($i === 0 && $card['price'])
                            <span class="absolute bottom-3 left-3 z-10 inline-flex items-center rounded-full bg-brand-gold/90 px-3 py-1 text-[11px] font-semibold text-white backdrop-blur-sm">
                                {{ $card['price'] }}
                            </span>
                        @endif
                    </div>
                @endforeach
            @else
                <div class="row-span-2 rounded-xl bg-gradient-to-br from-brand-bluemid to-brand-blue"></div>
                <div class="rounded-xl bg-gradient-to-br from-brand-blue to-brand-bluemid"></div>
                <div class="rounded-xl bg-gradient-to-br from-brand-golddark to-brand-gold"></div>
                <div class="rounded-xl bg-gradient-to-br from-brand-bluedark to-brand-blue"></div>
                <div class="rounded-xl bg-gradient-to-br from-brand-gold to-brand-golddark"></div>
                <div class="rounded-xl bg-gradient-to-br from-brand-bluemid to-brand-bluedark"></div>
                <div class="rounded-xl bg-gradient-to-br from-brand-blue to-brand-bluemid"></div>
                <div class="rounded-xl bg-gradient-to-br from-brand-golddark to-brand-bluemid"></div>
            @endif
        </div>

        {{-- Dark overlay --}}
        <div class="absolute inset-0 z-10 bg-gradient-to-b from-brand-bluedark/80 via-brand-bluedark/35 to-brand-bluedark/78 pointer-events-none"></div>

        {{-- Branded content --}}
        <div class="relative z-20 flex flex-col justify-between w-full p-12">

            {{-- Logo --}}
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-brand-gold flex-shrink-0">
                    <svg class="h-5 w-5 fill-white" viewBox="0 0 24 24">
                        <path d="M12 2L2 9.5V22h7v-7h6v7h7V9.5L12 2z"/>
                    </svg>
                </div>
                <span class="font-display text-[22px] font-bold text-white tracking-tight">
                    Realty<span class="text-brand-gold">Emails</span>
                </span>
            </div>

            {{-- Headline --}}
            <div class="animate-fadein">
                <h1 class="font-display text-[52px] font-semibold leading-[1.07] text-white tracking-tight">
                    The smartest way<br>
                    to <em class="italic text-brand-gold">find your</em><br>
                    next home.
                </h1>
                <p class="mt-5 max-w-sm text-[15px] leading-relaxed text-white/70">
                    Real-time listing alerts, market intelligence,
                    and curated property reports — delivered to your inbox.
                </p>
            </div>

            {{-- Live ticker --}}
            <div class="flex items-center gap-3 overflow-hidden">
                <span class="h-2 w-2 flex-shrink-0 rounded-full bg-brand-gold animate-pulse2"></span>
                <div class="overflow-hidden flex-1">
                    <div class="flex gap-10 animate-ticker whitespace-nowrap">
                        @if(count($tickerCards))
                            @foreach($tickerCards as $card)
                                <div class="inline-flex items-center gap-2.5 text-white/80 text-[13px] font-medium">
                                    @if($card['agent'])
                                        <img
                                            src="{{ $card['agent'] }}"
                                            alt="{{ $card['name'] }}"
                                            class="h-7 w-7 rounded-lg object-cover border border-white/20 flex-shrink-0"
                                        >
                                    @endif
                                    <span>{{ Str::limit($card['street'], 28) }}</span>
                                    @if($card['price'])
                                        <span class="text-brand-gold font-semibold">{{ $card['price'] }}</span>
                                    @endif
                                    <span class="text-white/25 mx-1">·</span>
                                </div>
                            @endforeach
                        @else
                            @foreach(['4821 Ocean Drive', '72 Maple Court', '1 Harbor View', '890 Sunset Blvd'] as $t)
                                <div class="inline-flex items-center gap-2 text-white/70 text-[13px]">
                                    <span>{{ $t }}</span>
                                    <span class="text-white/25 mx-2">·</span>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>


    {{-- ══════════════════════════════════════
         RIGHT — Login Form Panel
    ══════════════════════════════════════ --}}
    <div class="relative flex w-full flex-col justify-center bg-slate-50 px-8 py-14 sm:px-12 lg:w-[480px] lg:flex-none">

        {{-- Top gradient bar --}}
        <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-brand-blue to-brand-gold"></div>

        {{-- Mobile logo --}}
        <div class="flex items-center gap-3 mb-10 lg:hidden">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-gold">
                <svg class="h-5 w-5 fill-white" viewBox="0 0 24 24">
                    <path d="M12 2L2 9.5V22h7v-7h6v7h7V9.5L12 2z"/>
                </svg>
            </div>
            <span class="font-display text-[20px] font-bold text-brand-bluedark">
                Realty<span class="text-brand-gold">Emails</span>
            </span>
        </div>

        {{-- Form header --}}
        <div class="mb-8">
            <p class="text-[11px] font-semibold uppercase tracking-[.14em] text-brand-golddark mb-2">
                Member Portal
            </p>
            <h2 class="font-display text-[38px] font-bold leading-none text-brand-bluedark tracking-tight">
                Welcome back.
            </h2>
            <p class="mt-3 text-[14px] text-slate-500 leading-relaxed">
                Sign in to access your listing alerts,<br>
                saved searches &amp; market reports.
            </p>
        </div>

        {{-- Errors --}}
        @if ($errors->any())
            <div class="mb-6 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3.5 text-[13px] text-red-700">
                <svg class="mt-0.5 h-4 w-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <div>@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
            </div>
        @endif

        @if (session('status'))
            <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3.5 text-[13px] text-green-700">
                {{ session('status') }}
            </div>
        @endif

        {{-- Form --}}
        <form method="POST" action="{{ route('member.login') }}" novalidate class="space-y-5">
            @csrf

            {{-- Email --}}
            <div>
                <label for="email" class="block text-[11px] font-semibold uppercase tracking-[.08em] text-brand-blue mb-2">
                    Email Address
                </label>
                <div class="relative">
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="you@example.com"
                        autocomplete="email"
                        required
                        class="block w-full rounded-xl border border-slate-200 bg-white py-3.5 pl-11 pr-4 text-[15px] text-slate-800 placeholder-slate-300 outline-none transition focus:border-brand-blue focus:ring-4 focus:ring-brand-blue/10"
                    >
                    <svg class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 h-[18px] w-[18px] text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <rect x="2" y="4" width="20" height="16" rx="3"/>
                        <path d="m2 7 10 7 10-7"/>
                    </svg>
                </div>
            </div>

            {{-- Password --}}
            <div>
                <label for="password" class="block text-[11px] font-semibold uppercase tracking-[.08em] text-brand-blue mb-2">
                    Password
                </label>
                <div class="relative">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="••••••••••"
                        autocomplete="current-password"
                        required
                        class="block w-full rounded-xl border border-slate-200 bg-white py-3.5 pl-11 pr-12 text-[15px] text-slate-800 placeholder-slate-300 outline-none transition focus:border-brand-blue focus:ring-4 focus:ring-brand-blue/10"
                    >
                    <svg class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 h-[18px] w-[18px] text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <rect x="3" y="11" width="18" height="11" rx="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    <button
                        type="button"
                        onclick="togglePw()"
                        aria-label="Toggle password visibility"
                        class="absolute right-3.5 top-1/2 -translate-y-1/2 flex items-center text-slate-400 hover:text-brand-blue transition-colors p-0.5"
                    >
                        <svg id="eyeIcon" class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Remember / Forgot --}}
            <div class="flex items-center justify-between pt-1">
                <label class="flex cursor-pointer select-none items-center gap-2.5 text-[13px] text-slate-500">
                    <input
                        type="checkbox"
                        name="remember"
                        {{ old('remember') ? 'checked' : '' }}
                        class="h-4 w-4 cursor-pointer rounded border-slate-300 accent-brand-blue"
                    >
                    Keep me signed in
                </label>
                <a href="#" class="text-[13px] font-semibold text-brand-blue hover:underline">
                    Forgot password?
                </a>
            </div>

            {{-- Submit --}}
            <button
                type="submit"
                class="relative mt-2 w-full overflow-hidden rounded-2xl bg-brand-blue py-4 text-[15px] font-semibold tracking-wide text-white shadow-lg shadow-brand-blue/30 transition hover:-translate-y-px hover:bg-brand-bluemid hover:shadow-xl hover:shadow-brand-blue/40 active:translate-y-0 active:shadow-md"
            >
                <span class="relative z-10">Sign In to RealtyEmails</span>
                <span class="pointer-events-none absolute inset-0 bg-gradient-to-b from-white/10 to-transparent"></span>
            </button>

        </form>

        {{-- Divider --}}
        <div class="my-7 flex items-center gap-3 text-[11px] uppercase tracking-widest text-slate-400">
            <span class="flex-1 border-t border-slate-200"></span>
            or
            <span class="flex-1 border-t border-slate-200"></span>
        </div>

        {{-- Register CTA --}}
        <p class="text-center text-[14px] text-slate-500">
            Not a member yet?
            <a href="{{ route('register') }}" class="ml-1 font-semibold text-brand-blue hover:underline">
                Create a free account →
            </a>
        </p>

        {{-- Stats strip --}}
        <div class="mt-10 grid grid-cols-3 gap-3 border-t border-slate-200 pt-8 text-center">
            <div>
                <p class="font-display text-[28px] font-bold leading-none text-brand-blue">42K+</p>
                <p class="mt-1.5 text-[10px] uppercase tracking-[.1em] text-slate-400">Active Listings</p>
            </div>
            <div>
                <p class="font-display text-[28px] font-bold leading-none text-brand-blue">9.8K</p>
                <p class="mt-1.5 text-[10px] uppercase tracking-[.1em] text-slate-400">Agents</p>
            </div>
            <div>
                <p class="font-display text-[28px] font-bold leading-none text-brand-blue">Daily</p>
                <p class="mt-1.5 text-[10px] uppercase tracking-[.1em] text-slate-400">Alerts Sent</p>
            </div>
        </div>

    </div>
</div>

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

</body>
</html>