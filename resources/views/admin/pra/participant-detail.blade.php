@extends('admin.layout')

@section('title', 'Detail Peserta PRA 2026')

@section('content')
@include('admin.pra.partials.nav')

<form
    id="participant-form"
    method="POST"
    action="{{ route('admin.pra.participant.update', $registration) }}"
>
    @csrf
    @method('PATCH')
    
<div class="toolbar" style="margin-top:16px;">
    <div>
        <h2 class="section-title">{{ $registration->full_name }}</h2>
        <p class="muted">
            Kelas {{ $registration->class_before }}
            - {{ $registration->group?->name ?: 'Grup belum tersedia' }}
        </p>
    </div>

    <div class="toolbar-actions">

        <button
            type="submit"
            form="participant-form"
            class="btn btn-primary">
            Simpan Perubahan
        </button>

        <a class="btn btn-secondary"
           href="{{ route('admin.pra.participants') }}">
           Kembali ke Peserta
        </a>

    </div>
</div>

<div class="detail-grid" style="margin-top:16px;">
    <section class="detail-panel detail-copy">
        <div class="section-head">
            <h2 class="section-title">Data Peserta</h2>
            <p class="section-copy">Ringkasan data pendaftaran PRA 2026.</p>
        </div>

        <div class="detail-kv">

    <div class="detail-kv-item">
        <span>Nama Lengkap</span>
        <input type="text" name="full_name"
               value="{{ old('full_name',$registration->full_name) }}">
    </div>

    <div class="detail-kv-item">
        <span>Nama Panggilan</span>
        <input type="text" name="nickname"
               value="{{ old('nickname',$registration->nickname) }}">
    </div>

    <div class="detail-kv-item">
        <span>Jenis Kelamin</span>
        <select name="gender">
            <option value="Laki-laki" @selected($registration->gender=='Laki-laki')>Laki-laki</option>
            <option value="Perempuan" @selected($registration->gender=='Perempuan')>Perempuan</option>
        </select>
    </div>

    <div class="detail-kv-item">
        <span>Tanggal Lahir</span>
        <input type="date"
               name="birth_date"
               value="{{ optional($registration->birth_date)->format('Y-m-d') }}">
    </div>

    <div class="detail-kv-item">
        <span>Kelas</span>
        <input type="text"
               name="class_before"
               value="{{ old('class_before',$registration->class_before) }}">
    </div>

    <div class="detail-kv-item">
        <span>Sekolah Minggu</span>
        <input type="text"
               name="church_branch"
               value="{{ old('church_branch',$registration->church_branch) }}">
    </div>

    <div class="detail-kv-item">
    <span>Grup PRA</span>

    <select name="event_group_id">
        @foreach($event->groups as $group)
            <option
                value="{{ $group->id }}"
                @selected($registration->event_group_id == $group->id)>
                {{ $group->name }}
            </option>
        @endforeach
    </select>
</div>

    <div class="detail-kv-item">
        <span>Ada Alergi?</span>
        <select name="has_allergy">
            <option value="0" @selected(!$registration->has_allergy)>Tidak</option>
            <option value="1" @selected($registration->has_allergy)>Ya</option>
        </select>
    </div>

    <div class="detail-kv-item">
        <span>Catatan Alergi</span>
        <textarea name="allergy_notes" rows="3">{{ old('allergy_notes',$registration->allergy_notes) }}</textarea>
    </div>

</div>
</section>

<aside class="detail-panel detail-card">
    <div class="section-head">
        <h2 class="section-title">Kontak & Pembayaran</h2>
        <p class="section-copy">Informasi untuk konfirmasi panitia.</p>
    </div>

    <div class="detail-kv">

        <div class="detail-kv-item">
            <span>Orang Tua/Wali</span>
            <input type="text"
                   name="parent_name"
                   value="{{ old('parent_name',$registration->parent_name) }}">
        </div>

        <div class="detail-kv-item">
            <span>No WhatsApp</span>
            <input type="text"
                   name="whatsapp_number"
                   value="{{ old('whatsapp_number',$registration->whatsapp_number) }}">
        </div>

        <div class="detail-kv-item">
            <span>Alamat</span>
            <textarea name="address" rows="3">{{ old('address',$registration->address) }}</textarea>
        </div>

        <div class="detail-kv-item">
            <span>Metode Pembayaran</span>
            <strong>
                {{ $registration->payment_method === 'cash' ? 'Tunai' : 'Transfer' }}
            </strong>
        </div>

        <div class="detail-kv-item">
            <span>Status Pembayaran</span>
            <strong>{{ $registration->paymentStatusLabel() }}</strong>
        </div>

        <div class="detail-kv-item">
            <span>Status Kehadiran</span>
            <strong>{{ $registration->attendanceStatusLabel() }}</strong>
        </div>

        <div class="detail-kv-item">
            <span>Tanggal Daftar</span>
            <strong>
                {{ optional($registration->registered_at)->format('d M Y H:i') ?: '-' }}
            </strong>
        </div>

        <div class="detail-kv-item">
            <span>Bukti Transfer</span>

            <strong>
                @if($registration->paymentProof)

    <a href="{{ asset('storage/'.$registration->paymentProof->file_path) }}"
       target="_blank">
        Lihat bukti pembayaran
    </a>

    <br><br>

    <img
        src="{{ asset('storage/'.$registration->paymentProof->file_path) }}"
        style="max-width:300px;border-radius:12px;">

@else

    Tidak ada upload

@endif
            </strong>
        </div>

    </div>
    </aside>
</div>
</form>

<form
    method="POST"
    action="{{ route('admin.pra.participants.resend-whatsapp', $registration) }}"
    style="margin-top:15px;">
    @csrf

    <button class="btn btn-secondary" type="submit">
        Resend WhatsApp Confirmation
    </button>
</form>
@endsection
