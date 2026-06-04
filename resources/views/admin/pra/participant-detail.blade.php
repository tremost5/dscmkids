@extends('admin.layout')

@section('title', 'Detail Peserta PRA 2026')

@section('content')
@include('admin.pra.partials.nav')

<div class="toolbar" style="margin-top:16px;">
    <div>
        <h2 class="section-title">{{ $registration->full_name }}</h2>
        <p class="muted">Kelas {{ $registration->class_before }} - {{ $registration->group?->name ?: 'Grup belum tersedia' }}</p>
    </div>
    <div class="toolbar-actions">
        <a class="btn btn-secondary" href="{{ route('admin.pra.participants') }}">Kembali ke Peserta</a>
        <form method="POST" action="{{ route('admin.pra.participants.resend-whatsapp', $registration) }}" class="inline-form" data-loading-form>
            @csrf
            <button class="btn btn-primary" type="submit">Resend WhatsApp Confirmation</button>
        </form>
    </div>
</div>

<div class="detail-grid" style="margin-top:16px;">
    <section class="detail-panel detail-copy">
        <div class="section-head">
            <h2 class="section-title">Data Peserta</h2>
            <p class="section-copy">Ringkasan data pendaftaran PRA 2026.</p>
        </div>

        <div class="detail-kv">
            <div class="detail-kv-item"><span>Nama Lengkap</span><strong>{{ $registration->full_name }}</strong></div>
            <div class="detail-kv-item"><span>Nama Panggilan</span><strong>{{ $registration->nickname }}</strong></div>
            <div class="detail-kv-item"><span>Jenis Kelamin</span><strong>{{ $registration->gender }}</strong></div>
            <div class="detail-kv-item"><span>Tanggal Lahir</span><strong>{{ optional($registration->birth_date)->format('d M Y') ?: '-' }}</strong></div>
            <div class="detail-kv-item"><span>Kelas</span><strong>{{ $registration->class_before }}</strong></div>
            <div class="detail-kv-item"><span>Sekolah Minggu</span><strong>{{ $registration->church_branch }}</strong></div>
            <div class="detail-kv-item"><span>Grup PRA</span><strong>{{ $registration->group?->name ?: '-' }}</strong></div>
            <div class="detail-kv-item"><span>Alergi</span><strong>{{ $registration->has_allergy ? ($registration->allergy_notes ?: 'Ya') : 'Tidak ada' }}</strong></div>
        </div>
    </section>

    <aside class="detail-panel detail-card">
        <div class="section-head">
            <h2 class="section-title">Kontak & Pembayaran</h2>
            <p class="section-copy">Informasi untuk konfirmasi panitia.</p>
        </div>

        <div class="detail-kv">
            <div class="detail-kv-item"><span>Orang Tua/Wali</span><strong>{{ $registration->parent_name }}</strong></div>
            <div class="detail-kv-item"><span>No WhatsApp</span><strong>{{ $registration->whatsapp_number }}</strong></div>
            <div class="detail-kv-item"><span>Alamat</span><strong>{{ $registration->address }}</strong></div>
            <div class="detail-kv-item"><span>Metode Pembayaran</span><strong>{{ $registration->payment_method === 'cash' ? 'Tunai' : 'Transfer' }}</strong></div>
            <div class="detail-kv-item"><span>Status Pembayaran</span><strong>{{ $registration->paymentStatusLabel() }}</strong></div>
            <div class="detail-kv-item"><span>Status Kehadiran</span><strong>{{ $registration->attendanceStatusLabel() }}</strong></div>
            <div class="detail-kv-item"><span>Tanggal Daftar</span><strong>{{ optional($registration->registered_at)->format('d M Y H:i') ?: '-' }}</strong></div>
            <div class="detail-kv-item">
                <span>Bukti Transfer</span>
                <strong>
                    @if($registration->paymentProof)
                        <a href="{{ asset('storage/'.$registration->paymentProof->file_path) }}" target="_blank" rel="noopener">Lihat bukti pembayaran</a>
                    @else
                        Tidak ada upload
                    @endif
                </strong>
            </div>
        </div>
    </aside>
</div>
@endsection
