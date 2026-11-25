
<!DOCTYPE html>
<html lang="en" dir="ltr" data-theme="jst">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=ibm-plex-sans-arabic:400,500,600,700&family=ibm-plex-sans:400,500,600,700" rel="stylesheet" />

    <title>
        @yield('title', $pageTitle . ' | JST')
    </title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    @stack('styles')

</head>

<body>

    <div class="drawer min-h-screen lg:drawer-open">

        <input
            id="jst-sidebar-toggle"
            type="checkbox"
            class="drawer-toggle"
        >

        {{-- Main Area --}}
        <div class="drawer-content flex min-h-screen flex-col">

            @include('dashboard.partials.header')

            <main class="jst-main">

                <div class="mx-auto w-full max-w-screen-2xl">

                    @yield('content')

                </div>

            </main>

            {{-- Footer --}}
            <footer class="border-t border-base-300 bg-base-100 px-6 py-4">

                <p class="text-center text-xs text-base-content/50">
                    © {{ date('Y') }} Jordan Smart Travel.
                    All rights reserved.
                </p>

            </footer>

        </div>

        {{-- Sidebar Area --}}
        <div class="drawer-side z-40">

            <label
                for="jst-sidebar-toggle"
                aria-label="Close navigation"
                class="drawer-overlay"
            ></label>

            @include('dashboard.partials.sidebar')

        </div>

    </div>

    @stack('scripts')

</body>
</html>
