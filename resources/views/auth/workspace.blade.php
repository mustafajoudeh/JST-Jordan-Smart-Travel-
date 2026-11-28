@extends('layouts.app')

@section('title', 'Your JST workspace')
@section('page', 'workspace')

@section('content')
<main class="workspace-notice">
    <a class="brand-lockup" href="{{ route('home') }}"><span class="brand-mark" aria-hidden="true"><img src="{{ asset('images/jst-symbol.svg') }}" alt=""></span><span class="brand-wordmark"><strong>JST</strong><small>JORDAN SMART TRAVEL</small></span></a>
    <div class="workspace-notice__card"><span class="workspace-notice__icon"><i data-lucide="map" aria-hidden="true"></i></span><p class="eyebrow"><span class="eyebrow__line"></span> Your JST account</p><h1>Your workspace is<br>taking shape.</h1><p>Workspace tools for this role are not connected yet. You can still explore JST while the experience is being built.</p><div class="workspace-notice__actions"><a class="button button--primary" href="{{ route('home') }}">Explore JST <i data-lucide="arrow-up-right" aria-hidden="true"></i></a><button class="button button--outline" type="button" data-logout>Sign out</button></div></div>
</main>
@endsection

