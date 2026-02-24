document.addEventListener('DOMContentLoaded', () => {
    const lightbox = document.getElementById('lightbox');
    const lightboxImage = document.getElementById('lightboxImage');
    const lightboxMeta = document.getElementById('lightboxMeta');
    const lightboxClose = document.getElementById('lightboxClose');
    const lightboxPrev = document.getElementById('lightboxPrev');
    const lightboxNext = document.getElementById('lightboxNext');
    const lightboxItems = Array.from(document.querySelectorAll('[data-lightbox-src]'));

    if (!lightbox || !lightboxImage || !lightboxMeta || lightboxItems.length === 0) {
        return;
    }

    let activeLightboxIndex = -1;

    function renderLightbox(index) {
        const safeIndex = (index + lightboxItems.length) % lightboxItems.length;
        const item = lightboxItems[safeIndex];

        activeLightboxIndex = safeIndex;
        lightboxImage.src = item.getAttribute('data-lightbox-src') || '';
        lightboxImage.alt = item.getAttribute('data-lightbox-title') || 'Gallery';
        lightboxMeta.textContent = (item.getAttribute('data-lightbox-title') || '') + ' | ' + (item.getAttribute('data-lightbox-meta') || '');
    }

    function closeLightbox() {
        lightbox.classList.remove('open');
        lightboxImage.src = '';
        lightboxMeta.textContent = '';
        document.body.style.overflow = '';
        activeLightboxIndex = -1;
    }

    function showNext(step) {
        if (!lightbox.classList.contains('open')) {
            return;
        }

        renderLightbox(activeLightboxIndex + step);
    }

    lightboxItems.forEach((img, index) => {
        img.addEventListener('click', () => {
            renderLightbox(index);
            lightbox.classList.add('open');
            document.body.style.overflow = 'hidden';
        });
    });

    lightboxClose?.addEventListener('click', closeLightbox);
    lightboxPrev?.addEventListener('click', () => showNext(-1));
    lightboxNext?.addEventListener('click', () => showNext(1));

    lightbox.addEventListener('click', (event) => {
        if (event.target === lightbox) {
            closeLightbox();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && lightbox.classList.contains('open')) {
            closeLightbox();
            return;
        }

        if (event.key === 'ArrowLeft') {
            showNext(-1);
        }

        if (event.key === 'ArrowRight') {
            showNext(1);
        }
    });
});
