@php
    $settingsNav = [
        ['label' => 'Umum', 'desc' => 'Tampilan dashboard', 'route' => 'settings.edit', 'icon' => 'heroicon-o-adjustments-horizontal'],
        ['label' => 'Profil', 'desc' => 'Akun & foto', 'route' => 'settings.profile', 'icon' => 'heroicon-o-user-circle'],
    ];
@endphp

<nav class="flex gap-2 lg:flex-col lg:gap-1" aria-label="Navigasi setting">
    @foreach ($settingsNav as $item)
        <a href="{{ route($item['route']) }}"
            @class([
                'group flex flex-1 items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors lg:flex-none',
                'bg-[var(--color-accent)]/10 text-[var(--color-accent)]' => request()->routeIs($item['route']),
                'text-[var(--color-text-muted)] hover:bg-[var(--color-surface-2)] hover:text-[var(--color-text)]' => ! request()->routeIs($item['route']),
            ])
        >
            <x-dynamic-component :component="$item['icon']" class="h-5 w-5 shrink-0" />
            <span class="min-w-0">
                <span class="block truncate">{{ $item['label'] }}</span>
                <span class="hidden truncate text-xs text-[var(--color-text-muted)] sm:block">{{ $item['desc'] }}</span>
            </span>
        </a>
    @endforeach
</nav>
