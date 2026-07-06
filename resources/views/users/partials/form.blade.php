{{--
    Form field bersama untuk tambah & edit user.
    Variabel:
      $user    → User|null (null = mode tambah)
      $roles   → Collection<Role>
      $devices → Collection<Device> (untuk operator)
--}}
@php
    $user = $user ?? null;
    $currentRole = old('role', $user?->roles->first()?->name);
    $selectedDevices = collect(old('devices', $user?->devices->pluck('id')->all() ?? []))
        ->map(fn ($id) => (string) $id)->all();
    $deviceOptions = $devices->map(fn ($d) => [
        'id' => (string) $d->id,
        'code' => $d->device_code,
        'name' => $d->name,
    ])->values();
@endphp

<div
    x-data="{
        role: '{{ $currentRole }}',
        open: false,
        search: '',
        options: @js($deviceOptions),
        selected: @js($selectedDevices),
        toggle(id) {
            this.selected = this.selected.includes(id)
                ? this.selected.filter(x => x !== id)
                : [...this.selected, id];
        },
        get filtered() {
            const q = this.search.toLowerCase().trim();
            return q === '' ? this.options : this.options.filter(o =>
                o.name.toLowerCase().includes(q) || o.code.toLowerCase().includes(q));
        },
        get chosen() {
            return this.options.filter(o => this.selected.includes(o.id));
        },
    }"
    class="space-y-5"
>
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
            <select id="role" name="role" x-model="role"
                class="flex h-9 w-full rounded-lg border border-[var(--color-input)] bg-transparent px-3 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--color-ring)]">
                <option value="">— Pilih role —</option>
                @foreach ($roles as $r)
                    <option value="{{ $r->name }}" @selected($currentRole === $r->name)>{{ ucfirst($r->name) }}</option>
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

    {{-- Assign alat — searchable multi-select, hanya untuk operator --}}
    <div x-show="role === 'operator'" x-cloak x-transition class="space-y-2">
        <label class="text-sm font-dash font-medium leading-none">Alat yang Ditangani</label>
        <p class="text-xs text-[var(--color-text-muted)]">Cari lalu pilih device yang menjadi tanggung jawab operator ini.</p>

        {{-- input tersembunyi untuk submit --}}
        <template x-for="id in selected" :key="id">
            <input type="hidden" name="devices[]" :value="id">
        </template>

        @if ($devices->isEmpty())
            <p class="rounded-lg border border-[var(--color-border)] bg-[var(--color-surface-2)] px-3 py-2.5 text-sm text-[var(--color-text-muted)]">
                Belum ada device terdaftar.
            </p>
        @else
            <div class="relative" @click.outside="open = false" @keydown.escape="open = false">
                {{-- kotak: chip terpilih + input cari --}}
                <div @click="open = true; $refs.search.focus()"
                    class="flex min-h-9 w-full flex-wrap items-center gap-1.5 rounded-lg border border-[var(--color-input)] bg-transparent px-2 py-1.5 text-sm shadow-sm transition-colors focus-within:ring-2 focus-within:ring-[var(--color-ring)]">

                    <template x-for="d in chosen" :key="d.id">
                        <span class="inline-flex items-center gap-1 rounded-md bg-[color-mix(in_srgb,var(--color-accent)_14%,transparent)] px-2 py-0.5 text-xs text-[var(--color-accent)]">
                            <span x-text="d.code" class="font-mono"></span>
                            <button type="button" @click.stop="toggle(d.id)" class="transition-colors hover:text-[var(--color-bahaya)]">
                                <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z"/></svg>
                            </button>
                        </span>
                    </template>

                    <input x-ref="search" x-model="search" @focus="open = true" type="text"
                        :placeholder="chosen.length ? '' : 'Cari kode atau nama alat…'"
                        class="min-w-[6rem] flex-1 border-0 bg-transparent p-0 text-sm placeholder:text-[var(--color-text-muted)] focus:outline-none focus:ring-0">
                </div>

                {{-- dropdown hasil --}}
                <div x-show="open" x-cloak x-transition.opacity
                    class="absolute z-20 mt-1 max-h-56 w-full overflow-y-auto rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] p-1 shadow-lg">
                    <template x-for="o in filtered" :key="o.id">
                        <button type="button" @click="toggle(o.id)"
                            class="flex w-full items-center gap-3 rounded-md px-2 py-2 text-left text-sm transition-colors hover:bg-[var(--color-surface-2)]">
                            <span class="flex h-4 w-4 shrink-0 items-center justify-center rounded border"
                                :class="selected.includes(o.id) ? 'border-[var(--color-accent)] bg-[var(--color-accent)] text-[var(--color-accent-foreground)]' : 'border-[var(--color-input)]'">
                                <svg x-show="selected.includes(o.id)" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.7 5.3a1 1 0 0 1 0 1.4l-7.5 7.5a1 1 0 0 1-1.4 0l-3.5-3.5a1 1 0 1 1 1.4-1.4l2.8 2.8 6.8-6.8a1 1 0 0 1 1.4 0Z" clip-rule="evenodd"/></svg>
                            </span>
                            <span class="font-mono text-xs text-[var(--color-text-muted)]" x-text="o.code"></span>
                            <span class="flex-1" x-text="o.name"></span>
                        </button>
                    </template>
                    <p x-show="filtered.length === 0" class="px-2 py-3 text-center text-xs text-[var(--color-text-muted)]">
                        Tidak ada alat cocok.
                    </p>
                </div>
            </div>
        @endif
        @error('devices')<p class="text-xs text-[var(--color-bahaya)]">{{ $message }}</p>@enderror
        @error('devices.*')<p class="text-xs text-[var(--color-bahaya)]">Device yang dipilih tidak valid.</p>@enderror
    </div>
</div>
