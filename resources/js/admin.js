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
            if (window.localStorage.getItem(collapsedKey) === '1' && window.innerWidth > 860) {
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
        if (window.innerWidth <= 860) {
            openSidebar();
            return;
        }

        setCollapsed(!body.classList.contains('admin-sidebar-collapsed'));
    });

    sidebarBackdrop?.addEventListener('click', closeSidebar);
    document.querySelectorAll('[data-close-sidebar-link]').forEach((link) => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 860) {
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
        window.setTimeout(dismiss, 5000 + (index * 400));
    });

    document.querySelectorAll('[data-broadcast-send-form]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (form.dataset.broadcastConfirmed === '1') {
                return;
            }

            const filterSource = document.querySelector('[data-broadcast-filter-source]');
            const messageSource = document.querySelector('[data-broadcast-message-source]');
            const filterField = form.querySelector('[data-broadcast-filter-field]');
            const messageField = form.querySelector('[data-broadcast-message-field]');
            const modal = document.querySelector('[data-broadcast-confirm-modal]');
            const confirmCount = modal?.querySelector('[data-broadcast-confirm-count]');
            const confirmButton = modal?.querySelector('[data-broadcast-confirm]');
            const cancelButton = modal?.querySelector('[data-broadcast-cancel]');

            if (filterSource instanceof HTMLSelectElement && filterField instanceof HTMLInputElement) {
                if (filterSource.value !== filterField.value) {
                    event.preventDefault();
                    window.alert('Klik Preview Penerima setelah mengubah filter sebelum mengirim broadcast.');
                    loadingLayer?.classList.remove('is-visible');
                    return;
                }

                filterField.value = filterSource.value;
            }

            if (messageSource instanceof HTMLTextAreaElement && messageField instanceof HTMLInputElement) {
                messageField.value = messageSource.value;
            }

            const recipientCount = form.getAttribute('data-broadcast-count') || '0';

            if (!modal || !confirmButton || !cancelButton) {
                if (!window.confirm(`Kirim pesan ke ${recipientCount} peserta?`)) {
                    event.preventDefault();
                    loadingLayer?.classList.remove('is-visible');
                }
                return;
            }

            event.preventDefault();
            if (confirmCount) confirmCount.textContent = recipientCount;
            modal.hidden = false;

            const closeModal = () => {
                modal.hidden = true;
            };

            const confirmSend = () => {
                form.dataset.broadcastConfirmed = '1';
                closeModal();
                form.requestSubmit();
            };

            cancelButton.onclick = closeModal;
            modal.onclick = (modalEvent) => {
                if (modalEvent.target === modal) closeModal();
            };
            confirmButton.onclick = confirmSend;
        });
    });

    document.querySelectorAll('form[data-loading-form], form:not([method="GET"])').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (event.defaultPrevented) {
                return;
            }

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
        if (window.innerWidth > 860) {
            closeSidebar();
        }
    });

    restoreCollapsed();
});
