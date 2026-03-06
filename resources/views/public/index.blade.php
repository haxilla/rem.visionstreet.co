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
  <section>Member Since Count:{{$memberSince->count()}}</section>


  <div class="agent-mosaic-luxury">

    <div class="agent-tile tile-lg-portrait">
        <img src="https://realtyrepublic.com/agentPhotos/8oz9nhtjjq/5980agtphoto-FCC4DDADA96F69C94C4C2A0D5FDCB626.jpg">
    </div>

    <div class="agent-tile tile-square">
        <img src="https://realtyrepublic.com/agentPhotos/8oz9nhtjjq/5980agtphoto-FCC4DDADA96F69C94C4C2A0D5FDCB626.jpg">
    </div>

    <div class="agent-tile tile-square">
        <img src="https://realtyrepublic.com/agentPhotos/8oz9nhtjjq/5980agtphoto-FCC4DDADA96F69C94C4C2A0D5FDCB626.jpg">
    </div>

    <div class="agent-tile tile-square">
        <img src="https://realtyrepublic.com/agentPhotos/8oz9nhtjjq/5980agtphoto-FCC4DDADA96F69C94C4C2A0D5FDCB626.jpg">
    </div>

    <div class="agent-tile tile-portrait">
        <img src="https://realtyrepublic.com/agentPhotos/8oz9nhtjjq/5980agtphoto-FCC4DDADA96F69C94C4C2A0D5FDCB626.jpg">
    </div>

    <div class="agent-tile tile-wide">
        <img src="https://realtyrepublic.com/agentPhotos/8oz9nhtjjq/5980agtphoto-FCC4DDADA96F69C94C4C2A0D5FDCB626.jpg">
    </div>

</div>


</body>
</html>