@extends('layouts.app')

@section('title', 'User Managemen')
@section('page-title', 'User Managemen')

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['label' => 'Dashboard', 'href' => route('dashboard')],
        ['label' => 'User Managemen'],
    ]" />
@endsection

@section('content')
<div class="space-y-6"
    x-data="{
        confirm: false,
        url: '',
        name: '',
        ask(url, name) { this.url = url; this.name = name; this.confirm = true; },
    }"
    @keydown.escape.window="confirm = false"
>

    <x-page-header
        title="User Managemen"
        description="Kelola akun pengguna dan hak akses sistem."
    >
        @can('create users')
            <x-slot:actions>
                <x-button href="{{ route('users.create') }}" variant="primary">
                    <x-heroicon-o-plus class="h-4 w-4" />
                    Tambah User
                </x-button>
            </x-slot:actions>
        @endcan
    </x-page-header>

    @if (session('status'))
        <div class="flex items-center gap-2 rounded-lg border border-[color-mix(in_srgb,var(--color-aman)_45%,transparent)] bg-[color-mix(in_srgb,var(--color-aman)_12%,transparent)] px-4 py-3 text-sm text-[var(--color-aman)]">
            <x-heroicon-o-check-circle class="h-4 w-4 shrink-0" />
            {{ session('status') }}
        </div>
    @endif

    <x-card padding="p-0">
        {{-- Toolbar: cari + filter role --}}
        <div class="border-b border-[var(--color-border)] p-4">
            <form method="GET" action="{{ route('users') }}" class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <div class="relative flex-1">
                    <x-heroicon-o-magnifying-glass class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[var(--color-text-muted)]" />
                    <input type="search" name="q" value="{{ $search }}" placeholder="Cari nama atau email…"
                        class="h-9 w-full rounded-lg border border-[var(--color-input)] bg-transparent pl-9 pr-3 text-sm shadow-sm transition-colors placeholder:text-[var(--color-text-muted)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--color-ring)]">
                </div>
                <select name="role"
                    class="h-9 rounded-lg border border-[var(--color-input)] bg-transparent px-3 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--color-ring)]">
                    <option value="">Semua role</option>
                    @foreach ($roles as $r)
                        <option value="{{ $r->name }}" @selected($role === $r->name)>{{ ucfirst($r->name) }}</option>
                    @endforeach
                </select>
                <x-button type="submit" variant="secondary">Terapkan</x-button>
                @if ($search !== '' || $role !== '')
                    <x-button href="{{ route('users') }}" variant="ghost">Reset</x-button>
                @endif
            </form>
        </div>

        @if ($users->isEmpty())
            <x-empty-state
                icon="inbox"
                title="{{ $total === 0 ? 'Belum ada user' : 'Tidak ada hasil' }}"
                message="{{ $total === 0 ? 'Tambahkan akun pengguna pertama.' : 'Coba ubah kata kunci atau filter role.' }}"
            >
                @if ($total === 0)
                    @can('create users')
                        <x-slot:action>
                            <x-button href="{{ route('users.create') }}" variant="primary">
                                <x-heroicon-o-plus class="h-4 w-4" />
                                Tambah User
                            </x-button>
                        </x-slot:action>
                    @endcan
                @endif
            </x-empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="rtable w-full text-sm">
                    <thead class="border-b border-[var(--color-border)] text-left text-xs uppercase tracking-wider text-[var(--color-text-muted)]">
                        <tr>
                            <th class="px-4 py-3 font-medium">Nama</th>
                            <th class="px-4 py-3 font-medium">Email</th>
                            <th class="px-4 py-3 font-medium">Role</th>
                            <th class="px-4 py-3 font-medium">Bergabung</th>
                            <th class="px-4 py-3 text-right font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-border)]">
                        @foreach ($users as $u)
                            <tr class="transition-colors hover:bg-[var(--color-surface-2)]">
                                <td data-label="Nama" class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        @if ($u->fotoUrl())
                                            <img src="{{ $u->fotoUrl() }}" alt="Foto {{ $u->name }}"
                                                class="h-8 w-8 shrink-0 rounded-full border border-[var(--color-border)] object-cover">
                                        @else
                                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[var(--color-accent)] text-xs font-semibold text-[var(--color-accent-foreground)]">{{ $u->initials() }}</span>
                                        @endif
                                        <span class="font-medium">{{ $u->name }}</span>
                                    </div>
                                </td>
                                <td data-label="Email" class="px-4 py-3 text-[var(--color-text-muted)]">{{ $u->email }}</td>
                                <td data-label="Role" class="px-4 py-3">
                                    @forelse ($u->roles as $r)
                                        <span class="inline-flex items-center rounded-full border border-[var(--color-border)] bg-[var(--color-surface-2)] px-2 py-0.5 text-xs font-medium">{{ ucfirst($r->name) }}</span>
                                    @empty
                                        <span class="text-[var(--color-text-muted)]">—</span>
                                    @endforelse
                                </td>
                                <td data-label="Bergabung" class="px-4 py-3 text-[var(--color-text-muted)]">
                                    {{ $u->created_at?->timezone('Asia/Jakarta')->format('d M Y') ?? '—' }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        @can('edit users')
                                            <a href="{{ route('users.edit', $u) }}"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-[var(--color-text-muted)] transition-colors hover:bg-[var(--color-surface)] hover:text-[var(--color-text)]"
                                                title="Edit">
                                                <x-heroicon-o-pencil-square class="h-4 w-4" />
                                            </a>
                                        @endcan
                                        @can('delete users')
                                            @unless ($u->is(auth()->user()))
                                                <button type="button"
                                                    @click="ask('{{ route('users.destroy', $u) }}', '{{ $u->name }}')"
                                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-[var(--color-text-muted)] transition-colors hover:bg-[color-mix(in_srgb,var(--color-bahaya)_12%,transparent)] hover:text-[var(--color-bahaya)]"
                                                    title="Hapus">
                                                    <x-heroicon-o-trash class="h-4 w-4" />
                                                </button>
                                            @endunless
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex flex-col items-center justify-between gap-3 border-t border-[var(--color-border)] p-4 sm:flex-row">
                <p class="text-xs text-[var(--color-text-muted)]">
                    Menampilkan {{ $users->firstItem() }}–{{ $users->lastItem() }} dari {{ $users->total() }} user
                </p>
                <x-pagination :paginator="$users" />
            </div>
        @endif
    </x-card>

    {{-- Modal konfirmasi hapus --}}
    <template x-teleport="body">
        <div x-show="confirm" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div x-show="confirm" x-transition.opacity @click="confirm = false"
                class="fixed inset-0 bg-black/50 backdrop-blur-sm"></div>
            <div x-show="confirm"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                class="card relative z-10 w-full max-w-md p-6">
                <div class="flex items-start gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[color-mix(in_srgb,var(--color-bahaya)_12%,transparent)] text-[var(--color-bahaya)]">
                        <x-heroicon-o-exclamation-triangle class="h-5 w-5" />
                    </span>
                    <div>
                        <h2 class="text-lg font-semibold leading-none tracking-tight">Hapus user?</h2>
                        <p class="mt-1.5 text-sm text-[var(--color-text-muted)]">
                            Akun <span class="font-medium text-[var(--color-text)]" x-text="name"></span> akan dihapus permanen. Tindakan ini tidak bisa dibatalkan.
                        </p>
                    </div>
                </div>
                <form method="POST" :action="url" class="mt-6 flex items-center justify-end gap-3">
                    @csrf
                    @method('DELETE')
                    <x-button type="button" variant="outline" @click="confirm = false">Batal</x-button>
                    <x-button type="submit" variant="destructive">Hapus</x-button>
                </form>
            </div>
        </div>
    </template>
</div>
@endsection
