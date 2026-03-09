@extends('admin.layout')

@section('title', 'Edit Testimonial')

@section('content')
<div class="page-grid">
    <div class="page-header">
        <div class="page-header-copy">
            <h1>Edit testimonial</h1>
            <p class="muted">Perbarui isi, rating, status tayang, dan balasan admin.</p>
        </div>
    </div>

    <form method="POST" enctype="multipart/form-data" action="{{ route('admin.testimonials.update', $testimonial) }}" class="form-shell" data-loading-form>
        @csrf
        @method('PUT')
        <section class="form-panel">
            <div class="field"><label>Nama Depan / Panggilan<input name="name" value="{{ old('name', $testimonial->name) }}" required></label></div>
            <div class="grid-2">
                <div class="field"><label>Keterangan<input name="role_label" value="{{ old('role_label', $testimonial->role_label) }}"></label></div>
                <div class="field"><label>Rating (1-5)<input type="number" min="1" max="5" name="rating" value="{{ old('rating', $testimonial->rating) }}" required></label></div>
            </div>
            <div class="field"><label>Isi Testimonial<textarea name="message" required>{{ old('message', $testimonial->message) }}</textarea></label></div>
            <div class="field"><label>Balasan Admin (opsional)<textarea name="admin_reply" placeholder="Tulis respon singkat admin untuk testimonial ini...">{{ old('admin_reply', $testimonial->admin_reply) }}</textarea></label></div>
            <div class="grid-2">
                <div class="field"><label>Urutan Tampil<input type="number" min="0" name="sort_order" value="{{ old('sort_order', $testimonial->sort_order) }}"></label></div>
                <div class="field"><label>Ganti Avatar<input type="file" name="avatar" accept="image/*"></label></div>
            </div>
            @if($testimonial->avatar_path)
                <img src="{{ asset('storage/'.$testimonial->avatar_path) }}" alt="avatar" class="thumb thumb--avatar">
            @endif
            <label class="toggle-field"><input type="checkbox" name="is_active" value="1" {{ old('is_active', $testimonial->is_active) ? 'checked' : '' }}> Aktif</label>
        </section>

        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Update</button>
            <a class="btn btn-secondary" href="{{ route('admin.testimonials.index') }}">Batal</a>
        </div>
    </form>
</div>
@endsection
