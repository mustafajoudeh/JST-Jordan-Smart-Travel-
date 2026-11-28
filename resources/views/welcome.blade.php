@extends('layouts.app')

@section('title', 'Discover Jordan with JST')
@section('page', 'home')

@section('content')
<div class="public-site jst-landing">
    @include('components.public-header')

    <main>
        <section class="cinematic-hero" id="discover" aria-labelledby="landing-title">
            <figure class="cinematic-hero__scene">
                <img
                    class="cinematic-hero__image"
                    src="https://images.unsplash.com/photo-1677752512016-986954947326?auto=format&amp;fit=crop&amp;w=2200&amp;q=88"
                    alt="The sandstone mountains of Wadi Rum rise over the open desert"
                    fetchpriority="high"
                    decoding="async"
                >
                <span class="cinematic-hero__shade" aria-hidden="true"></span>
                <figcaption class="cinematic-hero__location">
                    <span class="cinematic-hero__location-mark" aria-hidden="true"></span>
                    <span>Wadi Rum</span>
                    <span class="cinematic-hero__location-divider" aria-hidden="true"></span>
                    <span>Jordan</span>
                </figcaption>
            </figure>

            <div class="cinematic-hero__content">
                <p class="landing-kicker"><span>JST</span><span class="landing-kicker__rule" aria-hidden="true"></span><span>A way into Jordan</span></p>
                <h1 id="landing-title">Jordan,<br><em>unfolding.</em></h1>
                <p class="cinematic-hero__intro">Follow the landscapes, stories and places that call you.</p>
                <a class="landing-action landing-action--gold" href="#destinations">
                    <span>Explore Jordan</span>
                    <i data-lucide="arrow-down-right" aria-hidden="true"></i>
                </a>
            </div>

            <div class="cinematic-hero__index" aria-hidden="true"><span>01</span><span></span><span>THE JOURNEY</span></div>
        </section>

        <section class="destination-story" id="destinations" aria-labelledby="destinations-title">
            <header class="destination-story__heading reveal-on-scroll">
                <div>
                    <p class="landing-kicker"><span>Places to linger</span></p>
                    <h2 id="destinations-title">Everywhere<br>feels <em>different.</em></h2>
                </div>
                <p class="destination-story__lead">From carved stone to still water, let the land set your pace.</p>
            </header>

            <div class="destination-gallery">
                <figure class="destination-image destination-image--petra reveal-on-scroll">
                    <img
                        src="https://images.unsplash.com/photo-1551171129-8ce1ebb911b3?auto=format&amp;fit=crop&amp;w=1400&amp;q=86"
                        alt="Petra’s Treasury framed by rose-colored sandstone"
                        loading="lazy"
                        decoding="async"
                    >
                    <figcaption><span>01</span><strong>Petra</strong></figcaption>
                </figure>
                <figure class="destination-image destination-image--sea reveal-on-scroll">
                    <img
                        src="https://images.unsplash.com/photo-1743943932415-947f79353387?auto=format&amp;fit=crop&amp;w=1100&amp;q=84"
                        alt="Quiet water and salt-shaped shoreline at the Dead Sea"
                        loading="lazy"
                        decoding="async"
                    >
                    <figcaption><span>02</span><strong>The Dead Sea</strong></figcaption>
                </figure>
            </div>

            <div class="destination-story__foot reveal-on-scroll">
                <span>LANDSCAPE <i aria-hidden="true"></i> HERITAGE <i aria-hidden="true"></i> EXPERIENCE</span>
                <a class="landing-text-link" href="#trip-planner">Find your way <i data-lucide="arrow-down-right" aria-hidden="true"></i></a>
            </div>
        </section>

        <section class="trip-planner" id="trip-planner" aria-labelledby="trip-planner-title">
            <div class="trip-planner__copy reveal-on-scroll">
                <p class="landing-kicker"><span>JST / Trip planner</span></p>
                <h2 id="trip-planner-title">Start with what<br><em>moves you.</em></h2>
                <p>JST’s AI trip planner turns your interests into a personal journey through Jordan.</p>
                <a class="landing-action landing-action--light" href="{{ route('register') }}">
                    <span>Plan your journey</span>
                    <i data-lucide="arrow-up-right" aria-hidden="true"></i>
                </a>
            </div>

            <figure class="trip-planner__image reveal-on-scroll">
                <img
                    src="https://images.unsplash.com/photo-1667935460329-a7bcbc5c912e?auto=format&amp;fit=crop&amp;w=1500&amp;q=86"
                    alt="A stone structure among the ancient ruins of Jerash"
                    loading="lazy"
                    decoding="async"
                >
                <figcaption><span>JOURNEYS, SHAPED AROUND YOU</span></figcaption>
            </figure>
        </section>
    </main>

    @include('components.site-footer')
</div>
@endsection
