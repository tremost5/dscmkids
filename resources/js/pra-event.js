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

    const galleryViewer = document.querySelector('[data-pra-gallery-viewer]');
    const galleryActiveImage = galleryViewer?.querySelector('[data-pra-gallery-active]');
    const galleryCaption = galleryViewer?.querySelector('[data-pra-gallery-caption]');
    const galleryCounter = galleryViewer?.querySelector('[data-pra-gallery-counter]');
    const galleryImages = [...document.querySelectorAll('[data-pra-gallery-image]')];
    const galleryItems = [...galleryImages.reduce((items, image) => {
        const itemIndex = Number.parseInt(image.getAttribute('data-pra-gallery-index') || `${items.size}`, 10);
        if (!items.has(itemIndex)) {
            items.set(itemIndex, {
                src: image.currentSrc || image.src,
                alt: image.getAttribute('alt') || 'Foto PRA 2026',
            });
        }
        return items;
    }, new Map()).entries()]
        .sort(([left], [right]) => left - right)
        .map(([, item]) => item);
    let galleryIndex = 0;
    let galleryHistoryPushed = false;
    let previousBodyOverflow = '';
    let touchStartX = 0;

    const renderGallery = () => {
        if (!galleryActiveImage || !galleryCaption || !galleryCounter || galleryItems.length === 0) return;
        const item = galleryItems[galleryIndex];
        galleryActiveImage.src = item.src;
        galleryActiveImage.alt = item.alt;
        galleryCaption.textContent = item.alt;
        galleryCounter.textContent = `${galleryIndex + 1} / ${galleryItems.length}`;
    };

    const closeGallery = (fromHistory = false) => {
        if (!galleryViewer || galleryViewer.hidden) return;

        if (!fromHistory && galleryHistoryPushed) {
            window.history.back();
            return;
        }

        galleryViewer.hidden = true;
        document.body.style.overflow = previousBodyOverflow;
        galleryHistoryPushed = false;
    };

    const openGallery = (index) => {
        if (!galleryViewer || galleryItems.length === 0) return;
        previousBodyOverflow = document.body.style.overflow;
        galleryIndex = Math.max(0, Math.min(index, galleryItems.length - 1));
        renderGallery();
        galleryViewer.hidden = false;
        document.body.style.overflow = 'hidden';

        if (!galleryHistoryPushed) {
            window.history.pushState({ praGallery: true }, '', window.location.href);
            galleryHistoryPushed = true;
        }
    };

    const moveGallery = (direction) => {
        if (galleryItems.length === 0) return;
        galleryIndex = (galleryIndex + direction + galleryItems.length) % galleryItems.length;
        renderGallery();
    };

    galleryImages.forEach((image) => {
        image.addEventListener('click', () => {
            const imageIndex = Number.parseInt(image.getAttribute('data-pra-gallery-index') || '0', 10);
            const foundIndex = galleryItems.findIndex((item) => item.src === (image.currentSrc || image.src));
            openGallery(foundIndex >= 0 ? foundIndex : imageIndex);
        });
    });

    galleryViewer?.querySelector('[data-pra-gallery-close]')?.addEventListener('click', () => closeGallery());
    galleryViewer?.querySelector('[data-pra-gallery-prev]')?.addEventListener('click', () => moveGallery(-1));
    galleryViewer?.querySelector('[data-pra-gallery-next]')?.addEventListener('click', () => moveGallery(1));
    galleryViewer?.addEventListener('pointerdown', (event) => {
        touchStartX = event.clientX;
    });
    galleryViewer?.addEventListener('pointerup', (event) => {
        const diff = event.clientX - touchStartX;
        if (Math.abs(diff) < 44) return;
        moveGallery(diff > 0 ? -1 : 1);
    });

    window.addEventListener('popstate', () => {
        if (galleryViewer && !galleryViewer.hidden) {
            closeGallery(true);
        }
    });

    document.addEventListener('keydown', (event) => {
        if (!galleryViewer || galleryViewer.hidden) return;
        if (event.key === 'Escape') closeGallery();
        if (event.key === 'ArrowLeft') moveGallery(-1);
        if (event.key === 'ArrowRight') moveGallery(1);
    });

    const form = document.querySelector('[data-pra-form]');
    if (!form) return;

    const allergySelect = form.querySelector('[data-allergy-select]');
    const allergyNotes = form.querySelector('[data-allergy-notes]');
    const paymentSelect = form.querySelector('[data-payment-select]');
    const proofField = form.querySelector('[data-proof-field]');
    const proofInput = proofField?.querySelector('input');
    const cashInfo = form.querySelector('[data-cash-info]');
    const transferInfo = form.querySelector('[data-transfer-info]');
    const copyAccountButton = form.querySelector('[data-copy-account]');
    const accountNumber = form.querySelector('[data-account-number]')?.textContent?.trim() || '';

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
        if (transferInfo) transferInfo.hidden = !isTransfer;
    };

    const copyAccountNumber = async () => {
        if (!copyAccountButton || !accountNumber) return;

        try {
            if (navigator.clipboard?.writeText) {
                await navigator.clipboard.writeText(accountNumber);
            } else {
                const tempInput = document.createElement('input');
                tempInput.value = accountNumber;
                document.body.appendChild(tempInput);
                tempInput.select();
                document.execCommand('copy');
                tempInput.remove();
            }

            const originalText = copyAccountButton.textContent;
            copyAccountButton.textContent = 'Nomor Disalin';
            window.setTimeout(() => {
                copyAccountButton.textContent = originalText;
            }, 1800);
        } catch (error) {
            copyAccountButton.textContent = accountNumber;
        }
    };

    allergySelect?.addEventListener('change', syncAllergy);
    paymentSelect?.addEventListener('change', syncPayment);
    copyAccountButton?.addEventListener('click', copyAccountNumber);
    syncAllergy();
    syncPayment();
});
