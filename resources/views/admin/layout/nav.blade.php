{{-- FIXED NAVBAR --}}
<header class="fixed top-0 left-0 w-full z-50">

  {{-- background --}}
  <div class="absolute inset-0 bg-gradient-to-r from-[#1b2f63] via-[#223a75] to-[#2a4486]"></div>

  {{-- bottom divider --}}
  <div class="absolute bottom-0 left-0 right-0 h-px bg-white/10"></div>

  <div class="relative mx-auto max-w-screen-2xl px-6 lg:px-10" style="max-width:1600px;">
    <div class="flex h-[72px] items-center justify-between text-white">

      {{-- LOGO --}}
      <a href="/" class="flex items-center">
        <img src="{{ asset('images/RealtyEmails_logo1.png') }}" alt="RealtyEmails" class="h-9 w-auto">
      </a>

      {{-- DESKTOP TEXT --}}
      <nav class="hidden lg:flex items-center gap-10 text-[14px] font-medium tracking-[0.06em] text-white/80">
        ADMIN LOGIN
      </nav>

      {{-- MOBILE HAMBURGER --}}
      <button
        type="button"
        id="adminMobileMenuButton"
        class="lg:hidden flex h-10 w-10 items-center justify-center rounded-lg border border-white/20 bg-white/10 text-white"
        aria-label="Open admin menu"
      >
        ☰
      </button>

    </div>
  </div>
</header>

<div id="adminMobileMenuOverlay"
     class="fixed inset-0 z-[60] hidden lg:hidden">

    <div
        id="adminMobileMenuBackdrop"
        class="absolute inset-0 bg-black/50">
    </div>

    <div class="relative h-full w-72 bg-white shadow-xl">
        <div class="mb-6 text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">
            Admin Menu
        </div>

        <nav class="space-y-2">
            <a href="/admin" class="block rounded-xl px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                Dashboard
            </a>

            <a href="/admin/flyers" class="block rounded-xl px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                Flyers
            </a>

            <a href="/admin/campaigns" class="block rounded-xl px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                Campaigns
            </a>

            <a href="/admin/agents" class="block rounded-xl px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                Agents
            </a>

            <a href="/admin/logout" class="block rounded-xl px-4 py-3 text-sm font-semibold text-red-600 hover:bg-red-50">
                Log Out
            </a>
        </nav>
    </div>

</div>

<script>

document.addEventListener('DOMContentLoaded', function () {

    const button   = document.getElementById('adminMobileMenuButton');
    const overlay  = document.getElementById('adminMobileMenuOverlay');
    const backdrop = document.getElementById('adminMobileMenuBackdrop');

    if (!button || !overlay) {
        return;
    }

    button.addEventListener('click', function () {
        overlay.classList.remove('hidden');
    });

    backdrop.addEventListener('click', function () {
        overlay.classList.add('hidden');
    });

});

</script>