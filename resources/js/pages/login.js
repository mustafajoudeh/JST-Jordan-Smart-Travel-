import { Eye, EyeOff, createIcons } from 'lucide';
import { login, redirectForRole, sendPasswordReset } from '../api/auth';
import { ApiError, fieldMessage } from '../api/client';

function setFeedback(element, message, kind = 'error') {
    if (!element) return;
    element.textContent = message;
    element.classList.remove('hidden', 'form-feedback--error', 'form-feedback--success');
    element.classList.add(kind === 'success' ? 'form-feedback--success' : 'form-feedback--error');
}

function setLoading(form, loading) {
    const button = form.querySelector('[data-submit-button]');
    if (!button) return;
    button.disabled = loading;
    button.setAttribute('aria-busy', String(loading));
    form.classList.toggle('is-loading', loading);
}

function clearErrors(form) {
    form.querySelectorAll('[data-error-for]').forEach((node) => {
        node.textContent = '';
    });
    form.querySelectorAll('.text-input').forEach((input) => {
        input.removeAttribute('aria-invalid');
    });
    form.closest('.auth-form-wrap')?.querySelector('[data-form-feedback]')?.classList.add('hidden');
}

function showErrors(form, error) {
    if (error instanceof ApiError && error.status === 422 && Object.keys(error.errors).length) {
        Object.entries(error.errors).forEach(([field, messages]) => {
            const input = form.elements.namedItem(field);
            const target = form.querySelector(`[data-error-for="${field}"]`);
            if (target) target.textContent = fieldMessage(error, field) || messages?.[0] || '';
            input?.setAttribute('aria-invalid', 'true');
        });
        return;
    }

    setFeedback(form.closest('.auth-form-wrap')?.querySelector('[data-form-feedback]'), error.message || 'Something went wrong. Please try again.');
}

function bindPasswordToggles() {
    document.querySelectorAll('[data-password-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const input = document.getElementById(button.dataset.passwordToggle);
            if (!input) return;
            const visible = input.type === 'password';
            input.type = visible ? 'text' : 'password';
            button.setAttribute('aria-pressed', String(visible));
            button.setAttribute('aria-label', visible ? 'Hide password' : 'Show password');
            button.querySelector('[data-lucide]')?.setAttribute('data-lucide', visible ? 'eye-off' : 'eye');
            createIcons({ icons: { Eye, EyeOff }, root: button });
        });
    });
}

function bindResetDialog() {
    const dialog = document.querySelector('[data-reset-dialog]');
    const form = document.querySelector('[data-reset-form]');
    const openButton = document.querySelector('[data-open-reset]');

    openButton?.addEventListener('click', () => {
        dialog?.showModal();
        const sourceEmail = document.querySelector('#login-email')?.value;
        const resetEmail = document.querySelector('#reset-email');
        if (sourceEmail && resetEmail) resetEmail.value = sourceEmail;
        resetEmail?.focus();
    });

    form?.addEventListener('submit', async (event) => {
        event.preventDefault();
        const email = form.elements.namedItem('email').value.trim();
        const feedback = document.querySelector('[data-reset-feedback]');
        const errorField = document.querySelector('[data-reset-error]');
        errorField.textContent = '';
        feedback?.classList.add('hidden');

        if (!email || !form.elements.namedItem('email').checkValidity()) {
            errorField.textContent = 'Enter a valid email address.';
            return;
        }

        setLoading(form, true);
        try {
            await sendPasswordReset(email);
            setFeedback(feedback, 'If an account matches that address, a reset link will be sent.', 'success');
            form.reset();
        } catch (error) {
            setFeedback(feedback, error.message);
        } finally {
            setLoading(form, false);
        }
    });
}

export function initLogin() {
    bindPasswordToggles();
    bindResetDialog();
    const form = document.querySelector('[data-login-form]');

    form?.addEventListener('submit', async (event) => {
        event.preventDefault();
        clearErrors(form);
        const email = form.elements.namedItem('email').value.trim();
        const password = form.elements.namedItem('password').value;
        const emailInput = form.elements.namedItem('email');
        const passwordInput = form.elements.namedItem('password');

        if (!email || !emailInput.checkValidity()) {
            form.querySelector('[data-error-for="email"]').textContent = 'Enter a valid email address.';
            emailInput.setAttribute('aria-invalid', 'true');
            emailInput.focus();
            return;
        }

        if (!password) {
            form.querySelector('[data-error-for="password"]').textContent = 'Enter your password.';
            passwordInput.setAttribute('aria-invalid', 'true');
            passwordInput.focus();
            return;
        }

        setLoading(form, true);
        try {
            const user = await login({ email, password });
            redirectForRole(user);
        } catch (error) {
            showErrors(form, error);
        } finally {
            setLoading(form, false);
        }
    });
}
