{{-- resources/views/home/hero.blade.php --}}
<!DOCTYPE html>
<html lang="en" class="h-full bg-white text-gray-900">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{{ $pageTitle ?? 'Realty Emails' }}</title>

  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

  {{-- Modern fonts --}}
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Fraunces:opsz,wght@9..144,400;500;600&display=swap" rel="stylesheet">

  <style>
    :root{
      --font-sans: Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, Helvetica, Arial, "Apple Color Emoji","Segoe UI Emoji";
      --font-display: Fraunces, ui-serif, Georgia, Cambria, "Times New Roman", Times, serif;
    }
    body{
      font-family: var(--font-sans);
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
      text-rendering: geometricPrecision;
    }
    .font-display{ font-family: var(--font-display); }
  </style>
</head>

<body class="min-h-screen bg-white">
  
  <section>
    @include('public.includes.hero_card')
  </section>
  <section class="w-full bg-white">
    @include('public.includes.features_section')
  </section>

</body>
</html>