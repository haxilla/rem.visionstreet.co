@include('public.layout.head')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">

<style>
  :root {
    --navy:       #1a2235;
    --navy-mid:   #243047;
    --navy-light: #2e3d57;
    --gold:       #c9a96e;
    --gold-light: #e0c898;
    --cream:      #f8f5f0;
    --text-muted: #8a94a6;
  }

  /* ── Page shell ── */
  .re-login-wrap {
    min-height: calc(100vh - 6rem);
    display: flex;
    align-items: stretch;
    font-family: 'DM Sans', sans-serif;
  }

  /* ── LEFT PANEL ── */
  .re-left {
    flex: 1 1 55%;
    position: relative;
    overflow: hidden;
    display: none;
  }
  @media (min-width: 1024px) { .re-left { display: block; } }

  .re-left-bg {
    position: absolute; inset: 0;
    background-image: url('https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=1400&q=85&auto=format&fit=crop');
    background-size: cover;
    background-position: center;
    transform: scale(1.03);
    transition: transform 8s ease-out;
  }
  .re-left-bg.loaded { transform: scale(1); }

  /* dark gradient overlay */
  .re-left-overlay {
    position: absolute; inset: 0;
    background: linear-gradient(
      135deg,
      rgba(26,34,53,0.82) 0%,
      rgba(26,34,53,0.45) 50%,
      rgba(26,34,53,0.72) 100%
    );
  }

  /* gold accent bar left edge */
  .re-left::before {
    content: '';
    position: absolute;
    top: 0; left: 0; bottom: 0;
    width: 3px;
    background: linear-gradient(to bottom, transparent, var(--gold), transparent);
    z-index: 10;
  }

  .re-left-content {
    position: relative; z-index: 5;
    display: flex; flex-direction: column;
    justify-content: space-between;
    height: 100%;
    padding: 3rem 3.5rem;
    color: white;
    animation: fadeUp 0.9s ease both;
  }

  .re-brand {
    display: flex; align-items: center; gap: 0.75rem;
  }
  .re-brand-icon {
    width: 36px; height: 36px;
    background: rgba(201,169,110,0.2);
    border: 1px solid rgba(201,169,110,0.4);
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
  }
  .re-brand-name {
    font-family: 'Cormorant Garamond', serif;
    font-size: 1.4rem;
    font-weight: 500;
    letter-spacing: 0.02em;
    color: white;
  }
  .re-brand-name span { color: var(--gold); }

  .re-headline {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 2rem 0;
  }
  .re-eyebrow {
    display: flex; align-items: center; gap: 0.75rem;
    font-size: 0.7rem;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    color: var(--gold);
    font-weight: 500;
    margin-bottom: 1.25rem;
  }
  .re-eyebrow::before {
    content: '';
    width: 28px; height: 1px;
    background: var(--gold);
  }
  .re-tagline {
    font-family: 'Cormorant Garamond', serif;
    font-size: clamp(2.2rem, 3.5vw, 3.2rem);
    font-weight: 300;
    line-height: 1.18;
    color: white;
    margin-bottom: 1.5rem;
  }
  .re-tagline em {
    font-style: italic;
    color: var(--gold-light);
  }
  .re-sub {
    font-size: 0.88rem;
    color: rgba(255,255,255,0.58);
    line-height: 1.7;
    max-width: 340px;
  }

  /* Agent testimonial strip */
  .re-agents {
    display: flex; flex-direction: column; gap: 1rem;
  }
  .re-agents-label {
    font-size: 0.65rem;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    color: rgba(255,255,255,0.35);
  }
  .re-agents-row {
    display: flex; align-items: center; gap: 1rem;
  }
  .re-agent-avatars {
    display: flex;
  }
  .re-agent-avatars img {
    width: 38px; height: 38px;
    border-radius: 50%;
    border: 2px solid rgba(255,255,255,0.15);
    object-fit: cover;
    margin-left: -10px;
    transition: transform 0.2s;
  }
  .re-agent-avatars img:first-child { margin-left: 0; }
  .re-agent-avatars img:hover { transform: scale(1.1); z-index: 2; }
  .re-agents-stat {
    font-size: 0.82rem;
    color: rgba(255,255,255,0.65);
  }
  .re-agents-stat strong {
    color: white;
    font-weight: 500;
  }

  /* Property thumbnail strip */
  .re-props {
    display: flex; gap: 0.6rem;
    margin-top: 0.5rem;
  }
  .re-prop-thumb {
    flex: 1;
    height: 70px;
    border-radius: 8px;
    overflow: hidden;
    position: relative;
  }
  .re-prop-thumb img {
    width: 100%; height: 100%;
    object-fit: cover;
    filter: brightness(0.75);
    transition: filter 0.3s, transform 0.4s;
  }
  .re-prop-thumb:hover img { filter: brightness(0.9); transform: scale(1.06); }
  .re-prop-price {
    position: absolute; bottom: 6px; left: 8px;
    font-size: 0.62rem;
    font-weight: 500;
    color: white;
    background: rgba(26,34,53,0.7);
    padding: 2px 6px;
    border-radius: 4px;
    letter-spacing: 0.03em;
  }

  /* ── RIGHT PANEL ── */
  .re-right {
    flex: 0 0 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--cream);
    padding: 2.5rem 1.5rem;
    position: relative;
  }
  @media (min-width: 1024px) { .re-right { flex: 0 0 45%; } }

  /* subtle grid texture */
  .re-right::before {
    content: '';
    position: absolute; inset: 0;
    background-image:
      linear-gradient(rgba(26,34,53,0.03) 1px, transparent 1px),
      linear-gradient(90deg, rgba(26,34,53,0.03) 1px, transparent 1px);
    background-size: 40px 40px;
    pointer-events: none;
  }

  .re-form-wrap {
    width: 100%;
    max-width: 400px;
    position: relative;
    animation: fadeUp 0.7s 0.15s ease both;
  }

  /* gold top bar on card */
  .re-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow:
      0 1px 2px rgba(0,0,0,0.04),
      0 8px 32px rgba(26,34,53,0.10),
      0 24px 64px rgba(26,34,53,0.07);
  }
  .re-card-top {
    height: 3px;
    background: linear-gradient(90deg, var(--navy), var(--gold), var(--navy-light));
  }
  .re-card-header {
    background: var(--navy);
    padding: 2rem 2rem 1.75rem;
  }
  .re-card-badge {
    display: inline-flex; align-items: center; gap: 0.5rem;
    background: rgba(255,255,255,0.07);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 6px;
    padding: 0.3rem 0.75rem;
    margin-bottom: 1rem;
  }
  .re-card-badge span {
    font-size: 0.65rem;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    color: var(--gold);
    font-weight: 500;
  }
  .re-card-title {
    font-family: 'Cormorant Garamond', serif;
    font-size: 1.9rem;
    font-weight: 400;
    color: white;
    letter-spacing: 0.01em;
    line-height: 1.2;
    margin-bottom: 0.4rem;
  }
  .re-card-sub {
    font-size: 0.82rem;
    color: rgba(255,255,255,0.45);
  }

  .re-card-body { padding: 2rem; }

  /* Fields */
  .re-field { margin-bottom: 1.25rem; }
  .re-field-top {
    display: flex; justify-content: space-between; align-items: center;
    margin-bottom: 0.5rem;
  }
  .re-label {
    font-size: 0.67rem;
    font-weight: 500;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: var(--navy);
  }
  .re-forgot {
    font-size: 0.75rem;
    color: var(--text-muted);
    text-decoration: none;
    transition: color 0.2s;
  }
  .re-forgot:hover { color: var(--navy); }

  .re-input-wrap {
    position: relative;
  }
  .re-input-icon {
    position: absolute; left: 0.9rem; top: 50%; transform: translateY(-50%);
    color: var(--text-muted);
    pointer-events: none;
  }
  .re-input {
    width: 100%;
    padding: 0.8rem 0.9rem 0.8rem 2.6rem;
    border: 1.5px solid #e5e9f0;
    border-radius: 10px;
    font-size: 0.88rem;
    font-family: 'DM Sans', sans-serif;
    color: var(--navy);
    background: #fafbfc;
    transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
    outline: none;
    box-sizing: border-box;
  }
  .re-input::placeholder { color: #b0bac9; }
  .re-input:focus {
    border-color: var(--navy);
    background: white;
    box-shadow: 0 0 0 3px rgba(26,34,53,0.07);
  }
  .re-input.error { border-color: #e05a5a; background: #fff8f8; }

  .re-eye-btn {
    position: absolute; right: 0.9rem; top: 50%; transform: translateY(-50%);
    background: none; border: none; cursor: pointer;
    color: var(--text-muted);
    padding: 0; line-height: 1;
    transition: color 0.2s;
  }
  .re-eye-btn:hover { color: var(--navy); }

  .re-error-msg {
    display: flex; align-items: center; gap: 0.35rem;
    margin-top: 0.4rem;
    font-size: 0.75rem;
    color: #e05a5a;
  }

  /* Remember */
  .re-remember {
    display: flex; align-items: center; gap: 0.6rem;
    margin-bottom: 1.5rem;
    cursor: pointer;
  }
  .re-remember input[type="checkbox"] {
    width: 15px; height: 15px;
    border-radius: 4px;
    accent-color: var(--navy);
    cursor: pointer;
  }
  .re-remember-label {
    font-size: 0.83rem;
    color: #6b7585;
    user-select: none;
  }

  /* Alert */
  .re-alert {
    display: flex; gap: 0.6rem; align-items: flex-start;
    background: #fff3f3;
    border: 1px solid #fcd0d0;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    margin-bottom: 1.25rem;
    font-size: 0.82rem;
    color: #c0392b;
  }

  /* Submit */
  .re-submit {
    width: 100%;
    padding: 0.9rem;
    background: var(--navy);
    color: white;
    border: none;
    border-radius: 10px;
    font-family: 'DM Sans', sans-serif;
    font-size: 0.88rem;
    font-weight: 500;
    letter-spacing: 0.06em;
    cursor: pointer;
    transition: background 0.2s, box-shadow 0.2s, transform 0.1s;
    position: relative;
    overflow: hidden;
  }
  .re-submit::after {
    content: '';
    position: absolute; inset: 0;
    background: linear-gradient(90deg, transparent 0%, rgba(201,169,110,0.18) 50%, transparent 100%);
    transform: translateX(-100%);
    transition: transform 0.5s ease;
  }
  .re-submit:hover { background: var(--navy-mid); box-shadow: 0 4px 16px rgba(26,34,53,0.25); }
  .re-submit:hover::after { transform: translateX(100%); }
  .re-submit:active { transform: scale(0.99); }

  /* Divider */
  .re-divider {
    display: flex; align-items: center; gap: 0.75rem;
    margin: 1.25rem 0 0;
  }
  .re-divider::before, .re-divider::after {
    content: ''; flex: 1; height: 1px; background: #e8ecf2;
  }
  .re-divider span {
    font-size: 0.7rem;
    color: #b0bac9;
    letter-spacing: 0.1em;
    text-transform: uppercase;
  }

  .re-footer-note {
    text-align: center;
    font-size: 0.72rem;
    color: #9aa3b2;
    margin-top: 1.25rem;
    letter-spacing: 0.04em;
  }
  .re-footer-note a { color: inherit; text-decoration: underline; opacity: 0.7; }

  /* Mobile brand strip */
  .re-mobile-brand {
    display: flex; align-items: center; justify-content: center;
    gap: 0.6rem;
    margin-bottom: 1.75rem;
  }
  .re-mobile-brand-name {
    font-family: 'Cormorant Garamond', serif;
    font-size: 1.35rem;
    font-weight: 500;
    color: var(--navy);
  }
  .re-mobile-brand-name span { color: var(--gold); }
  @media (min-width: 1024px) { .re-mobile-brand { display: none; } }

  /* Animations */
  @keyframes fadeUp {
    from { opacity: 0; transform: translateY(18px); }
    to   { opacity: 1; transform: translateY(0); }
  }
</style>

<body data-section="admin"
  class="linkcheck relative bg-white min-h-screen font-sans text-gray-800 postgres">

  @include('public.layout.nav')

  <main class="transition-all duration-300 min-h-screen pt-24 relative"
        :class="collapsed ? 'ml-20' : 'ml-64'">
    <div class="ml-8 mr-8 lg:ml-10 lg:mr-10">
      <div class="pageswap w-full">

        <div class="re-login-wrap">

          {{-- ═══ LEFT: Imagery Panel ═══ --}}
          <div class="re-left">
            <div class="re-left-bg" id="reBg"></div>
            <div class="re-left-overlay"></div>

            <div class="re-left-content">
              {{-- Brand --}}
              <div class="re-brand">
                <div class="re-brand-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#c9a96e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
                  </svg>
                </div>
                <span class="re-brand-name">Realty<span>Em&#9998;ils</span></span>
              </div>

              {{-- Headline --}}
              <div class="re-headline">
                <div class="re-eyebrow">Premium Real Estate Platform</div>
                <h2 class="re-tagline">
                  Where <em>exceptional</em><br>properties find<br>their audience
                </h2>
                <p class="re-sub">
                  Manage listings, agent profiles, and email campaigns from a single powerful dashboard.
                </p>
              </div>

              {{-- Social proof --}}
              <div class="re-agents">
                <span class="re-agents-label">Trusted by top agents</span>
                <div class="re-agents-row">
                  <div class="re-agent-avatars">
                    <img src="https://images.unsplash.com/photo-1573496799652-408c2ac9fe98?w=80&h=80&q=80&auto=format&fit=crop&crop=face" alt="Agent">
                    <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?w=80&h=80&q=80&auto=format&fit=crop&crop=face" alt="Agent">
                    <img src="https://images.unsplash.com/photo-1580489944761-15a19d654956?w=80&h=80&q=80&auto=format&fit=crop&crop=face" alt="Agent">
                    <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=80&h=80&q=80&auto=format&fit=crop&crop=face" alt="Agent">
                  </div>
                  <div class="re-agents-stat">
                    <strong>2,400+</strong> agents across<br>North America
                  </div>
                </div>

                {{-- Property thumbnails --}}
                <div class="re-props">
                  <div class="re-prop-thumb">
                    <img src="https://images.unsplash.com/photo-1613977257363-707ba9348227?w=300&h=150&q=80&auto=format&fit=crop" alt="Property">
                    <span class="re-prop-price">$4.2M</span>
                  </div>
                  <div class="re-prop-thumb">
                    <img src="https://images.unsplash.com/photo-1512917774080-9991f1c4c750?w=300&h=150&q=80&auto=format&fit=crop" alt="Property">
                    <span class="re-prop-price">$2.8M</span>
                  </div>
                  <div class="re-prop-thumb">
                    <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=300&h=150&q=80&auto=format&fit=crop" alt="Property">
                    <span class="re-prop-price">$6.1M</span>
                  </div>
                </div>
              </div>

            </div>
          </div>{{-- /left --}}

          {{-- ═══ RIGHT: Login Form ═══ --}}
          <div class="re-right">
            <div class="re-form-wrap">

              {{-- Mobile brand (hidden on desktop) --}}
              <div class="re-mobile-brand">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#c9a96e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
                <span class="re-mobile-brand-name">Realty<span>Em&#9998;ils</span></span>
              </div>

              <div class="re-card">
                <div class="re-card-top"></div>

                <div class="re-card-header">
                  <div class="re-card-badge">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#c9a96e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                      <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/>
                    </svg>
                    <span>Admin Portal</span>
                  </div>
                  <h1 class="re-card-title">Welcome back</h1>
                  <p class="re-card-sub">Sign in to your admin account</p>
                </div>

                <div class="re-card-body">
                  <form method="POST" action="{{ route('admin.login') }}">
                    @csrf

                    {{-- Email --}}
                    <div class="re-field">
                      <div class="re-field-top">
                        <label class="re-label" for="email">Email Address</label>
                      </div>
                      <div class="re-input-wrap">
                        <span class="re-input-icon">
                          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>
                          </svg>
                        </span>
                        <input
                          id="email" name="email" type="email"
                          autocomplete="email" required
                          value="{{ old('email') }}"
                          placeholder="admin@realty.com"
                          class="re-input @error('email') error @enderror"
                        />
                      </div>
                      @error('email')
                        <div class="re-error-msg">
                          <svg width="12" height="12" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                          {{ $message }}
                        </div>
                      @enderror
                    </div>

                    {{-- Password --}}
                    <div class="re-field">
                      <div class="re-field-top">
                        <label class="re-label" for="password">Password</label>
                        <a href="#" class="re-forgot">Forgot password?</a>
                      </div>
                      <div class="re-input-wrap">
                        <span class="re-input-icon">
                          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/>
                          </svg>
                        </span>
                        <input
                          id="re-password" name="password" type="password"
                          autocomplete="current-password" required
                          placeholder="••••••••"
                          class="re-input @error('password') error @enderror"
                        />
                        <button type="button" class="re-eye-btn" onclick="reTogglePwd()" aria-label="Toggle password">
                          <svg id="re-eye" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                          </svg>
                        </button>
                      </div>
                      @error('password')
                        <div class="re-error-msg">
                          <svg width="12" height="12" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                          {{ $message }}
                        </div>
                      @enderror
                    </div>

                    {{-- Remember --}}
                    <label class="re-remember">
                      <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                      <span class="re-remember-label">Keep me signed in</span>
                    </label>

                    {{-- Session error --}}
                    @if(session('error'))
                      <div class="re-alert">
                        <svg width="15" height="15" viewBox="0 0 20 20" fill="currentColor" style="flex-shrink:0;margin-top:1px"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                        {{ session('error') }}
                      </div>
                    @endif

                    <button type="submit" class="re-submit">Sign In</button>

                    <div class="re-divider"><span>Secure access</span></div>

                  </form>
                </div>
              </div>

              <p class="re-footer-note">
                Restricted access &mdash; authorised personnel only &nbsp;·&nbsp;
                <a href="#">Privacy Policy</a>
              </p>

            </div>
          </div>{{-- /right --}}

        </div>

      </div>
    </div>
  </main>

  @include('public.layout.footer')

  <script>
    // Animate bg image in on load
    window.addEventListener('load', function() {
      var bg = document.getElementById('reBg');
      if (bg) bg.classList.add('loaded');
    });

    // Password toggle
    function reTogglePwd() {
      var inp  = document.getElementById('re-password');
      var icon = document.getElementById('re-eye');
      if (inp.type === 'password') {
        inp.type = 'text';
        icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>';
      } else {
        inp.type = 'password';
        icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
      }
    }
  </script>
</body>
</html>