<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ str_starts_with(app()->getLocale(), 'ar') ? 'rtl' : 'ltr' }}" data-theme="jst">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="api-base" content="{{ url('/api') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#f4f6f6">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=ibm-plex-mono:400,500,600&family=ibm-plex-sans:400,500,600,700&family=ibm-plex-sans-arabic:400,500,600,700" rel="stylesheet">
    <title>@yield('title', 'Jordan Smart Travel')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body data-page="@yield('page', 'home')" data-api-base="{{ url('/api') }}">
    @yield('content')
    @stack('scripts')
</body>
</html>
