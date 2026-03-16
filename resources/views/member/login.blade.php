{{--
    RealtyEmails — Member Login
    100% Tailwind CSS — zero custom CSS / style blocks
    -------------------------------------------------------
    Pass $slides — collection of listing models with:
        thePhotos, theMeta, theAgent, theAgentCleanup,
        theOffice, xFullStreet, xCity, xState, xxZip
--}}

@php
    $slides = $data['slides'];
    $cards  = [];

    foreach ($slides->take(4) as $the) {
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

        if ($listingImg) {
            $cards[] = [
                'img'    => $listingImg,
                'agent'  => $agentImg,
                'name'   => $the->theAgent->agtFullName ?? '',
                'office' => $the->theOffice->officeName ?? '',
                'street' => $the->xFullStreet ?? '',
                'city'   => trim(($the->xCity ?? '') . ', ' . ($the->xState ?? '')),
            ];
        }
    }
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
                        fadein: {
                            '0%':   { opacity: '0', transform: 'translateY(10px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)'    },
                        },
                        slowzoom: {
                            '0%':   { transform: 'scale(1.00)' },
                            '100%': { transform: 'scale(1.06)' },
                        },
                    },
                    animation: {
                        fadein:   'fadein .5s ease both',
                        slowzoom: 'slowzoom 14s ease infinite alternate',
                    },
                }
            }
        }
    </script>
</head>

