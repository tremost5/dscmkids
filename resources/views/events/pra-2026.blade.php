<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $event->title }} | DSCMKids</title>
    <meta name="description" content="{{ $event->subtitle ?: 'Pendaftaran Pekan Rohani Anak 2026 DSCMKids.' }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@600;700;800&family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    @if (!app()->environment('testing'))
        @vite(['resources/css/pra-event.css', 'resources/js/pra-event.js'])
    @endif
</head>
@php
    $mediaUrl = fn (?string $path) => $path ? route('storage.public', ['path' => $path]) : null;
    $heroImage = $banner?->background_image_path
        ? $mediaUrl($banner->background_image_path)
        : 'https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?q=80&w=1800&auto=format&fit=crop';
    $paymentInfo = is_array($event->payment_info) ? $event->payment_info : [];
    $contacts = collect($paymentInfo['contacts'] ?? [])->filter(fn ($item) => !empty($item['name']) && !empty($item['phone']))->values();
    $benefits = collect($event->benefits ?: []);
    $groupOne = $groups->firstWhere('slug', 'grup-1');
    $groupTwo = $groups->firstWhere('slug', 'grup-2');
    $groupOneGallery = $event->galleries->where('group_slug', 'grup-1')->values();
    $groupTwoGallery = $event->galleries->where('group_slug', 'grup-2')->values();
    $groupCards = collect([
        [
            'slug' => 'grup-1',
            'name' => 'GRUP 1',
            'class_text' => 'PG � TK � SD Kelas 1-4',
            'location' => 'NICC & KLUB BUNGA Theme Park Hotel',
            'display_location' => 'NICC & Klub Bunga Theme Park Hotel',
            'date_text' => '26-27 Juni 2026',
            'early_bird' => 'Biaya berlaku sebelum 21 Juni 2026',
            'group' => $groupOne,
            'accent' => 'coral',
            'image' => $mediaUrl($groupOneGallery->first()?->image_path) ?: $heroImage,
        ],
        [
            'slug' => 'grup-2',
            'name' => 'GRUP 2',
            'class_text' => 'SD Kelas 5-9',
            'location' => 'BROMO BENJOR PINE CAMPING GROUND, Tumpang - Malang',
            'display_location' => 'Bromo Benjor Pine Camping Ground',
            'date_text' => '03-04 Juli 2026',
            'early_bird' => 'Biaya berlaku sebelum 28 Juni 2026',
            'group' => $groupTwo,
            'accent' => 'teal',
            'image' => $mediaUrl($groupTwoGallery->first()?->image_path) ?: $heroImage,
        ],
    ]);
@endphp
<body>
<header class="pra-hero" style="--hero-image: url('{{ $heroImage }}')">
    <nav class="pra-nav">
        <a class="pra-brand" href="{{ route('landing') }}">
            <span>D</span>
            <strong>DSCMKids</strong>
        </a>
        <div class="pra-nav-links" data-pra-auto-collapse-nav>
            <a href="{{ route('landing') }}">Home</a>
            <a href="#countdown">Countdown</a>
            <a href="#grup">Grup</a>
            <a href="#galeri-grup-1">Galeri</a>
            <a href="#pembayaran">Pembayaran</a>
            <a href="#daftar">Daftar</a>
            <div class="pra-more" data-pra-more-menu hidden>
                <button class="pra-more-toggle" type="button" aria-expanded="false" aria-label="More navigation" data-pra-more-toggle>More</button>
                <div class="pra-more-panel" data-pra-more-panel></div>
            </div>
        </div>
        <button class="pra-menu-btn" type="button" data-pra-menu-toggle>Menu</button>
    </nav>
    <div class="pra-mobile-menu" data-pra-mobile-menu hidden>
        <button class="pra-mobile-close" type="button" data-pra-menu-close aria-label="Tutup menu">x</button>
        <a href="{{ route('landing') }}">Home DSCMKids</a>
        <a href="#countdown">Countdown PRA</a>
        <a href="#grup">Informasi Grup</a>
        <a href="#galeri-grup-1">Galeri</a>
        <a href="#pembayaran">Pembayaran</a>
        <a href="#daftar">Daftar Sekarang</a>
    </div>

    <div class="pra-hero-copy">
        <span class="pra-kicker">Event Sekolah Minggu</span>
        <h1>{{ $banner?->title ?: $event->title }}</h1>
        <p>{{ $banner?->subtitle ?: $event->subtitle }}</p>
        <div class="pra-hero-actions">
            <a class="pra-btn pra-btn-primary" href="#daftar">Daftar Sekarang</a>
            <a class="pra-btn pra-btn-light" href="#countdown">Lihat Tanggal PRA</a>
        </div>
    </div>
