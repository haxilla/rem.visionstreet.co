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
    <section>
        @include('public.includes.free_flyer_served_v1')
    </section>
    <section style="background: #f7f5f5;">
        @include('public.includes.top_views')
    </section>
</body>
</html>