<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <meta property="og:title" content="RandsBook - Personal Library App" />
        <meta property="og:type" content="website" />
        <meta property="og:url" content="https://www.randsbook.com" />
        <meta property="og:image" content="https://resources/og_img.jpg" />

        <meta name="twitter:title" content="RandsBook - Personal Library App">
        <meta name="twitter:description" content="Search, add, manage books in one signle app.">
        <meta name="twitter:image" content="https://resources/og_img.jpg">
        <meta name="twitter:card" content="summary_large_image">

        <title>{{ config('app.name', 'RandsBook') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap">
        <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="css/style.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <div class="font-sans text-gray-900 antialiased">
            {{ $slot }}
        </div>
    </body>
</html>
