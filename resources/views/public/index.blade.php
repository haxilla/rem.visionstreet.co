{{-- resources/views/home/hero.blade.php --}}
<!DOCTYPE html>
<html lang="en" class="h-full bg-white text-gray-900">
@include('public.layout.head');

<body class="min-h-screen bg-white pt-[50px]">
  @include('public.layout.nav')
  <section>
    @include('public.includes.hero_card')
  </section>
  <section>
    @include('public.includes.features_section')
  </section>
  <section class="bg-[#f5f5f7] py-12 lg:py-16">
    @include('public.includes.top_views_3')
  </section>
  @php
    $brandName   = $brandName   ?? 'RealtyEmails';
    $headline    = $headline    ?? 'The Agent Who Markets More Gets Seen More';
    $subLines    = $subLines    ?? [
        'RealtyEmails has helped agents promote listings proactively since 2006.',
        'Do more than just MLS and wait.',
        'Put your property in front of more agents, more buyers, and more opportunity.',
    ];
    $ctaText     = $ctaText     ?? 'Create FREE Flyer!';
    $heroMinH    = $heroMinH    ?? 'min-h-[560px]';

    $tilePattern = [
        'tile-lg-portrait',
        'tile-square',
        'tile-square',
        'tile-square',
        'tile-portrait',
        'tile-wide',
        'tile-square',
        'tile-square',
        'tile-portrait',
        'tile-square',
        'tile-square',
        'tile-lg-portrait',
    ];
@endphp

<div class="w-full bg-white px-4 sm:px-6 lg:px-8 py-6 lg:py-10">
    <div class="mx-auto w-full max-w-screen-2xl" style="max-width: 1600px;">
        <div class="grid grid-cols-1 lg:grid-cols-[430px_minmax(0,1fr)] {{ $heroMinH }} overflow-hidden rounded-2xl bg-white shadow-lg ring-1 ring-black/10">

            {{-- LEFT: Agent mosaic --}}
            <div class="relative overflow-hidden bg-[#18315f]">
                <div class="absolute inset-0 bg-gradient-to-b from-[#274a86]/40 via-transparent to-[#0f2347]/40"></div>

                <div class="relative h-full p-3 sm:p-4 lg:p-5">
                    <div class="agent-mosaic-luxury max-w-none">
                        @foreach($memberSince as $index => $agent)
                            @php
                                $tileClass = $tilePattern[$index % count($tilePattern)];

                                $folder = $agent->theAgentMeta->newRemID
                                    ?? $agent->theAgentCleanup->newRemID
                                    ?? null;

                                $photo = ($folder && !empty($agent->agtPhoto))
                                    ? "https://realtyrepublic.com/agentPhotos/{$folder}/{$agent->agtPhoto}"
                                    : null;
                            @endphp

                            @if($photo)
                                <div class="agent-tile {{ $tileClass }}">
                                    <img
                                        src="{{ $photo }}"
                                        alt="{{ $agent->agtFullName }}"
                                        loading="lazy"
                                        decoding="async"
                                    >
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- RIGHT: Brand message --}}
            <div class="relative flex items-center overflow-hidden bg-gradient-to-b from-[#2f4f7f] via-[#2b4275] to-[#1c2f57] px-8 py-14 sm:px-10 lg:px-14 text-white">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,.12),transparent_32%)]"></div>
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_bottom_left,rgba(255,255,255,.08),transparent_26%)]"></div>

                <div class="relative w-full max-w-2xl">

                    {{-- Kicker --}}
                    <div class="inline-flex items-center rounded-full border border-white/15 bg-white/10 px-4 py-1.5 text-[11px] sm:text-[12px] font-semibold tracking-[0.18em] uppercase backdrop-blur-sm">
                        RealtyEmails Since 2006
                    </div>

                    {{-- Headline --}}
                    <h1 class="font-display mt-6 text-[36px] sm:text-[48px] lg:text-[56px] font-medium tracking-tight leading-[1.02]">
                        {!! $headline !!}
                    </h1>

                    {{-- Supporting copy --}}
                    <div class="mt-7 max-w-xl space-y-3 text-[15px] sm:text-[17px] leading-relaxed text-white/88">
                        @foreach($subLines as $line)
                            <div>{{ $line }}</div>
                        @endforeach
                    </div>

                    {{-- Value points --}}
                    <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-3 max-w-2xl text-[14px] sm:text-[15px]">
                        <div class="rounded-xl border border-white/12 bg-white/8 px-4 py-3 backdrop-blur-sm">
                            Promote listings beyond passive MLS exposure
                        </div>
                        <div class="rounded-xl border border-white/12 bg-white/8 px-4 py-3 backdrop-blur-sm">
                            Reach thousands of local real estate agents
                        </div>
                        <div class="rounded-xl border border-white/12 bg-white/8 px-4 py-3 backdrop-blur-sm">
                            Generate flyers, landing pages, and print-ready marketing
                        </div>
                        <div class="rounded-xl border border-white/12 bg-white/8 px-4 py-3 backdrop-blur-sm">
                            Stand out as the agent who advertises proactively
                        </div>
                    </div>

                    {{-- CTA row --}}
                    <div class="mt-10 flex flex-col sm:flex-row sm:items-center gap-4">
                        <a
                            class="inline-flex items-center justify-center rounded-full border border-white/20 bg-white px-8 py-3 text-[13px] font-semibold tracking-wide text-[#1d315d] shadow-sm hover:bg-white/90"
                        >
                            {{ $ctaText }}
                        </a>

                        <div class="text-[13px] text-white/75">
                            Free flyer • Free website • No credit card required
                        </div>
                    </div>

                    {{-- Bottom proof line --}}
                    <div class="mt-10 border-t border-white/12 pt-5 text-[14px] sm:text-[15px] text-white/82">
                        The average agent waits for attention. RealtyEmails helps you go after it.
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>





