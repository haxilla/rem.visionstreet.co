@include('public.layout.head')

<body data-section="admin" class="relative bg-white min-h-screen font-sans text-gray-800">

@include('admin.layout.nav')
@include('admin.layout.ui')

{{--
    Admin > Areas > Needs review > Fix flyers: a city that flyers spelled wrong. Choose the right city and every
    flyer with the wrong one is changed. Controller: citiesController@fix / applyFixOne.
--}}
<main class="min-h-screen bg-[#f4f7fb] pt-24">
<div class="ui-wrap px-4 py-4 sm:px-6 lg:px-8">

    <div class="ui-top">
        <div class="ui-title">
            <h1>Fix flyers</h1>
            <span>{{ $row->city }}, {{ $row->state }}</span>
        </div>

        <a href="{{ route('admin.cities', ['view' => 'review']) }}" class="ui-btn" style="margin-left:auto">&larr; Back to Needs review</a>
    </div>

    @include('admin.cities._tabs', ['tabView' => 'review'])

    @if($errors->any())
        <div class="ui-alert bad">
            @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
        </div>
    @endif

    <div class="ui-card">
        <div class="ui-card-h">
            <h2>{{ number_format($flyerCount) }} {{ $flyerCount === 1 ? 'flyer has' : 'flyers have' }} the city "{{ $row->city }}" in {{ $row->state }}</h2>
            <p>Choose the city they should have. Every one of those flyers is changed, and "{{ $row->city }}" then leaves the Needs review list.</p>
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
                        <a href="{{ route('admin.cities', ['view' => 'review']) }}" class="ui-btn">Cancel</a>
                    </div>
                </form>
            @endif
        </div>
    </div>

    @if($sample->isNotEmpty())
        <div class="ui-card">
            <div class="ui-card-h">
                <h2>{{ $flyerCount > $sample->count() ? 'The newest ' . $sample->count() : 'These flyers' }}</h2>
                <p>Open one to see it, or to fix a flyer by itself (for example when the city is really a different place).</p>
            </div>

            <div class="ui-scroll">
                <table class="ui-table">
                    <thead>
                        <tr><th>Flyer</th><th>Address</th><th>City as typed</th><th>ZIP</th><th>Agent</th></tr>
                    </thead>
                    <tbody>
                        @foreach($sample as $f)
                            <tr>
                                <td><a href="/admin/flyerCamps/{{ $f->id }}" style="color:#214e9b;font-weight:650">#{{ $f->id }}</a></td>
                                <td>{{ $f->xFullStreet ?: '—' }}</td>
                                <td>{{ $f->xCity }}</td>
                                <td>{{ $f->xZip ?: '—' }}</td>
                                <td>
                                    @if($f->propagent_id)
                                        <a href="/admin/agentView/{{ $f->propagent_id }}" style="color:#214e9b">{{ $f->agent_name ?: 'Agent #' . $f->propagent_id }}</a>
                                    @else
                                        <span class="ui-muted">&mdash;</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>
</main>

@include('public.layout.footer')

</body>
</html>
