@include('public.layout.head')

<body data-section="admin" 
class="linkcheck relative bg-white min-h-screen font-sans text-gray-800 postgres">

  @include('public.layout.nav')

  <main class="transition-all duration-300 min-h-screen pt-24 relative"
  :class="collapsed ? 'ml-20' : 'ml-64'">
    <div class="ml-8 mr-8 lg:ml-10 lg:mr-10">
      <div class="pageswap p-6 w-full">
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About — RealtyEmails</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        display: ['Cormorant Garamond', 'serif'],
                        sans: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        ink: '#0D0E0F',
                        mist: '#F5F3EF',
                        gold: '#C9A84C',
                        'gold-light': '#E8D5A3',
                        'gold-dark': '#8C6E2A',
                    },
                    animation: {
                        'fade-up': 'fadeUp 0.8s ease forwards',
                        'fade-in': 'fadeIn 1s ease forwards',
                        'float': 'float 6s ease-in-out infinite',
                    },
                    keyframes: {
                        fadeUp: {
                            '0%': { opacity: '0', transform: 'translateY(32px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-12px)' },
                        },
                    },
                }
            }
        }
    </script>
    <style>
        .grain {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 100;
            opacity: 0.025;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)'/%3E%3C/svg%3E");
        }
        .hero-grid {
            background-image:
                linear-gradient(rgba(201,168,76,0.07) 1px, transparent 1px),
                linear-gradient(90deg, rgba(201,168,76,0.07) 1px, transparent 1px);
            background-size: 80px 80px;
        }
        .text-gradient {
            background: linear-gradient(135deg, #C9A84C 0%, #E8D5A3 50%, #C9A84C 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .card-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-6px);
            box-shadow: 0 24px 48px rgba(0,0,0,0.25);
        }
        .reveal {
            opacity: 0;
            transform: translateY(32px);
            transition: opacity 0.8s ease, transform 0.8s ease;
        }
        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }
        .marquee-track {
            display: flex;
            animation: marquee 20s linear infinite;
            white-space: nowrap;
        }
        @keyframes marquee {
            from { transform: translateX(0); }
            to { transform: translateX(-50%); }
        }
        .glow-gold { box-shadow: 0 0 60px rgba(201,168,76,0.15); }
    </style>
