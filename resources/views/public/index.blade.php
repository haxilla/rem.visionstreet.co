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
        <!--
    <section>
        @include('public.includes.member_since')
    </section>
    -->






<section >


</section>





</body>
</html>