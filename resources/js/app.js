const adminSidebar = document.querySelector('[data-admin-sidebar]');

if (adminSidebar) {
    const overlay = document.querySelector('[data-sidebar-overlay]');
    const openButton = document.querySelector('[data-sidebar-open]');
    const closeButton = document.querySelector('[data-sidebar-close]');

    const openSidebar = () => {
        adminSidebar.classList.remove('-translate-x-full');
        adminSidebar.classList.add('translate-x-0');
        overlay?.classList.remove('hidden');
        openButton?.setAttribute('aria-expanded', 'true');
    };

    const closeSidebar = () => {
        adminSidebar.classList.add('-translate-x-full');
        adminSidebar.classList.remove('translate-x-0');
        overlay?.classList.add('hidden');
        openButton?.setAttribute('aria-expanded', 'false');
    };

    openButton?.addEventListener('click', openSidebar);
    closeButton?.addEventListener('click', closeSidebar);
    overlay?.addEventListener('click', closeSidebar);

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeSidebar();
        }
    });
}
