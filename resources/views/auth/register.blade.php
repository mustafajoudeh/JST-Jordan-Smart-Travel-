@extends('layouts.app')

@section('title', 'Create an account | Jordan Smart Travel')
@section('page', 'register')

@section('content')
    <main class="auth-page auth-page--register">
        <section class="auth-story auth-story--register">
            <a class="brand-lockup brand-lockup--light" href="{{ route('home') }}" aria-label="Jordan Smart Travel home"><span class="brand-mark" aria-hidden="true"><img src="{{ asset('images/jst-symbol.svg') }}" alt=""></span><span
                    class="brand-wordmark"><strong>JST</strong><small>JORDAN SMART TRAVEL</small></span></a>
            <div class="auth-story__copy">
                <p class="eyebrow eyebrow--light"><span class="eyebrow__line"></span> Begin with a place</p>
                <h1>Make room<br>for <em>the journey.</em></h1>
                <p>Save the places that stay with you. Build a trip at your own pace.</p>
            </div>
            <div class="auth-story__foot"><span>DISCOVER · PLAN · GO</span><span>JORDAN, YOUR WAY</span></div>
        </section>
        <section class="auth-panel">
            <a class="auth-back" href="{{ route('home') }}"><i data-lucide="arrow-left" aria-hidden="true"></i> Back to
                JST</a>
            <div class="auth-form-wrap auth-form-wrap--register">
                <p class="eyebrow"><span class="eyebrow__line"></span> Create your account</p>
                <h2>Start with your name.</h2>
                <p class="auth-form__intro">A few details are all it takes to get going.</p>
                <div class="form-feedback hidden" role="alert" aria-live="polite" data-form-feedback></div>
                <form class="auth-form" data-register-form novalidate>
                    <div class="field-grid">
                        <div class="field-group"><label for="first-name">First name</label><input class="text-input"
                                id="first-name" name="first_name" autocomplete="given-name" maxlength="50" required>
                            <p class="field-error" data-error-for="first_name"></p>
                        </div>
                        <div class="field-group"><label for="last-name">Last name</label><input class="text-input"
                                id="last-name" name="last_name" autocomplete="family-name" maxlength="50" required>
                            <p class="field-error" data-error-for="last_name"></p>
                        </div>
                    </div>
                    <div class="field-group"><label for="register-email">Email address</label><input class="text-input"
                            id="register-email" name="email" type="email" autocomplete="email" inputmode="email"
                            placeholder="you@example.com" maxlength="150" required>
                        <p class="field-error" data-error-for="email"></p>
                    </div>
                    <div class="field-group"><label for="register-password">Password</label>
                        <div class="password-control"><input class="text-input" id="register-password" name="password"
                                type="password" autocomplete="new-password" minlength="8" required
                                aria-describedby="password-help"><button class="password-toggle" type="button"
                                aria-label="Show password" aria-pressed="false" data-password-toggle="register-password"><i
                                    data-lucide="eye" aria-hidden="true"></i></button></div>
                        <p class="field-hint" id="password-help">Use at least 8 characters.</p>
                        <p class="field-error" data-error-for="password"></p>
                    </div>
                    <div class="field-group"><label for="password-confirmation">Confirm password</label>
                        <div class="password-control"><input class="text-input" id="password-confirmation"
                                name="password_confirmation" type="password" autocomplete="new-password" required><button
                                class="password-toggle" type="button" aria-label="Show password" aria-pressed="false"
                                data-password-toggle="password-confirmation"><i data-lucide="eye"
                                    aria-hidden="true"></i></button></div>
                        <p class="field-error" data-error-for="password_confirmation"></p>
                    </div>
                    <button class="button button--primary button--full" type="submit" data-submit-button><span
                            data-button-label>Create account</span><i data-lucide="arrow-right" aria-hidden="true"></i><span
                            class="button-spinner" aria-hidden="true"></span></button>
                </form>
                <p class="auth-switch">Already have an account? <a href="{{ route('login') }}">Log in</a></p>
                <p class="auth-security"><i data-lucide="lock-keyhole" aria-hidden="true"></i> Registration creates a
                    traveler account. You’ll log in after confirming your email.</p>
            </div>
        </section>
    </main>
@endsection

