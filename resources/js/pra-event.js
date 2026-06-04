const ready = (callback) => {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', callback);
    } else {
        callback();
    }
};

ready(() => {
    const mobileMenu = document.querySelector('[data-pra-mobile-menu]');
    const menuToggle = document.querySelector('[data-pra-menu-toggle]');

    const closeMobileMenu = () => {
        if (!mobileMenu) return;
        mobileMenu.hidden = true;
        document.body.style.overflow = '';
        menuToggle?.setAttribute('aria-expanded', 'false');
    };

    const openMobileMenu = () => {
        if (!mobileMenu) return;
        mobileMenu.hidden = false;
        document.body.style.overflow = 'hidden';
        menuToggle?.setAttribute('aria-expanded', 'true');
    };

    document.querySelector('[data-pra-menu-toggle]')?.addEventListener('click', () => {
        if (!mobileMenu) return;
        if (mobileMenu.hidden) {
            openMobileMenu();
        } else {
            closeMobileMenu();
        }
    });

    mobileMenu?.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', closeMobileMenu);
    });
    document.querySelector('[data-pra-menu-close]')?.addEventListener('click', closeMobileMenu);

    const praNav = document.querySelector('[data-pra-auto-collapse-nav]');
    const praMoreMenu = praNav?.querySelector('[data-pra-more-menu]');
    const praMoreToggle = praNav?.querySelector('[data-pra-more-toggle]');
    const praMorePanel = praNav?.querySelector('[data-pra-more-panel]');
    const praNavLinks = praNav ? Array.from(praNav.querySelectorAll(':scope > a')) : [];
    let praCollapseQueued = false;

    const restorePraNavLinks = () => {
        if (!praNav || !praMoreMenu) return;
        praNavLinks.forEach((link) => praNav.insertBefore(link, praMoreMenu));
    };

    const closePraMore = () => {
        praMoreMenu?.classList.remove('open');
        praMoreToggle?.setAttribute('aria-expanded', 'false');
    };

    const syncPraNavCollapse = () => {
        if (!praNav || !praMoreMenu || !praMorePanel || praNavLinks.length === 0) return;

        restorePraNavLinks();
        praMorePanel.replaceChildren();
        closePraMore();
        praMoreMenu.hidden = true;

        if (window.innerWidth <= 720) return;

        praMoreMenu.hidden = false;
        const movableLinks = [...praNavLinks];

        while (praNav.scrollWidth > praNav.clientWidth + 1 && movableLinks.length > 1) {
            const link = movableLinks.pop();
            if (!link) break;
            praMorePanel.prepend(link);
        }

        if (praMorePanel.children.length === 0) {
            praMoreMenu.hidden = true;
        }
    };

    const queuePraNavCollapse = () => {
        if (praCollapseQueued) return;
        praCollapseQueued = true;
        window.requestAnimationFrame(() => {
            praCollapseQueued = false;
            syncPraNavCollapse();
        });
    };

    praMoreToggle?.addEventListener('click', (event) => {
        event.stopPropagation();
        const nextOpen = !praMoreMenu?.classList.contains('open');
        praMoreMenu?.classList.toggle('open', nextOpen);
        praMoreToggle.setAttribute('aria-expanded', nextOpen ? 'true' : 'false');
    });
    praMorePanel?.addEventListener('click', (event) => {
        if (event.target.closest('a')) closePraMore();
    });
    document.addEventListener('click', (event) => {
        if (!praMoreMenu?.contains(event.target)) closePraMore();
    });
    window.addEventListener('resize', () => {
        queuePraNavCollapse();
        if (window.innerWidth > 720) closeMobileMenu();
    });
    window.addEventListener('load', queuePraNavCollapse);
    if ('ResizeObserver' in window && praNav) {
        new ResizeObserver(queuePraNavCollapse).observe(praNav);
    }
    queuePraNavCollapse();

    const splashModal = document.querySelector('[data-splash-modal]');
    const showSplash = !window.sessionStorage.getItem('pra2026SplashClosed');
    if (splashModal && showSplash) {
        window.setTimeout(() => splashModal.classList.add('is-visible'), 450);
    }

    const closeSplash = () => {
        splashModal?.classList.remove('is-visible');
        window.sessionStorage.setItem('pra2026SplashClosed', '1');
    };

    document.querySelectorAll('[data-splash-close]').forEach((button) => {
        button.addEventListener('click', closeSplash);
    });
    document.querySelector('[data-splash-register]')?.addEventListener('click', closeSplash);

    document.querySelectorAll('[data-modal-close]').forEach((button) => {
        button.addEventListener('click', () => {
            button.closest('.pra-modal')?.classList.remove('is-visible');
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeMobileMenu();
            closePraMore();
        }
    });

    const updateCountdowns = () => {
        document.querySelectorAll('[data-countdown-card]').forEach((card) => {
            const output = card.querySelector('[data-countdown-output]');
            const dateValue = card.getAttribute('data-start');
            if (!output || !dateValue) return;

            const target = new Date(`${dateValue}T00:00:00+07:00`).getTime();
            const diff = target - Date.now();

            if (diff <= 0) {
                output.textContent = 'Event sedang berlangsung / sudah dimulai';
                return;
            }

            const days = Math.floor(diff / 86400000);
            const hours = Math.floor((diff % 86400000) / 3600000);
            const minutes = Math.floor((diff % 3600000) / 60000);
            const seconds = Math.floor((diff % 60000) / 1000);
            output.textContent = `${days} hari ${hours} jam ${minutes} menit ${seconds} detik`;
        });
    };

    updateCountdowns();
    window.setInterval(updateCountdowns, 1000);

    document.querySelectorAll('[data-pra-slider]').forEach((slider) => {
        const slides = [...slider.querySelectorAll('img')];
        if (slides.length < 2) return;
        let index = 0;

        window.setInterval(() => {
            slides[index]?.classList.remove('active');
            index = (index + 1) % slides.length;
            slides[index]?.classList.add('active');
        }, 3600);
    });

    const form = document.querySelector('[data-pra-form]');
    if (!form) return;

    const allergySelect = form.querySelector('[data-allergy-select]');
    const allergyNotes = form.querySelector('[data-allergy-notes]');
    const paymentSelect = form.querySelector('[data-payment-select]');
    const proofField = form.querySelector('[data-proof-field]');
    const proofInput = proofField?.querySelector('input');
    const cashInfo = form.querySelector('[data-cash-info]');

    const syncAllergy = () => {
        const show = allergySelect?.value === 'yes';
        if (allergyNotes) allergyNotes.hidden = !show;
        allergyNotes?.querySelector('textarea')?.toggleAttribute('required', show);
    };

    const syncPayment = () => {
        const method = paymentSelect?.value;
        const isTransfer = method === 'transfer';
        if (proofField) proofField.hidden = !isTransfer;
        if (proofInput) proofInput.required = isTransfer;
        if (cashInfo) cashInfo.hidden = method !== 'cash';
    };

    allergySelect?.addEventListener('change', syncAllergy);
    paymentSelect?.addEventListener('change', syncPayment);
    syncAllergy();
    syncPayment();
});
