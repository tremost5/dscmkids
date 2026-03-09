@extends('admin.layout')

@section('title', 'Kirim Broadcast')

@section('content')
<div class="page-grid">
    <div class="page-header">
        <div class="page-header-copy">
            <h1>Kirim broadcast notifikasi</h1>
            <p class="muted">Broadcast dikirim lewat queue background. Email dikirim per murid, sedangkan WhatsApp memakai webhook opsional.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.notifications.store') }}" class="form-shell" data-loading-form>
        @csrf
        <section class="form-panel">
            <div class="field"><label>Judul<input name="title" value="{{ old('title') }}" required></label></div>
            <div class="field"><label>Pesan<textarea name="message" required>{{ old('message') }}</textarea></label></div>
            <div class="field"><label>Channel
                <select name="channel">
                    <option value="email" @selected(old('channel') === 'email')>Email</option>
                    <option value="whatsapp" @selected(old('channel') === 'whatsapp')>WhatsApp (Webhook)</option>
                    <option value="email_whatsapp" @selected(old('channel') === 'email_whatsapp')>Email + WhatsApp</option>
                </select>
            </label></div>
        </section>

        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Kirim</button>
            <a class="btn btn-secondary" href="{{ route('admin.notifications.index') }}">Batal</a>
        </div>
    </form>
</div>
@endsection
