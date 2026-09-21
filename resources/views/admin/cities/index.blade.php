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
            $view         = $view ?? 'list';
            $search       = $search ?? '';
            $filters      = $filters ?? ['state' => '', 'region' => '', 'subregion' => '', 'mls' => '', 'list' => ''];
            $hasLocal     = $hasLocal ?? false;
            $pendingCount = $pendingCount ?? 0;

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

            @if($view === 'list')
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
            @endif

            {{-- compares every flyer's city + state with this list and adds the missing ones for review --}}
            <form method="POST" action="{{ route('admin.cities.sync') }}" style="margin-left:auto">
                @csrf
                <button type="submit" class="ui-btn" title="Adds any city that flyers use but this list doesn't have yet, waiting for a region">Check flyers for new cities</button>
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

        {{-- ALL CITIES / NEEDS REVIEW / NO STATE --}}
        @include('admin.cities._tabs', ['tabView' => $view])

        @if($view === 'list')

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
                    <select name="list" class="ui-select" aria-label="Distro list" onchange="this.form.submit()">
                        <option value="">All distro lists</option>
                        <option value="-" @selected($filters['list'] === '-')>No distro list</option>
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
                            @if($hasLocal)<th>Distro list</th>@endif
                            <th>MLS</th>
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
                                @if($hasLocal)
                                    <td>
                                        @if($c->local_list)
                                            <span class="ui-pill" style="font-family:ui-monospace,SFMono-Regular,Menlo,monospace">{{ $c->local_list }}</span>
                                        @else
                                            <span class="ui-muted">&mdash;</span>
                                        @endif
                                    </td>
                                @endif
                                <td>{!! $c->mls_system ? e($c->mls_system) : '<span class="ui-muted">&mdash;</span>' !!}</td>
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

        @else

        {{-- What "Check flyers for new cities" just looked at --}}
        @if($report = session('sync_report'))
            <div class="ui-card">
                <div class="ui-card-h">
                    <h2>{{ $report['added'] > 0 ? number_format($report['added']) . ' new ' . ($report['added'] === 1 ? 'city' : 'cities') . ' added for review' : 'No new cities found' }}</h2>
                    <p>
                        Checked {{ number_format($report['flyers']) }} flyers in {{ number_format($report['pairs']) }} different city + state
                        combinations: {{ number_format($report['known']) }} already in the list,
                        {{ number_format($report['added']) }} added,
                        {{ number_format(count($report['unusable'])) }} could not be added.
                    </p>
                </div>

                <div class="ui-card-b">
                    <div style="font-size:13px;color:#475569;line-height:1.7">
                        <strong>States on your flyers:</strong>
                        @foreach($report['states'] as $st => $n)
                            <span class="ui-pill" style="margin:0 4px 4px 0">{{ $st }} &middot; {{ number_format($n) }}</span>
                        @endforeach
                    </div>

                    @if(count($report['unusable']) > 0)
                        <div style="margin-top:14px;font-size:13px;font-weight:700;color:#92400e">
                            Could not be added &mdash; these flyers have no usable state (the old system stored "N0"). Ordered by how many flyers use each city:
                        </div>
                        <div class="ui-scroll" style="margin-top:6px">
                            <table class="ui-table">
                                <thead><tr><th>City</th><th>State as stored</th><th>Flyers</th><th>Probably</th><th>Why</th></tr></thead>
                                <tbody>
                                    @foreach(array_slice($report['unusable'], 0, 25) as $u)
                                        <tr>
                                            <td>{{ $u['city'] ?: '(blank)' }}</td>
                                            <td>{{ $u['state'] }}</td>
                                            <td>{{ number_format($u['flyers']) }}</td>
                                            <td>{!! !empty($u['guess']) ? '<strong>' . e($u['guess']) . '</strong>' : '<span class="ui-muted">&mdash;</span>' !!}</td>
                                            <td>{{ $u['why'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- NEEDS REVIEW: the cities that have no region yet --}}
        <div class="ui-card">
            <div class="ui-card-h">
                <h2>Cities that need a region</h2>
                <p>Each was added from a flyer with just a city and state. Choose its region (and, where it applies, its sub-area and distro list) to clear it from this list.</p>
            </div>

            @if($review->isEmpty())
                <div class="ui-empty">Nothing needs review &mdash; every city in the list has a region.</div>
            @else
                @php $suggestions = $suggestions ?? []; $withSuggestion = collect($suggestions)->filter()->count(); @endphp

                {{-- the mass fix: the ticked cities are misspellings, so the FLYERS get the right city (no page nesting: the boxes and button point at this form) --}}
                <form id="fixForm" method="POST" action="{{ route('admin.cities.fixFlyers') }}"
                      onsubmit="var n=document.querySelectorAll('.fixBox:checked').length; if(!n){alert('Tick the cities to fix first.');return false;} return confirm('Change the city on the flyers of '+n+' ticked '+(n==1?'city':'cities')+' to the spelling shown? This changes the flyers themselves.');">
                    @csrf
                </form>

                <form id="unusedForm" method="POST" action="{{ route('admin.cities.removeUnused') }}" onsubmit="return confirm('Delete every city on this list that no flyer uses?')">@csrf</form>

                <div class="ui-filters">
                    <button type="submit" form="fixForm" class="ui-btn sm primary">Fix flyers for ticked cities</button>
                    <button type="button" class="ui-btn sm" onclick="document.querySelectorAll('.fixBox').forEach(function(b){b.checked=true})">Tick every match ({{ $withSuggestion }})</button>
                    <button type="button" class="ui-btn sm" onclick="document.querySelectorAll('.fixBox').forEach(function(b){b.checked=false})">Untick all</button>
                    <button type="submit" form="unusedForm" class="ui-btn sm" title="Deletes the entries that no flyer uses any more - what is left after you correct flyers by hand">Remove entries no flyer uses</button>
                    <span class="ui-muted" style="font-size:13px">"Should be" is the known city in the same state that each one is closest to. Check them, then fix the flyers - the misspelled city then leaves this list.</span>
                </div>

                <div class="ui-scroll">
                    <table class="ui-table">
                        <thead>
                            <tr>
                                <th style="width:34px"></th>
                                <th>City</th>
                                <th>State</th>
                                <th>Flyers</th>
                                <th>Should be</th>
                                <th>Newest flyer</th>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($review as $r)
                                @php $sg = $suggestions[$r->id] ?? null; @endphp
                                <tr>
                                    <td>
                                        @if($sg)
                                            <input type="checkbox" class="fixBox" name="ids[]" value="{{ $r->id }}" form="fixForm" aria-label="Fix the flyers of {{ $r->city }}">
                                        @endif
                                    </td>
                                    <td style="font-weight:650"><a href="{{ route('admin.cities.fix', $r->id) }}" style="color:#0f172a" title="See the flyers that use this">{{ $r->city }}</a></td>
                                    <td>{{ $r->state }}</td>
                                    <td><a href="{{ route('admin.cities.fix', $r->id) }}" style="color:#214e9b;font-weight:650" title="See the flyers that use this">{{ number_format($r->flyers) }}</a></td>
                                    <td>
                                        @if($sg)
                                            <span class="ui-pill" style="{{ $sg['how'] === 'spelling' ? 'background:#dcfce7;color:#166534' : 'background:#fef3c7;color:#92400e' }}"
                                                  title="{{ $sg['how'] === 'spelling' ? 'Only punctuation or capitals differ' : 'A close spelling - check it' }}">{{ $sg['city'] }}</span>
                                        @else
                                            <span class="ui-muted">&mdash;</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($r->last_flyer)
                                            <a href="/admin/flyerCamps/{{ $r->last_flyer }}" style="color:#214e9b;font-weight:650">#{{ $r->last_flyer }}</a>
                                        @else
                                            <span class="ui-muted">&mdash;</span>
                                        @endif
                                    </td>
                                    <td class="actions">
                                        <a href="{{ route('admin.cities.edit', ['id' => $r->id, 'from' => 'review']) }}" class="ui-btn sm primary" title="It is a real city - give it a region">Set up</a>
                                        <a href="{{ route('admin.cities.fix', $r->id) }}" class="ui-btn sm" title="It is a misspelling - correct the flyers">Fix flyers</a>

                                        <form method="POST" action="{{ route('admin.cities.destroy', $r->id) }}"
                                              onsubmit="return confirm({{ \Illuminate\Support\Js::from('Delete ' . $r->city . ', ' . $r->state . ' from the list? If a flyer still uses it, it will come back the next time the flyers are checked - fix the flyer\'s city instead.') }})">
                                            @csrf
                                            <button type="submit" class="ui-btn sm danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        @endif

    @endif

</div>
</main>

@include('public.layout.footer')

</body>
</html>
