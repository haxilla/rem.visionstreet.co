{{-- An agent's own page, at /{agent_slug}: their contact details and the listings they have finished.
     Only reachable through guestController::agentPage (this file is also reachable by URL through
     the /{segment} convention, where none of the variables exist). --}}
@php abort_unless(isset($agent, $listings), 404); @endphp
@php
    $pageTitle = trim($agent->agtFullName . ' - ' . ($office->officeName ?? 'Realty Emails'), ' -');

    $phone      = trim((string) $agent->agtMainPhone);
    $phoneHref  = preg_replace('/[^0-9+]/', '', $phone);
    $email      = trim((string) $agent->agtEmail);
    $designations = trim((string) $agent->agtDesigs);

    $officeLines = collect([
        $office->officeAddress1 ?? null,
        $office->officeAddress2 ?? null,
    ])->filter()->all();

    $officeCity = trim(($office->officeCity ?? '') . (($office->officeCity ?? '') !== '' ? ', ' : '') . ($office->officeState ?? '') . ' ' . ($office->officeZip ?? ''));
@endphp
<!DOCTYPE html>
<html lang="en" class="h-full bg-white text-gray-900">
@include('public.layout.head')

<body class="min-h-screen bg-[#f4f5f8] pt-[50px]">
    @include('public.layout.nav')

    <main class="mx-auto max-w-[1100px] px-4 py-8 sm:px-6 lg:py-12">

        {{-- THE AGENT --}}
        <section class="overflow-hidden rounded-[28px] bg-white shadow-[0_18px_50px_rgba(26,43,89,.10)]">
            <div class="flex flex-col gap-6 p-6 sm:flex-row sm:items-center sm:p-10">

                @if($photo['url'])
                    <img src="{{ $photo['url'] }}" alt="{{ $agent->agtFullName }}"
                         class="h-40 w-40 shrink-0 self-center rounded-2xl object-cover shadow-md sm:h-48 sm:w-48">
                @endif

                <div class="min-w-0 flex-1">
                    <h1 class="font-display text-3xl font-medium leading-tight text-[#1f2f57] sm:text-4xl">
                        {{ $agent->agtFullName }}
                    </h1>

                    @if($designations !== '')
                        <p class="mt-1 text-sm font-medium text-slate-500">{{ $designations }}</p>
                    @endif

                    @if(!empty($office?->officeName))
                        <p class="mt-3 text-lg font-semibold text-slate-800">{{ $office->officeName }}</p>
                    @endif

                    @if($officeLines || $officeCity !== '')
                        <p class="mt-1 text-sm leading-relaxed text-slate-600">
                            @foreach($officeLines as $line)
                                {{ $line }}<br>
                            @endforeach
                            {{ $officeCity }}
                        </p>
                    @endif

                    <div class="mt-5 flex flex-wrap gap-3">
                        @if($phone !== '' && $phoneHref !== '')
                            <a href="tel:{{ $phoneHref }}"
                               class="rounded-xl bg-[#1f2f57] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#182545]">
                                Call {{ $phone }}
                            </a>
                        @endif

                        @if($email !== '')
                            <a href="mailto:{{ $email }}"
                               class="rounded-xl border border-[#1f2f57] px-5 py-2.5 text-sm font-semibold text-[#1f2f57] hover:bg-slate-50">
                                Email me
                            </a>
                        @endif
                    </div>
                </div>

                @if($logo['url'])
                    <img src="{{ $logo['url'] }}" alt="{{ $office->officeName ?? '' }}"
                         class="h-auto max-h-24 w-auto max-w-[180px] shrink-0 self-center object-contain">
                @endif

            </div>
        </section>

        {{-- THE LISTINGS --}}
        <section class="mt-10">
            <h2 class="font-display text-2xl font-medium text-[#1f2f57]">
                Listings
                @if($listings->isNotEmpty())
                    <span class="ml-1 text-base font-normal text-slate-500">({{ $listings->count() }})</span>
                @endif
            </h2>

            @if($listings->isEmpty())
                <p class="mt-4 rounded-2xl bg-white p-8 text-center text-slate-500 shadow-sm">
                    No listings to show yet.
                </p>
            @else
                <div class="mt-5 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($listings as $flyer)
                        @php
                            $cover = $flyer->thePhotos->first();
                            $thumb = ($cover && $flyer->theMeta && $flyer->theMeta->zipDir && $flyer->theMeta->mlsDir)
                                ? "/hqphotos/{$flyer->theMeta->zipDir}/{$flyer->theMeta->mlsDir}/{$cover->photoName}"
                                : null;

                            $beds  = $flyer->xxBeds ?: $flyer->xBeds;
                            $baths = $flyer->xxBaths ?: $flyer->xBaths;
                            $sqft  = $flyer->xxSqft ?: $flyer->xSqft;
                        @endphp

                        <a href="/homedetails/{{ $flyer->url_slug }}"
                           class="group overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 transition hover:shadow-lg">
                            <div class="aspect-[4/3] w-full overflow-hidden bg-slate-100">
                                @if($thumb)
                                    <img src="{{ $thumb }}" alt="{{ $flyer->xFullStreet }}" loading="lazy"
                                         class="h-full w-full object-cover transition group-hover:scale-105">
                                @endif
                            </div>

                            <div class="p-4">
                                @if($flyer->xListPrice)
                                    <div class="text-xl font-semibold text-[#1f2f57]">${{ number_format($flyer->xListPrice) }}</div>
                                @endif

                                <div class="mt-1 font-medium text-slate-800">{{ $flyer->xFullStreet }}</div>
                                <div class="text-sm text-slate-500">{{ trim($flyer->xCity . ', ' . $flyer->state . ' ' . $flyer->xZip) }}</div>

                                @if($beds || $baths || $sqft)
                                    <div class="mt-2 flex flex-wrap gap-x-3 text-sm font-medium text-slate-600">
                                        @if($beds)<span>{{ $beds }} bd</span>@endif
                                        @if($baths)<span>{{ $baths }} ba</span>@endif
                                        @if($sqft)<span>{{ number_format((int) $sqft) }} sqft</span>@endif
                                    </div>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>

    </main>

    @include('public.layout.footer')
</body>
</html>
