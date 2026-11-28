<aside class="jst-sidebar dashboard-sidebar">
    <a class="brand-lockup dashboard-sidebar__brand" href="{{ route('home') }}" aria-label="Jordan Smart Travel home">
        <span class="brand-mark" aria-hidden="true"><img src="{{ asset('images/jst-symbol.svg') }}" alt=""></span>
        <span class="brand-wordmark"><strong>JST</strong><small>PLATFORM CONSOLE</small></span>
    </a>
    <div class="dashboard-sidebar__workspace"><span class="workspace-dot"></span><span data-workspace-label>System administration</span></div>
    <nav class="dashboard-nav" aria-label="Platform navigation">
        <a class="dashboard-nav__item is-active" href="{{ route('admin.dashboard') }}" aria-current="page"><i data-lucide="layout-dashboard" aria-hidden="true"></i><span>Overview</span></a>
        <details class="dashboard-nav__group">
            <summary><i data-lucide="folder-open" aria-hidden="true"></i><span>Content management</span><i class="dashboard-nav__chevron" data-lucide="chevron-down" aria-hidden="true"></i></summary>
            <div class="dashboard-nav__children">
                <span class="dashboard-nav__item is-disabled" aria-disabled="true"><i data-lucide="map-pin" aria-hidden="true"></i><span>Destinations</span><small>Planned</small></span>
                <span class="dashboard-nav__item is-disabled" aria-disabled="true"><i data-lucide="calendar-days" aria-hidden="true"></i><span>Events</span><small>Planned</small></span>
                <span class="dashboard-nav__item is-disabled" aria-disabled="true"><i data-lucide="activity" aria-hidden="true"></i><span>Activities</span><small>Planned</small></span>
                <span class="dashboard-nav__item is-disabled" aria-disabled="true"><i data-lucide="sparkles" aria-hidden="true"></i><span>Experiences</span><small>Planned</small></span>
            </div>
        </details>
        <details class="dashboard-nav__group" @if(request()->routeIs('admin.governorates', 'admin.categories')) open @endif>
            <summary><i data-lucide="map" aria-hidden="true"></i><span>Locations &amp; classification</span><i class="dashboard-nav__chevron" data-lucide="chevron-down" aria-hidden="true"></i></summary>
            <div class="dashboard-nav__children">
                <a class="dashboard-nav__item {{ request()->routeIs('admin.governorates') ? 'is-active' : '' }}" href="{{ route('admin.governorates') }}" @if(request()->routeIs('admin.governorates')) aria-current="page" @endif><i data-lucide="map" aria-hidden="true"></i><span>Governorates</span></a>
                <a class="dashboard-nav__item {{ request()->routeIs('admin.categories') ? 'is-active' : '' }}" href="{{ route('admin.categories') }}" @if(request()->routeIs('admin.categories')) aria-current="page" @endif><i data-lucide="tags" aria-hidden="true"></i><span>Categories</span></a>
            </div>
        </details>
        <details class="dashboard-nav__group" data-platform-settings-group @if(request()->routeIs('admin.platform-settings')) open @endif>
            <summary><i data-lucide="building-2" aria-hidden="true"></i><span>Provider management</span><i class="dashboard-nav__chevron" data-lucide="chevron-down" aria-hidden="true"></i></summary>
            <div class="dashboard-nav__children">
                <span class="dashboard-nav__item is-disabled" aria-disabled="true"><i data-lucide="building-2" aria-hidden="true"></i><span>Providers</span><small>Planned</small></span>
                <span class="dashboard-nav__item is-disabled" aria-disabled="true"><i data-lucide="briefcase-business" aria-hidden="true"></i><span>Services</span><small>Planned</small></span>
            </div>
        </details>
        <details class="dashboard-nav__group">
            <summary><i data-lucide="route" aria-hidden="true"></i><span>Trip management</span><i class="dashboard-nav__chevron" data-lucide="chevron-down" aria-hidden="true"></i></summary>
            <div class="dashboard-nav__children">
                <span class="dashboard-nav__item is-disabled" aria-disabled="true"><i data-lucide="route" aria-hidden="true"></i><span>Trips</span><small>Planned</small></span>
                <span class="dashboard-nav__item is-disabled" aria-disabled="true"><i data-lucide="list" aria-hidden="true"></i><span>Itinerary items</span><small>Planned</small></span>
            </div>
        </details>
        <details class="dashboard-nav__group">
            <summary><i data-lucide="wallet-cards" aria-hidden="true"></i><span>Financial management</span><i class="dashboard-nav__chevron" data-lucide="chevron-down" aria-hidden="true"></i></summary>
            <div class="dashboard-nav__children">
                <span class="dashboard-nav__item is-disabled" aria-disabled="true"><i data-lucide="credit-card" aria-hidden="true"></i><span>Payments</span><small>Planned</small></span>
            </div>
        </details>
        <details class="dashboard-nav__group">
            <summary><i data-lucide="shield-check" aria-hidden="true"></i><span>Platform management</span><i class="dashboard-nav__chevron" data-lucide="chevron-down" aria-hidden="true"></i></summary>
            <div class="dashboard-nav__children">
                <span class="dashboard-nav__item is-disabled" aria-disabled="true"><i data-lucide="users" aria-hidden="true"></i><span>Users</span><small>Planned</small></span>
                <a class="dashboard-nav__item {{ request()->routeIs('admin.platform-settings') ? 'is-active' : '' }}" href="{{ route('admin.platform-settings') }}" data-system-admin-only @if(request()->routeIs('admin.platform-settings')) aria-current="page" @endif><i data-lucide="settings" aria-hidden="true"></i><span>Platform settings</span></a>
            </div>
        </details>
    </nav>
    <div class="dashboard-sidebar__bottom">
        <a class="sidebar-public-link" href="{{ route('home') }}"><i data-lucide="arrow-up-right" aria-hidden="true"></i><span>View public site</span></a>
        <div class="sidebar-account"><span class="sidebar-account__avatar" data-user-initial>J</span><span class="sidebar-account__text"><strong data-user-name>JST Admin</strong><small data-user-role>System administrator</small></span></div>
        <button class="sidebar-logout" type="button" data-logout><i data-lucide="log-out" aria-hidden="true"></i><span>Log Out</span></button>
    </div>
</aside>
