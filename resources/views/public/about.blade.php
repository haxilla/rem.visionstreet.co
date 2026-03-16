@include('public.layout.head')

<body data-section="admin" 
class="linkcheck relative bg-white min-h-screen font-sans text-gray-800 postgres">

  @include('public.layout.nav')

  <main class="transition-all duration-300 min-h-screen pt-24 relative"
  :class="collapsed ? 'ml-20' : 'ml-64'">
    <div class="ml-8 mr-8 lg:ml-10 lg:mr-10">
      <div class="pageswap p-6 w-full">
       {{-- HERO --}}
<div class="grid lg:grid-cols-2 gap-8 mb-8 items-center">

    {{-- Left: copy --}}
    <div>
        <p class="text-blue-600 text-xs font-semibold tracking-[0.2em] uppercase mb-4">About RealtyEmails</p>
        <h1 class="font-serif text-4xl lg:text-5xl font-normal leading-tight text-gray-900 mb-5">
            Helping Agents Market<br>
            <em>Every Listing</em> Like a Pro.
        </h1>
        <p class="text-gray-500 text-base font-light leading-relaxed max-w-md mb-8">
            We build professional real estate e-flyers and email marketing tools that help agents in Arizona and Nevada get more exposure on every listing — fast, affordable, and easy.
        </p>
        <div class="flex flex-wrap gap-3">
            <a href="#" class="inline-flex items-center px-6 py-2.5 rounded-full bg-blue-900 text-white text-sm font-medium hover:bg-blue-800 transition-colors">
                Create FREE Flyer
            </a>
            <a href="#about-story" class="inline-flex items-center px-6 py-2.5 rounded-full border border-gray-300 text-gray-600 text-sm font-light hover:bg-gray-50 transition-colors">
                Our Story ↓
            </a>
        </div>
    </div>

    {{-- Right: image with overlaid stats --}}
    <div class="relative rounded-xl overflow-hidden shadow-md h-[300px]">
        <img
            src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=900&q=80&auto=format&fit=crop"
            alt="Real estate professional"
            class="absolute inset-0 w-full h-full object-cover"
        >
        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/10 to-transparent"></div>
        <div class="absolute bottom-5 left-5 right-5 flex justify-between">
            <div>
                <p class="text-2xl font-semibold text-white">12,000+</p>
                <p class="text-white/70 text-xs">Agents & Brokers</p>
            </div>
            <div class="text-right">
                <p class="text-2xl font-semibold text-white">2M+</p>
                <p class="text-white/70 text-xs">Emails Sent Monthly</p>
            </div>
        </div>
    </div>
</div>

{{-- STATS --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    @php
        $stats = [
            ['12,000+', 'Agents & Brokers'],
            ['2M+',     'Emails Delivered'],
            ['38%',     'Avg. Open Rate'],
            ['4.9 ★',   'Customer Rating'],
        ];
    @endphp
    @foreach($stats as $s)
        <div class="border border-gray-200 rounded-xl p-6 text-center bg-white">
            <p class="text-3xl font-light text-gray-900 mb-1">{{ $s[0] }}</p>
            <p class="text-gray-400 text-xs tracking-wide">{{ $s[1] }}</p>
        </div>
    @endforeach
</div>

{{-- STORY --}}
<div id="about-story" class="border border-gray-200 rounded-xl bg-white shadow-sm p-8 lg:p-10 mb-8">
    <div class="grid lg:grid-cols-2 gap-10 items-center">

        {{-- Image collage --}}
        <div class="grid grid-cols-2 gap-3">
            <div class="col-span-2 rounded-lg overflow-hidden h-[200px]">
                <img
                    src="https://images.unsplash.com/photo-1600880292203-757bb62b4baf?w=800&q=80&auto=format&fit=crop"
                    alt="Agent meeting"
                    class="w-full h-full object-cover"
                >
            </div>
            <div class="rounded-lg overflow-hidden h-[130px]">
                <img
                    src="https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=500&q=80&auto=format&fit=crop"
                    alt="Home exterior"
                    class="w-full h-full object-cover"
                >
            </div>
            <div class="rounded-lg overflow-hidden h-[130px]">
                <img
                    src="https://images.unsplash.com/photo-1512917774080-9991f1c4c750?w=500&q=80&auto=format&fit=crop"
                    alt="Luxury home"
                    class="w-full h-full object-cover"
                >
            </div>
        </div>

        {{-- Copy --}}
        <div>
            <p class="text-blue-600 text-xs font-semibold tracking-[0.2em] uppercase mb-3">Our Story</p>
            <h2 class="font-serif text-3xl font-normal leading-tight text-gray-900 mb-5">
                Built by Agents,<br><em>for Agents.</em>
            </h2>
            <div class="space-y-3 text-gray-500 font-light leading-relaxed text-sm">
                <p>RealtyEmails was founded with a single mission: give real estate agents a fast, professional way to market their listings beyond just the MLS.</p>
                <p>We started by sending listing e-flyers to thousands of local agents in Arizona and Nevada. Today we've grown into a full marketing platform — but our roots are firmly planted in helping individual agents compete and win.</p>
                <p>No bloated software. No complicated setup. Just professional tools that work the moment you need them.</p>
            </div>
            <div class="mt-6 pt-5 border-t border-gray-100 flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 text-xs font-semibold flex-shrink-0">RE</div>
                <div>
                    <p class="text-gray-800 text-sm font-medium">The RealtyEmails Team</p>
                    <p class="text-gray-400 text-xs">Serving AZ &amp; NV agents</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- FEATURES --}}