</header>

@if(session('registration_success'))
    <div class="pra-modal is-visible" data-success-modal>
        <div class="pra-modal-card">
            <button type="button" class="pra-modal-close" data-modal-close aria-label="Tutup">x</button>
            <span class="pra-modal-mark">OK</span>
            <h2>Pendaftaran Berhasil</h2>
            <p>Terima kasih telah mendaftar PRA 2026. Kami akan menghubungi melalui WhatsApp untuk informasi lebih lanjut.</p>
            <strong>Tuhan Yesus Memberkati.</strong>
        </div>
    </div>
@endif

<div class="pra-modal" data-splash-modal>
    <div class="pra-modal-card pra-modal-card-splash">
        <button type="button" class="pra-modal-close" data-splash-close aria-label="Tutup">x</button>
        <span class="pra-modal-mark">PRA</span>
        <h2>{{ $banner?->splash_title ?: 'DAFTAR PRA 2026' }}</h2>
        <p>{{ $banner?->splash_subtitle ?: 'Daftarkan anak untuk pengalaman rohani yang seru dan bermakna.' }}</p>
        <div class="pra-modal-actions">
            <a class="pra-btn pra-btn-primary" href="#daftar" data-splash-register>Daftar Sekarang</a>
            <button type="button" class="pra-btn pra-btn-ghost" data-splash-close>Tutup</button>
        </div>
    </div>
</div>

<main>
    <section class="pra-section pra-countdown" id="countdown">
        <div class="pra-section-head">
            <span class="pra-kicker">Hitung Mundur</span>
            <h2>Hitung Mundur Menuju PRA 2026</h2>
            <p>Setiap grup memiliki tanggal dan lokasi yang sudah disiapkan sesuai kebutuhan usia peserta.</p>
        </div>
        <div class="pra-count-grid">
            @foreach($groupCards as $card)
                <article class="pra-count-card">
                    <span>{{ $card['name'] }}</span>
                    <h3>{{ $card['slug'] === 'grup-1' ? 'Kelas PG TK - 4 SD' : 'Kelas 5 SD - 9 SMP' }}</h3>
                    <p>{{ optional($card['group']?->starts_on)->translatedFormat('d F Y') }} - {{ optional($card['group']?->ends_on)->translatedFormat('d F Y') }}</p>
                    <p>{{ $card['location'] }}</p>
                    <div class="pra-timer" data-countdown="{{ optional($card['group']?->starts_on)->format('Y-m-d') }}">Menyiapkan countdown...</div>
                </article>
            @endforeach
        </div>
    </section>

    <section class="pra-section pra-group-section" id="grup">
    <div class="pra-section-head">
        <span class="pra-kicker">Informasi Singkat Grup</span>
        <h2>PRA 2026 disusun sesuai tahap usia anak</h2>
        <p>{{ $event->description }}</p>

        <div class="pra-benefit-inline">
            @foreach($benefits as $benefit)
                <span>{{ $benefit }}</span>
            @endforeach
        </div>
    </div>

    <div class="pra-group-grid">
        @foreach($groupCards as $card)
            <article class="pra-group-card pra-group-card--{{ $card['accent'] }}">
                <img
                    class="pra-group-card-image"
                    src="{{ $card['image'] }}"
                    alt="Lokasi {{ $card['name'] }}"
                    loading="lazy"
                >

                <div class="pra-group-card-head">
                    <span>{{ $card['name'] }}</span>
                    <strong>{{ $card['class_text'] }}</strong>
                </div>

                <div class="pra-group-card-meta">
                    <div class="pra-group-meta-item">
                        <span class="pra-group-meta-icon" aria-hidden="true">T</span>
                        <strong>{{ $card['date_text'] }}</strong>
                    </div>
                    <div class="pra-group-meta-item">
                        <span class="pra-group-meta-icon" aria-hidden="true">L</span>
                        <strong>{{ $card['display_location'] }}</strong>
                    </div>
                    <div class="pra-group-meta-item pra-group-meta-item--price">
                        <span class="pra-group-meta-icon" aria-hidden="true">Rp</span>
                        <strong>Rp 175.000</strong>
                    </div>
                </div>

                <p class="pra-group-early">{{ $card['early_bird'] }}</p>

                <a class="pra-btn pra-btn-light" href="#daftar">
                    Daftarkan Anak
                </a>
            </article>
        @endforeach
    </div>
