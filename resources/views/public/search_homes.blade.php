@include('public.layout.head')

@php
$paginator   = $data['searchAll'];
$pageItems   = collect($paginator->items());
$featured    = $pageItems->first();
$listings    = $pageItems->slice(1);
$searchValue = request('q', '');

/* ── helper: build listing image URL ── */
$listingImg = function($item) {
    $photoObj = $item->thePhotos->where('def', '=', '1')->first();
    $photo    = $photoObj?->photoName;
    if ($photo && $item->theMeta?->zipDir && $item->theMeta?->mlsDir) {
        return "https://realtyrepublic.com/hqphotos/{$item->theMeta->zipDir}/{$item->theMeta->mlsDir}/{$photo}";
    }
    return null;
};

/* ── helper: build agent image URL ── */
$agentImg = function($item) {
    if (!empty($item->theAgent?->agtPhoto) && !empty($item->theAgent?->theAgentCleanup?->newRemID)) {
        return "https://realtyrepublic.com/agentPhotos/{$item->theAgent->theAgentCleanup->newRemID}/{$item->theAgent->agtPhoto}";
    }
    return null;
};

/* ── helper: format price ── */
$priceLabel = function($item) {
    $price = $item->xPrice ?? $item->xListPrice;
    return $price ? '$' . number_format($price) : null;
};
@endphp

