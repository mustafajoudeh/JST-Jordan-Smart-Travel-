@extends('layouts.app')

@section('title', 'Log in | Jordan Smart Travel')
@section('page', 'login')

@section('content')
    <main class="auth-page">
        <section class="auth-story auth-story--login">
            <a class="brand-lockup brand-lockup--light" href="{{ route('home') }}" aria-label="Jordan Smart Travel home"><span class="brand-mark" aria-hidden="true"><img src="{{ asset('images/jst-symbol.svg') }}" alt=""></span><span
                    class="brand-wordmark"><strong>JST</strong><small>JORDAN SMART TRAVEL</small></span></a>
            <div class="auth-story__copy">
                <p class="eyebrow eyebrow--light"><span class="eyebrow__line"></span> Welcome back</p>
                <h1>Pick up where<br>your <em>curiosity</em><br>left off.</h1>
                <p>Your next Jordan story is just around the corner.</p>
            </div>
            <div class="auth-story__foot"><span>31° 57′ N &nbsp; 35° 56′ E</span><span>AMMAN · JORDAN</span></div>
        </section>
        <section class="auth-panel">
            <a class="auth-back" href="{{ route('home') }}"><i data-lucide="arrow-left" aria-hidden="true"></i> Back to
                JST</a>
            <div class="auth-form-wrap">
                <p class="eyebrow"><span class="eyebrow__line"></span> Your account</p>
                <h2>Good to see you.</h2>
                <p class="auth-form__intro">Log in to continue planning your journey.</p>
                <div class="form-feedback hidden" role="alert" aria-live="polite" data-form-feedback></div>
                <form class="auth-form" data-login-form novalidate>
                    <div class="field-group">
                        <label for="login-email">Email address</label>
                        <input class="text-input" id="login-email" name="email" type="email" autocomplete="email"
                            inputmode="email" placeholder="you@example.com" required aria-describedby="login-email-error">
                        <p class="field-error" id="login-email-error" data-error-for="email"></p>
                    </div>
                    <div class="field-group">
                        <div class="field-label-row"><label for="login-password">Password</label><button class="inline-link"
                                type="button" data-open-reset>Forgot password?</button></div>
                        <div class="password-control"><input class="text-input" id="login-password" name="password"
                                type="password" autocomplete="current-password" placeholder="Enter your password" required
                                aria-describedby="login-password-error"><button class="password-toggle" type="button"
                                aria-label="Show password" aria-pressed="false" data-password-toggle="login-password"><i
                                    data-lucide="eye" aria-hidden="true"></i></button></div>
                        <p class="field-error" id="login-password-error" data-error-for="password"></p>
                    </div>
                    <button class="button button--primary button--full" type="submit" data-submit-button><span
                            data-button-label>Log in</span><i data-lucide="arrow-right" aria-hidden="true"></i><span
                            class="button-spinner" aria-hidden="true"></span></button>
                </form>
                <p class="auth-switch">New to JST? <a href="{{ route('register') }}">Create an account</a></p>
                <p class="auth-security"><i data-lucide="shield-check" aria-hidden="true"></i> Your account is protected
                    with secure token authentication.</p>
            </div>
        </section>
    </main>

    <dialog class="reset-dialog" data-reset-dialog aria-labelledby="reset-title">
        <form method="dialog"><button class="icon-button reset-dialog__close" aria-label="Close"><i data-lucide="x"
                    aria-hidden="true"></i></button></form>
        <p class="eyebrow"><span class="eyebrow__line"></span> Account recovery</p>
        <h2 id="reset-title">Reset your password</h2>
        <p>We’ll send a secure reset link to the email address on your account.</p>
        <div class="form-feedback hidden" role="status" aria-live="polite" data-reset-feedback></div>
        <form class="auth-form" data-reset-form novalidate>
            <div class="field-group"><label for="reset-email">Email address</label><input class="text-input"
                    id="reset-email" name="email" type="email" autocomplete="email" required>
                <p class="field-error" data-reset-error></p>
            </div>
            <button class="button button--primary button--full" type="submit" data-submit-button><span
                    data-button-label>Send reset link</span><i data-lucide="arrow-right" aria-hidden="true"></i><span
                    class="button-spinner" aria-hidden="true"></span></button>
        </form>
    </dialog>
@endsection

