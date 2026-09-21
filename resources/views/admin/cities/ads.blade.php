@include('public.layout.head')

<body data-section="admin" class="relative bg-white min-h-screen font-sans text-gray-800">

@include('admin.layout.nav')
@include('admin.layout.ui')

{{--
    Admin > Areas > Ads: the flyers marked as ads (propflyers.is_ad), each with a button to take the mark off.
    Controller: citiesController@ads / unmarkAd.
--}}
<main class="min-h-screen bg-[#f4f7fb] pt-24">
<div class="ui-wrap px-4 py-4 sm:px-6 lg:px-8">

    <div class="ui-top">
        <div class="ui-title">
            <h1>Areas</h1>
            <span>flyers marked as ads</span>
        </div>
    </div>

    @include('admin.cities._tabs', ['tabView' => 'ads'])

    @if(session('status'))
        <div class="ui-alert ok">{{ session('status') }}</div>
    @endif

    @if($errors->any())
        <div class="ui-alert bad">
            @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
        </div>
    @endif

    @if($flyers === null)

        <div class="ui-alert warn">
            The flyers table doesn't have the <code>is_ad</code> column yet, so no flyer can be marked as an ad. Run the SQL that adds it first.
        </div>

    @else

        <div class="ui-card">
            <div class="ui-card-h">
                <h2>Flyers marked as ads</h2>
                <p>
                    An ad is an advertisement, not a property, so it has no real city: the Areas tools ignore it and it never adds a city to the list.
                    "Not an ad" takes the mark off that one flyer, and its city counts again.
                </p>
            </div>

            <form method="GET" action="{{ route('admin.cities.ads') }}" class="ui-filters">
                <div class="ui-search" style="max-width:340px">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                    <input type="text" name="q" value="{{ $search }}" placeholder="Address, city, ZIP, agent or flyer #" aria-label="Search" autocomplete="off">
                </div>

                <button type="submit" class="ui-btn sm">Search</button>

                @if($search !== '')
                    <a href="{{ route('admin.cities.ads') }}" class="ui-btn sm">Clear</a>
                @endif

                <span class="ui-muted" style="font-size:13px">{{ number_format($flyers->total()) }} {{ $flyers->total() === 1 ? 'ad' : 'ads' }}</span>
            </form>

            <div class="ui-scroll">
                <table class="ui-table">
                    <thead>
                        <tr>
                            <th>Flyer</th>
                            <th>Address</th>
                            <th>City as typed</th>
                            <th>State</th>
                            <th>ZIP</th>
                            <th>Agent</th>
                            <th>Created</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($flyers as $f)
                            @php $created = $f->created_at ?: $f->creationDate; @endphp
                            <tr>
                                <td><a href="/admin/flyerCamps/{{ $f->id }}" style="color:#214e9b;font-weight:650">#{{ $f->id }}</a></td>
                                <td>{{ $f->xFullStreet ?: '—' }}</td>
                                <td>{!! trim((string) $f->xCity) !== '' ? e($f->xCity) : '<span class="ui-muted">&mdash;</span>' !!}</td>
                                <td>{{ trim((string) $f->xState) !== '' ? $f->xState : ($f->state ?: '—') }}</td>
                                <td>{{ $f->xZip ?: '—' }}</td>
                                <td>
                                    @if($f->propagent_id)
                                        <a href="/admin/agentView/{{ $f->propagent_id }}" style="color:#214e9b">{{ $f->agent_name ?: 'Agent #' . $f->propagent_id }}</a>
                                    @else
                                        <span class="ui-muted">&mdash;</span>
                                    @endif
                                </td>
                                <td style="white-space:nowrap">{{ $created ? \Carbon\Carbon::parse($created)->format('m/d/Y') : '—' }}</td>
                                <td class="actions">
                                    <form method="POST" action="{{ route('admin.flyers.unmarkAd', $f->id) }}"
                                          onsubmit="return confirm('Take the ad mark off flyer #{{ $f->id }}? Its city counts again.')">
                                        @csrf
                                        <button type="submit" class="ui-btn sm">Not an ad</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="ui-empty">
                                    @if($search !== '')
                                        No ads match. <a href="{{ route('admin.cities.ads') }}" style="color:#214e9b;font-weight:650">Clear the search</a>.
                                    @else
                                        No flyer is marked as an ad. Use "It's an ad" on a city's flyers page or the No state tab.
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

    @endif

</div>
</main>

@include('public.layout.footer')

</body>
</html>
