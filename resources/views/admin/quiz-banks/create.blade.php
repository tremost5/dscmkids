@extends('admin.layout')

@section('title', 'Tambah Bank Soal')

@section('content')
<h1 style="margin-top:0;">Tambah Bank Soal Harian</h1>
<p class="muted">Gunakan visual builder: tambah soal, isi opsi, lalu centang jawaban benar.</p>

<style>
    .qb-wrap { display:grid; gap:10px; }
    .qb-item { border:1px solid #d8e1ee; border-radius:12px; background:#f8fafd; padding:10px; }
    .qb-item-head { display:flex; justify-content:space-between; align-items:center; gap:8px; margin-bottom:8px; }
    .qb-item-title { font-weight:800; }
    .qb-options { display:grid; gap:8px; margin-top:8px; }
    .qb-option { display:grid; grid-template-columns:auto 1fr auto; gap:8px; align-items:center; }
    .qb-option input[type="text"] { margin-top:0; }
    .qb-actions-inline { display:flex; gap:8px; flex-wrap:wrap; margin-top:8px; }
</style>

<form method="POST" action="{{ route('admin.quiz-banks.store') }}">
    @csrf

    <div class="field">
        <label for="day_key">Hari</label>
        <select id="day_key" name="day_key" style="width:100%;border:1px solid #cbd5e1;border-radius:10px;padding:10px;font:inherit;">
            @foreach($dayKeys as $key => $label)
                <option value="{{ $key }}" @selected(old('day_key') === $key)>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div class="field">
        <label for="title">Judul Quiz</label>
        <input id="title" name="title" value="{{ old('title') }}" required>
    </div>

    <div class="field">
        <label for="memory_verse">Ayat Hafalan</label>
        <input id="memory_verse" name="memory_verse" value="{{ old('memory_verse') }}" placeholder="Contoh: Yohanes 13:34">
    </div>

    <div class="field">
        <label>Soal & Opsi Jawaban</label>
        <div id="questionBuilder" class="qb-wrap"></div>
        <div class="qb-actions-inline">
            <button type="button" class="btn btn-secondary" id="addQuestionBtn">+ Tambah Soal</button>
        </div>
        <textarea id="questions_json" name="questions_json" style="display:none;">{{ old('questions_json', '[]') }}</textarea>
        <p class="muted" style="font-size:12px;margin-top:6px;">Minimal 1 soal, tiap soal minimal 2 opsi, dan 1 jawaban benar.</p>
    </div>

    <label style="display:flex;align-items:center;gap:8px;margin-bottom:14px;">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true)) style="width:auto;margin:0;">
        Aktifkan bank soal ini
    </label>

    <div class="actions">
        <button class="btn btn-primary" type="submit">Simpan</button>
        <a class="btn btn-secondary" href="{{ route('admin.quiz-banks.index') }}">Batal</a>
    </div>
</form>

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
        row.className = 'qb-option';
        row.innerHTML = `
            <input type="radio" class="qb-correct-radio" ${option.is_correct ? 'checked' : ''} title="Jawaban benar">
            <input type="text" value="${String(option.text || '').replace(/"/g, '&quot;')}" placeholder="Teks opsi jawaban">
            <button type="button" class="btn btn-danger qb-remove-option">Hapus</button>
        `;
        return row;
    }

    function refreshQuestionNumbers() {
        builderEl.querySelectorAll('.qb-item').forEach((item, idx) => {
            const title = item.querySelector('.qb-item-title');
            if (title) {
                title.textContent = 'Soal #' + (idx + 1);
            }
        });
    }

    function buildQuestionCard(question = { question: '', options: [{ text: '', is_correct: true }, { text: '', is_correct: false }] }) {
        const card = document.createElement('div');
        card.className = 'qb-item';
        card.innerHTML = `
            <div class="qb-item-head">
                <div class="qb-item-title">Soal</div>
                <button type="button" class="btn btn-danger qb-remove-question">Hapus Soal</button>
            </div>
            <input type="text" class="qb-question-text" placeholder="Tulis pertanyaan..." value="${String(question.question || '').replace(/"/g, '&quot;')}">
            <div class="qb-options"></div>
            <div class="qb-actions-inline">
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
                target.closest('.qb-option')?.remove();
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
        return Array.from(builderEl.querySelectorAll('.qb-item')).map((card) => {
            const questionText = card.querySelector('.qb-question-text')?.value || '';
            const options = Array.from(card.querySelectorAll('.qb-option')).map((row) => {
                return {
                    text: row.querySelector('input[type="text"]')?.value || '',
                    is_correct: Boolean(row.querySelector('input[type="radio"]')?.checked),
                };
            });
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
