<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Pendamping PRA 2026 | DSCMKids</title>
    <meta name="description" content="Form pendaftaran pendamping PRA 2026 DSCMKids.">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@600;700;800&family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    @if (!app()->environment('testing'))
        @vite([
            'resources/css/pra-event.css',
            'resources/js/pra-event.js'
        ])
    @endif
</head>
@php
    $mediaUrl = fn (?string $path) => $path ? route('storage.public', ['path' => $path]) : null;
    $heroImage = $banner?->background_image_path
        ? $mediaUrl($banner->background_image_path)
        : 'https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?q=80&w=1800&auto=format&fit=crop';
    $paymentInfo = is_array($paymentInfo) ? $paymentInfo : [];
    $contacts = collect($paymentInfo['contacts'] ?? [])->filter(fn ($item) => !empty($item['name']) && !empty($item['phone']))->values();
    $cashNote = $paymentInfo['cash_note'] ?? 'Silahkan melakukan pembayaran dan konfirmasikan ke bagian informasi pembayaran berikut:';
    $transferNote = $paymentInfo['transfer_note'] ?? 'Upload bukti pembayaran setelah transfer. Status akan menjadi Menunggu Verifikasi.';
    $accountNumber = '4000299613';
    $accountName = 'Tri Ratna Setiyowati';
    $children = collect($children ?? [])->values();
@endphp
<body>
<header class="pra-hero" style="--hero-image: url('{{ $heroImage }}')">
    <nav class="pra-nav">
        <a class="pra-brand" href="{{ route('landing') }}">
            <span>D</span>
            <strong>DSCMKids</strong>
        </a>
        <div class="pra-nav-links">
            <a href="{{ route('landing') }}">Home</a>
            <a href="{{ route('events.pra-2026') }}">Halaman Peserta</a>
            <a href="#form-pendamping">Form Pendamping</a>
        </div>
    </nav>

    <div class="pra-hero-copy">
        <span class="pra-kicker">PRA 2026</span>
        <h1>Pendaftaran Pendamping PRA 2026</h1>
        <p>Pendamping bisa memilih lebih dari satu murid, menentukan tanggal kehadiran, dan memilih metode pembayaran yang sesuai.</p>
        <div class="pra-hero-actions">
            <a class="pra-btn pra-btn-primary" href="#form-pendamping">Isi Form</a>
            <a class="pra-btn pra-btn-light" href="{{ route('events.pra-2026') }}">Lihat Halaman Peserta</a>
        </div>
    </div>
</header>

@if(session('registration_success'))
<div class="pra-modal is-visible" data-success-modal>
    <div class="pra-modal-card">
        <button type="button" class="pra-modal-close" data-modal-close aria-label="Tutup">x</button>
        <span class="pra-modal-mark">OK</span>
        <h2>Pendaftaran Pendamping Berhasil</h2>
        <p>Terima kasih, pendaftaran pendamping PRA 2026 sudah kami terima. Kami akan mengirim informasi lanjutan melalui WhatsApp.</p>
        <strong>Tuhan Yesus Memberkati.</strong>
    </div>
</div>
@endif

