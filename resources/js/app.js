document.addEventListener('DOMContentLoaded', () => {
    // Animasi ringan: elemen dengan class .reveal muncul saat masuk viewport.
    const revealElements = document.querySelectorAll('.reveal');

    if (revealElements.length > 0 && 'IntersectionObserver' in window) {
        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            },
            { threshold: 0.12 },
        );

        revealElements.forEach((element) => observer.observe(element));
    } else {
        revealElements.forEach((element) => element.classList.add('is-visible'));
    }

    // Toggle sidebar dashboard pada layar kecil.
    const sidebarToggles = document.querySelectorAll('[data-sidebar-toggle]');
    const sidebar = document.querySelector('[data-sidebar]');
    const sidebarOverlay = document.querySelector('[data-sidebar-overlay]');

    if (sidebarToggles.length > 0 && sidebar) {
        const closeSidebar = () => {
            sidebar.classList.add('-translate-x-full');
            sidebarOverlay?.classList.add('hidden');
        };

        sidebarToggles.forEach((toggle) => {
            toggle.addEventListener('click', () => {
                const isOpen = !sidebar.classList.contains('-translate-x-full');

                if (isOpen) {
                    closeSidebar();
                } else {
                    sidebar.classList.remove('-translate-x-full');
                    sidebarOverlay?.classList.remove('hidden');
                }
            });
        });

        sidebarOverlay?.addEventListener('click', closeSidebar);
    }

    // Toggle menu navigasi website publik pada layar kecil.
    const navToggle = document.querySelector('[data-nav-toggle]');
    const navMenu = document.querySelector('[data-nav-menu]');

    if (navToggle && navMenu) {
        navToggle.addEventListener('click', () => {
            navMenu.classList.toggle('hidden');
        });
    }
});
