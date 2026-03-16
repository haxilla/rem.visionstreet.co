@include('public.layout.head')

<body data-section="admin" 
class="linkcheck relative bg-white min-h-screen font-sans text-gray-800 postgres">

  @include('public.layout.nav')

  <main class="transition-all duration-300 min-h-screen pt-24 relative"
  :class="collapsed ? 'ml-20' : 'ml-64'">
    <div class="ml-8 mr-8 lg:ml-10 lg:mr-10">
      <div class="pageswap p-6 w-full">
      <style>
    .about-page { font-family: inherit; }
    .about-page .hero-img { object-position: center 30%; }
    .about-page .clip-diagonal {
        clip-path: polygon(0 0, 100% 0, 100% 88%, 0 100%);
    }
    .about-page .stat-hover:hover { background: #1e3560; color: white; transform: translateY(-3px); }
    .about-page .stat-hover:hover p { color: white !important; }
    .about-page .stat-hover { transition: all 0.25s ease; }
    .about-page .feature-card:hover .feature-icon { background: #1e3560; }
    .about-page .feature-card:hover .feature-icon svg { color: white; }
    .about-page .feature-icon { transition: background 0.2s; }
    .about-page .feature-icon svg { transition: color 0.2s; }
    .about-page .img-zoom:hover img { transform: scale(1.05); }
    .about-page .img-zoom img { transition: transform 0.5s ease; }
</style>

<div class="about-page max-w-5xl mx-auto space-y-6 pb-8">

    {{-- ═══ HERO ═══ --}}
    <div class="relative rounded-2xl overflow-hidden bg-[#1e3560] min-h-[420px] flex items-end">

        {{-- Full-bleed background photo --}}
        <img
            src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=1200&q=85&auto=format&fit=crop"
            alt="City skyline"
            class="absolute inset-0 w-full h-full object-cover hero-img opacity-30"
        >

        {{-- Content --}}
        <div class="relative z-10 grid lg:grid-cols-2 w-full items-end">

            {{-- Big headline --}}
            <div class="p-10 lg:p-14 lg:pb-14">
                <span class="inline-block text-blue-300 text-[10px] font-bold tracking-[0.25em] uppercase border border-blue-400/40 rounded-full px-3 py-1 mb-6">About RealtyEmails</span>
                <h1 class="font-serif text-5xl lg:text-6xl font-normal leading-[1.08] text-white mb-6">
                    The #1 Listing<br>Email Platform<br>for <em class="text-blue-200">AZ & NV</em> Agents.
                </h1>
                <p class="text-white/55 text-sm font-light leading-relaxed max-w-xs mb-8">
                    Fast, professional e-flyers and email tools that get your listing in front of thousands of local agents — in minutes.
                </p>
                <div class="flex flex-wrap gap-3">
                    <a href="#" class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-white text-[#1e3560] text-sm font-bold hover:bg-blue-50 transition-colors shadow-lg">
                        Create FREE Flyer
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                    <a href="#about-story" class="inline-flex items-center px-6 py-3 rounded-full border border-white/20 text-white/75 text-sm hover:bg-white/10 transition-colors">
                        Our Story ↓
                    </a>
                </div>
            </div>

            {{-- Floating stat cards --}}
            <div class="hidden lg:flex flex-col gap-3 p-10 pb-14 items-end">
                <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl px-6 py-5 text-right">
                    <p class="font-serif text-4xl text-white font-light">12,000<span class="text-blue-300">+</span></p>
                    <p class="text-white/50 text-xs mt-1">Agents & Brokers Trust Us</p>
                </div>
                <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl px-6 py-5 text-right">
                    <p class="font-serif text-4xl text-white font-light">38<span class="text-blue-300">%</span></p>
                    <p class="text-white/50 text-xs mt-1">Average Email Open Rate</p>
                </div>
                <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl px-6 py-5 text-right">
                    <p class="font-serif text-4xl text-white font-light">2M<span class="text-blue-300">+</span></p>
                    <p class="text-white/50 text-xs mt-1">Emails Delivered Monthly</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ STORY — full-bleed photo left ═══ --}}
    <div id="about-story" class="rounded-2xl overflow-hidden border border-gray-200 bg-white shadow-sm">
        <div class="grid lg:grid-cols-2 min-h-[460px]">

            {{-- Left: stacked photos --}}
            <div class="grid grid-rows-2 min-h-[320px] lg:min-h-full">
                <div class="overflow-hidden img-zoom">
                    <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=700&q=85&auto=format&fit=crop"
                         alt="Luxury home" class="w-full h-full object-cover">
                </div>
                <div class="grid grid-cols-2">
                    <div class="overflow-hidden border-r border-t border-white img-zoom">
                        <img src="https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=400&q=80&auto=format&fit=crop"
                             alt="Modern home" class="w-full h-full object-cover">
                    </div>
                    <div class="overflow-hidden border-t border-white img-zoom">
                        <img src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=400&q=80&auto=format&fit=crop"
                             alt="Beautiful property" class="w-full h-full object-cover">
                    </div>
                </div>
            </div>

            {{-- Right: copy --}}
            <div class="p-10 lg:p-12 flex flex-col justify-center">
                <p class="text-blue-600 text-[10px] font-bold tracking-[0.25em] uppercase mb-4">Our Story</p>
                <h2 class="font-serif text-4xl font-normal leading-tight text-gray-900 mb-6">
                    Built by Agents,<br><em>for Agents.</em>
                </h2>
                <div class="space-y-4 text-gray-500 font-light leading-relaxed text-sm">
                    <p>RealtyEmails was founded with a single mission: give real estate agents a fast, professional way to market their listings beyond just the MLS.</p>
                    <p>We started by sending listing e-flyers to thousands of local agents in Arizona and Nevada. Today we've grown into a full marketing platform — but our roots are firmly planted in helping individual agents compete and win.</p>
                    <p>No bloated software. No complicated setup. Just professional tools that work the moment you need them.</p>
                </div>

                {{-- Inline mini-stats --}}
                <div class="grid grid-cols-3 gap-3 mt-8 pt-6 border-t border-gray-100">
                    @foreach([['2012','Founded'],['AZ + NV','Markets'],['5 min','Avg. Setup']] as $m)
                        <div class="text-center">
                            <p class="font-serif text-2xl text-[#1e3560] font-normal">{{ $m[0] }}</p>
                            <p class="text-gray-400 text-xs mt-0.5">{{ $m[1] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ WHAT WE DO — dark band ═══ --}}
    <div class="rounded-2xl bg-[#1e3560] overflow-hidden">
        <div class="p-10 lg:p-12">
            <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4 mb-8">
                <div>
                    <p class="text-blue-300 text-[10px] font-bold tracking-[0.25em] uppercase mb-3">What We Do</p>
                    <h2 class="font-serif text-3xl lg:text-4xl font-normal text-white leading-tight">
                        Every tool you need<br>to <em class="text-blue-200">close more deals.</em>
                    </h2>
                </div>
                <a href="#" class="self-start lg:self-auto inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-white/10 border border-white/20 text-white text-sm hover:bg-white/20 transition-colors whitespace-nowrap">
                    See all features →
                </a>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-3">
                @php
                    $features = [
                        ['icon'=>'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z','title'=>'Email Blast','desc'=>'Thousands of interested local RE agents in AZ & NV see your listing the same day.'],
                        ['icon'=>'M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z','title'=>'Social Sharing','desc'=>'One-click sharing to Facebook, Twitter, and all the platforms buyers actually use.'],
                        ['icon'=>'M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z','title'=>'Print Brochures','desc'=>'Color brochures with a QR code linking straight to your property website.'],
                        ['icon'=>'M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9','title'=>'Property Website','desc'=>'Every listing gets its own free website with a professional URL — instantly.'],
                        ['icon'=>'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z','title'=>'Real-Time Analytics','desc'=>'See exactly who opened, clicked, and is ready to act — prioritize the right leads.'],
                        ['icon'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z','title'=>'Ready in 5 Minutes','desc'=>'No training. No setup fees. Launch a polished flyer faster than writing a single email.'],
                    ];
                @endphp
                @foreach($features as $f)
                    <div class="feature-card bg-white/5 border border-white/10 rounded-xl p-5 hover:bg-white/10 transition-all group cursor-default">
                        <div class="feature-icon w-8 h-8 rounded-lg bg-blue-500/20 flex items-center justify-center mb-4">
                            <svg class="w-4 h-4 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $f['icon'] }}"/>
                            </svg>
                        </div>
                        <h3 class="text-white font-medium text-sm mb-1.5">{{ $f['title'] }}</h3>
                        <p class="text-white/40 font-light text-xs leading-relaxed">{{ $f['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ═══ TESTIMONIAL — photo background ═══ --}}
    <div class="relative rounded-2xl overflow-hidden min-h-[320px] flex items-center">
        <img
            src="https://images.unsplash.com/photo-1560472355-536de3962603?w=1200&q=85&auto=format&fit=crop&crop=center"
            alt="Real estate agent"
            class="absolute inset-0 w-full h-full object-cover"
        >
        <div class="absolute inset-0 bg-gradient-to-r from-[#1e3560]/95 via-[#1e3560]/80 to-[#1e3560]/30"></div>
        <div class="relative z-10 p-10 lg:p-14 max-w-2xl">
            <div class="flex gap-1 mb-5">
                @for($i=0;$i<5;$i++)
                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                @endfor
            </div>
            <p class="font-serif text-2xl lg:text-3xl font-normal leading-relaxed text-white mb-8">
                "I went from embarrassing Word-doc emails to having the most polished listing outreach in my market. My inquiries doubled in 90 days."
            </p>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-blue-400/30 border border-white/20 flex items-center justify-center text-white text-xs font-bold">KL</div>
                <div>
                    <p class="text-white font-medium text-sm">Kristin Lopez</p>
                    <p class="text-blue-200 text-xs">Realtor, Compass · Phoenix, AZ</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ CTA ═══ --}}
    <div class="relative rounded-2xl overflow-hidden">
        <img
            src="https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=1200&q=85&auto=format&fit=crop"
            alt="Beautiful home"
            class="absolute inset-0 w-full h-full object-cover"
        >
        <div class="absolute inset-0 bg-[#1e3560]/88"></div>
        <div class="relative z-10 p-12 lg:p-16 text-center">
            <p class="text-blue-300 text-[10px] font-bold tracking-[0.25em] uppercase mb-4">Get Started Today</p>
            <h2 class="font-serif text-4xl lg:text-5xl font-normal text-white leading-tight mb-4">
                Maximize Exposure<br>On <em class="text-blue-200">Your Next Listing.</em>
            </h2>
            <p class="text-white/50 font-light text-sm max-w-sm mx-auto mb-8">
                Professional e-flyers with a free property website. No credit card. Launch in minutes.
            </p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="#" class="inline-flex items-center justify-center gap-2 px-8 py-3 rounded-full bg-white text-[#1e3560] text-sm font-bold hover:bg-blue-50 transition-colors shadow-lg">
                    Create FREE Flyer
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
                <a href="#" class="inline-flex items-center justify-center px-8 py-3 rounded-full border border-white/20 text-white text-sm hover:bg-white/10 transition-colors">
                    View Pricing
                </a>
            </div>
            <p class="text-white/30 text-xs mt-5">No credit card required · Launch in minutes</p>
        </div>
    </div>

</div>
      </div>
    </div>
  </main>
  @include('public.layout.footer')
</body>
</html>