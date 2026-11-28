<header class="dashboard-header">
    <div class="dashboard-header__start">
        <label for="dashboard-sidebar-toggle" class="icon-button dashboard-header__menu" aria-label="Open platform navigation"><i data-lucide="menu" aria-hidden="true"></i></label>
        <div class="dashboard-breadcrumb"><span>JST Console</span><i data-lucide="chevron-right" aria-hidden="true"></i><strong>@if(request()->routeIs('admin.governorates')) Governorates @elseif(request()->routeIs('admin.categories')) Categories @elseif(request()->routeIs('admin.platform-settings')) Platform settings @else Overview @endif</strong></div>
    </div>
    <div class="dashboard-header__end"><span class="dashboard-live"><span></span> Live platform data</span>
        <div class="dashboard-header__profile"><span class="profile-avatar" data-user-initial>J</span><span><strong data-user-name>JST Admin</strong><small data-user-role>System administrator</small></span></div>
    </div>
</header>
