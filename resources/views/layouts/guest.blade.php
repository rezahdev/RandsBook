<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <meta name="title" content="RandsBook - Personal Library App">
        <meta name="description" content="Log in to RandsBook.com personal library app to search, add, and manage your through one single app.">
        
        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="website">
        <meta property="og:url" content="https://randsbook.com/login">
        <meta property="og:title" content="RandsBook - Personal Library App">
        <meta property="og:description" content="Log in to RandsBook.com personal library app to search, add, and manage your through one single app.">
        <meta property="og:image" content="https://randsbook.com/resources/og_img.png">
        
        <!-- Twitter -->
        <meta property="twitter:card" content="summary_large_image">
        <meta property="twitter:url" content="https://randsbook.com/login">
        <meta property="twitter:title" content="RandsBook - Personal Library App">
        <meta property="twitter:description" content="Log in to RandsBook.com personal library app to search, add, and manage your through one single app.">
        <meta property="twitter:image" content="https://randsbook.com/resources/og_img.png">

        <title>{{ config('app.name', 'RandsBook') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap">
        <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="css/style.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <div class="font-sans text-gray-900 antialiased" id="main">
            {{ $slot }}
            @include('layouts.footer')
        </div>    
    </body>
</html>
