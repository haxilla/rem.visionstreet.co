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
                        gold: '#C9A84C',
                        'gold-light': '#E8D5A3',
                    },
                    animation: {
                        'fade-up': 'fadeUp 0.9s ease forwards',
                        'fade-in': 'fadeIn 1s ease forwards',
                        'float': 'float 6s ease-in-out infinite',
                    },
                    keyframes: {
                        fadeUp: { '0%': { opacity: '0', transform: 'translateY(32px)' }, '100%': { opacity: '1', transform: 'translateY(0)' } },
                        fadeIn: { '0%': { opacity: '0' }, '100%': { opacity: '1' } },
                        float: { '0%,100%': { transform: 'translateY(0)' }, '50%': { transform: 'translateY(-10px)' } },
                    },
                }
            }
        }
    </script>
    <style>
        .grain { position:fixed;inset:0;pointer-events:none;z-index:100;opacity:.03;background-image:url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E"); }
        .hero-grid { background-image: linear-gradient(rgba(201,168,76,.06) 1px,transparent 1px),linear-gradient(90deg,rgba(201,168,76,.06) 1px,transparent 1px); background-size:80px 80px; }
        .text-gradient { background:linear-gradient(135deg,#C9A84C 0%,#E8D5A3 50%,#C9A84C 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text; }
        .reveal { opacity:0;transform:translateY(28px);transition:opacity .8s ease,transform .8s ease; }
        .reveal.visible { opacity:1;transform:translateY(0); }
        .img-hover { transition:transform .6s ease; }
        .img-hover:hover { transform:scale(1.04); }
        .marquee-track { display:flex;animation:marquee 22s linear infinite;white-space:nowrap; }
        @keyframes marquee { from{transform:translateX(0)} to{transform:translateX(-50%)} }
        .card-lift { transition:transform .3s ease,box-shadow .3s ease; }
        .card-lift:hover { transform:translateY(-5px);box-shadow:0 20px 40px rgba(0,0,0,.3); }
        .glow { box-shadow:0 0 80px rgba(201,168,76,.12); }
        /* Parallax helper */
        .parallax-img { transition: transform 0s; will-change: transform; }
    </style>
</head>
<body class="bg-ink text-white font-sans antialiased overflow-x-hidden">

<div class="grain"></div>

{{-- ─── NAV ─── --}}
<nav class="fixed top-0 left-0 right-0 z-50 backdrop-blur-md bg-ink/80 border-b border-white/5">
    <div class="max-w-7xl mx-auto px-6 lg:px-12 flex items-center justify-between h-16">
        <a href="/" class="font-display text-xl font-light">Realty<span class="text-gradient font-semibold">Emails</span></a>
        <div class="hidden md:flex items-center gap-8 text-sm text-white/50 font-light">
            <a href="#" class="hover:text-white transition-colors">Features</a>
            <a href="#" class="hover:text-white transition-colors">Templates</a>
            <a href="#" class="hover:text-white transition-colors">Pricing</a>
            <a href="#" class="hover:text-white transition-colors">About</a>
        </div>
        <a href="#" class="hidden md:inline-flex items-center gap-2 text-sm font-medium px-5 py-2.5 rounded-full bg-gold text-ink hover:bg-gold-light transition-colors">
            Start free
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
        </a>
    </div>
</nav>

{{-- ─── HERO ─── --}}
<section class="relative min-h-screen flex items-center hero-grid pt-16 overflow-hidden">

    {{-- Ambient glow --}}
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute top-1/4 left-1/4 w-[600px] h-[600px] rounded-full bg-gold/5 blur-[130px]"></div>
        <div class="absolute bottom-0 right-0 w-[400px] h-[400px] rounded-full bg-gold/4 blur-[100px]"></div>
    </div>

    <div class="max-w-7xl mx-auto px-6 lg:px-12 w-full py-20">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">

            {{-- Left: copy --}}
            <div>
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full border border-gold/30 bg-gold/5 text-gold text-xs font-medium tracking-widest uppercase mb-10 animate-fade-in">
                    <span class="w-1.5 h-1.5 rounded-full bg-gold animate-pulse"></span>
                    Trusted by 12,000+ agents
                </div>
                <h1 class="font-display font-light text-5xl md:text-6xl lg:text-7xl leading-[1.07] tracking-tight mb-8 animate-fade-up">
                    Email marketing<br>
                    <em class="text-gradient not-italic">built for agents</em><br>
                    <span class="text-white/30">who close deals.</span>
                </h1>
                <p class="text-white/50 text-lg font-light leading-relaxed max-w-md mb-10 animate-fade-up" style="animation-delay:.15s;opacity:0;animation-fill-mode:forwards">
                    Beautiful listing templates. Intelligent drip automation. The platform that makes every agent look like they have a full marketing team behind them.
                </p>
                <div class="flex flex-col sm:flex-row gap-3 animate-fade-up" style="animation-delay:.3s;opacity:0;animation-fill-mode:forwards">
                    <a href="#" class="inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-full bg-gold text-ink font-medium hover:bg-gold-light transition-all text-sm">
                        Get started free
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                    <a href="#story" class="inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-full border border-white/10 text-white/50 hover:text-white hover:border-white/20 transition-all text-sm font-light">
                        Our story ↓
                    </a>
                </div>
            </div>

            {{-- Right: hero image collage --}}
            <div class="relative animate-fade-up" style="animation-delay:.2s;opacity:0;animation-fill-mode:forwards">

                {{-- Main image --}}
                <div class="relative rounded-2xl overflow-hidden h-[420px] border border-white/10">
                    <img
                        src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=900&q=80&auto=format&fit=crop"
                        alt="Real estate agent at work"
                        class="w-full h-full object-cover img-hover"
                    >
                    {{-- Dark overlay at bottom for text legibility --}}
                    <div class="absolute inset-0 bg-gradient-to-t from-ink/80 via-transparent to-transparent"></div>
                    {{-- Overlay stat --}}
                    <div class="absolute bottom-5 left-5 right-5 flex items-end justify-between">
                        <div>
                            <p class="font-display text-3xl font-light text-gradient">38%</p>
                            <p class="text-white/60 text-xs font-light">avg. open rate</p>
                        </div>
                        <div class="text-right">
                            <p class="font-display text-3xl font-light text-gradient">2M+</p>
                            <p class="text-white/60 text-xs font-light">emails/month</p>
                        </div>
                    </div>
                </div>

                {{-- Floating small card: delivered notification --}}
                <div class="absolute -bottom-5 -left-5 bg-ink/95 border border-white/10 rounded-xl px-4 py-3 flex items-center gap-3 shadow-2xl backdrop-blur">
                    <div class="w-8 h-8 rounded-full bg-green-500/20 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    </div>
                    <div>
                        <p class="text-white text-xs font-medium">Email delivered & opened</p>
                        <p class="text-white/30 text-xs">Just listed · 3 Bed / 2 Bath — Austin, TX</p>
                    </div>
                </div>

                {{-- Floating avatar cluster: "agents using it" --}}
                <div class="absolute -top-4 -right-4 bg-ink/90 border border-white/10 rounded-xl px-4 py-3 flex items-center gap-3 shadow-2xl backdrop-blur">
                    <div class="flex -space-x-2">
                        @foreach([
                            'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=60&h=60&q=80&auto=format&fit=crop&crop=face',
                            'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=60&h=60&q=80&auto=format&fit=crop&crop=face',
                            'https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?w=60&h=60&q=80&auto=format&fit=crop&crop=face',
                        ] as $avatar)
                            <img src="{{ $avatar }}" class="w-7 h-7 rounded-full border-2 border-ink object-cover" alt="Agent">
                        @endforeach
                    </div>
                    <div>
                        <p class="text-white text-xs font-medium">12,000+ agents</p>
                        <div class="flex items-center gap-1">
                            <span class="text-gold text-xs">★★★★★</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─── MARQUEE ─── --}}
<div class="border-y border-white/5 py-4 overflow-hidden bg-white/[0.015]">
    <div class="marquee-track">
        @foreach(range(1,2) as $i)
            <div class="flex items-center gap-10 px-5">
                @foreach(['Listing Announcements','Lead Nurture Sequences','Open House Invites','Market Reports','Sold Announcements','Client Newsletters','Drip Campaigns','Price Reduction Alerts'] as $item)
                    <span class="text-white/20 text-xs font-light tracking-[0.15em] uppercase">{{ $item }}</span>
                    <span class="text-gold/30">✦</span>
                @endforeach
            </div>
        @endforeach
    </div>
</div>

{{-- ─── STATS ─── --}}
<section class="border-b border-white/5">
    <div class="max-w-7xl mx-auto px-6 lg:px-12">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-px bg-white/[0.04]">
            @php $stats = [['12,000+','Agents & brokers'],['2M+','Emails sent monthly'],['38%','Average open rate'],['4.9★','Avg. customer rating']]; @endphp
            @foreach($stats as $s)
                <div class="reveal bg-ink px-8 py-12 lg:py-16 hover:bg-white/[0.02] transition-colors">
                    <p class="font-display text-4xl lg:text-5xl font-light text-gradient mb-2">{{ $s[0] }}</p>
                    <p class="text-white/35 text-sm font-light">{{ $s[1] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ─── STORY ─── --}}
<section id="story" class="py-24 lg:py-36 overflow-hidden">
    <div class="max-w-7xl mx-auto px-6 lg:px-12">
        <div class="grid lg:grid-cols-2 gap-16 lg:gap-24 items-center">

            {{-- Images stacked collage --}}
            <div class="reveal relative order-2 lg:order-1" style="transition-delay:.15s">
                <div class="grid grid-cols-2 gap-4">
                    {{-- Tall left image --}}
                    <div class="rounded-2xl overflow-hidden h-[360px] row-span-2 border border-white/8">
                        <img src="https://images.unsplash.com/photo-1600880292203-757bb62b4baf?w=600&q=80&auto=format&fit=crop"
                             alt="Agent showing property"
                             class="w-full h-full object-cover img-hover">
                    </div>
                    {{-- Top right --}}
                    <div class="rounded-2xl overflow-hidden h-[170px] border border-white/8">
                        <img src="https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=600&q=80&auto=format&fit=crop"
                             alt="Modern home exterior"
                             class="w-full h-full object-cover img-hover">
                    </div>
                    {{-- Bottom right --}}
                    <div class="rounded-2xl overflow-hidden h-[170px] border border-white/8">
                        <img src="https://images.unsplash.com/photo-1582407947304-fd86f28f7c9a?w=600&q=80&auto=format&fit=crop"
                             alt="Luxury interior"
                             class="w-full h-full object-cover img-hover">
                    </div>
                </div>
                {{-- Gold accent line --}}
                <div class="absolute -bottom-6 -left-6 w-24 h-24 border border-gold/20 rounded-2xl pointer-events-none"></div>
            </div>

            {{-- Copy --}}
            <div class="reveal order-1 lg:order-2">
                <p class="text-gold text-xs font-medium tracking-[0.2em] uppercase mb-5">Our story</p>
                <h2 class="font-display font-light text-4xl lg:text-5xl leading-tight mb-8">
                    Born from frustration.<br>
                    <em class="text-white/35 not-italic">Built with purpose.</em>
                </h2>
                <div class="space-y-5 text-white/45 font-light leading-relaxed text-base lg:text-lg">
                    <p>In 2021, our founders were running a boutique brokerage in Austin. They tried every email tool on the market — and every single one was built for e-commerce, not real estate.</p>
                    <p>Generic templates. No listing integrations. Zero understanding of the agent workflow. So they built their own.</p>
                    <p>Today, RealtyEmails powers over 12,000 agents and teams across the US — helping them send emails that look like they came from a luxury brand, not a bulk mailer.</p>
                </div>
                <div class="mt-10 pt-8 border-t border-white/8 flex items-center gap-4">
                    <img src="https://images.unsplash.com/photo-1556157382-97eda2d62296?w=80&h=80&q=80&auto=format&fit=crop&crop=face"
                         alt="Co-founder"
                         class="w-11 h-11 rounded-full object-cover border border-white/10">
                    <div>
                        <p class="text-white text-sm font-medium">James & Mara Chen</p>
                        <p class="text-white/30 text-xs font-light">Co-Founders, RealtyEmails</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─── FEATURES ─── --}}