<div class="mb-8">
    <p class="text-blue-600 text-xs font-semibold tracking-[0.2em] uppercase mb-2">What We Do</p>
    <h2 class="font-serif text-3xl font-normal text-gray-900 leading-tight mb-6">
        Everything You Need to <em>Get Your Listing Noticed.</em>
    </h2>
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
        @php
            $features = [
                ['icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
                 'title' => 'Email Blast',
                 'desc'  => 'Reach thousands of interested local RE agents in AZ & NV instantly with your listing.'],
                ['icon' => 'M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z',
                 'title' => 'Social Sharing',
                 'desc'  => 'Easily post to Facebook, Twitter, and other social media sites with a single click.'],
                ['icon' => 'M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z',
                 'title' => 'Print Brochures',
                 'desc'  => 'Print color brochures with a custom link to your online flyer — perfect for open houses.'],
                ['icon' => 'M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9',
                 'title' => 'Listing Website',
                 'desc'  => 'Every flyer comes with a free property website — your listing gets its own professional URL.'],
                ['icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                 'title' => 'Analytics',
                 'desc'  => 'Track opens, clicks, and views so you always know which leads are most engaged.'],
                ['icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                 'title' => 'Launch in Minutes',
                 'desc'  => 'No complicated setup. Create a professional flyer in under 5 minutes — no credit card required.'],
            ];
        @endphp
        @foreach($features as $f)
            <div class="bg-white border border-gray-200 rounded-xl p-6 hover:shadow-md transition-shadow">
                <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center mb-4">
                    <svg class="w-4 h-4 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $f['icon'] }}"/>
                    </svg>
                </div>
                <h3 class="text-gray-800 font-medium text-sm mb-1.5">{{ $f['title'] }}</h3>
                <p class="text-gray-400 font-light text-sm leading-relaxed">{{ $f['desc'] }}</p>
            </div>
        @endforeach
    </div>
</div>

{{-- TESTIMONIAL --}}
<div class="border border-gray-200 rounded-xl overflow-hidden bg-white shadow-sm mb-8">
    <div class="grid lg:grid-cols-2 gap-0">
        <div class="relative h-56 lg:h-auto min-h-[260px]">
            <img
                src="https://images.unsplash.com/photo-1551836022-d5d88e9218df?w=800&q=80&auto=format&fit=crop"
                alt="Happy real estate agent"
                class="absolute inset-0 w-full h-full object-cover"
            >
        </div>
        <div class="p-8 lg:p-10 flex flex-col justify-center">
            <svg class="w-7 h-7 text-gray-300 mb-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
            </svg>
            <p class="font-serif text-xl lg:text-2xl font-normal leading-relaxed text-gray-800 mb-6">
                "I went from embarrassing Word-doc emails to having the most polished listing outreach in my market. My inquiries doubled in 90 days."
            </p>
            <div class="flex items-center gap-3 border-t border-gray-100 pt-5">
                <div class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 text-xs font-semibold flex-shrink-0">KL</div>
                <div>
                    <p class="text-gray-800 text-sm font-medium">Kristin Lopez</p>
                    <p class="text-gray-400 text-xs">Realtor, Compass · Phoenix, AZ</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- CTA --}}
<div class="bg-blue-50 border border-blue-100 rounded-xl p-10 lg:p-12 text-center">
    <p class="text-blue-600 text-xs font-semibold tracking-[0.2em] uppercase mb-3">Get Started Today</p>
    <h2 class="font-serif text-3xl font-normal text-gray-900 leading-tight mb-3">
        Ready to Maximize Exposure<br><em>On Your Listing?</em>
    </h2>
    <p class="text-gray-500 font-light text-sm max-w-md mx-auto mb-7">
        Create a professional real estate e-flyer with a free website for you and your property. No credit card required. Launch in minutes.
    </p>
    <div class="flex flex-col sm:flex-row gap-3 justify-center">
        <a href="#" class="inline-flex items-center justify-center px-8 py-2.5 rounded-full bg-blue-900 text-white text-sm font-medium hover:bg-blue-800 transition-colors">
            Create FREE Flyer
        </a>
        <a href="#" class="inline-flex items-center justify-center px-8 py-2.5 rounded-full border border-blue-200 text-blue-800 text-sm font-light hover:bg-white transition-colors">
            View Pricing
        </a>
    </div>
    <p class="text-gray-400 text-xs mt-4">No credit card required · Launch in minutes</p>
</div>
      </div>
    </div>
  </main>
  @include('public.layout.footer')
</body>
</html>