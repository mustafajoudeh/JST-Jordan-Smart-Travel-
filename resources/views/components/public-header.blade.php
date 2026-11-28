<header class="landing-header">
    <div class="landing-header__inner">
        <a class="brand-lockup" href="{{ route('home') }}" aria-label="Jordan Smart Travel home">
            <span class="brand-mark" aria-hidden="true"><img src="{{ asset('images/jst-symbol.svg') }}" alt=""></span>
            <span class="brand-wordmark"><strong>JST</strong><small>JORDAN SMART TRAVEL</small></span>
        </a>
        <button class="landing-menu-toggle" type="button" aria-label="Open navigation" aria-controls="landing-navigation" aria-expanded="false" data-menu-toggle>
            <i data-lucide="menu" aria-hidden="true"></i>
        </button>
        <nav class="landing-nav" id="landing-navigation" aria-label="Main navigation" data-site-nav>
            <a href="{{ route('home') }}#destinations">Places</a>
            <a href="{{ route('home') }}#trip-planner">Trip planner</a>
            <div class="landing-nav__account" data-auth-links>
                <a class="landing-nav__login" href="{{ route('login') }}">Log in</a>
                <a class="landing-nav__action" href="{{ route('register') }}">Plan with JST <i data-lucide="arrow-up-right" aria-hidden="true"></i></a>
            </div>
            <div class="landing-nav__account hidden" data-authenticated-links>
                <a class="landing-nav__login" href="{{ route('admin.dashboard') }}" data-home-dashboard>Dashboard</a>
                <button class="landing-nav__logout" type="button" data-logout>Sign out</button>
            </div>
        </nav>
    </div>
</header>