</head>
<body class="bg-ink text-white font-sans antialiased overflow-x-hidden">

    <div class="grain"></div>

    {{-- NAV --}}
    <nav class="fixed top-0 left-0 right-0 z-50 border-b border-white/5 backdrop-blur-md bg-ink/80">
        <div class="max-w-7xl mx-auto px-6 lg:px-12 flex items-center justify-between h-16">
            <a href="/" class="font-display text-xl font-light tracking-wide">
                Realty<span class="text-gradient font-semibold">Emails</span>
            </a>
            <div class="hidden md:flex items-center gap-8 text-sm text-white/50 font-light">
                <a href="#" class="hover:text-white transition-colors duration-200">Features</a>
                <a href="#" class="hover:text-white transition-colors duration-200">Templates</a>
                <a href="#" class="hover:text-white transition-colors duration-200">Pricing</a>
                <a href="#" class="hover:text-white transition-colors duration-200">About</a>
            </div>
            <a href="#" class="hidden md:inline-flex items-center gap-2 text-sm font-medium px-5 py-2 rounded-full bg-gold text-ink hover:bg-gold-light transition-colors duration-200">
                Start free
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
    </nav>

    {{-- HERO --}}
    <section class="relative min-h-screen flex flex-col justify-center hero-grid pt-16">
        <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
            <div class="w-[700px] h-[700px] rounded-full bg-gold/5 blur-[120px]"></div>
        </div>
        <div class="absolute top-1/4 right-16 lg:right-32 opacity-10 animate-float hidden lg:block">
            <svg class="w-48 h-48 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="0.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
        </div>
        <div class="max-w-7xl mx-auto px-6 lg:px-12 py-24 lg:py-32">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full border border-gold/30 bg-gold/5 text-gold text-xs font-medium tracking-widest uppercase mb-10 animate-fade-in">
                <span class="w-1.5 h-1.5 rounded-full bg-gold animate-pulse"></span>
                Trusted by 12,000+ agents
            </div>
            <h1 class="font-display font-light text-5xl md:text-7xl lg:text-8xl leading-[1.05] tracking-tight max-w-4xl mb-8 animate-fade-up">
                The email platform<br>
                built for <em class="text-gradient not-italic">real estate</em><br>
                <span class="text-white/30">that actually converts.</span>
            </h1>
            <p class="text-white/50 text-lg md:text-xl font-light max-w-xl leading-relaxed mb-12 animate-fade-up" style="animation-delay:0.15s; opacity:0; animation-fill-mode:forwards;">
                RealtyEmails combines stunning listing templates with intelligent drip automation — so agents close more deals by staying top of mind, effortlessly.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 animate-fade-up" style="animation-delay:0.3s; opacity:0; animation-fill-mode:forwards;">
                <a href="#" class="inline-flex items-center justify-center gap-2 px-8 py-4 rounded-full bg-gold text-ink font-medium hover:bg-gold-light transition-all duration-200 text-sm">
                    Get started free
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
                <a href="#story" class="inline-flex items-center justify-center gap-2 px-8 py-4 rounded-full border border-white/10 text-white/60 hover:text-white hover:border-white/20 transition-all duration-200 text-sm font-light">
                    Our story
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </a>
            </div>
        </div>
        <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 text-white/20 text-xs tracking-widest uppercase">
            <span>Scroll</span>
            <div class="w-px h-12 bg-gradient-to-b from-white/20 to-transparent"></div>
        </div>
    </section>

    {{-- MARQUEE --}}
    <div class="border-y border-white/5 py-5 overflow-hidden bg-white/[0.02]">
        <div class="marquee-track">
            @foreach(range(1, 2) as $i)
                <div class="flex items-center gap-12 px-6">
                    @foreach(['Listing Announcements', 'Lead Nurture Sequences', 'Open House Invites', 'Market Reports', 'Sold Announcements', 'Client Newsletters', 'Drip Campaigns', 'Price Reduction Alerts'] as $item)
                        <span class="text-white/25 text-sm font-light tracking-widest uppercase">{{ $item }}</span>
                        <span class="text-gold/40 text-lg">✦</span>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>

    {{-- STATS --}}
    <section class="py-24 lg:py-32 border-b border-white/5">
        <div class="max-w-7xl mx-auto px-6 lg:px-12">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-px bg-white/5">
                @php
                    $stats = [
                        ['12,000+', 'Real estate professionals'],
                        ['2M+', 'Emails delivered monthly'],
                        ['38%', 'Average open rate'],
                        ['4.9★', 'Customer satisfaction'],
                    ];
                @endphp
                @foreach($stats as $stat)
                    <div class="reveal bg-ink p-10 lg:p-14 flex flex-col gap-3 hover:bg-white/[0.03] transition-colors duration-300">
                        <span class="font-display text-4xl lg:text-5xl font-light text-gradient">{{ $stat[0] }}</span>
                        <span class="text-white/40 text-sm font-light leading-relaxed">{{ $stat[1] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- STORY --}}
    <section id="story" class="py-24 lg:py-36">
        <div class="max-w-7xl mx-auto px-6 lg:px-12">
            <div class="grid lg:grid-cols-2 gap-16 lg:gap-24 items-center">
                <div class="reveal">
                    <p class="text-gold text-xs font-medium tracking-[0.2em] uppercase mb-6">Our story</p>
                    <h2 class="font-display font-light text-4xl lg:text-5xl leading-tight mb-8">
                        Born from frustration.<br>
                        <em class="text-white/40 not-italic">Built with purpose.</em>
                    </h2>
                    <div class="space-y-5 text-white/50 font-light leading-relaxed text-base lg:text-lg">
                        <p>In 2021, our founders were running a boutique brokerage in Austin. They tried every email tool on the market — and every single one was built for e-commerce, not real estate.</p>
                        <p>Generic templates. No listing integrations. Zero understanding of the agent workflow. So they built their own.</p>
                        <p>Today, RealtyEmails powers over 12,000 agents and teams across the US — helping them send emails that look like they came from a luxury brand, not a bulk mailer.</p>
                    </div>
                    <div class="mt-10 flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-gold/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div>
                            <p class="text-white text-sm font-medium">James & Mara Chen</p>
                            <p class="text-white/30 text-xs font-light">Co-Founders, RealtyEmails</p>
                        </div>
                    </div>
                </div>
                <div class="reveal relative" style="transition-delay: 0.2s">
                    <div class="relative rounded-2xl overflow-hidden bg-white/[0.03] border border-white/10 p-8 glow-gold">
                        <div class="bg-white/5 rounded-xl border border-white/10 p-5 mb-4">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-8 h-8 rounded-full bg-gold/20 flex items-center justify-center text-gold text-xs font-bold">RE</div>
                                <div>
                                    <p class="text-white text-xs font-medium">RealtyEmails Team</p>
                                    <p class="text-white/30 text-xs">to: sarah@example.com</p>
                                </div>
                                <span class="ml-auto text-xs text-gold bg-gold/10 px-2 py-0.5 rounded-full">Just listed</span>
                            </div>
                            <div class="h-2 bg-white/10 rounded w-3/4 mb-2"></div>
                            <div class="h-2 bg-white/10 rounded w-full mb-2"></div>
                            <div class="h-2 bg-white/10 rounded w-2/3 mb-4"></div>
                            <div class="bg-gold/10 border border-gold/20 rounded-lg p-3 flex gap-3 items-center">
                                <div class="w-12 h-12 rounded-md bg-gold/20 flex-shrink-0"></div>
                                <div class="flex-1">
                                    <div class="h-2 bg-gold/30 rounded w-full mb-1.5"></div>
                                    <div class="h-2 bg-gold/20 rounded w-2/3"></div>
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-3">
                            @foreach([['42%', 'Open rate'], ['8.1%', 'Click rate'], ['3x', 'More replies']] as $m)
                                <div class="bg-white/[0.03] rounded-lg p-3 text-center border border-white/5">
                                    <p class="font-display text-xl font-light text-gradient">{{ $m[0] }}</p>
                                    <p class="text-white/30 text-xs mt-1">{{ $m[1] }}</p>
                                </div>
                            @endforeach
                        </div>
                        <div class="absolute top-0 right-0 w-32 h-32 bg-gold/5 rounded-full blur-3xl pointer-events-none"></div>
                    </div>
                    <div class="absolute -bottom-5 -left-5 bg-ink border border-white/10 rounded-xl px-4 py-3 flex items-center gap-3 shadow-2xl">
                        <div class="w-8 h-8 rounded-full bg-green-500/20 flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        </div>
                        <div>
                            <p class="text-white text-xs font-medium">Email delivered</p>
                            <p class="text-white/30 text-xs">2 seconds ago · opened</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- FEATURES --}}
    <section class="py-24 lg:py-32 bg-white/[0.02] border-y border-white/5">
        <div class="max-w-7xl mx-auto px-6 lg:px-12">
            <div class="max-w-2xl mb-16 reveal">
                <p class="text-gold text-xs font-medium tracking-[0.2em] uppercase mb-5">What we do</p>
                <h2 class="font-display font-light text-4xl lg:text-5xl leading-tight">
                    Everything an agent needs.<br>
                    <span class="text-white/30">Nothing they don't.</span>
                </h2>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
                @php
                    $features = [
                        ['icon' => 'M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0L9.75 14.5', 'title' => 'Listing templates', 'desc' => 'Just-listed, open house, price drop, and sold announcements — each designed to look like they came from a top-tier brokerage.', 'tag' => '150+ templates'],
                        ['icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'title' => 'Smart drip campaigns', 'desc' => 'Nurture cold leads with pre-built sequences that trigger based on behavior — from first inquiry through closing day.', 'tag' => 'Fully automated'],
                        ['icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'title' => 'Sphere of influence', 'desc' => 'Monthly newsletters and market reports to keep your past clients, friends, and family remembering your name.', 'tag' => 'Referral engine'],
                        ['icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'title' => 'Deep analytics', 'desc' => 'Know exactly who opened, clicked, and forwarded — so you prioritize follow-ups with precision instead of guesswork.', 'tag' => 'Real-time'],
                        ['icon' => 'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4', 'title' => 'MLS integration', 'desc' => 'Pull listing data directly into your templates. Address, photos, price, features — auto-populated in seconds.', 'tag' => 'Coming soon'],
                        ['icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'title' => 'Deliverability built in', 'desc' => 'Warmed sending infrastructure, SPF/DKIM setup, and reputation monitoring so your emails land in the inbox — every time.', 'tag' => '99.2% delivery'],
                    ];
                @endphp
                @foreach($features as $i => $f)
                    <div class="reveal card-hover group relative bg-white/[0.03] border border-white/[0.08] rounded-2xl p-7 hover:border-gold/30 transition-colors duration-300" style="transition-delay: {{ $i * 0.08 }}s">
                        <div class="w-10 h-10 rounded-xl bg-gold/10 flex items-center justify-center mb-5 group-hover:bg-gold/20 transition-colors duration-300">
                            <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $f['icon'] }}"/>
                            </svg>
                        </div>
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <h3 class="text-white font-medium text-base">{{ $f['title'] }}</h3>
                            <span class="text-xs text-gold/70 bg-gold/5 border border-gold/15 px-2 py-0.5 rounded-full whitespace-nowrap flex-shrink-0">{{ $f['tag'] }}</span>
                        </div>
                        <p class="text-white/40 font-light text-sm leading-relaxed">{{ $f['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- TESTIMONIAL --}}
    <section class="py-24 lg:py-36 relative overflow-hidden">
        <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
            <div class="w-[500px] h-[500px] rounded-full bg-gold/4 blur-[100px]"></div>
        </div>
        <div class="max-w-4xl mx-auto px-6 lg:px-12 text-center reveal">
            <svg class="w-8 h-8 text-gold/40 mx-auto mb-8" fill="currentColor" viewBox="0 0 24 24">
                <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
            </svg>
            <p class="font-display font-light text-3xl md:text-4xl lg:text-5xl leading-tight text-white/90 mb-10">
                "I went from sending embarrassing Word-doc emails to having the most polished outreach in my market. My listing inquiries doubled in 90 days."
            </p>
            <div class="flex items-center justify-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gold/20 flex items-center justify-center text-gold text-sm font-bold">KL</div>
                <div class="text-left">
                    <p class="text-white text-sm font-medium">Kristin Lopez</p>
                    <p class="text-white/30 text-xs font-light">Realtor, Compass · Miami, FL</p>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="py-24 lg:py-32 border-t border-white/5">
        <div class="max-w-7xl mx-auto px-6 lg:px-12">
            <div class="relative rounded-3xl overflow-hidden bg-gradient-to-br from-gold/10 via-gold/5 to-transparent border border-gold/20 p-12 lg:p-20 text-center reveal glow-gold">
                <div class="absolute inset-0 hero-grid opacity-30 pointer-events-none"></div>
                <p class="text-gold text-xs font-medium tracking-[0.2em] uppercase mb-6">Ready to stand out?</p>
                <h2 class="font-display font-light text-4xl lg:text-6xl leading-tight mb-6">
                    Start sending emails<br>worth opening.
                </h2>
                <p class="text-white/40 font-light text-lg max-w-md mx-auto mb-10">
                    Join 12,000+ agents using RealtyEmails to win more listings and close more deals.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-2 px-10 py-4 rounded-full bg-gold text-ink font-medium hover:bg-gold-light transition-all duration-200">
                        Start free — no credit card
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                    <a href="{{ route('demo') }}" class="inline-flex items-center justify-center gap-2 px-10 py-4 rounded-full border border-white/15 text-white/60 hover:text-white hover:border-white/25 transition-all duration-200 font-light">
                        Book a demo
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- FOOTER --}}
    <footer class="border-t border-white/5 py-12">
        <div class="max-w-7xl mx-auto px-6 lg:px-12 flex flex-col md:flex-row items-center justify-between gap-6">
            <span class="font-display text-lg font-light">Realty<span class="text-gradient font-semibold">Emails</span></span>
            <p class="text-white/20 text-sm font-light">© {{ date('Y') }} RealtyEmails. All rights reserved.</p>
            <div class="flex items-center gap-6 text-sm text-white/30 font-light">
                <a href="#" class="hover:text-white/60 transition-colors">Privacy</a>
                <a href="#" class="hover:text-white/60 transition-colors">Terms</a>
                <a href="#" class="hover:text-white/60 transition-colors">Contact</a>
            </div>
        </div>
    </footer>

    <script>
        const reveals = document.querySelectorAll('.reveal');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) entry.target.classList.add('visible');
            });
        }, { threshold: 0.1 });
        reveals.forEach(el => observer.observe(el));
    </script>

</body>
</html>
      </div>
    </div>
  </main>
  @include('public.layout.footer')
</body>
</html>