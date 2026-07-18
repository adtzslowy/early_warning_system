@php
    // Menu dikelompokkan per kategori. Tambah/pindah item cukup di sini —
    // 'group' => label kategori (null = tanpa header), 'items' => menu-nya.
    $menu = [
        [
            'group' => null,
            'items' => [['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'heroicon-o-home']],
        ],
        [
            'group' => 'Master Data',
            'items' => [
                ['label' => 'Devices', 'route' => 'devices', 'icon' => 'heroicon-o-cpu-chip'],
                ['label' => 'Sensor', 'route' => 'sensors', 'icon' => 'heroicon-o-signal'],
                ['label' => 'Monitoring', 'route' => 'monitoring', 'icon' => 'heroicon-o-chart-bar'],
            ],
        ],
        [
            'group' => 'Analitik & Peringatan',
            'items' => [
                ['label' => 'Prediction', 'route' => 'prediction', 'icon' => 'heroicon-o-arrow-trending-up'],
                ['label' => 'Alert', 'route' => 'alerts', 'icon' => 'heroicon-o-exclamation-circle'],
                ['label' => 'Notification Log', 'route' => 'notifications.log', 'icon' => 'heroicon-o-bell'],
            ],
        ],

        [
            'group' => 'Administrasi',
            'items' => [
                ['label' => 'User Manajemen', 'route' => 'users', 'icon' => 'heroicon-o-users', 'can' => 'view users'],
            ],
        ],
    ];
@endphp

<div class="flex h-full flex-col">
    <div class="flex items-center gap-3 border-b border-[var(--color-border)] px-6 py-5">
        <div
            class="flex h-9 w-9 items-center justify-center rounded-lg bg-[var(--color-accent)]/15 text-[var(--color-accent)]">
            <x-heroicon-o-signal class="h-5 w-5" />
        </div>
        <div class="font-display leading-tight">
            <p class="text-sm font-semibold tracking-tight">EWS Banjir Rob</p>
            <p class="font-mono text-[10px] text-[var(--color-text-muted)]">Ketapang Coastal Network</p>
        </div>
    </div>

    <nav class="flex-1 space-y-6 overflow-y-auto px-3 py-4">
        @foreach ($menu as $section)
            @php
                $visibleItems = array_filter($section['items'], function ($item) {
                    return empty($item['can']) || auth()->user()?->can($item['can']);
                });
            @endphp
            @if (!empty($visibleItems))
            <div class="space-y-1">
                @if (!empty($section['group']))
                    <p
                        class="px-3 pb-1 text-[10px] font-semibold uppercase tracking-wider text-[var(--color-text-muted)]">
                        {{ $section['group'] }}
                    </p>
                @endif

                @foreach ($section['items'] as $item)
                    @php
                        $active = request()->routeIs($item['route']) || request()->routeIs($item['route'] . '.*');
                        if (!empty($item['can']) && !auth()->user()?->can($item['can'])) {
                            continue;
                        }
                    @endphp
                    <a href="{{ Route::has($item['route']) ? route($item['route']) : '#' }}"
                        @class([
                            'group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors',
                            'bg-[var(--color-accent)]/10 text-[var(--color-accent)]' => $active,
                            'text-[var(--color-text-muted)] hover:bg-[var(--color-surface-2)] hover:text-[var(--color-text)]' => !$active,
                        ])>
                        <x-dynamic-component :component="$item['icon']" class="h-5 w-5 shrink-0" />
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </div>
            @endif
        @endforeach
    </nav>

    {{-- Akun user --}}
    <div class="border-t border-[var(--color-border)] p-3">
        @auth
            @php
                $user = auth()->user();
                $initials = \Illuminate\Support\Str::of($user->name)
                    ->explode(' ')
                    ->map(fn($w) => \Illuminate\Support\Str::substr($w, 0, 1))
                    ->take(2)
                    ->implode('');
                $itemClass =
                    'flex items-center gap-2.5 rounded-md px-2.5 py-2 text-sm text-[var(--color-text-muted)] transition-colors hover:bg-[var(--color-surface-2)] hover:text-[var(--color-text)]';
            @endphp

            <div x-data="{ open: false }" class="relative">
                {{-- Popup muncul DI ATAS trigger --}}
                <div x-show="open" x-cloak @click.outside="open = false" @keydown.escape.window="open = false"
                    x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1"
                    class="absolute bottom-full left-0 right-0 mb-2 overflow-hidden rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] p-1 shadow-lg">
                    <a href="{{ route('settings.edit') }}" class="{{ $itemClass }}">
                        <x-heroicon-o-cog-6-tooth class="h-4 w-4 shrink-0" />
                        Setting
                    </a>
                    <a href="{{ Route::has('profile') ? route('profile') : '#' }}" class="{{ $itemClass }}">
                        <x-heroicon-o-user class="h-4 w-4 shrink-0" />
                        Profil
                    </a>

                    <div class="my-1 border-t border-[var(--color-border)]"></div>

                    <form method="POST" action="{{ Route::has('logout') ? route('logout') : '#' }}">
                        @csrf
                        <button type="submit"
                            class="flex w-full items-center gap-2.5 rounded-md px-2.5 py-2 text-left text-sm text-[var(--color-bahaya)] transition-colors hover:bg-[color-mix(in_srgb,var(--color-bahaya)_12%,transparent)]">
                            <x-heroicon-o-arrow-right-on-rectangle class="h-4 w-4 shrink-0" />
                            Keluar
                        </button>
                    </form>
                </div>

                {{-- Trigger: nama + email --}}
                <button type="button" @click="open = !open" :class="open && 'bg-[var(--color-surface-2)]'"
                    class="flex w-full items-center gap-3 rounded-lg px-2 py-2 text-left transition-colors hover:bg-[var(--color-surface-2)]">
                    @if ($user->fotoUrl())
                        <img src="{{ $user->fotoUrl() }}" alt="Foto profil"
                            class="h-9 w-9 shrink-0 rounded-full border border-[var(--color-border)] object-cover">
                    @else
                        <span
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[var(--color-accent)] text-xs font-semibold text-[var(--color-accent-foreground)]">{{ $initials }}</span>
                    @endif
                    <span class="min-w-0 flex-1">
                        <span class="flex items-center gap-1 min-w-0">
                            <span class="truncate text-sm font-medium text-[var(--color-text)]">{{ $user->name }}</span>
                            <x-verified-badge :user="$user" />
                        </span>
                        <span class="block truncate text-xs text-[var(--color-text-muted)]">{{ $user->email }}</span>
                    </span>
                    <x-heroicon-o-chevron-up-down class="h-4 w-4 shrink-0 text-[var(--color-text-muted)]" />
                </button>
            </div>
        @else
            <a href="{{ Route::has('login') ? route('login') : '#' }}"
                class="flex items-center justify-center gap-2 rounded-lg border border-[var(--color-border)] px-3 py-2.5 text-sm font-medium text-[var(--color-text)] transition-colors hover:bg-[var(--color-surface-2)]">
                <x-heroicon-o-arrow-left-on-rectangle class="h-4 w-4 shrink-0" />
                Masuk
            </a>
        @endauth
    </div>
</div>
