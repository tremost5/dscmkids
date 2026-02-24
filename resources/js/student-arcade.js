document.addEventListener('DOMContentLoaded', () => {
    const cfg = window.DSCM_ARCADE_DATA || {};
    const isStudent = Boolean(cfg.isStudent);
    const submitUrl = String(cfg.submitUrl || '');
    const csrfToken = String(cfg.csrfToken || '');
    const feedbackEl = document.getElementById('arcadeFeedback');
    const leaderboardEl = document.getElementById('arcadeLeaderboardList');

    async function submitScore(gameKey, score) {
        if (!isStudent || !submitUrl || !csrfToken) {
            if (feedbackEl) {
                feedbackEl.textContent = 'Login murid dulu untuk menyimpan skor.';
            }
            return;
        }

        try {
            const response = await fetch(submitUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ game_key: gameKey, score }),
            });

            const data = await response.json();
            if (!response.ok) {
                if (feedbackEl) feedbackEl.textContent = String(data.message || 'Gagal simpan skor.');
                return;
            }

            if (feedbackEl) {
                feedbackEl.textContent = 'Skor tersimpan: ' + Number(data.best_score || 0) + ' | Total poin: ' + Number(data.points || 0);
            }

            if (leaderboardEl && Array.isArray(data.leaderboard)) {
                leaderboardEl.innerHTML = data.leaderboard.map((row) => {
                    return '<div class="leaderboard-row"><strong>#' + Number(row.rank || 0) + ' ' + String(row.name || 'Murid') + '</strong><span>' + String(row.game_key || '-') + ' - ' + Number(row.score || 0) + ' pts</span></div>';
                }).join('');
            }
        } catch (error) {
            if (feedbackEl) feedbackEl.textContent = 'Koneksi gagal. Coba lagi.';
        }
    }

    // Game 1: Memory Match
    const memoryWords = ['Kasih', 'Iman', 'Doa', 'Firman', 'Yesus', 'Syukur'];
    const memoryBoard = document.getElementById('memoryBoard');
    const memoryStatus = document.getElementById('memoryStatus');
    let memoryStartMs = 0;
    let memoryMatched = 0;
    let memoryOpen = [];
    function initMemory() {
        if (!memoryBoard) return;
        const cards = memoryWords.concat(memoryWords).sort(() => Math.random() - 0.5);
        memoryBoard.innerHTML = '';
        memoryOpen = [];
        memoryMatched = 0;
        memoryStartMs = Date.now();
        cards.forEach((word) => {
            const el = document.createElement('button');
            el.type = 'button';
            el.className = 'memory-card';
            el.dataset.word = word;
            el.textContent = '?';
            el.addEventListener('click', () => {
                if (el.classList.contains('matched') || el.classList.contains('open') || memoryOpen.length >= 2) return;
                el.classList.add('open');
                el.textContent = word;
                memoryOpen.push(el);
                if (memoryOpen.length === 2) {
                    const [a, b] = memoryOpen;
                    if (a.dataset.word === b.dataset.word) {
                        a.classList.add('matched');
                        b.classList.add('matched');
                        memoryMatched += 1;
                        memoryOpen = [];
                        if (memoryMatched === memoryWords.length && memoryStatus) {
                            const seconds = Math.max(1, Math.round((Date.now() - memoryStartMs) / 1000));
                            const score = Math.max(50, 220 - (seconds * 8));
                            memoryStatus.textContent = 'Selesai! Waktu ' + seconds + 's | Skor ' + score;
                            memoryStatus.dataset.score = String(score);
                        }
                    } else {
                        setTimeout(() => {
                            a.classList.remove('open');
                            b.classList.remove('open');
                            a.textContent = '?';
                            b.textContent = '?';
                            memoryOpen = [];
                        }, 550);
                    }
                }
            });
            memoryBoard.appendChild(el);
        });
        if (memoryStatus) {
            memoryStatus.textContent = 'Temukan semua pasangan kata.';
            memoryStatus.dataset.score = '0';
        }
    }
    document.getElementById('memoryStart')?.addEventListener('click', initMemory);
    document.getElementById('memorySubmit')?.addEventListener('click', () => submitScore('memory_match', Number(memoryStatus?.dataset.score || 0)));

    // Game 2: Bible Guess
    const guessData = [
        { q: 'Siapa yang membangun bahtera?', options: ['Musa', 'Nuh', 'Daud', 'Petrus'], answer: 'Nuh' },
        { q: 'Siapa yang melawan Goliat?', options: ['Daud', 'Elia', 'Yusuf', 'Yohanes'], answer: 'Daud' },
        { q: 'Siapa ibu Yesus?', options: ['Marta', 'Maria', 'Debora', 'Rut'], answer: 'Maria' },
        { q: 'Siapa murid yang menyangkal Yesus?', options: ['Petrus', 'Paulus', 'Yudas Tadeus', 'Filipus'], answer: 'Petrus' },
        { q: 'Siapa yang ditelan ikan besar?', options: ['Yunus', 'Yusuf', 'Yosua', 'Yakub'], answer: 'Yunus' },
    ];
    const guessGame = document.getElementById('guessGame');
    const guessStatus = document.getElementById('guessStatus');
    let guessIndex = 0;
    let guessCorrect = 0;
    function renderGuessQuestion() {
        if (!guessGame) return;
        const row = guessData[guessIndex];
        if (!row) {
            const score = guessCorrect * 20;
            guessGame.innerHTML = '<strong>Quiz selesai.</strong>';
            if (guessStatus) {
                guessStatus.textContent = 'Benar: ' + guessCorrect + '/5 | Skor ' + score;
                guessStatus.dataset.score = String(score);
            }
            return;
        }

        guessGame.innerHTML = '<div><strong>' + (guessIndex + 1) + '. ' + row.q + '</strong><div class="guess-options"></div></div>';
        const wrap = guessGame.querySelector('.guess-options');
        row.options.forEach((opt) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'guess-option';
            btn.textContent = opt;
            btn.addEventListener('click', () => {
                if (opt === row.answer) guessCorrect += 1;
                guessIndex += 1;
                renderGuessQuestion();
            });
            wrap?.appendChild(btn);
        });
    }
    document.getElementById('guessStart')?.addEventListener('click', () => {
        guessIndex = 0;
        guessCorrect = 0;
        if (guessStatus) {
            guessStatus.textContent = 'Jawab semua pertanyaan.';
            guessStatus.dataset.score = '0';
        }
        renderGuessQuestion();
    });
    document.getElementById('guessSubmit')?.addEventListener('click', () => submitScore('bible_guess', Number(guessStatus?.dataset.score || 0)));

    // Game 3: Verse Builder
    const verseLines = [
        ['kasihilah', 'seorang', 'akan', 'yang', 'lain'],
        ['Tuhan', 'adalah', 'gembalaku'],
        ['bersukacitalah', 'senantiasa', 'di', 'dalam', 'Tuhan'],
    ];
    const verseBuilder = document.getElementById('verseBuilder');
    const verseStatus = document.getElementById('verseStatus');
    let targetWords = [];
    let pickedWords = [];
    function initVerse() {
        if (!verseBuilder) return;
        targetWords = verseLines[Math.floor(Math.random() * verseLines.length)];
        pickedWords = [];
        const shuffled = [...targetWords].sort(() => Math.random() - 0.5);

        verseBuilder.innerHTML = '<div class="verse-target" id="verseTarget"></div><div class="verse-bank" id="verseBank"></div>';
        const targetEl = verseBuilder.querySelector('#verseTarget');
        const bankEl = verseBuilder.querySelector('#verseBank');

        shuffled.forEach((word) => {
            const chip = document.createElement('button');
            chip.type = 'button';
            chip.className = 'verse-chip';
            chip.textContent = word;
            chip.addEventListener('click', () => {
                pickedWords.push(word);
                chip.remove();
                if (targetEl) targetEl.innerHTML = pickedWords.map((w) => '<span class="verse-chip">' + w + '</span>').join('');
                if (pickedWords.length === targetWords.length) {
                    const correct = pickedWords.join(' ') === targetWords.join(' ');
                    const score = correct ? 120 : 40;
                    if (verseStatus) {
                        verseStatus.textContent = (correct ? 'Benar!' : 'Belum tepat.') + ' Skor ' + score;
                        verseStatus.dataset.score = String(score);
                    }
                }
            });
            bankEl?.appendChild(chip);
        });

        if (verseStatus) {
            verseStatus.textContent = 'Susun ayat dengan urutan benar.';
            verseStatus.dataset.score = '0';
        }
    }
    document.getElementById('verseStart')?.addEventListener('click', initVerse);
    document.getElementById('verseSubmit')?.addEventListener('click', () => submitScore('verse_builder', Number(verseStatus?.dataset.score || 0)));
});

