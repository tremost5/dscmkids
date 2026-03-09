<div class="grid-2">
    <div class="field">
        <label>Nama
            <input name="name" value="{{ old('name', $targetUser?->name) }}" required>
        </label>
    </div>
    <div class="field">
        <label>Email
            <input type="email" name="email" value="{{ old('email', $targetUser?->email) }}" required>
        </label>
    </div>
    <div class="field">
        <label>Role
            <select name="role" required>
                @foreach($roleOptions as $key => $item)
                    <option value="{{ $key }}" @selected(old('role', $targetUser?->role ?? 'student') === $key)>{{ $item['label'] }}</option>
                @endforeach
            </select>
        </label>
    </div>
    <div class="field">
        <label>Kelas
            <input name="class_group" value="{{ old('class_group', $targetUser?->class_group) }}">
        </label>
    </div>
    <div class="field">
        <label>Password {{ $targetUser ? '(kosongkan jika tidak diubah)' : '' }}
            <input type="password" name="password" {{ $targetUser ? '' : 'required' }}>
        </label>
    </div>
    <div class="field">
        <label>Konfirmasi Password
            <input type="password" name="password_confirmation" {{ $targetUser ? '' : 'required' }}>
        </label>
    </div>
</div>

<label class="toggle-field">
    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $targetUser?->is_active ?? true) ? 'checked' : '' }}>
    Akun aktif
</label>
