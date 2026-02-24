@extends('admin.layout')

@section('title', 'Edit Testimonial')

@section('content')
<h1 style="margin-top:0;">Edit Testimonial</h1>
<form method="POST" enctype="multipart/form-data" action="{{ route('admin.testimonials.update', $testimonial) }}">
    @csrf
    @method('PUT')
    <div class="field"><label>Nama Depan / Panggilan<input name="name" value="{{ old('name', $testimonial->name) }}" required></label></div>
    <div class="grid-2">
        <div class="field"><label>Keterangan<input name="role_label" value="{{ old('role_label', $testimonial->role_label) }}"></label></div>
        <div class="field"><label>Rating (1-5)<input type="number" min="1" max="5" name="rating" value="{{ old('rating', $testimonial->rating) }}" required></label></div>
    </div>
    <div class="field"><label>Isi Testimonial<textarea name="message" required>{{ old('message', $testimonial->message) }}</textarea></label></div>
    <div class="grid-2">
        <div class="field"><label>Urutan Tampil<input type="number" min="0" name="sort_order" value="{{ old('sort_order', $testimonial->sort_order) }}"></label></div>
        <div class="field"><label>Ganti Avatar<input type="file" name="avatar" accept="image/*"></label></div>
    </div>
    @if($testimonial->avatar_path)
        <div class="field"><img src="{{ asset('storage/'.$testimonial->avatar_path) }}" alt="avatar" style="width:70px;height:70px;object-fit:cover;border-radius:50%;"></div>
    @endif
    <div class="field"><label style="display:flex;align-items:center;gap:8px;"><input type="checkbox" name="is_active" value="1" {{ old('is_active', $testimonial->is_active) ? 'checked' : '' }} style="width:auto;"> Aktif</label></div>
    <div class="actions">
        <button class="btn btn-primary" type="submit">Update</button>
        <a class="btn btn-secondary" href="{{ route('admin.testimonials.index') }}">Batal</a>
    </div>
</form>
@endsection

