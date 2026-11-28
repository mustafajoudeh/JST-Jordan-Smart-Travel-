@extends('layouts.app')

@section('title', 'Platform overview | JST')
@section('page', 'admin-dashboard')

@section('content')
<div class="dashboard-gate" data-dashboard-gate role="status"><span class="brand-mark" aria-hidden="true"><img src="{{ asset('images/jst-symbol.svg') }}" alt=""></span><p>Checking your JST account…</p></div>
<div class="drawer dashboard-drawer lg:drawer-open hidden" data-dashboard-app>
    <input id="dashboard-sidebar-toggle" class="drawer-toggle" type="checkbox" aria-label="Toggle navigation">
    <div class="drawer-content dashboard-content">
        @include('dashboard.partials.header')
        <main class="dashboard-main">
            <div class="dashboard-container">
                @yield('dashboard-content')
            </div>
        </main>
        <footer class="dashboard-footer"><span>JST Platform Console</span><span>Jordan Smart Travel · {{ date('Y') }}</span></footer>
    </div>
    <div class="drawer-side dashboard-drawer__side">
        <label for="dashboard-sidebar-toggle" class="drawer-overlay" aria-label="Close navigation"></label>
        @include('dashboard.partials.sidebar')
    </div>
</div>
@endsection
