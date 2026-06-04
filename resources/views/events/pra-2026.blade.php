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
    $schedule = collect($event->schedule ?: []);
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
            <a href="#lokasi">Lokasi</a>
            <a href="#peserta">Peserta</a>
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
        <a href="#lokasi">Lokasi</a>
        <a href="#jadwal">Jadwal</a>
        <a href="#peserta">Peserta</a>
        <a href="#daftar">Daftar Sekarang</a>
    </div>

    <div class="pra-hero-copy">
        <span class="pra-kicker">Event Sekolah Minggu</span>
        <h1>{{ $banner?->title ?: $event->title }}</h1>
        <p>{{ $banner?->subtitle ?: $event->subtitle }}</p>
        <div class="pra-hero-actions">
            <a class="pra-btn pra-btn-primary" href="#daftar">Daftar Sekarang</a>
            <a class="pra-btn pra-btn-light" href="#countdown">Lihat Tanggal</a>
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
            <span class="pra-kicker">Tanggal Pelaksanaan</span>
            <h2>Kelompok PRA Sesuai Kelas Anak</h2>
        </div>
        <div class="pra-count-grid">
            @foreach($groups as $group)
                <article class="pra-count-card" data-countdown-card data-start="{{ optional($group->starts_on)->format('Y-m-d') }}">
                    <div>
                        <h3>{{ $group->name }}</h3>
                        <p>{{ optional($group->starts_on)->translatedFormat('d F Y') }} - {{ optional($group->ends_on)->translatedFormat('d F Y') }}</p>
                    </div>
                    <div class="pra-class-pills">
                        @foreach(($group->class_levels ?: []) as $level)
                            <span>{{ $level }}</span>
                        @endforeach
                    </div>
                    <div class="pra-timer" data-countdown-output>Loading...</div>
                </article>
            @endforeach
        </div>
    </section>

    <section class="pra-section pra-about" id="tentang">
        <div>
            <span class="pra-kicker">Tentang PRA</span>
            <h2>{{ $event->subtitle ?: 'Petualangan Iman yang Tak Terlupakan' }}</h2>
        </div>
        <p>{{ $event->description }}</p>
    </section>

    <section class="pra-section pra-location" id="lokasi">
        <div class="pra-section-head">
            <span class="pra-kicker">Lokasi Kegiatan</span>
            <h2>{{ $event->location_name ?: 'Lokasi PRA 2026' }}</h2>
            <p>{{ $event->location_description }}</p>
        </div>
        <div class="pra-location-grid">
            <div class="pra-slider" data-pra-slider>
                @forelse($event->galleries as $photo)
                    <img
                        class="{{ $loop->first ? 'active' : '' }}"
                        src="{{ $mediaUrl($photo->image_path) }}"
                        alt="{{ $photo->title ?: 'Foto lokasi PRA' }}"
                        loading="lazy"
                        data-pra-gallery-image
                        data-pra-gallery-index="{{ $loop->index }}"
                    >
                @empty
                    <img class="active" src="https://images.unsplash.com/photo-1516627145497-ae6968895b74?q=80&w=1200&auto=format&fit=crop" alt="Anak-anak berkegiatan bersama" loading="lazy" data-pra-gallery-image data-pra-gallery-index="0">
                    <img src="https://images.unsplash.com/photo-1517048676732-d65bc937f952?q=80&w=1200&auto=format&fit=crop" alt="Ruang kegiatan anak" loading="lazy" data-pra-gallery-image data-pra-gallery-index="1">
                    <img src="https://images.unsplash.com/photo-1529390079861-591de354faf5?q=80&w=1200&auto=format&fit=crop" alt="Aktivitas kelompok anak" loading="lazy" data-pra-gallery-image data-pra-gallery-index="2">
                @endforelse
            </div>
            <div class="pra-video-stack">
                @forelse($event->videos as $video)
                    <article class="pra-video-card">
                        <strong>{{ $video->title ?: 'Preview lokasi' }}</strong>
                        <p>{{ $video->description ?: 'Video singkat suasana lokasi kegiatan.' }}</p>
                        @if($video->video_path)
                            <video controls src="{{ $mediaUrl($video->video_path) }}"></video>
                        @elseif($video->video_url)
                            <a class="pra-btn pra-btn-light" href="{{ $video->video_url }}" target="_blank" rel="noopener">Buka Video</a>
                        @endif
                    </article>
                @empty
                    <article class="pra-video-card">
                        <strong>Video preview</strong>
                        <p>Admin dapat menambahkan video lokasi dari dashboard PRA.</p>
                        <div class="pra-video-placeholder">Preview</div>
                    </article>
                @endforelse
            </div>
        </div>
        <div class="pra-gallery-strip">
            @forelse($event->galleries->take(6) as $photo)
                <img
                    src="{{ $mediaUrl($photo->image_path) }}"
                    alt="{{ $photo->title ?: 'Galeri PRA' }}"
                    loading="lazy"
                    data-pra-gallery-image
                    data-pra-gallery-index="{{ $loop->index }}"
                >
            @empty
                <img src="https://images.unsplash.com/photo-1503676260728-1c00da094a0b?q=80&w=500&auto=format&fit=crop" alt="Galeri kegiatan anak" loading="lazy" data-pra-gallery-image data-pra-gallery-index="3">
                <img src="https://images.unsplash.com/photo-1509062522246-3755977927d7?q=80&w=500&auto=format&fit=crop" alt="Anak belajar bersama" loading="lazy" data-pra-gallery-image data-pra-gallery-index="4">
                <img src="https://images.unsplash.com/photo-1472162072942-cd5147eb3902?q=80&w=500&auto=format&fit=crop" alt="Aktivitas kreatif" loading="lazy" data-pra-gallery-image data-pra-gallery-index="5">
            @endforelse
        </div>
    </section>

    <section class="pra-section" id="benefit">
        <div class="pra-section-head">
            <span class="pra-kicker">Kenapa Harus Ikut</span>
            <h2>Rohani bertumbuh, anak tetap senang</h2>
        </div>
        <div class="pra-benefit-grid">
            @foreach($benefits as $benefit)
                <article class="pra-benefit-card">
                    <span>{{ $loop->iteration }}</span>
                    <strong>{{ $benefit }}</strong>
                </article>
            @endforeach
        </div>
    </section>

    <section class="pra-section pra-timeline" id="jadwal">
        <div class="pra-section-head">
            <span class="pra-kicker">Jadwal Acara</span>
            <h2>Jadwal Kegiatan PRA 2026</h2>
        </div>
        <div class="pra-timeline-list">
            @foreach($schedule as $item)
                <article>
                    <time>{{ $item['time'] ?? '-' }}</time>
                    <div>
                        <h3>{{ $item['title'] ?? 'Agenda PRA' }}</h3>
                        <p>{{ $item['description'] ?? '' }}</p>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <section class="pra-section pra-stats" id="statistik">
        <article><span>Total Peserta</span><strong>{{ $stats['total'] }}</strong></article>
        <article><span>Peserta Grup 1</span><strong>{{ $stats['group_1'] }}</strong></article>
        <article><span>Peserta Grup 2</span><strong>{{ $stats['group_2'] }}</strong></article>
    </section>

    <section class="pra-section" id="peserta">
        <div class="pra-section-head">
            <span class="pra-kicker">Peserta Terdaftar</span>
            <h2>Peserta yang Sudah Mendaftar</h2>
        </div>
        <div class="pra-participant-grid">
            @foreach($groups as $group)
                <section class="pra-participant-column">
                    <h3>{{ $group->name }}</h3>
                    @forelse(($registrationsByGroup[$group->slug] ?? collect()) as $registration)
                        <article class="pra-participant-card">
                            <div class="pra-avatar {{ $registration->gender === 'Perempuan' ? 'girl' : 'boy' }}">{{ strtoupper(mb_substr($registration->nickname, 0, 1)) }}</div>
                            <div>
                                <strong>{{ $registration->nickname }}</strong>
                                <span>Kelas {{ $registration->class_before }}</span>
                            </div>
                        </article>
                    @empty
                        <div class="pra-empty">
                            <strong>Belum ada peserta di grup ini.</strong>
                            <span>Yuk, jadi salah satu keluarga pertama yang mendaftarkan anak untuk PRA 2026.</span>
                        </div>
                    @endforelse
                </section>
            @endforeach
        </div>
    </section>

    <section class="pra-section pra-payment" id="pembayaran">
        <div class="pra-section-head">
            <span class="pra-kicker">Informasi Pembayaran</span>
            <h2>Pembayaran</h2>
            <p>Pembayaran dapat dilakukan melalui transfer atau tunai dengan menghubungi panitia pendaftaran.</p>
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
