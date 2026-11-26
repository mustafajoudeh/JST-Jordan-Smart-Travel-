@import "tailwindcss";

/* Tailwind source files */
@source "../**/*.blade.php";
@source "../**/*.js";
@source "../../storage/framework/views/*.php";

/* daisyUI */
@plugin "daisyui" {
    themes: false;
}

/* JST Brand Theme */
@plugin "daisyui/theme" {
    name: "jst";
    default: true;
    prefersdark: false;
    color-scheme: light;

    /* Surfaces */
    --color-base-100: #faf9f5;
    --color-base-200: #f1f2ec;
    --color-base-300: #e3e5dd;
    --color-base-content: #19373d;

    /* Brand */
    --color-primary: #0f4c5c;
    --color-primary-content: #ffffff;

    --color-secondary: #5b7f2b;
    --color-secondary-content: #ffffff;

    --color-accent: #f0a93a;
    --color-accent-content: #0a3440;

    --color-neutral: #0a3440;
    --color-neutral-content: #ffffff;

    /* Status */
    --color-info: #347c8a;
    --color-info-content: #ffffff;

    --color-success: #5b7f2b;
    --color-success-content: #ffffff;

    --color-warning: #f0a93a;
    --color-warning-content: #0a3440;

    --color-error: #b84c45;
    --color-error-content: #ffffff;

    /* Shape */
    --radius-selector: 0.5rem;
    --radius-field: 0.5rem;
    --radius-box: 0.75rem;
}

/* =====================================
   Base
===================================== */

@layer base {
    * {
        box-sizing: border-box;
    }

    body {
        min-height: 100vh;
        font-family:
            "IBM Plex Sans Arabic", "IBM Plex Sans", system-ui, sans-serif;
        color: var(--color-base-content);
        background:
            radial-gradient(circle at 100% 0%, rgba(15, 76, 92, 0.035), transparent 28rem),
            var(--color-base-200);
        -webkit-font-smoothing: antialiased;
        text-rendering: optimizeLegibility;
    }

    button,
    a {
        -webkit-tap-highlight-color: transparent;
    }
}

/* =====================================
   Dashboard Sidebar
===================================== */