<style>
  @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400;1,600&family=DM+Sans:wght@300;400;500;600&display=swap');

  :root {
    --navy:       #0e1e3d;
    --navy-mid:   #162d5a;
    --navy-light: #1e3f7a;
    --gold:       #c9a84c;
    --gold-light: #e8c96a;
    --cream:      #f7f4ef;
    --slate:      #64748b;
    --card-bg:    #ffffff;
    --radius-lg:  28px;
    --radius-md:  18px;
    --shadow-lg:  0 24px 64px rgba(14,30,61,.14);
    --shadow-md:  0 8px 32px rgba(14,30,61,.10);
  }

  * { box-sizing: border-box; margin: 0; padding: 0; }

  body {
    font-family: 'DM Sans', sans-serif;
    background: var(--cream);
    color: var(--navy);
    min-height: 100vh;
  }

  /* ── LAYOUT ──────────────────────────────────── */
  .page-wrap {
    padding: 2rem 2rem 4rem;
    max-width: 1440px;
    margin: 0 auto;
  }

  /* ── HERO ────────────────────────────────────── */
  .hero {
    position: relative;
    border-radius: var(--radius-lg);
    overflow: hidden;
    background: var(--navy);
    min-height: 520px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    box-shadow: var(--shadow-lg);
  }

  @media (max-width: 900px) {
    .hero { grid-template-columns: 1fr; }
    .hero-card-wrap { display: none; }
  }

  /* diagonal split */
  .hero::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(105deg, var(--navy) 48%, transparent 49%);
    pointer-events: none;
    z-index: 1;
  }

  .hero-bg {
    position: absolute;
    inset: 0;
    background-size: cover;
    background-position: center;
    opacity: .22;
  }

  .hero-left {
    position: relative;
    z-index: 2;
    padding: 3.5rem 3rem 3.5rem 3.5rem;
    display: flex;
    flex-direction: column;
    justify-content: center;
    color: #fff;
  }

  .hero-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    font-size: 10px;
    font-weight: 600;
    letter-spacing: .22em;
    text-transform: uppercase;
    color: var(--gold-light);
    margin-bottom: 1.5rem;
  }

  .hero-eyebrow::before {
    content: '';
    display: block;
    width: 28px;
    height: 1px;
    background: var(--gold-light);
  }

  .hero-title {
    font-family: 'Playfair Display', Georgia, serif;
    font-size: clamp(42px, 5vw, 66px);
    line-height: 1;
    font-weight: 700;
    color: #fff;
    margin-bottom: 1.25rem;
  }

  .hero-title em {
    font-style: italic;
    color: var(--gold-light);
  }

  .hero-sub {
    font-size: 15px;
    line-height: 1.75;
    color: rgba(255,255,255,.72);
    max-width: 440px;
    margin-bottom: 2rem;
  }

  /* search bar */
  .search-bar {
    display: flex;
    background: rgba(255,255,255,.1);
    border: 1px solid rgba(255,255,255,.2);
    border-radius: 50px;
    backdrop-filter: blur(12px);
    overflow: hidden;
    max-width: 560px;
  }

  .search-bar input {
    flex: 1;
    background: transparent;
    border: none;
    outline: none;
    padding: 1rem 1.4rem;
    font-family: 'DM Sans', sans-serif;
    font-size: 14px;
    color: #fff;
  }

  .search-bar input::placeholder { color: rgba(255,255,255,.5); }

  .search-bar button {
    background: var(--gold);
    border: none;
    padding: .85rem 1.75rem;
    font-family: 'DM Sans', sans-serif;
    font-size: 13px;
    font-weight: 600;
    color: var(--navy);
    cursor: pointer;
    border-radius: 0 50px 50px 0;
    letter-spacing: .04em;
    transition: background .2s;
  }

  .search-bar button:hover { background: var(--gold-light); }

  /* featured card on right */
  .hero-card-wrap {
    position: relative;
    z-index: 2;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2.5rem 3rem 2.5rem 4rem;
  }

  .featured-card {
    background: #fff;
    border-radius: var(--radius-md);
    overflow: hidden;
    width: 100%;
    max-width: 400px;
    box-shadow: 0 32px 80px rgba(0,0,0,.35);
    text-decoration: none;
    display: block;
    transform: rotate(1.2deg);
    transition: transform .35s ease, box-shadow .35s ease;
  }

  .featured-card:hover {
    transform: rotate(0deg) translateY(-4px);
    box-shadow: 0 40px 100px rgba(0,0,0,.45);
  }

  .featured-card img.prop-photo {
    width: 100%;
    height: 240px;
    object-fit: cover;
    display: block;
  }

  .featured-card .card-body {
    padding: 1.4rem 1.5rem 1.5rem;
  }

  .featured-badge {
    display: inline-block;
    font-size: 9px;
    font-weight: 700;
    letter-spacing: .18em;
    text-transform: uppercase;
    color: var(--gold);
    background: rgba(201,168,76,.1);
    border: 1px solid rgba(201,168,76,.3);
    border-radius: 4px;
    padding: 3px 8px;
    margin-bottom: .75rem;
  }

  .card-street {
    font-family: 'Playfair Display', serif;
    font-size: 18px;
    font-weight: 600;
    color: var(--navy);
    line-height: 1.3;
  }

  .card-city {
    font-size: 13px;
    color: var(--slate);
    margin-top: .25rem;
  }

  .card-price {
    font-size: 22px;
    font-weight: 700;
    color: var(--navy-light);
    margin-top: .6rem;
    letter-spacing: -.01em;
  }

  .card-agent {
    display: flex;
    align-items: center;
    gap: .75rem;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #f1f0ee;
  }

  .card-agent img {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid var(--cream);
    flex-shrink: 0;
  }

  .agent-name {
    font-size: 13px;
    font-weight: 600;
    color: var(--navy);
  }

  .agent-office {
    font-size: 11px;
    color: var(--slate);
    margin-top: 1px;
  }

  /* ── MAIN CONTENT ────────────────────────────── */
  .content-grid {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 2rem;
    margin-top: 2.5rem;
  }

  @media (max-width: 1024px) {
    .content-grid { grid-template-columns: 1fr; }
    .sidebar { display: none; }
  }

  /* ── SECTION HEADER ──────────────────────────── */
  .section-header {
    display: flex;
    align-items: baseline;
    gap: 1rem;
    margin-bottom: 1.5rem;
  }

  .section-title {
    font-family: 'Playfair Display', serif;
    font-size: 26px;
    font-weight: 600;
    color: var(--navy);
  }

  .section-count {
    font-size: 12px;
    color: var(--slate);
    letter-spacing: .06em;
    text-transform: uppercase;
  }

  /* ── LISTING CARDS ───────────────────────────── */
  .listing-card {
    display: flex;
    background: var(--card-bg);
    border-radius: var(--radius-md);
    overflow: hidden;
    box-shadow: var(--shadow-md);
    text-decoration: none;
    color: inherit;
    transition: transform .25s ease, box-shadow .25s ease;
    border: 1px solid rgba(14,30,61,.06);
  }

  .listing-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 20px 56px rgba(14,30,61,.14);
  }

  .listing-card + .listing-card { margin-top: 1.25rem; }

  .listing-thumb {
    width: 200px;
    min-width: 200px;
    height: 148px;
    object-fit: cover;
    display: block;
  }

  .listing-thumb-placeholder {
    width: 200px;
    min-width: 200px;
    height: 148px;
    background: linear-gradient(135deg, #e8edf5, #d4dbe9);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #a0aec0;
    font-size: 11px;
    letter-spacing: .08em;
    text-transform: uppercase;
  }

  .listing-info {
    flex: 1;
    padding: 1.1rem 1.4rem 1.1rem 1.25rem;
    display: flex;
    flex-direction: column;
    justify-content: center;
  }

  .listing-street {
    font-family: 'Playfair Display', serif;
    font-size: 17px;
    font-weight: 600;
    color: var(--navy);
    line-height: 1.3;
  }

  .listing-city {
    font-size: 13px;
    color: var(--slate);
    margin-top: .2rem;
  }

  .listing-price {
    font-size: 17px;
    font-weight: 700;
    color: var(--navy-light);
    margin-top: .5rem;
    letter-spacing: -.01em;
  }

  .listing-agent {
    display: flex;
    align-items: center;
    gap: .6rem;
    margin-top: .85rem;
  }

  .listing-agent img {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid var(--cream);
  }

  .listing-agent-name {
    font-size: 12.5px;
    font-weight: 600;
    color: var(--navy);
  }

  .listing-agent-office {
    font-size: 11px;
    color: var(--slate);
  }

  /* pagination override */
  .pagination-wrap {
    margin-top: 2.5rem;
    display: flex;
    justify-content: center;
  }

  .pagination-wrap nav span[aria-current="page"] span,
  .pagination-wrap nav a {
    font-family: 'DM Sans', sans-serif;
    font-size: 14px;
    border-radius: 10px !important;
  }

  /* ── SIDEBAR ─────────────────────────────────── */
  .sidebar { display: flex; flex-direction: column; gap: 1.5rem; }

  .sidebar-card {
    background: var(--card-bg);
    border-radius: var(--radius-md);
    padding: 1.75rem;
    box-shadow: var(--shadow-md);
    border: 1px solid rgba(14,30,61,.06);
  }

  .sidebar-card-title {
    font-family: 'Playfair Display', serif;
    font-size: 20px;
    font-weight: 600;
    color: var(--navy);
    margin-bottom: .4rem;
  }

  .sidebar-card p {
    font-size: 13px;
    color: var(--slate);
    line-height: 1.6;
    margin-bottom: 1rem;
  }

  .sidebar-input {
    width: 100%;
    border: 1.5px solid #dde3ee;
    border-radius: 10px;
    padding: .7rem 1rem;
    font-family: 'DM Sans', sans-serif;
    font-size: 13.5px;
    color: var(--navy);
    outline: none;
    transition: border-color .2s;
    margin-bottom: .75rem;
  }

  .sidebar-input:focus { border-color: var(--navy-light); }

  .sidebar-btn {
    width: 100%;
    background: var(--navy);
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: .75rem;
    font-family: 'DM Sans', sans-serif;
    font-size: 13.5px;
    font-weight: 600;
    cursor: pointer;
    letter-spacing: .04em;
    transition: background .2s;
  }

  .sidebar-btn:hover { background: var(--navy-light); }

  .sidebar-btn-gold {
    background: var(--gold);
    color: var(--navy);
  }

  .sidebar-btn-gold:hover { background: var(--gold-light); }

  .sidebar-divider {
    display: flex;
    align-items: center;
    gap: .75rem;
    margin: 1rem 0;
    color: #cbd5e1;
    font-size: 11px;
    letter-spacing: .1em;
    text-transform: uppercase;
  }

  .sidebar-divider::before,
  .sidebar-divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: #e9edf5;
  }

  /* stats strip inside sidebar */
  .stat-strip {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: .75rem;
    margin-top: 1rem;
  }

  .stat-item {
    background: var(--cream);
    border-radius: 10px;
    padding: .85rem .9rem;
    text-align: center;
  }

  .stat-num {
    font-family: 'Playfair Display', serif;
    font-size: 22px;
    font-weight: 700;
    color: var(--navy);
    line-height: 1;
  }

  .stat-label {
    font-size: 10px;
    color: var(--slate);
    letter-spacing: .08em;
    text-transform: uppercase;
    margin-top: 3px;
  }
