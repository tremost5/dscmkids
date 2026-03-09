document.addEventListener('DOMContentLoaded', () => {
    const landingData = window.DSCM_LANDING_DATA || {};
    const slides = Array.isArray(landingData.slides) ? landingData.slides : [];
    const classData = Array.isArray(landingData.classData) ? landingData.classData : [];
    const presentTotal = Number(landingData.presentTotal || 0);
    const absentTotal = Number(landingData.absentTotal || 0);
    const isStudentLoggedIn = Boolean(landingData.isStudentLoggedIn);
    const quizSubmitUrl = String(landingData.quizSubmitUrl || '');
    const rewardClaimUrl = String(landingData.rewardClaimUrl || '');
    const resetSeenUrl = String(landingData.resetSeenUrl || '');
    const csrfToken = String(landingData.csrfToken || '');

    const slideEls = Array.from(document.querySelectorAll('[data-slide]'));
    const dots = Array.from(document.querySelectorAll('[data-dot]'));
    const titleEl = document.getElementById('heroTitle');
    const subtitleEl = document.getElementById('heroSubtitle');
    const heroContent = document.getElementById('heroContent');

    const slideDuration = 4600;
    let index = 0;

    function animateText(newTitle, newSubtitle) {
        if (!heroContent || !titleEl || !subtitleEl) {
            return;
        }

        heroContent.classList.remove('enter');
        setTimeout(() => {
            titleEl.textContent = newTitle || 'Sekolah Minggu DSCMKids';
            subtitleEl.textContent = newSubtitle || '';
            heroContent.classList.add('enter');
        }, 220);
    }

    function goToSlide(nextIndex) {
        if (!slideEls.length || nextIndex === index) {
            return;
        }

        slideEls[index]?.classList.remove('active');
        dots[index]?.classList.remove('active');

        index = nextIndex;

        slideEls[index]?.classList.add('active');
        dots[index]?.classList.add('active');
        animateText(slides[index]?.title, slides[index]?.subtitle);
    }

    dots.forEach((dot, i) => {
        dot.addEventListener('click', () => {
            goToSlide(i);
            resetAutoRotate();
        });
    });

    let timer;
    function startAutoRotate() {
        if (slideEls.length <= 1) {
            return;
        }

        timer = setInterval(() => {
            goToSlide((index + 1) % slideEls.length);
        }, slideDuration);
    }

    function resetAutoRotate() {
        clearInterval(timer);
        startAutoRotate();
    }

    startAutoRotate();

    const siteMenuToggle = document.getElementById('siteMenuToggle');
    const siteMenuClose = document.getElementById('siteMenuClose');
    const siteMobileMenu = document.getElementById('siteMobileMenu');

    function openSiteMenu() {
        if (!siteMobileMenu) {
            return;
        }

        siteMobileMenu.hidden = false;
        siteMenuToggle?.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden';
    }

    function closeSiteMenu() {
        if (!siteMobileMenu) {
            return;
        }

        siteMobileMenu.hidden = true;
        siteMenuToggle?.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
    }

    siteMenuToggle?.addEventListener('click', () => {
        if (siteMobileMenu?.hidden === false) {
            closeSiteMenu();
            return;
        }

        openSiteMenu();
    });

    siteMenuClose?.addEventListener('click', closeSiteMenu);
    siteMobileMenu?.addEventListener('click', (event) => {
        if (event.target === siteMobileMenu) {
            closeSiteMenu();
        }
    });
    siteMobileMenu?.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', closeSiteMenu);
    });

    const quickNav = document.querySelector('.quick-nav');
    if (quickNav) {
        const quickLinks = Array.from(quickNav.querySelectorAll('a'));
        const sectionMap = quickLinks
            .map((link) => {
                const href = link.getAttribute('href') || '';
                if (!href.startsWith('#')) {
                    return null;
                }

                const section = document.querySelector(href);
                return section ? { link, section } : null;
            })
            .filter(Boolean);

        const setActiveQuickLink = (activeLink) => {
            quickLinks.forEach((link) => {
                link.classList.toggle('active', link === activeLink);
            });
        };

        quickNav.addEventListener('click', (event) => {
            const targetLink = event.target.closest('a');
            if (!targetLink) {
                return;
            }

            setActiveQuickLink(targetLink);
        });

        if ('IntersectionObserver' in window && sectionMap.length > 0) {
            const sectionObserver = new IntersectionObserver((entries) => {
                const visibleEntry = entries
                    .filter((entry) => entry.isIntersecting)
                    .sort((a, b) => b.intersectionRatio - a.intersectionRatio)[0];

                if (!visibleEntry) {
                    return;
                }

                const current = sectionMap.find((item) => item.section === visibleEntry.target);
                if (current) {
                    setActiveQuickLink(current.link);
                }
            }, {
                threshold: [0.2, 0.45, 0.7],
                rootMargin: '-18% 0px -52% 0px',
            });

            sectionMap.forEach((item) => sectionObserver.observe(item.section));
            setActiveQuickLink(sectionMap[0].link);
        }
    }

    const surfaceToast = document.querySelector('[data-surface-toast]');
    if (surfaceToast) {
        const closeToast = () => {
            surfaceToast.style.opacity = '0';
            surfaceToast.style.transform = 'translateY(-6px)';
            window.setTimeout(() => surfaceToast.remove(), 180);
        };

        surfaceToast.querySelector('[data-surface-toast-close]')?.addEventListener('click', closeToast);
        window.setTimeout(closeToast, 4200);
    }

    const labels = classData.map((x) => x.class).concat(['Tidak Hadir']);
    const values = classData.map((x) => Number(x.present)).concat([absentTotal]);
    const colors = ['#2563eb','#0ea5e9','#0d9488','#14b8a6','#f59e0b','#f97316','#ec4899','#a855f7','#6366f1','#94a3b8'];

    document.querySelectorAll('[data-chip]').forEach((el) => {
        const i = Number(el.getAttribute('data-chip'));
        el.style.background = colors[i % colors.length];
    });

    const donutCanvas = document.getElementById('attendanceDonut');
    if (typeof Chart !== 'undefined' && donutCanvas && labels.length > 0) {
        new Chart(donutCanvas.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels,
                datasets: [{ data: values, backgroundColor: labels.map((_, i) => colors[i % colors.length]), borderWidth: 0, hoverOffset: 8 }],
            },
            options: { responsive: true, cutout: '68%', plugins: { legend: { display: false }, tooltip: { backgroundColor: '#0f172a', titleColor: '#fff', bodyColor: '#e2e8f0' } } }
        });
    }

    function animateCounter(el, targetValue) {
        if (!el || Number.isNaN(targetValue) || el.dataset.counterAnimated === '1') {
            return;
        }

        el.dataset.counterAnimated = '1';
        const suffix = el.getAttribute('data-suffix') || '';
        const duration = 720;
        const start = performance.now();

        function tick(now) {
            const progress = Math.min(1, (now - start) / duration);
            const eased = 1 - Math.pow(1 - progress, 3);
            const current = Math.round(targetValue * eased);

            el.textContent = current.toLocaleString('id-ID') + suffix;
            if (progress < 1) {
                requestAnimationFrame(tick);
            }
        }

        requestAnimationFrame(tick);
    }

    const rollEl = document.getElementById('centerRoll');
    if (rollEl) {
        animateCounter(rollEl, presentTotal);
    }

    const revealEls = Array.from(document.querySelectorAll('.reveal'));
    if ('IntersectionObserver' in window && revealEls.length > 0) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    const childItems = Array.from(entry.target.querySelectorAll('.stat, .item, .teacher, .photo, .game-card, .quiz-item'));
                    childItems.forEach((child, index) => {
                        child.style.transitionDelay = (index * 55) + 'ms';
                    });
                    entry.target.classList.add('is-visible');

                    entry.target.querySelectorAll('[data-counter]').forEach((counterEl) => {
                        const target = Number(counterEl.getAttribute('data-counter') || 0);
                        animateCounter(counterEl, target);
                    });

                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.14 });

        revealEls.forEach((el) => observer.observe(el));
    } else {
        revealEls.forEach((el) => el.classList.add('is-visible'));
    }

    window.addEventListener('resize', () => {
        if (window.innerWidth > 900) {
            closeSiteMenu();
        }
    });

    const challengeInputs = Array.from(document.querySelectorAll('[data-challenge-index]'));
    const challengeProgressBar = document.getElementById('challengeProgressBar');
    const challengeScore = document.getElementById('challengeScore');
    const challengeStorageKey = 'dscmkids-weekly-challenges-v1';

    function loadChallengeState() {
        try {
            const raw = localStorage.getItem(challengeStorageKey);
            if (!raw) {
                return {};
            }
            const parsed = JSON.parse(raw);
            return typeof parsed === 'object' && parsed ? parsed : {};
        } catch (error) {
            return {};
        }
    }

    function saveChallengeState(nextState) {
        try {
            localStorage.setItem(challengeStorageKey, JSON.stringify(nextState));
        } catch (error) {
            // Ignore localStorage write errors (private mode / quota).
        }
    }

    function refreshChallengeUI() {
        const total = challengeInputs.length;
        const done = challengeInputs.filter((input) => input.checked).length;
        const percent = total === 0 ? 0 : Math.round((done / total) * 100);

        if (challengeProgressBar) {
            challengeProgressBar.style.width = percent + '%';
        }
        if (challengeScore) {
            challengeScore.textContent = done + '/' + total + ' Tantangan selesai';
        }
    }

    if (challengeInputs.length > 0) {
        const initialState = loadChallengeState();
        challengeInputs.forEach((input) => {
            const indexKey = input.getAttribute('data-challenge-index') || '';
            input.checked = Boolean(initialState[indexKey]);
            input.addEventListener('change', () => {
                const nextState = loadChallengeState();
                nextState[indexKey] = input.checked;
                saveChallengeState(nextState);
                refreshChallengeUI();
            });
        });
        refreshChallengeUI();
    }

    const quizForm = document.getElementById('dailyQuizForm');
    const quizResultBox = document.getElementById('quizResultBox');
    const dailyLeaderboardList = document.getElementById('dailyLeaderboardList');

    function renderLeaderboardRows(rows) {
        if (!dailyLeaderboardList || !Array.isArray(rows)) {
            return;
        }

        if (rows.length === 0) {
            dailyLeaderboardList.innerHTML = '<div class="leaderboard-row">Belum ada skor hari ini.</div>';
            return;
        }

        dailyLeaderboardList.innerHTML = rows.map((row) => {
            const rank = Number(row.rank || 0);
            const score = Number(row.score || 0);
            const safeName = String(row.name || 'Murid');

            return '<div class="leaderboard-row"><strong>#' + rank + ' ' + safeName + '</strong><span>' + score + ' pts</span></div>';
        }).join('');
    }

    if (quizForm && isStudentLoggedIn && quizSubmitUrl && csrfToken) {
        quizForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            const formData = new FormData(quizForm);
            const payload = { quiz_key: '', answers: {} };
            payload.quiz_key = String(formData.get('quiz_key') || '');

            for (const [name, value] of formData.entries()) {
                const match = String(name).match(/^answers\[(.+)\]$/);
                if (match) {
                    payload.answers[match[1]] = String(value);
                }
            }

            if (!payload.quiz_key) {
                if (quizResultBox) {
                    quizResultBox.textContent = 'Quiz key tidak valid.';
                }
                return;
            }

            try {
                if (quizResultBox) {
                    quizResultBox.textContent = 'Mengirim jawaban...';
                }

                const response = await fetch(quizSubmitUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify(payload),
                });

                const data = await response.json();
                if (!response.ok) {
                    const failMessage = String(data.message || 'Gagal menyimpan kuis.');
                    if (quizResultBox) {
                        quizResultBox.textContent = failMessage;
                    }
                    return;
                }

                if (quizResultBox) {
                    quizResultBox.textContent =
                        'Skor: ' + Number(data.best_score_today || data.score || 0) +
                        ' | Badge Harian: ' + String(data.daily_badge || '-') +
                        ' | Poin Total: ' + Number(data.points || 0);
                }
                renderLeaderboardRows(data.leaderboard || []);
            } catch (error) {
                if (quizResultBox) {
                    quizResultBox.textContent = 'Koneksi terputus. Coba lagi beberapa saat.';
                }
            }
        });
    }

    const claimWeeklyRewardBtn = document.getElementById('claimWeeklyRewardBtn');
    const claimRewardResult = document.getElementById('claimRewardResult');
    if (claimWeeklyRewardBtn && rewardClaimUrl && csrfToken) {
        claimWeeklyRewardBtn.addEventListener('click', async () => {
            claimWeeklyRewardBtn.disabled = true;
            if (claimRewardResult) {
                claimRewardResult.textContent = 'Memproses klaim reward...';
            }

            try {
                const response = await fetch(rewardClaimUrl, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                });
                const data = await response.json();
                if (!response.ok) {
                    if (claimRewardResult) {
                        claimRewardResult.textContent = String(data.message || 'Klaim reward gagal.');
                    }
                    claimWeeklyRewardBtn.disabled = false;
                    return;
                }

                if (claimRewardResult) {
                    claimRewardResult.textContent = 'Berhasil klaim +' + Number(data.reward_points || 0) + ' poin (' + String(data.badge || '-') + ').';
                }
            } catch (error) {
                if (claimRewardResult) {
                    claimRewardResult.textContent = 'Koneksi gagal. Coba lagi.';
                }
                claimWeeklyRewardBtn.disabled = false;
            }
        });
    }

    const dismissResetNotice = document.getElementById('dismissResetNotice');
    const resetNotice = document.getElementById('dailyResetNotice');
    if (dismissResetNotice && resetSeenUrl && csrfToken) {
        dismissResetNotice.addEventListener('click', async () => {
            if (resetNotice) {
                resetNotice.remove();
            }

            try {
                await fetch(resetSeenUrl, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                });
            } catch (error) {
                // Ignore dismiss network error.
            }
        });
    }

    const lightbox = document.getElementById('lightbox');
    const lightboxImage = document.getElementById('lightboxImage');
    const lightboxMeta = document.getElementById('lightboxMeta');
    const lightboxClose = document.getElementById('lightboxClose');
    const lightboxPrev = document.getElementById('lightboxPrev');
    const lightboxNext = document.getElementById('lightboxNext');
    const lightboxItems = Array.from(document.querySelectorAll('[data-lightbox-src]'));
    let activeLightboxIndex = -1;

    function renderLightbox(nextIndex) {
        if (!lightboxItems.length || !lightboxImage || !lightboxMeta) {
            return;
        }

        const safeIndex = (nextIndex + lightboxItems.length) % lightboxItems.length;
        const item = lightboxItems[safeIndex];

        activeLightboxIndex = safeIndex;
        lightboxImage.src = item.getAttribute('data-lightbox-src') || '';
        lightboxImage.alt = item.getAttribute('data-lightbox-title') || 'Gallery';
        lightboxMeta.textContent = (item.getAttribute('data-lightbox-title') || '') + ' | ' + (item.getAttribute('data-lightbox-meta') || '');
    }

    function closeLightbox() {
        if (!lightbox || !lightboxImage || !lightboxMeta) {
            return;
        }

        lightbox.classList.remove('open');
        lightboxImage.src = '';
        lightboxMeta.textContent = '';
        document.body.style.overflow = '';
        activeLightboxIndex = -1;
    }

    lightboxItems.forEach((img, i) => {
        img.addEventListener('click', () => {
            if (!lightbox) {
                return;
            }

            renderLightbox(i);
            lightbox.classList.add('open');
            document.body.style.overflow = 'hidden';
        });
    });

    function showNext(step) {
        if (!lightbox || !lightbox.classList.contains('open')) {
            return;
        }

        renderLightbox(activeLightboxIndex + step);
    }

    lightboxClose?.addEventListener('click', closeLightbox);
    lightboxPrev?.addEventListener('click', () => showNext(-1));
    lightboxNext?.addEventListener('click', () => showNext(1));
    lightbox?.addEventListener('click', (event) => {
        if (event.target === lightbox) {
            closeLightbox();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && siteMobileMenu?.hidden === false) {
            closeSiteMenu();
            return;
        }

        if (event.key === 'Escape' && lightbox?.classList.contains('open')) {
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
