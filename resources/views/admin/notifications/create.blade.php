@extends('admin.layout')

@section('title', 'Kirim Broadcast')

@section('content')
<h1 style="margin-top:0;">Kirim Broadcast Notifikasi</h1>
<p class="muted">Broadcast dikirim lewat queue background. Email dikirim per murid, sedangkan WhatsApp menggunakan webhook opsional.</p>

<form method="POST" action="{{ route('admin.notifications.store') }}">
    @csrf
    <div class="field"><label>Judul<input name="title" value="{{ old('title') }}" required></label></div>
    <div class="field"><label>Pesan<textarea name="message" required>{{ old('message') }}</textarea></label></div>
    <div class="field"><label>Channel
        <select name="channel" style="width:100%;padding:10px;border:1px solid #cbd5e1;border-radius:10px;margin-top:6px;">
            <option value="email" @selected(old('channel') === 'email')>Email</option>
            <option value="whatsapp" @selected(old('channel') === 'whatsapp')>WhatsApp (Webhook)</option>
            <option value="email_whatsapp" @selected(old('channel') === 'email_whatsapp')>Email + WhatsApp</option>
        </select>
    </label></div>
    <div class="actions">
        <button class="btn btn-primary" type="submit">Kirim</button>
        <a class="btn btn-secondary" href="{{ route('admin.notifications.index') }}">Batal</a>
    </div>
</form>
@endsection
