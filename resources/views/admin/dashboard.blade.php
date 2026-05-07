@include('public.layout.head')

<body data-section="admin"
class="relative bg-white min-h-screen font-sans text-gray-800">

  @include('public.layout.nav')

  <main class="transition-all duration-300 min-h-screen pt-24 relative"
  :class="collapsed ? 'ml-20' : 'ml-64'">
    <div class="ml-8 mr-8 lg:ml-10 lg:mr-10">
      <div class="pageswap p-6 w-full">

      @php

    $waitingFlyerCamps    = $data['waitingFlyerCamps'] ?? collect();
    $inProgressFlyerCamps = $data['inProgressFlyerCamps'] ?? collect();
    $completeFlyerCamps   = $data['completeFlyerCamps'] ?? collect();

    $campaignsWaiting     = $data['campaignsWaiting'] ?? 0;
    $campaignsInProgress  = $data['campaignsInProgress'] ?? 0;
    $campaignsCompleted   = $data['campaignsCompleted'] ?? 0;

@endphp


<div class="min-h-screen bg-[#f4f7fb]">
<div class="flex min-h-screen">

    @include('admin.includes.sidebar')


    {{-- MAIN --}}
    <main class="flex-1 px-6 py-6 lg:px-10 lg:py-8">


        {{-- HEADER --}}
        <div class="rounded-[24px] bg-white px-8 py-7 shadow-[0_12px_35px_rgba(15,23,42,0.06)]">

            <div class="text-[12px] font-semibold uppercase tracking-[0.22em] text-[#214e9b]/70">
                Dashboard
            </div>

            <h1 class="mt-2 text-[32px] font-semibold text-slate-900">
                Campaign Overview
            </h1>

            <p class="mt-2 text-[14px] text-slate-600">
                Campaign status summary
            </p>

        </div>



        {{-- STATUS CARDS --}}
        <div class="mt-8 grid grid-cols-1 gap-6 xl:grid-cols-3">



            {{-- WAITING --}}
            <div class="rounded-[24px] bg-white p-6 shadow">

                <div class="flex justify-between">

                    <div>

                        <div class="text-xs uppercase tracking-wider text-amber-600">
                            Campaign Status
                        </div>

                        <div class="text-lg font-semibold">
                            Waiting
                        </div>

                    </div>

                    <span class="bg-amber-100 text-amber-700 text-xs px-3 py-1 rounded-full">
                        Waiting
                    </span>

                </div>


                <div class="mt-6 text-4xl font-semibold">
                    {{ $campaignsWaiting }}
                </div>


                <div class="mt-4 text-sm text-slate-500">
                    Flyer groups:
                    {{ $waitingFlyerCamps->count() }}
                </div>

            </div>




            {{-- IN PROGRESS --}}
            <div class="rounded-[24px] bg-white p-6 shadow">

                <div class="flex justify-between">

                    <div>

                        <div class="text-xs uppercase tracking-wider text-blue-700">
                            Campaign Status
                        </div>

                        <div class="text-lg font-semibold">
                            In Progress
                        </div>

                    </div>

                    <span class="bg-blue-100 text-blue-700 text-xs px-3 py-1 rounded-full">
                        Active
                    </span>

                </div>


                <div class="mt-6 text-4xl font-semibold">
                    {{ $campaignsInProgress }}
                </div>


                <div class="mt-4 text-sm text-slate-500">
                    Flyer groups:
                    {{ $inProgressFlyerCamps->count() }}
                </div>

            </div>




            {{-- COMPLETED --}}
            <div class="rounded-[24px] bg-white p-6 shadow">

                <div class="flex justify-between">

                    <div>

                        <div class="text-xs uppercase tracking-wider text-emerald-700">
                            Campaign Status
                        </div>

                        <div class="text-lg font-semibold">
                            Completed
                        </div>

                    </div>

                    <span class="bg-emerald-100 text-emerald-700 text-xs px-3 py-1 rounded-full">
                        Done
                    </span>

                </div>


                <div class="mt-6 text-4xl font-semibold">
                    {{ $campaignsCompleted }}
                </div>


                <div class="mt-4 text-sm text-slate-500">
                    Flyer groups:
                    {{ $completeFlyerCamps->count() }}
                </div>

            </div>



        </div>




        {{-- GROUP PREVIEW --}}
        <div class="mt-10 grid grid-cols-1 gap-6 xl:grid-cols-3">



            {{-- WAITING --}}
            <div class="bg-white rounded-xl p-5 shadow">

                <div class="font-semibold mb-3">
                    Waiting Groups
                </div>

                @foreach($waitingFlyerCamps->take(5) as $flyerId => $campaigns)

                    @php
                        $first = $campaigns->first();
                    @endphp

                    <div class="border rounded p-3 mb-2">

                        <div class="font-medium">
                            Flyer {{ $flyerId }}: {{ $first['address'] ?? 'No Address' }} 
                        </div>

                        <div class="text-sm text-gray-600">
                            {{ $first['campLabel'] ?? '' }}
                        </div>

                    </div>

                @endforeach

            </div>




            {{-- IN PROGRESS --}}
            <div class="bg-white rounded-xl p-5 shadow">

                <div class="font-semibold mb-3">
                    In Progress Groups
                </div>

                @foreach($inProgressFlyerCamps->take(5) as $flyerId => $campaigns)

                    @php
                        $first = $campaigns->first();
                    @endphp

                    <div class="border rounded p-3 mb-2">

                        <div class="font-medium">
                            Flyer {{ $flyerId }}: {{ $first['address'] ?? 'No Address' }}
                        </div>

                        <div class="text-sm text-gray-600">
                            {{ $first['campLabel'] ?? '' }}
                        </div>

                    </div>

                @endforeach

            </div>




            {{-- COMPLETED --}}
            <div class="bg-white rounded-xl p-5 shadow">

                <div class="font-semibold mb-3">
                    Completed Groups
                </div>

                @foreach($completeFlyerCamps->take(5) as $flyerId => $campaigns)

                    @php
                        $first = $campaigns->first();
                    @endphp

                    <div class="border rounded p-3 mb-2">

                        <div class="font-medium">
                            <a href="/flyer/{{ $flyerId }}">
                                Flyer {{ $flyerId }}: {{ $first['address'] ?? 'No Address' }}
                            </a>
                        </div>

                        <div class="text-sm text-gray-600">
                            {{ $first['campLabel'] ?? '' }}
                        </div>

                    </div>

                @endforeach

            </div>



        </div>



    </main>

</div>
</div>

      </div>
    </div>
  </main>

  @include('public.layout.footer')
</body>
</html>