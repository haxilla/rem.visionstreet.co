@include('public.layout.head')

<body data-section="admin" class="relative bg-white min-h-screen font-sans text-gray-800">

@include('admin.layout.nav')
@include('admin.layout.ui')

{{--
    Admin > Areas > Needs review > a city: every flyer that uses it (to open and correct one by one), a mass
    fix for a misspelling, and a way to delete the entry itself. Controller: citiesController@fix / applyFixOne.
--}}
<main class="min-h-screen bg-[#f4f7fb] pt-24">
<div class="ui-wrap px-4 py-4 sm:px-6 lg:px-8">

    <div class="ui-top">
        <div class="ui-title">
            <h1>{{ $row->city }}, {{ $row->state }}</h1>
            <span>{{ number_format($flyerCount) }} {{ $flyerCount === 1 ? 'flyer' : 'flyers' }} use this</span>
        </div>

        <div style="margin-left:auto;display:flex;gap:8px;flex-wrap:wrap">
            <a href="{{ route('admin.cities', ['view' => 'review']) }}" class="ui-btn">&larr; Back to Needs review</a>

            <form method="POST" action="{{ route('admin.cities.destroy', $row->id) }}"
                  onsubmit="return confirm({{ \Illuminate\Support\Js::from('Delete ' . $row->city . ', ' . $row->state . ' from the list?' . ($flyerCount > 0 ? ' ' . $flyerCount . ' flyer(s) still use it, so it comes back the next time the flyers are checked unless you correct those flyers first.' : '')) }})">
                @csrf
                <button type="submit" class="ui-btn danger">Delete this entry</button>
            </form>
        </div>
    </div>

    @include('admin.cities._tabs', ['tabView' => 'review'])

    @if(session('status'))
        <div class="ui-alert ok">{{ session('status') }}</div>
    @endif

    @if($errors->any())
        <div class="ui-alert bad">
            @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
        </div>
    @endif

    {{-- THE FLYERS --}}
    <div class="ui-card">
        <div class="ui-card-h">
            <h2>Flyers that use "{{ $row->city }}" in {{ $row->state }}</h2>
            <p>
                Open a flyer to correct its city or state (a wrong state, or something typed that isn't a city). Once none is left, delete the entry
                with the button above - or use "Remove entries no flyer uses" on the Needs review list.
            </p>
        </div>

        @if($sample->isEmpty())
            <div class="ui-empty">No flyer uses this any more. It is safe to delete the entry.</div>
        @else
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
                            <th>Last sent</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($sample as $f)
                            @php $created = $f->created_at ?: $f->creationDate; @endphp
                            <tr>
                                <td>
                                    <a href="/admin/flyerCamps/{{ $f->id }}" style="color:#214e9b;font-weight:650">#{{ $f->id }}</a>
                                </td>
                                <td>{{ $f->xFullStreet ?: '—' }}</td>
                                <td>{{ $f->xCity }}</td>
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
                                <td style="white-space:nowrap">{!! $f->xLastDeliveryDate ? e(\Carbon\Carbon::parse($f->xLastDeliveryDate)->format('m/d/Y')) : '<span class="ui-muted">never</span>' !!}</td>
                                <td class="actions">
                                    @unless($f->deleted_at)
                                        <a href="/admin/flyerEdit/{{ $f->id }}" class="ui-btn sm primary" title="Opens the flyer as its agent, where the address can be edited">Edit flyer</a>

                                        <form method="POST" action="{{ route('admin.flyers.markAd', $f->id) }}"
                                              onsubmit="return confirm('Mark flyer #{{ $f->id }} as an ad? It stops counting for this list (only this flyer).')">
                                            @csrf
                                            <input type="hidden" name="city_id" value="{{ $row->id }}">
                                            <button type="submit" class="ui-btn sm" title="An ad, not a property: only THIS flyer is marked, and it stops adding a city to the list">It's an ad</button>
                                        </form>
                                    @endunless
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($sample->hasPages())
                <div style="padding:14px 16px;border-top:1px solid #eef1f6">
                    {{ $sample->links() }}
                </div>
            @endif
        @endif
    </div>

    {{-- MASS FIX for a misspelling --}}
    @if($flyerCount > 0)
        <div class="ui-card">
            <div class="ui-card-h">
                <h2>Or: it's a misspelling</h2>
                <p>Choose the city these flyers should have, and all {{ number_format($flyerCount) }} are changed at once. This entry then goes away.</p>
            </div>

            <div class="ui-card-b">
                @if(empty($choices))
                    <div class="ui-alert warn">There are no set-up cities in {{ $row->state }} to choose from yet. If "{{ $row->city }}" is a real city, use <a href="{{ route('admin.cities.edit', ['id' => $row->id, 'from' => 'review']) }}" style="color:#214e9b;font-weight:650">Set up</a> instead.</div>
                @else
                    <form method="POST" action="{{ route('admin.cities.fix.apply', $row->id) }}"
                          onsubmit="return confirm('Change the city on {{ $flyerCount }} flyer(s) to ' + this.to.value + '?')">
                        @csrf

                        <div class="ui-field" style="max-width:380px">
                            <label for="to">The flyers should say</label>
                            <select id="to" name="to" class="ui-select" style="width:100%" required>
                                <option value="">Choose the correct city&hellip;</option>
                                @foreach($choices as $city)
                                    <option value="{{ $city }}" @selected($suggestion && $suggestion['city'] === $city)>{{ $city }}, {{ $row->state }}</option>
                                @endforeach
                            </select>

                            @if($suggestion)
                                <div class="ui-help">Closest match: {{ $suggestion['city'] }}.</div>
                            @endif
                        </div>

                        <div class="ui-form-actions">
                            <button type="submit" class="ui-btn primary">Change {{ number_format($flyerCount) }} {{ $flyerCount === 1 ? 'flyer' : 'flyers' }}</button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    @endif

</div>
</main>

@include('public.layout.footer')

</body>
</html>
