import { Eye, EyeOff, createIcons } from 'lucide';
import { register } from '../api/auth';
import { ApiError, fieldMessage } from '../api/client';

function setLoading(form, loading) {
    const button = form.querySelector('[data-submit-button]');
    if (!button) return;
    button.disabled = loading;
    button.setAttribute('aria-busy', String(loading));
    form.classList.toggle('is-loading', loading);
}

function showFeedback(form, message, success = false) {
    const feedback = form.closest('.auth-form-wrap')?.querySelector('[data-form-feedback]');
    if (!feedback) return;
    feedback.textContent = message;
    feedback.classList.remove('hidden', 'form-feedback--error', 'form-feedback--success');
    feedback.classList.add(success ? 'form-feedback--success' : 'form-feedback--error');
}

export function initRegister() {
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

    const form = document.querySelector('[data-register-form]');
    form?.addEventListener('submit', async (event) => {
        event.preventDefault();
        form.querySelectorAll('[data-error-for]').forEach((node) => { node.textContent = ''; });
        form.querySelectorAll('.text-input').forEach((input) => input.removeAttribute('aria-invalid'));
        form.closest('.auth-form-wrap')?.querySelector('[data-form-feedback]')?.classList.add('hidden');

        const fields = ['first_name', 'last_name', 'email', 'password', 'password_confirmation'];
        const values = Object.fromEntries(fields.map((field) => [field, field.startsWith('password') ? form.elements.namedItem(field).value : form.elements.namedItem(field).value.trim()]));
        const passwordInput = form.elements.namedItem('password');
        const confirmation = form.elements.namedItem('password_confirmation');

        if (values.password.length < 8) {
            const target = form.querySelector('[data-error-for="password"]');
            target.textContent = 'Use at least 8 characters.';
            passwordInput.setAttribute('aria-invalid', 'true');
            passwordInput.focus();
            return;
        }

        if (values.password !== values.password_confirmation) {
            form.querySelector('[data-error-for="password_confirmation"]').textContent = 'The passwords do not match.';
            confirmation.setAttribute('aria-invalid', 'true');
            confirmation.focus();
            return;
        }

        for (const field of fields) {
            const input = form.elements.namedItem(field);
            if (!input.checkValidity()) {
                input.setAttribute('aria-invalid', 'true');
                form.querySelector(`[data-error-for="${field}"]`).textContent = field === 'email' ? 'Enter a valid email address.' : 'This field is required.';
                input.focus();
                return;
            }
        }

        setLoading(form, true);
        try {
            await register(values);
            form.reset();
            showFeedback(form, 'Your account is ready. Check your email for a verification link, then log in.', true);
            form.querySelector('[data-submit-button]').classList.add('hidden');
        } catch (error) {
            if (error instanceof ApiError && error.status === 422) {
                Object.keys(error.errors).forEach((field) => {
                    const input = form.elements.namedItem(field);
                    input?.setAttribute('aria-invalid', 'true');
                    const target = form.querySelector(`[data-error-for="${field}"]`);
                    if (target) target.textContent = fieldMessage(error, field);
                });
            } else {
                showFeedback(form, error.message || 'We could not create your account. Please try again.');
            }
        } finally {
            setLoading(form, false);
        }
    });
}
