document.addEventListener('DOMContentLoaded', () => {
    const body = document.body;
    const sidebarToggle = document.getElementById('adminSidebarToggle');
    const sidebarBackdrop = document.getElementById('adminSidebarBackdrop');
    const sidebarCollapse = document.getElementById('adminSidebarCollapse');
    const loadingLayer = document.getElementById('adminLoadingLayer');
    const collapsedKey = 'dscmkids-admin-sidebar-collapsed';

    function setCollapsed(collapsed) {
        body.classList.toggle('admin-sidebar-collapsed', collapsed);
        try {
            window.localStorage.setItem(collapsedKey, collapsed ? '1' : '0');
        } catch (error) {
            // Ignore localStorage failures.
        }
    }

    function restoreCollapsed() {
        try {
            if (window.localStorage.getItem(collapsedKey) === '1' && window.innerWidth > 980) {
                body.classList.add('admin-sidebar-collapsed');
            }
        } catch (error) {
            // Ignore localStorage failures.
        }
    }

    function openSidebar() {
        body.classList.add('admin-sidebar-open');
    }

    function closeSidebar() {
        body.classList.remove('admin-sidebar-open');
    }

    sidebarToggle?.addEventListener('click', () => {
        if (window.innerWidth <= 980) {
            openSidebar();
            return;
        }

        setCollapsed(!body.classList.contains('admin-sidebar-collapsed'));
    });

    sidebarBackdrop?.addEventListener('click', closeSidebar);
    document.querySelectorAll('[data-close-sidebar-link]').forEach((link) => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 980) {
                closeSidebar();
            }
        });
    });

    sidebarCollapse?.addEventListener('click', () => {
        setCollapsed(!body.classList.contains('admin-sidebar-collapsed'));
    });

    document.querySelectorAll('[data-toast]').forEach((toast, index) => {
        const closeButton = toast.querySelector('[data-toast-close]');
        const dismiss = () => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(-6px)';
            window.setTimeout(() => toast.remove(), 180);
        };

        closeButton?.addEventListener('click', dismiss);
        window.setTimeout(dismiss, 4200 + (index * 400));
    });

    document.querySelectorAll('form[data-loading-form], form:not([method="GET"])').forEach((form) => {
        form.addEventListener('submit', () => {
            loadingLayer?.classList.add('is-visible');
        });
    });

    document.querySelectorAll('[data-check-all]').forEach((input) => {
        const selector = input.getAttribute('data-check-all');
        if (!selector) {
            return;
        }

        input.addEventListener('change', () => {
            document.querySelectorAll(selector).forEach((target) => {
                if (target instanceof HTMLInputElement) {
                    target.checked = input.checked;
                }
            });
        });
    });

    window.addEventListener('pageshow', () => {
        loadingLayer?.classList.remove('is-visible');
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth > 980) {
            closeSidebar();
        }
    });

    restoreCollapsed();
});
