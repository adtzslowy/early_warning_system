@php $user = $user ?? null; @endphp

<div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
    <x-input
        label="Nama"
        name="name"
        value="{{ old('name', $user?->name) }}"
        placeholder="mis. Aditya Prasetyo"
        :error="$errors->first('name')"
    />

    <x-input
        label="Email"
        name="email"
        type="email"
        value="{{ old('email', $user?->email) }}"
        placeholder="nama@contoh.com"
        :error="$errors->first('email')"
    />

    <div class="sm:col-span-2 space-y-1.5">
        <label for="role" class="text-sm font-dash font-medium leading-none">Role</label>
        <select id="role" name="role"
            class="flex h-9 w-full rounded-lg border border-[var(--color-input)] bg-transparent px-3 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--color-ring)]">
            <option value="">— Pilih role —</option>
            @foreach ($roles as $r)
                <option value="{{ $r->name }}"
                    @selected(old('role', $user?->roles->first()?->name) === $r->name)>
                    {{ ucfirst($r->name) }}
                </option>
            @endforeach
        </select>
        @error('role')<p class="text-xs text-[var(--color-bahaya)]">{{ $message }}</p>@enderror
    </div>

    <x-input
        label="{{ $user ? 'Kata Sandi Baru' : 'Kata Sandi' }}"
        name="password"
        type="password"
        placeholder="Minimal 8 karakter"
        :error="$errors->first('password')"
        autocomplete="new-password"
    />

    <x-input
        label="Konfirmasi Kata Sandi"
        name="password_confirmation"
        type="password"
        placeholder="Ulangi kata sandi"
        autocomplete="new-password"
    />

    @if ($user)
        <p class="sm:col-span-2 -mt-2 text-xs text-[var(--color-text-muted)]">
            Kosongkan kata sandi bila tidak ingin mengubahnya.
        </p>
    @endif
</div>
