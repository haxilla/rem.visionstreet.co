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
        @include('public.includes.wide_99_promo')
    </section>
    <section >
        @include('public.includes.top_views_4up_v1')
    </section>
    <section >
        @include('public.includes.top_views_4up_v2')
    </section>

</body>
</html>