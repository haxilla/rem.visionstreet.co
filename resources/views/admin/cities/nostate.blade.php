@include('public.layout.head')

<body data-section="admin" class="relative bg-white min-h-screen font-sans text-gray-800">

@include('admin.layout.nav')
@include('admin.layout.ui')

{{--
    Admin > Areas > No state: the flyers whose state is blank or "N0", so an admin can decide flyer by flyer
    whether to fix or delete them. Read-only. Controller: citiesController@noState.
--}}
<style>
    .ns-tiles { display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 12px; }
    .ns-tile  { border: 1px solid #e6eaf2; border-radius: 14px; padding: 12px 16px; background: #fff; }
    .ns-tile .k { font-size: 11.5px; font-weight: 800; letter-spacing: .05em; text-transform: uppercase; color: #64748b; }
    .ns-tile .v { margin-top: 4px; font-size: 24px; font-weight: 800; color: #0f172a; }
    .ns-tile .n { margin-top: 2px; font-size: 12px; color: #64748b; line-height: 1.35; }
    .ns-addr  { max-width: 260px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
</style>

@php
    $anyFilter = $search !== '' || array_filter($filters) !== [];
@endphp

<main class="min-h-screen bg-[#f4f7fb] pt-24">
<div class="ui-wrap px-4 py-4 sm:px-6 lg:px-8">

    <div class="ui-top">
        <div class="ui-title">
            <h1>Areas</h1>
            <span>flyers with no state</span>
        </div>
    </div>

    @include('admin.cities._tabs', ['tabView' => 'nostate'])

    {{-- WHAT THIS IS --}}
    <div class="ui-card">
        <div class="ui-card-h">
            <h2>Flyers with no usable state</h2>
            <p>
                The old system stored the state as <strong>N0</strong> whenever it couldn't read it. Without a state a flyer can't be looked up by
                city + state, so its send areas can't be worked out. Use this list to decide, flyer by flyer, whether each one should be fixed or deleted.
                Nothing on this page changes a flyer.
            </p>
        </div>

        <div class="ui-card-b">
            <div class="ns-tiles">
                <div class="ns-tile"><div class="k">No state</div><div class="v">{{ number_format($summary['total']) }}</div><div class="n">live flyers</div></div>
                <div class="ns-tile"><div class="k">Were sent</div><div class="v">{{ number_format($summary['sent']) }}</div><div class="n">have a delivery on record &mdash; worth fixing, not deleting</div></div>
                <div class="ns-tile"><div class="k">Never sent</div><div class="v">{{ number_format($summary['never_sent']) }}</div><div class="n">drafts &mdash; the likeliest to be deleted</div></div>
                <div class="ns-tile"><div class="k">No city either</div><div class="v">{{ number_format($summary['no_city']) }}</div><div class="n">nothing to go on</div></div>
                <div class="ns-tile"><div class="k">Has a typed state</div><div class="v">{{ number_format($summary['raw_state']) }}</div><div class="n">text in xState the clean-up couldn't read</div></div>
                <div class="ns-tile"><div class="k">Agent missing</div><div class="v">{{ number_format($summary['no_agent']) }}</div><div class="n">the account no longer exists</div></div>
            </div>
        </div>
    </div>

    {{-- SEARCH + FILTERS + CSV --}}
    <form method="GET" action="{{ route('admin.cities.noState') }}" class="ui-card" style="overflow:visible">
        <div class="ui-filters" style="border-bottom:0;background:#fff">
            <div class="ui-search" style="max-width:340px">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                <input type="text" name="q" value="{{ $search }}" placeholder="Address, city, ZIP, agent or flyer #" aria-label="Search" autocomplete="off">
            </div>

            <select name="sent" class="ui-select" aria-label="Sent" onchange="this.form.submit()">
                <option value="">Sent and never sent</option>
                <option value="sent" @selected($filters['sent'] === 'sent')>Were sent</option>
                <option value="never" @selected($filters['sent'] === 'never')>Never sent</option>
            </select>

            <select name="city" class="ui-select" aria-label="City" onchange="this.form.submit()">
                <option value="">With or without a city</option>
                <option value="has" @selected($filters['city'] === 'has')>Has a city</option>
                <option value="none" @selected($filters['city'] === 'none')>No city</option>
            </select>

            <button type="submit" class="ui-btn sm">Search</button>

            @if($anyFilter)
                <a href="{{ route('admin.cities.noState') }}" class="ui-btn sm">Clear</a>
            @endif

            <span class="ui-muted" style="font-size:13px">{{ number_format($flyers->total()) }} shown</span>

            <a href="{{ route('admin.cities.noState', array_filter(['q' => $search, 'sent' => $filters['sent'], 'city' => $filters['city'], 'export' => 'csv'])) }}"
               class="ui-btn sm" style="margin-left:auto">Download as CSV</a>
        </div>
    </form>

    {{-- THE LIST --}}
    <div class="ui-card">
        <div class="ui-scroll">
            <table class="ui-table">
                <thead>
                    <tr>
                        <th>Flyer</th>
                        <th>Address</th>
                        <th>City</th>
                        <th>ZIP</th>
                        <th>State as typed</th>
                        <th>Agent</th>
                        <th>Created</th>
                        <th>Last sent</th>
                        <th style="text-align:right">Views</th>
                        <th>Probably</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($flyers as $f)
                        @php
                            $guess   = \App\Support\NoStateFlyers::guess($f, $cityStates);
                            $created = $f->created_at ?: $f->creationDate;
                        @endphp

                        <tr>
                            <td><a href="/admin/flyerCamps/{{ $f->id }}" style="color:#214e9b;font-weight:650">#{{ $f->id }}</a></td>
                            <td class="ns-addr" title="{{ $f->xFullStreet }}">{{ $f->xFullStreet ?: '—' }}</td>
                            <td>{!! trim((string) $f->xCity) !== '' ? e($f->xCity) : '<span class="ui-muted">&mdash;</span>' !!}</td>
                            <td>{{ $f->xZip ?: '—' }}</td>
                            <td>{!! trim((string) $f->xState) !== '' ? e($f->xState) : '<span class="ui-muted">&mdash;</span>' !!}</td>
                            <td>
                                @if($f->agent_name !== null || $f->propagent_id)
                                    <a href="/admin/agentView/{{ $f->propagent_id }}" style="color:#214e9b">{{ $f->agent_name ?: 'Agent #' . $f->propagent_id }}</a>
                                @else
                                    <span class="ui-muted">&mdash;</span>
                                @endif
                            </td>
                            <td style="white-space:nowrap">{{ $created ? \Carbon\Carbon::parse($created)->format('m/d/Y') : '—' }}</td>
                            <td style="white-space:nowrap">{!! $f->xLastDeliveryDate ? e(\Carbon\Carbon::parse($f->xLastDeliveryDate)->format('m/d/Y')) : '<span class="ui-muted">never</span>' !!}</td>
                            <td style="text-align:right">{{ number_format((int) $f->xWebViews) }}</td>
                            <td>
                                @if($guess)
                                    <span class="ui-pill" title="from the {{ $guess[1] === 'zip' ? 'ZIP code' : 'city name' }}">{{ $guess[0] }} <span class="ui-muted" style="font-weight:600">({{ $guess[1] }})</span></span>
                                @else
                                    <span class="ui-muted">&mdash;</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="ui-empty">
                                @if($anyFilter)
                                    No flyers match. <a href="{{ route('admin.cities.noState') }}" style="color:#214e9b;font-weight:650">Clear the search and filters</a>.
                                @else
                                    Every flyer has a usable state.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($flyers->hasPages())
            <div style="padding:14px 16px;border-top:1px solid #eef1f6">
                {{ $flyers->links() }}
            </div>
        @endif
    </div>

</div>
</main>

@include('public.layout.footer')

</body>
</html>
