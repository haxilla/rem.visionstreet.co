@php
    use App\Models\Core\Propagent;

    $navAgent = Propagent::find(auth('member')->id());

    $navAgentImg = null;
    if ($navAgent && !empty($navAgent->agtPhoto)) {
        $navPhotoPath = public_path("agentPhotos/{$navAgent->photoToken()}/{$navAgent->agtPhoto}");
        if (file_exists($navPhotoPath)) {
            $navAgentImg = asset("agentPhotos/{$navAgent->photoToken()}/{$navAgent->agtPhoto}");
        }
    }

    $navInitials = '?';
    if ($navAgent && $navAgent->agtFullName) {
        $navNameParts = preg_split('/\s+/', trim($navAgent->agtFullName));
        $navInitials = strtoupper(substr($navNameParts[0], 0, 1) . substr(end($navNameParts), 0, 1));
    }
@endphp

{{-- FIXED NAVBAR --}}
<header class="fixed top-0 left-0 w-full z-50">

  {{-- background --}}
  <div class="absolute inset-0 bg-gradient-to-r from-[#1b2f63] via-[#223a75] to-[#2a4486]"></div>

  {{-- bottom divider --}}
  <div class="absolute bottom-0 left-0 right-0 h-px bg-white/10"></div>

  <div class="relative mx-auto max-w-screen-2xl px-6 lg:px-10" style="max-width:1600px;">
    <div class="flex h-[72px] items-center justify-between text-white">

      {{-- LOGO --}}
      <a href="/member/dashboard" class="flex items-center shrink-0">
        <img src="{{ asset('images/RealtyEmails_logo1.png') }}" alt="RealtyEmails" class="h-9 w-auto">
      </a>

      {{-- DESKTOP NAV LINKS (absolutely centered on the header, so the
           logo/avatar being different widths can't push it off-center -
           justify-between alone only guarantees equal gaps, not a
           centered middle element) --}}
      <nav class="hidden lg:flex absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 items-center gap-8 text-[14px] font-medium tracking-[0.02em] text-white/80">
        <a href="/member/flyer/create" class="hover:text-white transition">Create New Flyer</a>
        <a href="/member/dashboard" class="hover:text-white transition">My Flyers</a>
        <a href="/member/campaigns" class="hover:text-white transition">Campaigns</a>
        <a href="/member/copy-center" class="hover:text-white transition">Copy Center</a>
        <a href="/member/buy-credits" class="hover:text-white transition">Buy Credits</a>
      </nav>

      {{-- RIGHT SIDE --}}
      <div class="flex items-center gap-3">

        {{-- ACCOUNT DROPDOWN (desktop) --}}
        <details class="relative hidden lg:block">
          <summary class="flex cursor-pointer list-none items-center rounded-full transition">
            @if($navAgentImg)
              <img src="{{ $navAgentImg }}" alt="" class="h-10 w-10 rounded-full object-cover object-top ring-2 ring-white/25 hover:ring-white/60 transition">
            @else
              <span class="flex h-10 w-10 items-center justify-center rounded-full bg-white/15 text-sm font-black text-white ring-2 ring-white/25 hover:ring-white/60 transition">
                {{ $navInitials }}
              </span>
            @endif
          </summary>

          <div class="absolute right-0 top-[calc(100%+10px)] w-64 rounded-2xl bg-white p-2 text-slate-700 shadow-xl ring-1 ring-black/10">
            <div class="flex items-center gap-3 px-3 py-3">
              @if($navAgentImg)
                <img src="{{ $navAgentImg }}" alt="" class="h-11 w-11 rounded-full object-cover object-top ring-1 ring-slate-200">
              @else
                <span class="flex h-11 w-11 items-center justify-center rounded-full bg-[#123f91]/10 text-sm font-black text-[#123f91] ring-1 ring-slate-200">
                  {{ $navInitials }}
                </span>
              @endif
              <div class="min-w-0">
                <div class="truncate text-sm font-black text-slate-900">{{ $navAgent->agtFullName ?? 'Member' }}</div>
                @if($navAgent && $navAgent->agtEmail)
                  <div class="truncate text-xs text-slate-500">{{ $navAgent->agtEmail }}</div>
                @endif
              </div>
            </div>
            <div class="mb-1 h-px bg-slate-100"></div>
            <a href="/member/agent-info" class="block rounded-xl px-4 py-2.5 text-sm font-bold hover:bg-slate-100">Agent Info</a>
            <a href="/member/account" class="block rounded-xl px-4 py-2.5 text-sm font-bold hover:bg-slate-100">Account Info</a>
            <div class="my-1 h-px bg-slate-100"></div>
            <a href="/logout" class="block rounded-xl px-4 py-2.5 text-sm font-bold text-red-600 hover:bg-red-50">Log out</a>
          </div>
        </details>

        {{-- MOBILE HAMBURGER --}}
        <details class="relative lg:hidden">
          <summary class="flex h-10 w-10 cursor-pointer list-none items-center justify-center rounded-lg text-white hover:bg-white/10 transition">
            <span class="text-xl leading-none">&#9776;</span>
          </summary>

          <div class="absolute right-0 top-[calc(100%+8px)] w-64 max-w-[calc(100vw-24px)] rounded-2xl bg-white p-2 text-slate-700 shadow-xl ring-1 ring-black/10">
            <div class="flex items-center gap-3 px-3 py-3">
              @if($navAgentImg)
                <img src="{{ $navAgentImg }}" alt="" class="h-11 w-11 rounded-full object-cover object-top ring-1 ring-slate-200">
              @else
                <span class="flex h-11 w-11 items-center justify-center rounded-full bg-[#123f91]/10 text-sm font-black text-[#123f91] ring-1 ring-slate-200">
                  {{ $navInitials }}
                </span>
              @endif
              <div class="min-w-0">
                <div class="truncate text-sm font-black text-slate-900">{{ $navAgent->agtFullName ?? 'Member' }}</div>
                @if($navAgent && $navAgent->agtEmail)
                  <div class="truncate text-xs text-slate-500">{{ $navAgent->agtEmail }}</div>
                @endif
              </div>
            </div>
            <div class="mb-1 h-px bg-slate-100"></div>
            <a href="/member/flyer/create" class="block rounded-xl px-4 py-2.5 text-sm font-bold hover:bg-slate-100">Create New Flyer</a>
            <a href="/member/dashboard" class="block rounded-xl px-4 py-2.5 text-sm font-bold hover:bg-slate-100">My Flyers</a>
            <a href="/member/campaigns" class="block rounded-xl px-4 py-2.5 text-sm font-bold hover:bg-slate-100">Campaigns</a>
            <a href="/member/copy-center" class="block rounded-xl px-4 py-2.5 text-sm font-bold hover:bg-slate-100">Copy Center</a>
            <a href="/member/buy-credits" class="block rounded-xl px-4 py-2.5 text-sm font-bold hover:bg-slate-100">Buy Credits</a>
            <div class="my-1 h-px bg-slate-100"></div>
            <a href="/member/agent-info" class="block rounded-xl px-4 py-2.5 text-sm font-bold hover:bg-slate-100">Agent Info</a>
            <a href="/member/account" class="block rounded-xl px-4 py-2.5 text-sm font-bold hover:bg-slate-100">Account Info</a>
            <div class="my-1 h-px bg-slate-100"></div>
            <a href="/logout" class="block rounded-xl px-4 py-2.5 text-sm font-bold text-red-600 hover:bg-red-50">Log out</a>
          </div>
        </details>

      </div>

    </div>
  </div>
</header>
