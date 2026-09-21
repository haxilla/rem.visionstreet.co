{{--
    The tabs across the top of the Areas pages: All cities | Needs review | No state.
    Needs: $tabView ('list', 'review' or 'nostate'). $total (the number of cities) is used when it is passed.
--}}
@php
    $tabView    = $tabView ?? 'list';
    $tabTotal   = $total ?? \App\Models\Core\PostalCity::count();
    $tabPending = \App\Support\AreaReview::pendingCount();
    $tabNoState = \App\Support\NoStateFlyers::count();
    $tabAds     = \App\Support\FlyerAd::count();
@endphp

<nav class="ui-chips" style="background:#fff;border-radius:14px;margin-bottom:12px;box-shadow:0 8px 28px rgba(15,23,42,.06)" aria-label="Areas views">
    <a href="{{ route('admin.cities') }}" class="ui-chip {{ $tabView === 'list' ? 'is-on' : '' }}">
        All cities <span class="ui-count">{{ number_format($tabTotal) }}</span>
    </a>

    <a href="{{ route('admin.cities', ['view' => 'review']) }}" class="ui-chip {{ $tabView === 'review' ? 'is-on' : '' }}">
        Needs review
        <span class="ui-count" @if($tabPending > 0 && $tabView !== 'review') style="background:#fef3c7;color:#92400e" @endif>{{ number_format($tabPending) }}</span>
    </a>

    <a href="{{ route('admin.cities.noState') }}" class="ui-chip {{ $tabView === 'nostate' ? 'is-on' : '' }}">
        No state
        <span class="ui-count" @if($tabNoState > 0 && $tabView !== 'nostate') style="background:#fee2e2;color:#991b1b" @endif>{{ number_format($tabNoState) }}</span>
    </a>

    <a href="{{ route('admin.cities.ads') }}" class="ui-chip {{ $tabView === 'ads' ? 'is-on' : '' }}">
        Ads <span class="ui-count">{{ number_format($tabAds) }}</span>
    </a>
</nav>

{{-- right after marking a flyer as an ad: one click to take it back --}}
@if(session('undo_ad'))
    <form method="POST" action="{{ route('admin.flyers.unmarkAd', session('undo_ad')) }}" class="ui-alert" style="display:flex;align-items:center;gap:12px;flex-wrap:wrap">
        @csrf
        <span>Marked the wrong flyer?</span>
        <button type="submit" class="ui-btn sm">Undo - flyer #{{ session('undo_ad') }} is not an ad</button>
    </form>
@endif
