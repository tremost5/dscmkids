@extends('admin.layout')

@section('title', 'Edit User')

@section('content')
<div class="page-grid">
    <div class="page-header">
        <div class="page-header-copy">
            <h1>Edit user</h1>
            <p class="muted">Perbarui role, akses, status, dan kredensial akun.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.users.update', $targetUser) }}" class="form-shell" data-loading-form>
        @csrf
        @method('PUT')
        <section class="form-panel">
            @include('admin.users.partials.form', ['targetUser' => $targetUser])
        </section>
        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Update</button>
            <a class="btn btn-secondary" href="{{ route('admin.users.index') }}">Kembali</a>
        </div>
    </form>
</div>
@endsection
