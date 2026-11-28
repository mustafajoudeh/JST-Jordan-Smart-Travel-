import { getCurrentUser, hasSession, logout } from '../api/auth';

export function initHome() {
    const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    const hero = document.querySelector(".cinematic-hero");
    const supportsParallax = window.matchMedia("(hover: hover) and (pointer: fine) and (min-width: 1024px)").matches;

    if (hero && supportsParallax && !reducedMotion) {
        hero.addEventListener("pointermove", (event) => {
            const bounds = hero.getBoundingClientRect();
            const horizontal = (event.clientX - bounds.left) / bounds.width - 0.5;
            const vertical = (event.clientY - bounds.top) / bounds.height - 0.5;
            hero.style.setProperty("--hero-shift-x", `${horizontal * -7}px`);
            hero.style.setProperty("--hero-shift-y", `${vertical * -5}px`);
        });

        hero.addEventListener("pointerleave", () => {
            hero.style.setProperty("--hero-shift-x", "0px");
            hero.style.setProperty("--hero-shift-y", "0px");
        });
    }

    if (!reducedMotion && "IntersectionObserver" in window) {
        document.documentElement.classList.add("has-motion");

        const revealObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add("is-visible");
                    observer.unobserve(entry.target);
                }
            });
        }, { rootMargin: "0px 0px -8% 0px", threshold: 0.08 });

        document.querySelectorAll(".reveal-on-scroll").forEach((element) => revealObserver.observe(element));
    }

    const menuButton = document.querySelector('[data-menu-toggle]');
    const menu = document.querySelector('[data-site-nav]');

    const closeMenu = () => {
        menuButton?.setAttribute('aria-expanded', 'false');
        menuButton?.setAttribute('aria-label', 'Open navigation');
        menu?.classList.remove('is-open');
    };

    menuButton?.addEventListener('click', () => {
        const expanded = menuButton.getAttribute('aria-expanded') === 'true';
        menuButton.setAttribute('aria-expanded', String(!expanded));
        menuButton.setAttribute('aria-label', expanded ? 'Open navigation' : 'Close navigation');
        menu?.classList.toggle('is-open', !expanded);
    });

    menu?.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', closeMenu);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && menuButton?.getAttribute('aria-expanded') === 'true') {
            closeMenu();
            menuButton.focus();
        }
    });

    const authenticated = hasSession();
    document.querySelector('[data-auth-links]')?.classList.toggle('hidden', authenticated);
    document.querySelector('[data-authenticated-links]')?.classList.toggle('hidden', !authenticated);

    const user = getCurrentUser();
    const dashboardLink = document.querySelector('[data-home-dashboard]');
    if (user && dashboardLink) {
        dashboardLink.href = user.role === 'system_admin' ? '/admin/dashboard' : '/workspace';
    }

    document.querySelectorAll('[data-logout]').forEach((button) => {
        button.addEventListener('click', () => logout());
    });
}