<section class="py-24 lg:py-32 bg-white/[0.015] border-y border-white/5">
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
                ['icon'=>'M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0L9.75 14.5','title'=>'Listing templates','desc'=>'Just-listed, open house, price drop, and sold announcements — designed to look like they came from a top-tier brokerage.','tag'=>'150+ templates'],
                ['icon'=>'M13 10V3L4 14h7v7l9-11h-7z','title'=>'Smart drip campaigns','desc'=>'Nurture cold leads with sequences that trigger based on behavior — from first inquiry through closing day.','tag'=>'Fully automated'],
                ['icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z','title'=>'Sphere of influence','desc'=>'Monthly newsletters and market reports that keep your past clients, friends, and family remembering your name.','tag'=>'Referral engine'],
                ['icon'=>'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z','title'=>'Deep analytics','desc'=>'Know who opened, clicked, and forwarded — prioritize follow-ups with precision instead of guesswork.','tag'=>'Real-time'],
                ['icon'=>'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4','title'=>'MLS integration','desc'=>'Pull listing data directly into templates. Address, photos, price, features — auto-populated in seconds.','tag'=>'Coming soon'],
                ['icon'=>'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z','title'=>'Deliverability built in','desc'=>'Warmed infrastructure, SPF/DKIM setup, and reputation monitoring so your emails land in the inbox every time.','tag'=>'99.2% delivery'],
            ];
            @endphp
            @foreach($features as $i => $f)
                <div class="reveal card-lift group bg-white/[0.03] border border-white/[0.07] rounded-2xl p-7 hover:border-gold/25 transition-colors" style="transition-delay:{{ $i * 0.07 }}s">
                    <div class="w-10 h-10 rounded-xl bg-gold/10 flex items-center justify-center mb-5 group-hover:bg-gold/20 transition-colors">
                        <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $f['icon'] }}"/>
                        </svg>
                    </div>
                    <div class="flex items-start justify-between gap-2 mb-3">
                        <h3 class="text-white font-medium text-base">{{ $f['title'] }}</h3>
                        <span class="text-xs text-gold/60 bg-gold/5 border border-gold/15 px-2 py-0.5 rounded-full whitespace-nowrap flex-shrink-0">{{ $f['tag'] }}</span>
                    </div>
                    <p class="text-white/35 font-light text-sm leading-relaxed">{{ $f['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ─── TEAM / PHOTO BREAK ─── --}}
<section class="py-24 lg:py-32">
    <div class="max-w-7xl mx-auto px-6 lg:px-12">
        <div class="reveal mb-14">
            <p class="text-gold text-xs font-medium tracking-[0.2em] uppercase mb-5">The team</p>
            <h2 class="font-display font-light text-4xl lg:text-5xl leading-tight max-w-xl">
                Real people who care<br>
                <span class="text-white/30">about your success.</span>
            </h2>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
            @php
            $team = [
                ['name'=>'James Chen','role'=>'Co-Founder & CEO','img'=>'https://images.unsplash.com/photo-1556157382-97eda2d62296?w=500&h=600&q=80&auto=format&fit=crop&crop=face'],
                ['name'=>'Mara Chen','role'=>'Co-Founder & CPO','img'=>'https://images.unsplash.com/photo-1580489944761-15a19d654956?w=500&h=600&q=80&auto=format&fit=crop&crop=face'],
                ['name'=>'David Park','role'=>'Head of Engineering','img'=>'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=500&h=600&q=80&auto=format&fit=crop&crop=face'],
                ['name'=>'Sofia Reyes','role'=>'Head of Customer Success','img'=>'https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?w=500&h=600&q=80&auto=format&fit=crop&crop=face'],
            ];
            @endphp
            @foreach($team as $i => $member)
                <div class="reveal group" style="transition-delay:{{ $i * 0.1 }}s">
                    <div class="rounded-2xl overflow-hidden mb-4 border border-white/8 h-[280px] md:h-[320px]">
                        <img src="{{ $member['img'] }}"
                             alt="{{ $member['name'] }}"
                             class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-500 img-hover">
                    </div>
                    <p class="text-white font-medium text-sm">{{ $member['name'] }}</p>
                    <p class="text-white/35 text-xs font-light mt-0.5">{{ $member['role'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ─── TESTIMONIAL ─── --}}
<section class="py-24 lg:py-32 bg-white/[0.015] border-y border-white/5 overflow-hidden relative">
    <div class="absolute inset-0 pointer-events-none flex items-center justify-center">
        <div class="w-[500px] h-[500px] rounded-full bg-gold/4 blur-[100px]"></div>
    </div>
    <div class="max-w-7xl mx-auto px-6 lg:px-12">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-24 items-center">
            {{-- Photo --}}
            <div class="reveal order-2 lg:order-1">
                <div class="relative rounded-2xl overflow-hidden h-[420px] border border-white/8 glow">
                    <img src="https://images.unsplash.com/photo-1551836022-d5d88e9218df?w=900&q=80&auto=format&fit=crop"
                         alt="Happy real estate agent"
                         class="w-full h-full object-cover img-hover">
                    <div class="absolute inset-0 bg-gradient-to-t from-ink/60 to-transparent"></div>
                </div>
            </div>
            {{-- Quote --}}
            <div class="reveal order-1 lg:order-2" style="transition-delay:.15s">
                <svg class="w-8 h-8 text-gold/40 mb-8" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/></svg>
                <p class="font-display font-light text-3xl lg:text-4xl leading-tight text-white/90 mb-8">
                    "I went from sending embarrassing Word-doc emails to having the most polished outreach in my market. My listing inquiries doubled in 90 days."
                </p>
                <div class="flex items-center gap-4">
                    <img src="https://images.unsplash.com/photo-1551836022-d5d88e9218df?w=80&h=80&q=80&auto=format&fit=crop&crop=face"
                         alt="Kristin Lopez"
                         class="w-12 h-12 rounded-full object-cover border border-white/10">
                    <div>
                        <p class="text-white text-sm font-medium">Kristin Lopez</p>
                        <p class="text-white/30 text-xs font-light">Realtor, Compass · Miami, FL</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─── CTA ─── --}}
<section class="py-24 lg:py-32 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-6 lg:px-12">
        <div class="relative rounded-3xl overflow-hidden border border-gold/20 glow">
            {{-- Background image with overlay --}}
            <div class="absolute inset-0">
                <img src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=1400&q=80&auto=format&fit=crop"
                     alt="Luxury home"
                     class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-ink/85"></div>
                <div class="absolute inset-0 bg-gradient-to-br from-gold/8 to-transparent"></div>
            </div>
            <div class="relative z-10 p-12 lg:p-20 text-center">
                <p class="text-gold text-xs font-medium tracking-[0.2em] uppercase mb-6">Ready to stand out?</p>
                <h2 class="font-display font-light text-4xl lg:text-6xl leading-tight mb-6">
                    Start sending emails<br>worth opening.
                </h2>
                <p class="text-white/40 font-light text-lg max-w-md mx-auto mb-10">
                    Join 12,000+ agents using RealtyEmails to win more listings and close more deals.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="#" class="inline-flex items-center justify-center gap-2 px-10 py-4 rounded-full bg-gold text-ink font-medium hover:bg-gold-light transition-all">
                        Start free — no credit card
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                    <a href="#" class="inline-flex items-center justify-center gap-2 px-10 py-4 rounded-full border border-white/15 text-white/60 hover:text-white hover:border-white/25 transition-all font-light">
                        Book a demo
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─── FOOTER ─── --}}
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
    const observer = new IntersectionObserver(entries => {
        entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('visible'); });
    }, { threshold: 0.08 });
    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
</script>

</body>
</html>