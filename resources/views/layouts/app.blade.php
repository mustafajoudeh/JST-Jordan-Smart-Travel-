<head>
    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])
</head>

<body data-page="@yield('page', 'home')">

    @yield('content')

</body>