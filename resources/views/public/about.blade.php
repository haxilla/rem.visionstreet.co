<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About — RealtyEmails</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;1,400&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        display: ['Playfair Display', 'serif'],
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        navy: '#1e3a5f',
                        'navy-dark': '#162d4a',
                        'navy-light': '#2a4f7c',
                        'navy-pale': '#eef2f7',
                    },
                }
            }
        }
    </script>
    <style>
        .reveal { opacity:0; transform:translateY(20px); transition:opacity .7s ease, transform .7s ease; }
        .reveal.visible { opacity:1; transform:translateY(0); }
    </style>
</head>
<body class="bg-white text-gray-800 font-sans antialiased">

    {{-- ─── NAV (matching site style) ─── --}}
    <nav class="bg-navy-dark sticky top-0 z-50 shadow-sm">
        <div class="max-w-6xl mx-auto px-6 flex items-center justify-between h-14">
            <a href="/" class="font-display text-white text-xl font-semibold tracking-tight">
                RealtyEm<span class="inline-block relative">a<span class="absolute -top-1 left-0 text-xs text-blue-300">✉</span></span>ils
            </a>
            <div class="hidden md:flex items-center gap-7 text-sm text-white/70 font-light">
                <a href="#" class="hover:text-white transition-colors">Pricing</a>
                <a href="#" class="hover:text-white transition-colors">Search Homes</a>
                <a href="#" class="hover:text-white transition-colors">Free Trial</a>
                <a href="#" class="hover:text-white transition-colors font-medium text-white">About</a>
            </div>
            <div class="flex items-center gap-4">
                <a href="#" class="text-sm text-white/70 hover:text-white transition-colors">Log in</a>
                <a href="#" class="text-sm font-medium px-4 py-1.5 rounded-full border border-white/30 text-white hover:bg-white/10 transition-colors">Sign Up</a>
            </div>
        </div>
    </nav>

    {{-- ─── HERO ─── --}}
    <section class="bg-navy-dark text-white py-20 lg:py-28">
        <div class="max-w-6xl mx-auto px-6">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">
                <div>
                    <p class="text-blue-300 text-xs font-medium tracking-[0.2em] uppercase mb-5">About RealtyEmails</p>
                    <h1 class="font-display text-4xl lg:text-5xl font-normal leading-tight mb-6">
                        Helping Agents Market<br>
                        <em>Every Listing</em> Like a Pro.
                    </h1>
                    <p class="text-white/60 text-base font-light leading-relaxed max-w-md mb-8">
                        We build professional real estate e-flyers and email marketing tools that help agents in Arizona and Nevada get more exposure on every listing — fast, affordable, and easy.
                    </p>
                    <div class="flex flex-wrap gap-3">
                        <a href="#" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-white text-navy-dark text-sm font-medium hover:bg-blue-50 transition-colors">
                            Create FREE Flyer
                        </a>
                        <a href="#story" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full border border-white/25 text-white/80 text-sm font-light hover:bg-white/10 transition-colors">
                            Our Story ↓
                        </a>
                    </div>
                </div>

                {{-- Hero image --}}
                <div class="relative rounded-xl overflow-hidden shadow-2xl h-[340px]">
                    <img
                        src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=800&q=80&auto=format&fit=crop"
                        alt="Real estate professional"
                        class="w-full h-full object-cover"
                    >
                    <div class="absolute inset-0 bg-gradient-to-t from-navy-dark/60 to-transparent"></div>
                    <div class="absolute bottom-5 left-5 right-5 flex justify-between items-end">
                        <div>
                            <p class="font-display text-2xl text-white font-normal">12,000+</p>
                            <p class="text-white/60 text-xs">Agents & Brokers</p>
                        </div>
                        <div class="text-right">
                            <p class="font-display text-2xl text-white font-normal">2M+</p>
                            <p class="text-white/60 text-xs">Emails Sent Monthly</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ─── STATS BAR ─── --}}
    <section class="bg-navy text-white">
        <div class="max-w-6xl mx-auto px-6">
            <div class="grid grid-cols-2 md:grid-cols-4 divide-x divide-white/10">
                @php
                    $stats = [
                        ['12,000+', 'Agents & Brokers'],
                        ['2M+', 'Emails Delivered'],
                        ['38%', 'Avg. Open Rate'],
                        ['4.9 ★', 'Customer Rating'],
                    ];
                @endphp
                @foreach($stats as $s)
                    <div class="py-8 px-8 text-center">
                        <p class="font-display text-3xl font-normal text-white mb-1">{{ $s[0] }}</p>
                        <p class="text-white/50 text-xs font-light tracking-wide">{{ $s[1] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ─── STORY ─── --}}
    <section id="story" class="py-20 lg:py-28 bg-white">
        <div class="max-w-6xl mx-auto px-6">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">

                {{-- Image collage --}}
                <div class="reveal grid grid-cols-2 gap-4">
                    <div class="rounded-lg overflow-hidden h-[280px] shadow-md col-span-2">
                        <img src="https://images.unsplash.com/photo-1600880292203-757bb62b4baf?w=800&q=80&auto=format&fit=crop"
                             alt="Agent showing property" class="w-full h-full object-cover">
                    </div>
                    <div class="rounded-lg overflow-hidden h-[160px] shadow-md">
                        <img src="https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=400&q=80&auto=format&fit=crop"
                             alt="Home exterior" class="w-full h-full object-cover">
                    </div>
                    <div class="rounded-lg overflow-hidden h-[160px] shadow-md">
                        <img src="https://images.unsplash.com/photo-1582407947304-fd86f28f7c9a?w=400&q=80&auto=format&fit=crop"
                             alt="Luxury interior" class="w-full h-full object-cover">
                    </div>
                </div>

                {{-- Copy --}}
                <div class="reveal" style="transition-delay:.15s">
                    <p class="text-navy text-xs font-medium tracking-[0.2em] uppercase mb-4">Our Story</p>
                    <h2 class="font-display text-3xl lg:text-4xl font-normal leading-tight text-navy-dark mb-6">
                        Built by Agents,<br>
                        <em>for Agents.</em>
                    </h2>
                    <div class="space-y-4 text-gray-600 font-light leading-relaxed text-base">
                        <p>RealtyEmails was founded in 2012 with a single mission: give real estate agents a fast, professional way to market their listings beyond just the MLS.</p>
                        <p>We started by sending listing e-flyers to thousands of local agents in Arizona and Nevada. Today we've grown into a full marketing platform — but our roots are still firmly planted in helping individual agents compete and win.</p>
                        <p>No bloated software. No complicated setup. Just professional tools that work the moment you need them.</p>
                    </div>
                    <div class="mt-8 pt-6 border-t border-gray-100 flex items-center gap-4">
                        <img src="https://images.unsplash.com/photo-1556157382-97eda2d62296?w=80&h=80&q=80&auto=format&fit=crop&crop=face"
                             alt="Founder" class="w-10 h-10 rounded-full object-cover border border-gray-200">
                        <div>
                            <p class="text-navy-dark text-sm font-medium">The RealtyEmails Team</p>
                            <p class="text-gray-400 text-xs font-light">Serving AZ & NV since 2012</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ─── WHAT WE DO ─── --}}
    <section class="py-20 lg:py-28 bg-navy-pale">
        <div class="max-w-6xl mx-auto px-6">
            <div class="text-center max-w-xl mx-auto mb-14 reveal">
                <p class="text-navy text-xs font-medium tracking-[0.2em] uppercase mb-4">What We Do</p>
                <h2 class="font-display text-3xl lg:text-4xl font-normal text-navy-dark leading-tight">
                    Everything You Need to<br><em>Get Your Listing Noticed.</em>
                </h2>
                <p class="text-gray-500 font-light text-base mt-4 leading-relaxed">Each flyer includes the tools to email, share, and print — all from one platform.</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @php
                    $features = [
                        ['icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'title' => 'Email Blast', 'desc' => 'Reach thousands of interested local RE agents in AZ & NV instantly with your listing.'],
                        ['icon' => 'M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z', 'title' => 'Social Sharing', 'desc' => 'Easily post to Facebook, Twitter, and other social media sites with a single click.'],
                        ['icon' => 'M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z', 'title' => 'Print Brochures', 'desc' => 'Print color brochures with a custom link to your online flyer — perfect for open houses.'],
                        ['icon' => 'M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9', 'title' => 'Listing Website', 'desc' => 'Every flyer comes with a free property website — your listing gets its own professional URL.'],
                        ['icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'title' => 'Analytics', 'desc' => 'Track opens, clicks, and views so you always know which leads are most engaged.'],
                        ['icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'title' => 'Launch in Minutes', 'desc' => 'No complicated setup. Create a professional flyer in under 5 minutes — no credit card required.'],
                    ];
                @endphp
                @foreach($features as $i => $f)
                    <div class="reveal bg-white rounded-xl p-7 shadow-sm border border-gray-100 hover:shadow-md transition-shadow" style="transition-delay:{{ $i * 0.07 }}s">
                        <div class="w-10 h-10 rounded-lg bg-navy-pale flex items-center justify-center mb-5">
                            <svg class="w-5 h-5 text-navy" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $f['icon'] }}"/>
                            </svg>
                        </div>
                        <h3 class="text-navy-dark font-medium text-sm tracking-wide mb-2">{{ $f['title'] }}</h3>
                        <p class="text-gray-500 font-light text-sm leading-relaxed">{{ $f['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ─── TEAM ─── --}}
    <section class="py-20 lg:py-28 bg-white">
        <div class="max-w-6xl mx-auto px-6">
            <div class="text-center max-w-lg mx-auto mb-14 reveal">
                <p class="text-navy text-xs font-medium tracking-[0.2em] uppercase mb-4">The Team</p>
                <h2 class="font-display text-3xl lg:text-4xl font-normal text-navy-dark leading-tight">
                    Real People, <em>Real Support.</em>
                </h2>
                <p class="text-gray-500 font-light text-base mt-4">We're a small, dedicated team that genuinely cares about your success as an agent.</p>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @php
                    $team = [
                        ['James Chen', 'Co-Founder & CEO', 'https://images.unsplash.com/photo-1556157382-97eda2d62296?w=400&h=480&q=80&auto=format&fit=crop&crop=face'],
                        ['Mara Chen', 'Co-Founder & CPO', 'https://images.unsplash.com/photo-1580489944761-15a19d654956?w=400&h=480&q=80&auto=format&fit=crop&crop=face'],
                        ['David Park', 'Head of Engineering', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=480&q=80&auto=format&fit=crop&crop=face'],
                        ['Sofia Reyes', 'Customer Success', 'https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?w=400&h=480&q=80&auto=format&fit=crop&crop=face'],
                    ];
                @endphp
                @foreach($team as $i => $m)
                    <div class="reveal text-center" style="transition-delay:{{ $i * 0.1 }}s">
                        <div class="rounded-xl overflow-hidden mb-4 shadow-sm border border-gray-100 h-[220px] md:h-[260px]">
                            <img src="{{ $m[2] }}" alt="{{ $m[0] }}" class="w-full h-full object-cover grayscale hover:grayscale-0 transition-all duration-500">
                        </div>
                        <p class="text-navy-dark font-medium text-sm">{{ $m[0] }}</p>
                        <p class="text-gray-400 text-xs font-light mt-0.5">{{ $m[1] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ─── TESTIMONIAL ─── --}}
    <section class="py-20 lg:py-28 bg-navy-dark text-white">
        <div class="max-w-6xl mx-auto px-6">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">
                <div class="reveal order-2 lg:order-1">
                    <div class="rounded-xl overflow-hidden shadow-2xl h-[360px]">
                        <img src="https://images.unsplash.com/photo-1551836022-d5d88e9218df?w=800&q=80&auto=format&fit=crop"
                             alt="Happy real estate agent"
                             class="w-full h-full object-cover">
                    </div>
                </div>
                <div class="reveal order-1 lg:order-2" style="transition-delay:.15s">
                    <svg class="w-8 h-8 text-white/25 mb-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
                    </svg>
                    <p class="font-display text-2xl lg:text-3xl font-normal leading-relaxed text-white/90 mb-8">
                        "I went from embarrassing Word-doc emails to having the most polished listing outreach in my market. My inquiries doubled in 90 days."
                    </p>
                    <div class="flex items-center gap-4 border-t border-white/10 pt-6">
                        <img src="https://images.unsplash.com/photo-1551836022-d5d88e9218df?w=80&h=80&q=80&auto=format&fit=crop&crop=face"
                             alt="Kristin Lopez"
                             class="w-11 h-11 rounded-full object-cover border border-white/20">
                        <div>
                            <p class="text-white text-sm font-medium">Kristin Lopez</p>
                            <p class="text-white/40 text-xs font-light">Realtor, Compass · Phoenix, AZ</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ─── CTA ─── --}}
    <section class="py-20 lg:py-28 bg-white">
        <div class="max-w-6xl mx-auto px-6">
            <div class="reveal bg-navy-pale rounded-2xl p-12 lg:p-16 text-center border border-navy/10">
                <p class="text-navy text-xs font-medium tracking-[0.2em] uppercase mb-4">Get Started Today</p>
                <h2 class="font-display text-3xl lg:text-4xl font-normal text-navy-dark leading-tight mb-4">
                    Ready to Maximize Exposure<br><em>On Your Listing?</em>
                </h2>
                <p class="text-gray-500 font-light text-base max-w-md mx-auto mb-8">
                    Create a professional real estate e-flyer with a free website for you and your property. No credit card required. Launch in minutes.
                </p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="#" class="inline-flex items-center justify-center px-8 py-3 rounded-full bg-navy-dark text-white text-sm font-medium hover:bg-navy-light transition-colors">
                        Create FREE Flyer
                    </a>
                    <a href="#" class="inline-flex items-center justify-center px-8 py-3 rounded-full border border-navy/20 text-navy text-sm font-light hover:bg-white transition-colors">
                        See Pricing
                    </a>
                </div>
                <p class="text-gray-400 text-xs font-light mt-5">No credit card required · Launch in minutes</p>
            </div>
        </div>
    </section>

    {{-- ─── FOOTER ─── --}}
    <footer class="bg-navy-dark border-t border-white/5 py-10">
        <div class="max-w-6xl mx-auto px-6 flex flex-col md:flex-row items-center justify-between gap-5">
            <span class="font-display text-white text-lg font-normal">RealtyEmails</span>
            <p class="text-white/25 text-sm font-light">© {{ date('Y') }} RealtyEmails. All rights reserved.</p>
            <div class="flex items-center gap-6 text-sm text-white/35 font-light">
                <a href="#" class="hover:text-white/70 transition-colors">Privacy</a>
                <a href="#" class="hover:text-white/70 transition-colors">Terms</a>
                <a href="#" class="hover:text-white/70 transition-colors">Contact</a>
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