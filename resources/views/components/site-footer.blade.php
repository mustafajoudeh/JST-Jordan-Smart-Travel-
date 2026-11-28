<footer class="landing-footer">
    <div class="landing-footer__main">
        <a class="brand-lockup brand-lockup--footer" href="{{ route('home') }}">
            <span class="brand-mark" aria-hidden="true"><img src="{{ asset('images/jst-symbol.svg') }}" alt=""></span>
            <span class="brand-wordmark"><strong>JST</strong><small>JORDAN SMART TRAVEL</small></span>
        </a>
        <nav class="landing-footer__links" aria-label="Footer navigation">
            <a href="{{ route('home') }}#destinations">Places</a>
            <a href="{{ route('home') }}#trip-planner">Plan a trip</a>
            <a href="{{ route('login') }}">Log in</a>
        </nav>
    </div>
    <div class="landing-footer__bottom">
        <span>© {{ date('Y') }} Jordan Smart Travel</span>
        <a href="{{ route('register') }}">Explore with JST <i data-lucide="arrow-up-right" aria-hidden="true"></i></a>
    </div>
</footer>