@layer components {
    .jst-sidebar {
        display: flex;
        flex-direction: column;
        width: 17.5rem;
        height: 100vh;
        position: sticky;
        top: 0;
        overflow-y: auto;
        background: var(--color-base-100);
        border-right: 1px solid var(--color-base-300);
        box-shadow: 0.25rem 0 1.25rem rgba(10, 52, 64, 0.025);
    }

    .jst-sidebar__brand {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        min-height: 5rem;
        padding: 1.25rem;
        border-bottom: 1px solid var(--color-base-300);
    }

    .jst-sidebar__logo {
        display: grid;
        place-items: center;
        width: 2.75rem;
        height: 2.75rem;
        flex-shrink: 0;
        border-radius: 0.75rem;
        background: var(--color-primary);
        color: var(--color-primary-content);
        font-size: 1.25rem;
        font-weight: 700;
    }

    .jst-sidebar__workspace {
        padding: 1.5rem 1.25rem 0.75rem;
        color: color-mix(in srgb, var(--color-base-content) 55%, transparent);
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.07em;
        text-transform: uppercase;
    }

    .jst-sidebar__nav {
        display: grid;
        align-content: start;
        gap: 0.3rem;
        flex: 1;
        padding: 0 0.85rem 1.25rem;
        overflow-y: auto;
    }

    /* Individual links and group headings */

    .jst-nav-link,
    .jst-nav-group__summary {
        display: flex;
        align-items: center;
        width: 100%;
        min-height: 2.75rem;
        gap: 0.75rem;
        padding: 0.7rem 0.85rem;
        border: 0;
        border-radius: 0.65rem;
        color: color-mix(in srgb, var(--color-base-content) 75%, transparent);
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        cursor: pointer;
        transition:
            background-color 160ms ease,
            color 160ms ease;
    }

    .jst-nav-link:hover,
    .jst-nav-group__summary:hover {
        color: var(--color-base-content);
        background: var(--color-base-200);
    }

    .jst-nav-link.is-active {
        color: var(--color-primary-content);
        background: var(--color-primary);
        font-weight: 600;
    }

    .jst-nav-group__summary.is-current {
        color: var(--color-primary);
        background: color-mix(
            in srgb,
            var(--color-primary) 9%,
            var(--color-base-100)
        );
    }

    .jst-nav-link i,
    .jst-nav-group__summary i {
        width: 1.15rem;
        height: 1.15rem;
        flex-shrink: 0;
    }

    /* Remove browser default marker */

    .jst-nav-group > summary {
        list-style: none;
    }

    .jst-nav-group > summary::-webkit-details-marker {
        display: none;
    }

    /* Chevron */

    .jst-nav-chevron {
        margin-left: auto;
        transition: transform 180ms ease;
    }

    .jst-nav-group[open] .jst-nav-chevron {
        transform: rotate(180deg);
    }

    /* Nested links */

    .jst-nav-group__items {
        display: grid;
        gap: 0.2rem;
        margin: 0.35rem 0 0.55rem 1.35rem;
        padding-left: 0.65rem;
        border-left: 1px solid var(--color-base-300);
    }

    .jst-nav-link--nested {
        min-height: 2.5rem;
        padding: 0.6rem 0.7rem;
        font-size: 0.8125rem;
    }

    .jst-nav-link--nested.is-active {
        background: color-mix(
            in srgb,
            var(--color-primary) 11%,
            var(--color-base-100)
        );
        color: var(--color-primary);
        font-weight: 700;
    }

    /* Sidebar Footer */

    .jst-sidebar__footer {
        padding: 1rem;
        border-top: 1px solid var(--color-base-300);
    }

    /* Keyboard Accessibility */

    .jst-nav-link:focus-visible,
    .jst-nav-group__summary:focus-visible {
        outline: 2px solid var(--color-accent);
        outline-offset: 2px;
    }

    /* Header */

    .jst-header {
        position: sticky;
        top: 0;
        z-index: 30;
        min-height: 4.25rem;
        background: color-mix(in srgb, var(--color-base-100) 94%, transparent);
        border-bottom: 1px solid var(--color-base-300);
        backdrop-filter: blur(12px);
    }

    .jst-main {
        flex: 1;
        width: 100%;
        padding: 2rem 1.5rem;
    }

    .jst-preview-card {
        min-height: 7.5rem;
        border-color: color-mix(in srgb, var(--color-base-300) 82%, transparent);
        border-radius: 1rem;
        box-shadow: 0 0.35rem 1.25rem rgba(10, 52, 64, 0.055);
        transition:
            transform 180ms ease,
            box-shadow 180ms ease,
            border-color 180ms ease;
    }

    .jst-preview-card:hover {
        transform: translateY(-2px);
        border-color: color-mix(in srgb, var(--color-primary) 22%, var(--color-base-300));
        box-shadow: 0 0.75rem 1.8rem rgba(10, 52, 64, 0.09);
    }

    .jst-preview-card__icon {
        display: grid;
        place-items: center;
        width: 2.25rem;
        height: 2.25rem;
        border-radius: 0.75rem;
        background: color-mix(in srgb, var(--color-primary) 8%, var(--color-base-100));
    }

    .jst-content-placeholder {
        min-height: 12rem;
        border-style: dashed;
        border-radius: 1rem;
        background:
            linear-gradient(135deg, rgba(255, 255, 255, 0.74), rgba(255, 255, 255, 0)),
            var(--color-base-100);
    }

    .jst-icon {
        flex-shrink: 0;
    }
}

/* Reduce animations when requested by the user */

@media (prefers-reduced-motion: reduce) {
    .jst-nav-link,
    .jst-nav-group__summary,
    .jst-nav-chevron,
    .jst-preview-card {
        transition: none;
    }
}

@media (max-width: 1023px) {
    .jst-sidebar {
        position: relative;
        height: 100vh;
        box-shadow: 0.5rem 0 2rem rgba(10, 52, 64, 0.12);
    }
}

@media (max-width: 639px) {
    .jst-main {
        padding: 1.25rem 1rem 2rem;
    }

    .jst-header .navbar {
        min-height: 4rem;
        padding-inline: 0.75rem;
    }

    .jst-preview-card {
        min-height: 6.75rem;
    }
}