<main>
    <section class="pra-section" id="form-pendamping">
        <div class="pra-section-head">
            <span class="pra-kicker">Form Pendaftaran</span>
            <h2>Daftar Pendamping PRA 2026</h2>
            <p>Isi data pendamping, pilih lebih dari satu murid, lalu tentukan tanggal kehadiran dan metode pembayaran.</p>
        </div>

        @if($errors->any())
            <div class="pra-alert">{{ $errors->first() }}</div>
        @endif

        <form class="pra-form" action="{{ route('events.pra-2026.pendamping.store') }}" method="POST" enctype="multipart/form-data" data-companion-form>
            @csrf

            <label>Nama Pendamping *
                <input name="companion_name" value="{{ old('companion_name') }}" required>
            </label>

            <label>No WhatsApp Pendamping *
                <input name="whatsapp_number" value="{{ old('whatsapp_number') }}" required>
            </label>

            <div class="pra-form-wide" data-child-list>
                @foreach($children as $index => $child)
                    <div class="pra-payment-info-card pra-companion-child-card" style="margin-bottom:16px;" data-child-row>
                        <div class="pra-companion-child-head">
                            <div class="pra-payment-info-head">
                                <span data-child-label>Anak #{{ $loop->iteration }}</span>
                                <strong>Data Murid</strong>
                            </div>
                            @if($loop->iteration > 1)
                                <button type="button" class="pra-btn pra-btn-light pra-child-remove" data-remove-child>Hapus Anak</button>
                            @endif
                        </div>
                        <div class="grid-2 pra-companion-child-grid">
                            <label>Kelas *
                                <select
                                    name="children[{{ $index }}][class_before]"
                                    required
                                    data-child-class
                                >
                                    <option value="">Pilih kelas</option>
                                    @foreach($classOptions as $level)
                                        <option value="{{ $level }}" @selected(($child['class_before'] ?? '') === $level)>{{ $level }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label>Nama Murid *
                                <select
                                    name="children[{{ $index }}][event_registration_id]"
                                    required
                                    data-child-student
                                    data-selected-student="{{ old('children.'.$index.'.event_registration_id', $child['event_registration_id'] ?? '') }}"
                                >
                                    <option value="">Pilih kelas terlebih dahulu</option>
                                </select>
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>

            <button class="pra-btn pra-btn-light pra-form-wide" type="button" data-add-child>+ Tambah Anak</button>

            <div class="pra-form-wide">
                <div class="pra-section-head" style="margin-bottom:12px;">
                    <span class="pra-kicker">Tanggal Kehadiran</span>
                    <p>Pilih minimal satu tanggal kehadiran pendamping.</p>
                </div>
                <div class="grid-2 pra-attendance-grid">
                    <label class="pra-checkline">
                        <input type="checkbox" name="attend_26_june" value="1" @checked(old('attend_26_june'))>
                        <span>26 Juni 2026</span>
                    </label>
                    <label class="pra-checkline">
                        <input type="checkbox" name="attend_27_june" value="1" @checked(old('attend_27_june'))>
                        <span>27 Juni 2026</span>
                    </label>
                </div>
            </div>

            <label>Metode Pembayaran *
                <select name="payment_method" required data-payment-select>
                    <option value="">Pilih</option>
                    <option value="cash" @selected(old('payment_method') === 'cash')>Tunai</option>
                    <option value="transfer" @selected(old('payment_method') === 'transfer')>Transfer BCA</option>
                </select>
            </label>

            <div class="pra-payment-info-card pra-payment-info-card--cash pra-form-wide" data-cash-info hidden>
                <div class="pra-payment-info-head">
                    <span>Tunai</span>
                    <strong>Pembayaran Tunai</strong>
                </div>
                <p>{{ $cashNote }}</p>
                <div class="pra-payment-contact-list">
                    @forelse($contacts as $contact)
                        <div>
                            <strong>{{ $contact['name'] }}</strong>
                            <span>{{ $contact['phone'] }}</span>
                        </div>
                    @empty
                        <div>
                            <strong>Kak Santi</strong>
                            <span>081334001127</span>
                        </div>
                        <div>
                            <strong>Kak Arie</strong>
                            <span>081334977979</span>
                        </div>
                        <div>
                            <strong>Kak Wenny</strong>
                            <span>085233107075</span>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="pra-payment-info-card pra-payment-info-card--transfer pra-form-wide" data-transfer-info hidden>
                <div class="pra-payment-info-head">
                    <span class="pra-bca-logo" aria-label="Bank BCA">BCA</span>
                    <strong>Transfer Bank BCA</strong>
                </div>
                <div class="pra-bank-detail">
                    <span>No. Rekening</span>
                    <div>
                        <strong data-account-number>{{ $accountNumber }}</strong>
                        <button type="button" data-copy-account>Copy Nomor Rekening</button>
                    </div>
                </div>
                <div class="pra-bank-detail">
                    <span>a/n</span>
                    <strong>{{ $accountName }}</strong>
                </div>
                <div class="pra-transfer-note">
                    <strong>Informasi Transfer</strong>
                    <p>{{ $transferNote }}</p>
                    <p>Setelah melakukan transfer, silakan upload bukti pembayaran pada form ini.</p>
                </div>
            </div>

            <label data-proof-field hidden>
                Upload Bukti Pembayaran *
                <input type="file" name="payment_proof" accept=".jpg,.jpeg,.png,.webp,.pdf">
            </label>

            <p class="pra-form-wide" style="font-size:12px;opacity:.8;">
                Data formulir disimpan otomatis setelah validasi berhasil dan nomor WhatsApp akan menerima konfirmasi.
            </p>

            <button class="pra-btn pra-btn-primary pra-form-wide" type="submit">
                Kirim Pendaftaran
            </button>
        </form>
    </section>
</main>

<footer class="pra-footer">
    <strong>DSCMKids</strong>
    <span>PRA 2026 - Pendaftaran Pendamping</span>
</footer>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('[data-companion-form]');
    if (!form) return;

    const studentEndpoint = @json(route('events.pra-2026.api.students'));
    const classOptions = @json($classOptions);
    const childList = form.querySelector('[data-child-list]');
    const addChildButton = form.querySelector('[data-add-child]');
    const paymentSelect = form.querySelector('[data-payment-select]');
    const cashInfo = form.querySelector('[data-cash-info]');
    const transferInfo = form.querySelector('[data-transfer-info]');
    const proofField = form.querySelector('[data-proof-field]');
    const proofInput = proofField ? proofField.querySelector('input[type="file"]') : null;
    const copyAccountButton = form.querySelector('[data-copy-account]');
    const accountNumber = form.querySelector('[data-account-number]');

    let nextIndex = childList ? childList.querySelectorAll('[data-child-row]').length : 0;

    function renderClassOptions(selected) {
        return ['<option value="">Pilih kelas</option>']
            .concat(classOptions.map(function (level) {
                return '<option value="' + level + '"' + (selected === level ? ' selected' : '') + '>' + level + '</option>';
            }))
            .join('');
    }

    function createChildRow(index, selectedClass, selectedStudent) {
        const wrapper = document.createElement('div');
        wrapper.className = 'pra-payment-info-card pra-companion-child-card';
        wrapper.style.marginBottom = '16px';
        wrapper.setAttribute('data-child-row', '1');

        wrapper.innerHTML = '' +
            '<div class="pra-companion-child-head">' +
            '<div class="pra-payment-info-head">' +
                '<span data-child-label>Anak #' + (index + 1) + '</span>' +
                '<strong>Data Murid</strong>' +
            '</div>' +
                (index > 0 ? '<button type="button" class="pra-btn pra-btn-light pra-child-remove" data-remove-child>Hapus Anak</button>' : '') +
            '</div>' +
            '<div class="grid-2 pra-companion-child-grid">' +
                '<label>Kelas *' +
                    '<select name="children[' + index + '][class_before]" required data-child-class>' +
                        renderClassOptions(selectedClass) +
                    '</select>' +
                '</label>' +
                '<label>Nama Murid *' +
                    '<select name="children[' + index + '][event_registration_id]" required data-child-student data-selected-student="' + (selectedStudent || '') + '">' +
                        '<option value="">Pilih kelas terlebih dahulu</option>' +
                    '</select>' +
                '</label>' +
            '</div>';

        return wrapper;
    }

    function refreshChildLabels() {
        childList.querySelectorAll('[data-child-row]').forEach(function (row, index) {
            const label = row.querySelector('[data-child-label]');
            if (label) {
                label.textContent = 'Anak #' + (index + 1);
            }
        });
    }

    async function loadStudents(row) {
        const classSelect = row.querySelector('[data-child-class]');
        const studentSelect = row.querySelector('[data-child-student]');
        const selectedStudent = studentSelect.dataset.selectedStudent || '';
        const classValue = classSelect.value;

        studentSelect.innerHTML = '<option value="">Memuat murid...</option>';

        if (!classValue) {
            studentSelect.innerHTML = '<option value="">Pilih kelas terlebih dahulu</option>';
            return;
        }

        try {
            const response = await fetch(studentEndpoint + '?class=' + encodeURIComponent(classValue), {
                headers: { 'Accept': 'application/json' }
            });

            const students = await response.json();

            studentSelect.innerHTML = ['<option value="">Pilih murid</option>']
                .concat(students.map(function (student) {
                    const fullName = student.full_name || student.nickname || '';
                    const label = student.class_before ? student.class_before + ' - ' + fullName : fullName;

                    return '<option value="' + student.id + '"' + (String(student.id) === String(selectedStudent) ? ' selected' : '') + '>' + label + '</option>';
                }))
                .join('');
        } catch (error) {
            studentSelect.innerHTML = '<option value="">Gagal memuat murid</option>';
        }
    }

    function wireRow(row) {
        row.querySelector('[data-child-class]')?.addEventListener('change', function () {
            const studentSelect = row.querySelector('[data-child-student]');
            if (studentSelect) {
                studentSelect.dataset.selectedStudent = '';
            }

            loadStudents(row);
        });

        row.querySelector('[data-remove-child]')?.addEventListener('click', function () {
            const rows = childList.querySelectorAll('[data-child-row]');

            if (rows.length <= 1) {
                return;
            }

            row.remove();
            refreshChildLabels();
        });

        loadStudents(row);
    }

        if (addChildButton) {
            addChildButton.addEventListener('click', function () {
                const row = createChildRow(nextIndex, '', '');
                childList.appendChild(row);
                wireRow(row);
                row.classList.add('is-new-child');
                window.requestAnimationFrame(function () {
                    row.classList.add('is-new-child--visible');
                });
                window.setTimeout(function () {
                    row.classList.remove('is-new-child', 'is-new-child--visible');
                }, 260);
                nextIndex++;
                refreshChildLabels();
            });
        }

    childList.querySelectorAll('[data-child-row]').forEach(function (row) {
        wireRow(row);
    });

    function togglePaymentUI() {
        const isTransfer = paymentSelect && paymentSelect.value === 'transfer';

        if (cashInfo) cashInfo.hidden = isTransfer;
        if (transferInfo) transferInfo.hidden = !isTransfer;
        if (proofField) proofField.hidden = !isTransfer;
        if (proofInput) proofInput.required = isTransfer;
    }

    paymentSelect?.addEventListener('change', togglePaymentUI);
    togglePaymentUI();

    copyAccountButton?.addEventListener('click', async function () {
        const value = accountNumber?.textContent?.trim() || '';

        if (!value) return;

        try {
            await navigator.clipboard.writeText(value);
            copyAccountButton.textContent = 'Nomor Rekening Tersalin';
            setTimeout(function () {
                copyAccountButton.textContent = 'Copy Nomor Rekening';
            }, 1500);
        } catch (error) {
            window.prompt('Salin nomor rekening ini:', value);
        }
    });
});
</script>
</body>
</html>
