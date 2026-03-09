@extends('admin.layout')

@section('title', 'Edit Bank Soal')

@section('content')
<div class="page-grid">
    <div class="page-header">
        <div class="page-header-copy">
            <h1>Edit bank soal harian</h1>
            <p class="muted">Ubah pertanyaan, opsi, dan jawaban benar lewat visual builder.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.quiz-banks.update', $quizBank) }}" class="form-shell" data-loading-form>
        @csrf
        @method('PUT')

        <section class="form-panel">
            <div class="grid-2">
                <div class="field">
                    <label for="day_key">Hari</label>
                    <select id="day_key" name="day_key">
                        @foreach($dayKeys as $key => $label)
                            <option value="{{ $key }}" @selected(old('day_key', $quizBank->day_key) === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="memory_verse">Ayat Hafalan</label>
                    <input id="memory_verse" name="memory_verse" value="{{ old('memory_verse', $quizBank->memory_verse) }}" placeholder="Contoh: Yohanes 13:34">
                </div>
            </div>

            <div class="field">
                <label for="title">Judul Quiz</label>
                <input id="title" name="title" value="{{ old('title', $quizBank->title) }}" required>
            </div>

            <div class="field">
                <label>Soal & Opsi Jawaban</label>
                <div id="questionBuilder" class="builder-wrap"></div>
                <div class="builder-actions">
                    <button type="button" class="btn btn-secondary" id="addQuestionBtn">+ Tambah Soal</button>
                </div>
                <textarea id="questions_json" name="questions_json" hidden>{{ old('questions_json', $existingJson ?: '[]') }}</textarea>
                <p class="helper-text">Minimal 1 soal, tiap soal minimal 2 opsi, dan 1 jawaban benar.</p>
            </div>

            <label class="toggle-field">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $quizBank->is_active))>
                Aktifkan bank soal ini
            </label>
        </section>

        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Update</button>
            <a class="btn btn-secondary" href="{{ route('admin.quiz-banks.index') }}">Batal</a>
        </div>
    </form>
</div>

<script>
(function () {
    const builderEl = document.getElementById('questionBuilder');
    const addQuestionBtn = document.getElementById('addQuestionBtn');
    const hiddenJsonEl = document.getElementById('questions_json');
    const formEl = builderEl?.closest('form');

    if (!builderEl || !addQuestionBtn || !hiddenJsonEl || !formEl) {
        return;
    }

    function buildOptionRow(option = { text: '', is_correct: false }) {
        const row = document.createElement('div');
        row.className = 'builder-option';
        row.innerHTML = `
            <input type="radio" class="qb-correct-radio" ${option.is_correct ? 'checked' : ''} title="Jawaban benar">
            <input type="text" value="${String(option.text || '').replace(/"/g, '&quot;')}" placeholder="Teks opsi jawaban">
            <button type="button" class="btn btn-danger qb-remove-option">Hapus</button>
        `;
        return row;
    }

    function refreshQuestionNumbers() {
        builderEl.querySelectorAll('.builder-card').forEach((item, idx) => {
            const title = item.querySelector('.builder-title');
            if (title) {
                title.textContent = 'Soal #' + (idx + 1);
            }
        });
    }

    function buildQuestionCard(question = { question: '', options: [{ text: '', is_correct: true }, { text: '', is_correct: false }] }) {
        const card = document.createElement('div');
        card.className = 'builder-card';
        card.innerHTML = `
            <div class="builder-head">
                <div class="builder-title">Soal</div>
                <button type="button" class="btn btn-danger qb-remove-question">Hapus Soal</button>
            </div>
            <input type="text" class="qb-question-text" placeholder="Tulis pertanyaan..." value="${String(question.question || '').replace(/"/g, '&quot;')}">
            <div class="builder-wrap qb-options"></div>
            <div class="builder-actions">
                <button type="button" class="btn btn-secondary qb-add-option">+ Tambah Opsi</button>
            </div>
        `;

        const optionsWrap = card.querySelector('.qb-options');
        const options = Array.isArray(question.options) && question.options.length > 0
            ? question.options
            : [{ text: '', is_correct: true }, { text: '', is_correct: false }];
        options.forEach((opt) => optionsWrap.appendChild(buildOptionRow(opt)));
        assignRadioGroup(card);

        card.querySelector('.qb-add-option')?.addEventListener('click', () => {
            optionsWrap.appendChild(buildOptionRow({ text: '', is_correct: false }));
            assignRadioGroup(card);
        });

        card.addEventListener('click', (event) => {
            const target = event.target;
            if (!(target instanceof HTMLElement)) {
                return;
            }

            if (target.classList.contains('qb-remove-option')) {
                target.closest('.builder-option')?.remove();
                return;
            }

            if (target.classList.contains('qb-remove-question')) {
                card.remove();
                refreshQuestionNumbers();
            }
        });

        return card;
    }

    function assignRadioGroup(card) {
        const groupName = 'qg_' + Math.random().toString(36).slice(2, 10);
        card.querySelectorAll('.qb-correct-radio').forEach((radio) => {
            radio.name = groupName;
        });
    }

    function collectPayload() {
        return Array.from(builderEl.querySelectorAll('.builder-card')).map((card) => {
            const questionText = card.querySelector('.qb-question-text')?.value || '';
            const options = Array.from(card.querySelectorAll('.builder-option')).map((row) => ({
                text: row.querySelector('input[type="text"]')?.value || '',
                is_correct: Boolean(row.querySelector('input[type="radio"]')?.checked),
            }));
            return { question: questionText, options };
        });
    }

    function hydrateFromJson(raw) {
        let parsed = [];
        try {
            parsed = JSON.parse(raw || '[]');
        } catch (error) {
            parsed = [];
        }

        if (!Array.isArray(parsed) || parsed.length === 0) {
            parsed = JSON.parse(@json($sampleJson));
        }

        parsed.forEach((q) => builderEl.appendChild(buildQuestionCard(q)));
        refreshQuestionNumbers();
    }

    addQuestionBtn.addEventListener('click', () => {
        builderEl.appendChild(buildQuestionCard());
        refreshQuestionNumbers();
    });

    formEl.addEventListener('submit', () => {
        hiddenJsonEl.value = JSON.stringify(collectPayload());
    });

    hydrateFromJson(hiddenJsonEl.value);
})();
</script>
@endsection
