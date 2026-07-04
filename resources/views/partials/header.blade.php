<header class="font-body sticky top-0 z-10 border-b border-[var(--color-border)] bg-[var(--color-bg)]/80 backdrop-blur-lg">
    <div x-data="{
        time: '',
        isDark: document.documentElement.classList.contains('dark'),
        tick() {
            this.time = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        },
        toggleTheme() {
            this.isDark = !this.isDark;
            document.documentElement.classList.toggle('dark', this.isDark);
            localStorage.theme = this.isDark ? 'dark' : 'light';
        }
    }" x-init="tick();
    setInterval(() => tick(), 1000)"
        class="flex items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-3">
            <button @click="sidebarOpen = true"
                class="rounded-lg p-2 text-[var(--color-text-muted)] hover:bg-[var(--color-surface-2)] lg:hidden">
                <x-heroicon-o-bars-3 class="h-5 w-5" />
            </button>

            <div>
                <x-breadcrumb :items="[['label' => 'Dashboard', 'href' => route('dashboard')]]" />
            </div>
        </div>

        <div class="flex items-center gap-3">
            {{-- Toggle light/dark --}}
            <button @click="toggleTheme()"
                class="rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] p-2 text-[var(--color-text-muted)] transition-colors hover:text-[var(--color-text)]"
                :aria-label="isDark ? 'Aktifkan mode terang' : 'Aktifkan mode gelap'">
                <x-heroicon-o-sun x-show="isDark" class="h-4 w-4" />
                <x-heroicon-o-moon x-show="!isDark" x-cloak class="h-4 w-4" />
            </button>
        </div>
    </div>
</header>