</style>

<body data-section="admin" class="relative min-h-screen font-sans postgres" style="background: var(--cream);">

@include('public.layout.nav')

<main class="transition-all duration-300 min-h-screen pt-24 relative" :class="collapsed ? 'ml-20' : 'ml-64'">
<div class="page-wrap">

  {{-- ══ HERO ══════════════════════════════════════════════════════════ --}}
  <section class="hero">

    {{-- optional background texture from featured listing --}}
    @if($featured && $listingImg($featured))
      <div class="hero-bg" style="background-image: url('{{ $listingImg($featured) }}')"></div>
    @endif

    {{-- LEFT: copy + search --}}
    <div class="hero-left">

      <span class="hero-eyebrow">Slide Show Gallery</span>

      <h1 class="hero-title">
        Discover<br><em>Homes</em><br>In Style
      </h1>

      <p class="hero-sub">
        Browse the latest Realty Emails listings in a curated visual experience.
      </p>

      <form method="GET" action="">
        <div class="search-bar">
          <input
            type="text"
            name="q"
            value="{{ $searchValue }}"
            placeholder="Search address, city, zip, agent…"
          >
          <button type="submit">Search</button>
        </div>
      </form>

    </div>

    {{-- RIGHT: featured listing card --}}
    @if($featured)
    @php
      $fImg    = $listingImg($featured);
      $fAgt    = $agentImg($featured);
      $fPrice  = $priceLabel($featured);
      $fURL    = "https://realtyrepublic.com/homedetails/{$featured->url_slug}";
      $fStreet = $featured->xFullStreet;
      $fCity   = trim("{$featured->xCity} {$featured->xState} {$featured->xxZip}");
    @endphp
    <div class="hero-card-wrap">
      <a href="{{ $fURL }}" target="_blank" class="featured-card">

        @if($fImg)
          <img class="prop-photo" src="{{ $fImg }}" alt="{{ $fStreet }}">
        @endif

        <div class="card-body">
          <div class="featured-badge">Featured</div>
          <div class="card-street">{{ $fStreet }}</div>
          <div class="card-city">{{ $fCity }}</div>
          @if($fPrice)
            <div class="card-price">{{ $fPrice }}</div>
          @endif
          <div class="card-agent">
            @if($fAgt)
              <img src="{{ $fAgt }}" alt="{{ $featured->theAgent->agtFullName }}">
            @endif
            <div>
              <div class="agent-name">{{ $featured->theAgent->agtFullName }}</div>
              <div class="agent-office">{{ $featured->theOffice->officeName ?? '' }}</div>
            </div>
          </div>
        </div>

      </a>
    </div>
    @endif

  </section>


  {{-- ══ CONTENT GRID ══════════════════════════════════════════════════ --}}
  <div class="content-grid">

    {{-- LIST --}}
    <div>

      <div class="section-header">
        <h2 class="section-title">Latest Listings</h2>
        <span class="section-count">{{ $paginator->total() }} properties</span>
      </div>

      @foreach($listings as $the)
      @php
        $img    = $listingImg($the);
        $agt    = $agentImg($the);
        $price  = $priceLabel($the);
        $url    = "https://realtyrepublic.com/homedetails/{$the->url_slug}";
        $street = $the->xFullStreet;
        $city   = trim("{$the->xCity} {$the->xState} {$the->xxZip}");
      @endphp

      <a href="{{ $url }}" target="_blank" class="listing-card">

        @if($img)
          <img class="listing-thumb" src="{{ $img }}" alt="{{ $street }}">
        @else
          <div class="listing-thumb-placeholder">No Photo</div>
        @endif

        <div class="listing-info">
          <div class="listing-street">{{ $street }}</div>
          <div class="listing-city">{{ $city }}</div>
          @if($price)
            <div class="listing-price">{{ $price }}</div>
          @endif
          <div class="listing-agent">
            @if($agt)
              <img src="{{ $agt }}" alt="{{ $the->theAgent->agtFullName }}">
            @endif
            <div>
              <div class="listing-agent-name">{{ $the->theAgent->agtFullName }}</div>
              <div class="listing-agent-office">{{ $the->theOffice->officeName ?? '' }}</div>
            </div>
          </div>
        </div>

      </a>

      @endforeach

      <div class="pagination-wrap">
        {{ $paginator->withQueryString()->links() }}
      </div>

    </div>


    {{-- SIDEBAR --}}
    <aside class="sidebar">

      {{-- Free trial --}}
      <div class="sidebar-card">
        <div class="sidebar-card-title">Start for Free</div>
        <p>Create a professional listing flyer in minutes — no credit card required.</p>
        <input class="sidebar-input" type="email" placeholder="Your email address">
        <button class="sidebar-btn sidebar-btn-gold">Get Started Free</button>
        <div class="sidebar-divider">or</div>
        <button class="sidebar-btn">Sign In</button>
      </div>

      {{-- Quick search --}}
      <div class="sidebar-card">
        <div class="sidebar-card-title">Quick Search</div>
        <form method="GET" action="">
          <input class="sidebar-input" type="text" name="q" placeholder="City, zip code, or agent…" value="{{ $searchValue }}">
          <button type="submit" class="sidebar-btn">Search Listings</button>
        </form>
      </div>

      {{-- Stats --}}
      <div class="sidebar-card">
        <div class="sidebar-card-title">By the Numbers</div>
        <div class="stat-strip">
          <div class="stat-item">
            <div class="stat-num">{{ number_format($paginator->total()) }}</div>
            <div class="stat-label">Listings</div>
          </div>
          <div class="stat-item">
            <div class="stat-num">50+</div>
            <div class="stat-label">Markets</div>
          </div>
          <div class="stat-item">
            <div class="stat-num">24/7</div>
            <div class="stat-label">Live Data</div>
          </div>
          <div class="stat-item">
            <div class="stat-num">Free</div>
            <div class="stat-label">To Start</div>
          </div>
        </div>
      </div>

    </aside>

  </div>

</div>
</main>

@include('public.layout.footer')
</body>