<body class="min-h-full font-body bg-slate-100">

    @include('public.layout.nav')

    <div class="flex min-h-screen items-center justify-center px-6 py-16">

        <div class="w-full max-w-5xl">

            {{-- ── Two-column card ─────────────────────────── --}}
            <div class="grid grid-cols-1 overflow-hidden rounded-3xl shadow-2xl shadow-brand-bluedark/20 lg:grid-cols-2">

                {{-- ════════════════════════════════════════
                     LEFT — Latest Listings
                ════════════════════════════════════════ --}}
                <div class="bg-brand-bluedark px-10 py-12">

                    {{-- Logo lives here now --}}
                    <div class="mb-8 flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-gold flex-shrink-0">
                            <svg class="h-5 w-5 fill-white" viewBox="0 0 24 24">
                                <path d="M12 2L2 9.5V22h7v-7h6v7h7V9.5L12 2z"/>
                            </svg>
                        </div>
                        <span class="font-display text-[22px] font-bold tracking-tight text-white">
                            Realty<span class="text-brand-gold">Emails</span>
                        </span>
                    </div>

                    <p class="mb-1 text-[11px] font-semibold uppercase tracking-[.14em] text-brand-gold">
                        Latest Listings
                    </p>
                    <h2 class="font-display text-[28px] font-semibold leading-tight text-white">
                        What's new on<br>RealtyEmails today.
                    </h2>

                    <div class="mt-8 flex flex-col gap-5">
                        @if(count($cards))
                            @foreach($cards as $i => $card)
                                <div
                                    class="flex gap-4 items-start animate-fadein"
                                    style="animation-delay: {{ $i * 80 }}ms"
                                >
                                    {{-- Photo --}}
                                    <div class="h-20 w-28 flex-shrink-0 overflow-hidden rounded-xl bg-brand-bluemid">
                                        <img
                                            src="{{ $card['img'] }}"
                                            alt="{{ $card['street'] }}"
                                            loading="lazy"
                                            class="h-full w-full object-cover animate-slowzoom"
                                            style="animation-delay: -{{ $i * 3 }}s"
                                        >
                                    </div>

                                    {{-- Info --}}
                                    <div class="min-w-0 pt-0.5">
                                        <p class="truncate text-[15px] font-semibold leading-snug text-white">
                                            {{ $card['street'] }}
                                        </p>
                                        <p class="mt-0.5 text-[13px] text-white/50">
                                            {{ $card['city'] }}
                                        </p>

                                        @if($card['agent'])
                                            <div class="mt-2.5 flex items-center gap-2">
                                                <img
                                                    src="{{ $card['agent'] }}"
                                                    alt="{{ $card['name'] }}"
                                                    class="h-6 w-6 rounded-lg object-cover ring-1 ring-white/20"
                                                >
                                                <span class="truncate text-[12px] text-white/60">
                                                    {{ $card['name'] }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                @if(!$loop->last)
                                    <div class="border-t border-white/10"></div>
                                @endif
                            @endforeach
                        @else
                            @foreach(range(1,4) as $n)
                                <div class="flex gap-4 items-start">
                                    <div class="h-20 w-28 flex-shrink-0 rounded-xl bg-white/10 animate-pulse"></div>
                                    <div class="flex-1 pt-1 space-y-2">
                                        <div class="h-4 w-3/4 rounded bg-white/10 animate-pulse"></div>
                                        <div class="h-3 w-1/2 rounded bg-white/8 animate-pulse"></div>
                                        <div class="h-3 w-2/3 rounded bg-white/8 animate-pulse"></div>
                                    </div>
                                </div>
                                @if($n < 4)
                                    <div class="border-t border-white/10"></div>
                                @endif
                            @endforeach
                        @endif
                    </div>

                </div>

                {{-- ════════════════════════════════════════
                     RIGHT — Login Form
                ════════════════════════════════════════ --}}
                <div class="relative flex flex-col justify-center bg-white px-10 py-12">

                    {{-- Top accent line --}}
                    <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-brand-blue to-brand-gold rounded-tr-3xl"></div>

                    {{-- Header --}}
                    <div class="mb-8">
                        <p class="mb-1.5 text-[11px] font-semibold uppercase tracking-[.14em] text-brand-golddark">
                            Member Portal
                        </p>
                        <h2 class="font-display text-[36px] font-bold leading-none tracking-tight text-brand-bluedark">
                            Welcome back.
                        </h2>
                        <p class="mt-3 text-[14px] leading-relaxed text-slate-500">
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
                            <label for="email" class="mb-2 block text-[11px] font-semibold uppercase tracking-[.08em] text-brand-blue">
                                Email Address
                            </label>
                            <div class="relative">
                                <input
                                    type="email"
                                    id="xxAgtUname"
                                    name="xxAgtUname"
                                    value="{{ old('xxAgtUname') }}"
                                    placeholder="you@example.com"
                                    autocomplete="email"
                                    required
                                    class="block w-full rounded-xl border border-slate-200 bg-slate-50 py-3.5 pl-11 pr-4 text-[15px] text-slate-800 placeholder-slate-300 outline-none transition focus:border-brand-blue focus:bg-white focus:ring-4 focus:ring-brand-blue/10"
                                >
                                <svg class="pointer-events-none absolute left-3.5 top-1/2 h-[18px] w-[18px] -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <rect x="2" y="4" width="20" height="16" rx="3"/>
                                    <path d="m2 7 10 7 10-7"/>
                                </svg>
                            </div>
                        </div>

                        {{-- Password --}}
                        <div>
                            <label for="password" class="mb-2 block text-[11px] font-semibold uppercase tracking-[.08em] text-brand-blue">
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
                                    class="block w-full rounded-xl border border-slate-200 bg-slate-50 py-3.5 pl-11 pr-12 text-[15px] text-slate-800 placeholder-slate-300 outline-none transition focus:border-brand-blue focus:bg-white focus:ring-4 focus:ring-brand-blue/10"
                                >
                                <svg class="pointer-events-none absolute left-3.5 top-1/2 h-[18px] w-[18px] -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <rect x="3" y="11" width="18" height="11" rx="2"/>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                </svg>
                                <button
                                    type="button"
                                    onclick="togglePw()"
                                    aria-label="Toggle password visibility"
                                    class="absolute right-3.5 top-1/2 -translate-y-1/2 p-0.5 text-slate-400 transition-colors hover:text-brand-blue"
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
                            class="relative mt-2 w-full overflow-hidden rounded-2xl bg-brand-blue py-4 text-[15px] font-semibold tracking-wide text-white shadow-lg shadow-brand-blue/25 transition hover:-translate-y-px hover:bg-brand-bluemid hover:shadow-xl active:translate-y-0"
                        >
                            <span class="relative z-10">Sign In to RealtyEmails</span>
                            <span class="pointer-events-none absolute inset-0 bg-gradient-to-b from-white/10 to-transparent"></span>
                        </button>

                    </form>

                    {{-- Divider --}}
                    <div class="my-6 flex items-center gap-3 text-[11px] uppercase tracking-widest text-slate-300">
                        <span class="flex-1 border-t border-slate-200"></span>
                        or
                        <span class="flex-1 border-t border-slate-200"></span>
                    </div>

                    {{-- Register CTA --}}
                    <p class="text-center text-[14px] text-slate-500">
                        Not a member yet?
                        <a href="#" class="ml-1 font-semibold text-brand-blue hover:underline">
                            Create a free account →
                        </a>
                    </p>

                </div>

            </div>

            {{-- Footer --}}
            <p class="mt-8 text-center text-[12px] text-slate-400">
                © {{ date('Y') }} RealtyEmails. All rights reserved.
            </p>

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