</section>

    @php($galleryViewerIndex = 0)
    <section class="pra-section pra-gallery-section" id="galeri-grup-1">
        <div class="pra-section-head">
            <span class="pra-kicker">Galeri Lokasi</span>
            <h2>Galeri PRA Grup 1{{ $groupOneGallery->isNotEmpty() ? ' ('.$groupOneGallery->count().' foto)' : '' }}</h2>
            <p>Suasana lokasi dan area kegiatan untuk peserta PG, TK A, TK B, dan SD kelas 1-4.</p>
        </div>
        <div class="pra-gallery-grid">
            @forelse($groupOneGallery as $photo)
                <img
                    src="{{ $mediaUrl($photo->image_path) }}"
                    alt="{{ $photo->title ?: 'Galeri PRA Grup 1' }}"
                    loading="lazy"
                    data-pra-gallery-image
                    data-pra-gallery-index="{{ $galleryViewerIndex }}"
                >
                @php($galleryViewerIndex++)
            @empty
                <div class="pra-empty pra-gallery-empty">
                    <strong>Galeri Grup 1 segera hadir.</strong>
                    <span>Foto lokasi akan tampil di sini setelah panitia menyiapkan dokumentasi resmi.</span>
                </div>
            @endforelse
        </div>
    </section>

    <section class="pra-section pra-gallery-section" id="galeri-grup-2">
        <div class="pra-section-head">
            <span class="pra-kicker">Galeri Lokasi</span>
            <h2>Galeri PRA Grup 2{{ $groupTwoGallery->isNotEmpty() ? ' ('.$groupTwoGallery->count().' foto)' : '' }}</h2>
            <p>Suasana lokasi dan area kegiatan untuk peserta SD kelas 5-9.</p>
        </div>
        <div class="pra-gallery-grid">
            @forelse($groupTwoGallery as $photo)
                <img
                    src="{{ $mediaUrl($photo->image_path) }}"
                    alt="{{ $photo->title ?: 'Galeri PRA Grup 2' }}"
                    loading="lazy"
                    data-pra-gallery-image
                    data-pra-gallery-index="{{ $galleryViewerIndex }}"
                >
                @php($galleryViewerIndex++)
            @empty
                <div class="pra-empty pra-gallery-empty">
                    <strong>Galeri Grup 2 segera hadir.</strong>
                    <span>Foto lokasi akan tampil di sini setelah panitia menyiapkan dokumentasi resmi.</span>
                </div>
            @endforelse
        </div>
    </section>

    <section class="pra-section pra-payment" id="pembayaran">
        <div class="pra-section-head">
            <span class="pra-kicker">Informasi Pendaftaran</span>
            <h2>Informasi Pendaftaran</h2>
            <p>Untuk informasi pendaftaran lebih lanjut, silakan menghubungi panitia pendaftaran berikut.</p>
        </div>
        <div class="pra-contact-grid">
            @foreach($contacts as $contact)
                @php($wa = preg_replace('/\D+/', '', $contact['phone']))
                @if(str_starts_with($wa, '0')) @php($wa = '62'.substr($wa, 1)) @endif
                <article class="pra-contact-card">
                    <strong>{{ $contact['name'] }}</strong>
                    <span>{{ $contact['phone'] }}</span>
                    <a href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener">WhatsApp</a>
                </article>
            @endforeach
        </div>
    </section>

    <section class="pra-section pra-participants" id="peserta">
        <div class="pra-section-head">
            <span class="pra-kicker">Peserta</span>
            <h2>Daftar Peserta Yang Sudah Mendaftarkan Diri</h2>
        </div>
        <div class="pra-participant-grid">
            @foreach($groupCards as $card)
                @php($registrations = $registrationsByGroup->get($card['slug'], collect()))
                <article class="pra-participant-card">
                    <div class="pra-group-card-head">
                        <span>{{ $card['name'] }}</span>
                        <strong>{{ $registrations->count() }} peserta</strong>
                    </div>
                    @forelse($registrations->take(8) as $registration)
                        <div class="pra-participant-row">
                            <strong>{{ $registration->nickname }}</strong>
                            <span>Kelas {{ $registration->class_before }}</span>
                        </div>
                    @empty
                        <div class="pra-empty">
                            <strong>Belum ada peserta yang ditampilkan.</strong>
                            <span>Nama peserta akan muncul setelah pendaftaran pertama untuk grup ini masuk.</span>
                        </div>
                    @endforelse
                </article>
            @endforeach
        </div>
    </section>

    <section class="pra-section pra-form-section" id="daftar">
        <div class="pra-section-head">
            <span class="pra-kicker">Form Pendaftaran</span>
            <h2>Daftar PRA 2026</h2>
            <p>Grup akan ditentukan otomatis dari kelas sebelum kenaikan.</p>
        </div>

        @if($errors->any())
            <div class="pra-alert">{{ $errors->first() }}</div>
        @endif

        <form class="pra-form" action="{{ route('events.pra-2026.register') }}" method="POST" enctype="multipart/form-data" data-pra-form>
            @csrf
            <label>Nama Lengkap *
                <input name="full_name" value="{{ old('full_name') }}" required>
            </label>
            <label>Nama Panggilan *
                <input name="nickname" value="{{ old('nickname') }}" required>
            </label>
            <label>Alergi Makanan / Obat *
                <select name="has_allergy" required data-allergy-select>
                    <option value="">Pilih</option>
                    <option value="no" @selected(old('has_allergy') === 'no')>Tidak</option>
                    <option value="yes" @selected(old('has_allergy') === 'yes')>Ya</option>
                </select>
            </label>
            <label class="pra-form-wide" data-allergy-notes hidden>Keterangan Alergi
                <textarea name="allergy_notes">{{ old('allergy_notes') }}</textarea>
            </label>
            <label>Tanggal Lahir *
                <input type="date" name="birth_date" value="{{ old('birth_date') }}" required>
            </label>
            <label>Jenis Kelamin *
                <select name="gender" required>
                    <option value="">Pilih</option>
                    <option @selected(old('gender') === 'Laki-laki')>Laki-laki</option>
                    <option @selected(old('gender') === 'Perempuan')>Perempuan</option>
                </select>
            </label>
            <label>Kelas Sebelum Kenaikan *
                <select name="class_before" required>
                    <option value="">Pilih kelas</option>
                    @foreach(['PG','TKA','TKB','1','2','3','4','5','6','7','8','9'] as $level)
                        <option value="{{ $level }}" @selected(old('class_before') === $level)>{{ $level }}</option>
                    @endforeach
                </select>
            </label>
            <label>Beribadah Sekolah Minggu Di *
                <select name="church_branch" required>
                    <option value="">Pilih</option>
                    <option @selected(old('church_branch') === 'NICC')>NICC</option>
                    <option @selected(old('church_branch') === 'GRASA')>GRASA</option>
                </select>
            </label>
            <label>Nama Orang Tua / Wali *
                <input name="parent_name" value="{{ old('parent_name') }}" required>
            </label>
            <label>No Whatsapp Aktif *
                <input name="whatsapp_number" value="{{ old('whatsapp_number') }}" required>
            </label>
            <label class="pra-form-wide">Alamat Lengkap *
                <textarea name="address" required>{{ old('address') }}</textarea>
            </label>
            <label>Metode Pembayaran *
                <select name="payment_method" required data-payment-select>
                    <option value="">Pilih</option>
                    <option value="cash" @selected(old('payment_method') === 'cash')>Tunai</option>
                    <option value="transfer" @selected(old('payment_method') === 'transfer')>Transfer</option>
                </select>
            </label>
            <div class="pra-payment-info-card pra-payment-info-card--cash pra-form-wide" data-cash-info hidden>
                <div class="pra-payment-info-head">
                    <span>Tunai</span>
                    <strong>Pembayaran Tunai</strong>
                </div>
                <p>Silakan melakukan pembayaran dan konfirmasikan kepada panitia:</p>
                <div class="pra-payment-contact-list">
                    <div><strong>Kak Santi</strong><span>081334001127</span></div>
                    <div><strong>Kak Arie</strong><span>081334977979</span></div>
                    <div><strong>Kak Wenny</strong><span>085233107075</span></div>
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
                        <strong data-account-number>4000299613</strong>
                        <button type="button" data-copy-account>Copy Nomor Rekening</button>
                    </div>
                </div>
                <div class="pra-bank-detail">
                    <span>a/n</span>
                    <strong>Tri Ratna Setiyowati</strong>
                </div>
                <div class="pra-transfer-note">
                    <strong>Informasi Transfer</strong>
                    <p>Pada keterangan transfer mohon tuliskan:</p>
                    <code>PRA - Nama Anak - Kelas</code>
                    <p>Contoh:</p>
                    <code>PRA - Samuel - Kelas 3</code>
                    <p>Setelah melakukan transfer, mohon upload bukti pembayaran pada form pendaftaran.</p>
                </div>
            </div>
            <label data-proof-field hidden>Upload Bukti Pembayaran *
                <input type="file" name="payment_proof" accept=".jpg,.jpeg,.png,.webp,.pdf">
            </label>
            <button class="pra-btn pra-btn-primary pra-form-wide" type="submit">Kirim Pendaftaran</button>
        </form>
    </section>
</main>

<div class="pra-gallery-viewer" data-pra-gallery-viewer hidden>
    <button class="pra-gallery-close" type="button" data-pra-gallery-close aria-label="Tutup galeri">x</button>
    <button class="pra-gallery-arrow pra-gallery-arrow--prev" type="button" data-pra-gallery-prev aria-label="Foto sebelumnya">‹</button>
    <figure class="pra-gallery-stage">
        <img src="" alt="" data-pra-gallery-active>
        <figcaption>
            <span data-pra-gallery-caption></span>
            <strong data-pra-gallery-counter>1 / 1</strong>
        </figcaption>
    </figure>
    <button class="pra-gallery-arrow pra-gallery-arrow--next" type="button" data-pra-gallery-next aria-label="Foto berikutnya">›</button>
</div>

<footer class="pra-footer">
    <strong>DSCMKids</strong>
    <span>PRA 2026 - Pekan Rohani Anak</span>
</footer>
</body>
</html>
