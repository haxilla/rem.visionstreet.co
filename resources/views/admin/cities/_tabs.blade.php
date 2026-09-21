{{--
    The tabs across the top of the Areas pages: All cities | Needs review | No state.
    Needs: $tabView ('list', 'review' or 'nostate'). $total (the number of cities) is used when it is passed.
--}}
@php
    $tabView    = $tabView ?? 'list';
    $tabTotal   = $total ?? \App\Models\Core\PostalCity::count();
    $tabPending = \App\Support\AreaReview::pendingCount();
    $tabNoState = \App\Support\NoStateFlyers::count();
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
</nav>
