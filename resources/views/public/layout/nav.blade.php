{{-- NAVBAR (matches hero style) --}}
<header class="relative w-full">
  {{-- background --}}
  <div class="absolute inset-0 bg-gradient-to-r from-[#1b2f63] via-[#223a75] to-[#2a4486]"></div>
  {{-- subtle bottom divider --}}
  <div class="absolute bottom-0 left-0 right-0 h-px bg-white/10"></div>

  <div class="relative mx-auto max-w-screen-2xl px-6 lg:px-10" style="max-width:1600px;">
    <div class="flex h-[72px] items-center justify-between">

      {{-- Logo --}}
      <a href="/" class="flex items-center gap-3">
        <img src="{{ asset('images/realtyemails-logo.png') }}" alt="RealtyEmails" class="h-9 w-auto" />
      </a>

      {{-- Menu --}}
      <nav class="hidden md:flex items-center gap-10 text-[14px] font-medium tracking-[0.06em] text-white/80">
        @php
          $nav = [
            ['label' => 'Features', 'href' => '#features'],
            ['label' => 'Pricing',  'href' => '#pricing'],
            ['label' => 'Examples', 'href' => '#examples'],
            ['label' => 'Support',  'href' => '#support'],
          ];
        @endphp

        @foreach($nav as $item)
          <a
            href="{{ $item['href'] }}"
            class="group relative py-1 transition-colors hover:text-white"
          >
            <span>{{ $item['label'] }}</span>

            {{-- elegant underline --}}
            <span class="pointer-events-none absolute left-1/2 -bottom-1 h-[2px] w-0 -translate-x-1/2 rounded-full bg-white/60 transition-all duration-200 group-hover:w-8"></span>
          </a>
        @endforeach
      </nav>

      {{-- Right --}}
      <div class="flex items-center gap-5">
        {{-- Search --}}
        <button class="rounded-full p-2 text-white/80 hover:text-white hover:bg-white/10 transition" aria-label="Search">
          <i class="ti-search text-[16px]"></i>
        </button>

        <a href="#" class="text-[14px] font-medium text-white/80 hover:text-white transition">
          Log in
        </a>

        <a
          href="#"
          class="inline-flex items-center justify-center rounded-full border border-white/20 bg-white/10 px-4 py-2 text-[13px] font-semibold text-white shadow-sm ring-1 ring-white/10 backdrop-blur-sm hover:bg-white/15 transition"
        >
          Sign Up
        </a>
      </div>

    </div>
  </div>
</header>