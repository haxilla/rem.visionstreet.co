@include('public.layout.head')

<body data-section="admin" class="relative bg-white min-h-screen font-sans text-gray-800">

@include('admin.layout.nav')
@include('admin.layout.ui')

{{-- Edit one row of postal_cities. Controller: citiesController@edit / update. --}}
<main class="min-h-screen bg-[#f4f7fb] pt-24">
<div class="ui-wrap px-4 py-4 sm:px-6 lg:px-8" style="max-width:880px">

    <div class="ui-top">
        <div class="ui-title">
            <h1>Edit city</h1>
            <span>{{ $city->city }}, {{ $city->state }}</span>
        </div>

        <a href="{{ route('admin.cities', ['q' => $city->city]) }}" class="ui-btn">&larr; Back to Cities &amp; Areas</a>
    </div>

    @if($errors->any())
        <div class="ui-alert bad">
            @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
        </div>
    @endif

    <div class="ui-card">
        <div class="ui-card-h">
            <h2>{{ $city->city }}</h2>
            <p>Change any field and save.</p>
        </div>

        <form method="POST" action="{{ route('admin.cities.update', $city->id) }}" class="ui-card-b" autocomplete="off">
            @csrf

            @include('admin.cities._form', ['city' => $city])

            <div class="ui-form-actions">
                <button type="submit" class="ui-btn primary">Save changes</button>
                <a href="{{ route('admin.cities', ['q' => $city->city]) }}" class="ui-btn">Cancel</a>
            </div>
        </form>
    </div>

    <div class="ui-card">
        <div class="ui-card-h">
            <h2>Delete this city</h2>
            <p>Removes it from the list for good.</p>
        </div>

        <form method="POST" action="{{ route('admin.cities.destroy', $city->id) }}" class="ui-card-b"
              onsubmit="return confirm({{ \Illuminate\Support\Js::from('Delete ' . $city->city . ', ' . $city->state . ' from the list?') }})">
            @csrf
            <button type="submit" class="ui-btn danger">Delete {{ $city->city }}</button>
        </form>
    </div>

</div>
</main>

@include('public.layout.footer')

</body>
</html>
