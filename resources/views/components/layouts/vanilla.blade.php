<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300..700&display=swap" rel="stylesheet">

  <!-- Styles / Scripts -->
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <!-- Custom CSS -->
  <link rel="stylesheet" href="{{ asset('css/custom/dynamic-gradient.css') }}">

  <title>{{ config('app.name') ?? 'PMSi - Internal' }}</title>
</head>

<body>
  {{ $slot }}
</body>

</html>
