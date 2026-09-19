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

@if(session('impersonator_admin_id'))
  @php
    // Only an impersonating admin ever reaches this banner, so trial mode
    // is shown here and nowhere else in the agent-facing pages.
    $navTrialMode  = \App\Models\Core\AdminSetting::trialMode();
    $navTrialEmail = $navTrialMode ? \App\Models\Core\AdminSetting::trialEmail() : '';
  @endphp

  {{-- Pinned bars (the wizard's Save & Continue) sit above this banner
       instead of underneath it. 2.75rem = the banner's h-11. --}}
  <style>.wz-pinned-bar { bottom: 2.75rem !important; }</style>

  {{-- IMPERSONATION BANNER: fixed to the bottom so it never needs the
       top header's height to be recalculated on every page --}}
  {{-- One fixed-height row that never wraps: the left side (who + trial
       mode) truncates with "..." when space runs out, and the Return
       button stays pinned on the right. Text steps down from 14px to 12px
       on small screens, and optional wording drops out progressively
       (sm: "Return" -> "Return to Admin", md: adds "impersonating",
       lg: adds the trial-mode detail). --}}
  <div class="fixed bottom-0 left-0 z-50 w-full bg-amber-500 text-amber-950">
    <div class="mx-auto flex h-11 max-w-[1600px] items-center gap-2 px-3 text-xs font-bold sm:gap-3 sm:px-6 sm:text-sm lg:px-10">

      <div class="flex min-w-0 flex-1 items-center gap-2 sm:gap-3">

        <span class="min-w-0 truncate">
          <span class="font-semibold">Viewing as</span>
          {{ $navAgent->agtFullName ?? 'this agent' }}
          <span class="hidden font-semibold md:inline">&mdash; impersonating</span>
        </span>

        {{-- Trial mode switch: always shown (ON or OFF) so it can be flipped
             either way. Posts without reloading the page, so a half-filled
             form isn't lost. Turning it OFF asks first, since real sends
             then use credits and agents get their own copy. --}}
        <form id="trialToggleForm" method="POST" action="{{ route('admin.trialToggle') }}" class="shrink-0">
          @csrf
          <button type="submit"
                  id="trialToggleBtn"
                  data-on="{{ $navTrialMode ? '1' : '0' }}"
                  data-base="whitespace-nowrap rounded-full px-4 py-1.5 text-[11px] font-black uppercase tracking-wide transition"
                  data-class-on="bg-red-700 text-white hover:bg-red-800"
                  data-class-off="bg-amber-600/40 text-amber-950 ring-1 ring-amber-950/30 hover:bg-amber-600/60"
                  title="{{ $navTrialMode ? 'Click to turn trial mode OFF' : 'Click to turn trial mode ON' }}"
                  class="whitespace-nowrap rounded-full px-4 py-1.5 text-[11px] font-black uppercase tracking-wide transition {{ $navTrialMode ? 'bg-red-700 text-white hover:bg-red-800' : 'bg-amber-600/40 text-amber-950 ring-1 ring-amber-950/30 hover:bg-amber-600/60' }}">
            Trial<span class="hidden sm:inline"> mode</span>: <span data-state>{{ $navTrialMode ? 'ON' : 'OFF' }}</span>
          </button>
        </form>

        <span id="trialDetail" class="min-w-0 {{ $navTrialMode ? '' : 'hidden' }}">
          <span class="hidden truncate font-semibold lg:block">
            No credits used &middot; agent copy to <span data-trial-email>{{ $navTrialEmail !== '' ? $navTrialEmail : 'test email (not set)' }}</span>
          </span>
        </span>

      </div>

      <a href="{{ route('admin.returnToAdmin') }}"
         class="shrink-0 whitespace-nowrap rounded-full bg-amber-950 px-4 py-1.5 text-[11px] font-black uppercase tracking-wide text-amber-50 transition hover:bg-amber-900">
        Return<span class="hidden sm:inline">&nbsp;to Admin</span>
      </a>

    </div>
  </div>

  <script>
  (function () {
      var form = document.getElementById('trialToggleForm');
      if (!form) return;

      var btn      = document.getElementById('trialToggleBtn');
      var detail   = document.getElementById('trialDetail');
      var emailEl  = detail.querySelector('[data-trial-email]');
      var stateEl  = btn.querySelector('[data-state]');

      function apply(on, email) {
          btn.dataset.on = on ? '1' : '0';
          btn.className  = btn.dataset.base + ' ' + (on ? btn.dataset.classOn : btn.dataset.classOff);
          btn.title      = on ? 'Click to turn trial mode OFF' : 'Click to turn trial mode ON';
          stateEl.textContent = on ? 'ON' : 'OFF';
          detail.classList.toggle('hidden', !on);
          emailEl.textContent = email || 'test email (not set)';
      }

      form.addEventListener('submit', function (e) {
          e.preventDefault();

          if (btn.dataset.on === '1' &&
              !confirm('Turn trial mode OFF?\n\nReal sends will use credits and agents will receive their own copy.')) {
              return;
          }

          btn.disabled = true;

          fetch(form.action, {
              method: 'POST',
              body: new FormData(form),   // includes the CSRF token
              headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
          })
              .then(function (response) {
                  return response.json().then(function (json) {
                      return { ok: response.ok, json: json };
                  });
              })
              .then(function (result) {
                  if (!result.ok) {
                      alert(result.json.message || 'Could not change trial mode.');
                      if (result.json.settingsUrl) window.location = result.json.settingsUrl;
                      return;
                  }
                  apply(result.json.trialMode, result.json.trialEmail);
              })
              .catch(function () {
                  alert('Could not change trial mode. Please try again.');
              })
              .then(function () {
                  btn.disabled = false;
              });
      });
  })();
  </script>
@endif
