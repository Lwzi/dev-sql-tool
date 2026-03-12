<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Dev SQL Tool') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>
  <body class="font-sans text-gray-900 antialiased">
    <div class="guest-shell flex items-center justify-center">
      <div class="guest-card">
        <div class="guest-kicker">Admin Access</div>
        <h1 class="guest-title">Login</h1>

        <div class="guest-content">
          {{ $slot }}
        </div>
      </div>
    </div>
  </body>
</html>
