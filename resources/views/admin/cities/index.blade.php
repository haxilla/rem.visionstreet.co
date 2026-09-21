@include('public.layout.head')

<body data-section="admin" class="relative bg-white min-h-screen font-sans text-gray-800">

@include('admin.layout.nav')
@include('admin.layout.ui')

{{--
    Admin > Areas: search, view, add, edit and delete the rows of postal_cities
    (which region / sub-area / MLS each city belongs to). Controller: citiesController.
--}}
<main class="min-h-screen bg-[#f4f7fb] pt-24">
<div class="ui-wrap px-4 py-4 sm:px-6 lg:px-8">

    @if($missing)

        <div class="ui-top"><div class="ui-title"><h1>Areas</h1></div></div>

        <div class="ui-alert warn">
            The <code>postal_cities</code> table wasn't found in the database this site uses. Create it with the
            postal_cities SQL script, in the same database as the agents (or tell me which database it is in).
        </div>

    @else

        @php
            $anyFilter = $search !== '' || array_filter($filters) !== [];

            // a chip / filter link: keep everything else, change one thing, go back to page 1
            $to = fn (array $change) => request()->fullUrlWithQuery($change + ['page' => null]);

            $optionsOf = function (string $column) {
                return \App\Models\Core\PostalCity::whereNotNull($column)->where($column, '<>', '')->distinct()->orderBy($column)->pluck($column);
            };
        @endphp

        {{-- TITLE + SEARCH + ADD --}}
        <div class="ui-top">
            <div class="ui-title">
                <h1>Areas</h1>
                <span>{{ number_format($total) }} {{ $total === 1 ? 'city' : 'cities' }}</span>
            </div>

            <form method="GET" action="{{ route('admin.cities') }}" class="ui-tools" id="citySearch">
                <input type="hidden" name="region" value="{{ $filters['region'] }}">
                <input type="hidden" name="subregion" value="{{ $filters['subregion'] }}">
                <input type="hidden" name="mls" value="{{ $filters['mls'] }}">
                <input type="hidden" name="list" value="{{ $filters['list'] }}">
                <input type="hidden" name="state" value="{{ $filters['state'] }}">

                <div class="ui-search">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                    <input type="text" name="q" value="{{ $search }}" placeholder="Search a city, region, sub-area or MLS" aria-label="Search cities" autocomplete="off">
                </div>

                <button type="submit" class="ui-btn">Search</button>
                <button type="button" class="ui-btn primary" onclick="var d=document.getElementById('addCity'); d.open=true; document.getElementById('city').focus(); d.scrollIntoView({behavior:'smooth', block:'start'});">+ Add city</button>
            </form>
        </div>

        @if(session('status'))
            <div class="ui-alert ok">{{ session('status') }}</div>
        @endif

        @if($errors->any())
            <div class="ui-alert bad">
                @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
            </div>
        @endif

        {{-- ADD A CITY --}}
        <details id="addCity" class="ui-card ui-add" @if($errors->any() && old('_form') === 'add') open @endif>
            <summary class="ui-card-h">
                <h2>Add a city</h2>
                <p>A city can appear only once per state.</p>
            </summary>

            <form method="POST" action="{{ route('admin.cities.store') }}" class="ui-card-b" autocomplete="off">
                @csrf
                <input type="hidden" name="_form" value="add">

                @include('admin.cities._form', ['city' => null])

                <div class="ui-form-actions">
                    <button type="submit" class="ui-btn primary">Add city</button>
                </div>
            </form>
        </details>

        {{-- THE LIST --}}
        <div class="ui-card">

            {{-- region chips --}}
            <nav class="ui-chips" aria-label="Regions">
                <a href="{{ $to(['region' => null]) }}" class="ui-chip {{ $filters['region'] === '' ? 'is-on' : '' }}">
                    All <span class="ui-count">{{ number_format($total) }}</span>
                </a>

                @foreach($regions as $r)
                    @php $key = $r->region ?? '-'; @endphp
                    <a href="{{ $to(['region' => $key]) }}" class="ui-chip {{ $filters['region'] === $key ? 'is-on' : '' }}">
                        {{ $r->region ?? 'No region' }} <span class="ui-count">{{ number_format($r->n) }}</span>
                    </a>
                @endforeach
            </nav>

            {{-- more filters --}}
            <form method="GET" action="{{ route('admin.cities') }}" class="ui-filters">
                <input type="hidden" name="q" value="{{ $search }}">
                <input type="hidden" name="region" value="{{ $filters['region'] }}">

                <select name="state" class="ui-select" aria-label="State" onchange="this.form.submit()">
                    <option value="">All states</option>
                    @foreach($states as $abbr => $stateName)
                        <option value="{{ $abbr }}" @selected($filters['state'] === $abbr)>{{ $abbr }}</option>
                    @endforeach
                </select>

                <select name="subregion" class="ui-select" aria-label="Sub-area" onchange="this.form.submit()">
                    <option value="">All sub-areas</option>
                    <option value="-" @selected($filters['subregion'] === '-')>No sub-area</option>
                    @foreach($optionsOf('subregion') as $v)
                        <option value="{{ $v }}" @selected($filters['subregion'] === $v)>{{ $v }}</option>
                    @endforeach
                </select>

                <select name="mls" class="ui-select" aria-label="MLS" onchange="this.form.submit()">
                    <option value="">All MLSs</option>
                    <option value="-" @selected($filters['mls'] === '-')>No MLS</option>
                    @foreach($optionsOf('mls_system') as $v)
                        <option value="{{ $v }}" @selected($filters['mls'] === $v)>{{ $v }}</option>
                    @endforeach
                </select>

                @if($hasLocal)
                    <select name="list" class="ui-select" aria-label="Local list" onchange="this.form.submit()">
                        <option value="">All local lists</option>
                        <option value="-" @selected($filters['list'] === '-')>No local list</option>
                        @foreach($optionsOf('local_list') as $v)
                            <option value="{{ $v }}" @selected($filters['list'] === $v)>{{ $v }}</option>
                        @endforeach
                    </select>
                @endif

                @if($anyFilter)
                    <a href="{{ route('admin.cities') }}" class="ui-btn sm">Clear filters</a>
                @endif

                <span class="ui-muted" style="margin-left:auto;font-size:13px">
                    {{ number_format($cities->total()) }} shown
                </span>
            </form>

            <div class="ui-scroll">
                <table class="ui-table">
                    <thead>
                        <tr>
                            <th>City</th>
                            <th>State</th>
                            <th>Region</th>
                            <th>Sub-area</th>
                            <th>MLS</th>
                            @if($hasLocal)<th>Local list</th>@endif
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($cities as $c)
                            <tr>
                                <td style="font-weight:650;color:#0f172a">{{ $c->city }}</td>
                                <td>{{ $c->state }}</td>
                                <td>
                                    @if($c->region)
                                        <span class="ui-pill {{ $c->region }}">{{ $c->region }}</span>
                                    @else
                                        <span class="ui-muted">&mdash;</span>
                                    @endif
                                </td>
                                <td>{!! $c->subregion ? e($c->subregion) : '<span class="ui-muted">&mdash;</span>' !!}</td>
                                <td>{!! $c->mls_system ? e($c->mls_system) : '<span class="ui-muted">&mdash;</span>' !!}</td>
                                @if($hasLocal)
                                    <td>
                                        @if($c->local_list)
                                            <span class="ui-pill" style="font-family:ui-monospace,SFMono-Regular,Menlo,monospace">{{ $c->local_list }}</span>
                                        @else
                                            <span class="ui-muted">&mdash;</span>
                                        @endif
                                    </td>
                                @endif
                                <td class="actions">
                                    <a href="{{ route('admin.cities.edit', $c->id) }}" class="ui-btn sm">Edit</a>

                                    <form method="POST" action="{{ route('admin.cities.destroy', $c->id) }}"
                                          onsubmit="return confirm({{ \Illuminate\Support\Js::from('Delete ' . $c->city . ', ' . $c->state . ' from the list?') }})">
                                        @csrf
                                        <button type="submit" class="ui-btn sm danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $hasLocal ? 7 : 6 }}" class="ui-empty">
                                    @if($anyFilter)
                                        No cities match. <a href="{{ route('admin.cities') }}" style="color:#214e9b;font-weight:650">Clear the search and filters</a>.
                                    @else
                                        The list is empty. Add the first city above.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($cities->hasPages())
                <div style="padding:14px 16px;border-top:1px solid #eef1f6">
                    {{ $cities->links() }}
                </div>
            @endif
        </div>

    @endif

</div>
</main>

@include('public.layout.footer')

</body>
</html>
