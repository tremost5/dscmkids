@extends('admin.layout')

@section('title', 'Tambah User')

@section('content')
<div class="page-grid">
    <div class="page-header">
        <div class="page-header-copy">
            <h1>Tambah user</h1>
            <p class="muted">Buat akun baru untuk admin, editor, atau murid.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.users.store') }}" class="form-shell" data-loading-form>
        @csrf
        <section class="form-panel">
            @include('admin.users.partials.form', ['targetUser' => null])
        </section>
        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Simpan</button>
            <a class="btn btn-secondary" href="{{ route('admin.users.index') }}">Batal</a>
        </div>
    </form>
</div>
@endsection