{{-- WIDE PROMO + FORM SECTION --}}
<section class="w-full py-12 lg:py-16">
    <div class="mx-auto w-full max-w-screen-2xl px-4 sm:px-6 lg:px-10" style="max-width: 1600px;">

        <div class="relative overflow-hidden rounded-[34px] shadow-[0_22px_55px_rgba(17,31,61,.20)]">

            {{-- background image --}}
            <div
                class="absolute inset-0 bg-cover bg-center"
                style="background-image: url('{{ asset('images/twilight_realty_emails_promo.jpg') }}');"
            ></div>

            {{-- dark luxury overlay --}}
            <div class="absolute inset-0 bg-[linear-gradient(90deg,rgba(9,18,43,.88)_0%,rgba(16,31,67,.78)_34%,rgba(20,36,72,.72)_58%,rgba(11,20,44,.86)_100%)]"></div>

            {{-- soft tone overlay --}}
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(255,255,255,.09),transparent_24%),radial-gradient(circle_at_bottom_right,rgba(255,255,255,.06),transparent_20%)]"></div>

            <div class="relative z-10 grid grid-cols-1 xl:grid-cols-[1.2fr_.8fr] gap-8 xl:gap-10 px-6 py-8 sm:px-8 sm:py-10 lg:px-12 lg:py-14">

                {{-- LEFT SIDE --}}
                <div class="flex min-h-full items-center">
                    <div class="max-w-[720px]">
                        <div class="inline-flex items-center rounded-full border border-white/15 bg-white/10 px-4 py-1.5 text-[12px] font-semibold uppercase tracking-[0.16em] text-white/82 backdrop-blur-sm">
                            Realty Emails Promotion
                        </div>

                        <h3 class="font-display mt-6 text-[36px] sm:text-[48px] lg:text-[62px] leading-[0.96] tracking-tight text-white">
                            One Price.<br>
                            All Your Listings.
                        </h3>

                        <div class="mt-5 text-[24px] sm:text-[30px] lg:text-[34px] font-medium leading-tight text-[#f0d28a]">
                            3 Months for $99
                        </div>

                        <p class="mt-6 max-w-[640px] text-[16px] sm:text-[18px] lg:text-[19px] leading-8 text-white/88">
                            Promote every listing you have for one low price. Each property may be sent once, with free resends available after 30 days upon request.
                        </p>

                        <div class="mt-8 flex flex-wrap gap-x-6 gap-y-3 text-[15px] sm:text-[16px] text-white/82">
                            <div class="flex items-center gap-3">
                                <span class="h-2.5 w-2.5 rounded-full bg-[#f0d28a]"></span>
                                <span>No extra fee per listing</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="h-2.5 w-2.5 rounded-full bg-[#f0d28a]"></span>
                                <span>Luxury presentation feel</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="h-2.5 w-2.5 rounded-full bg-[#f0d28a]"></span>
                                <span>Designed for visibility</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RIGHT SIDE FORM --}}
                <div class="flex items-center xl:justify-end">
                    <div class="w-full max-w-[470px] rounded-[30px] border border-white/14 bg-[rgba(10,20,44,.62)] p-5 sm:p-7 backdrop-blur-md shadow-[0_18px_45px_rgba(0,0,0,.24)]">
                        <div class="text-[12px] font-semibold uppercase tracking-[0.16em] text-white/65">
                            Get Started
                        </div>

                        <div class="mt-2 text-[24px] sm:text-[28px] font-semibold leading-tight text-white">
                            Submit your listing details.
                        </div>

                        <p class="mt-2 text-[14px] sm:text-[15px] leading-7 text-white/74">
                            Fill out the form and we’ll review your property information.
                        </p>

                        <form class="mt-6 grid grid-cols-1 gap-4" method="post" action="#">
                            @csrf

                            <div>
                                <label class="mb-1.5 block text-[13px] font-medium text-white/80">Name</label>
                                <input
                                    type="text"
                                    name="name"
                                    class="w-full rounded-2xl border border-white/12 bg-white/96 px-4 py-3 text-[15px] text-gray-900 placeholder:text-gray-400 outline-none"
                                    placeholder="Your name"
                                >
                            </div>

                            <div>
                                <label class="mb-1.5 block text-[13px] font-medium text-white/80">Email</label>
                                <input
                                    type="email"
                                    name="email"
                                    class="w-full rounded-2xl border border-white/12 bg-white/96 px-4 py-3 text-[15px] text-gray-900 placeholder:text-gray-400 outline-none"
                                    placeholder="you@example.com"
                                >
                            </div>

                            <div>
                                <label class="mb-1.5 block text-[13px] font-medium text-white/80">Brokerage Name</label>
                                <input
                                    type="text"
                                    name="brokerage_name"
                                    class="w-full rounded-2xl border border-white/12 bg-white/96 px-4 py-3 text-[15px] text-gray-900 placeholder:text-gray-400 outline-none"
                                    placeholder="Your brokerage"
                                >
                            </div>

                            <div class="pt-2">
                                <button
                                    type="submit"
                                    class="inline-flex w-full items-center justify-center rounded-full bg-[#f0d28a] px-8 py-3.5 text-[14px] font-semibold tracking-[0.08em] text-[#1d2f5f] shadow-lg transition hover:brightness-105"
                                >
                                    SUBMIT DETAILS
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>





</body>
</html>