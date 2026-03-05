{{-- resources/views/home/hero.blade.php --}}
<!DOCTYPE html>
<html lang="en" class="h-full bg-white text-gray-900">
@include('public.layout.head');

<body class="min-h-screen bg-white">

  <section>
    @include('includes.hero_card')
  </section>
  <section>
    @include('includes.features_section')
  </section>

</body>
</html>