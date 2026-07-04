<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') · EWS Banjir Rob</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@100..900&family=Geist+Mono:wght@100..900&display=swap"
        rel="stylesheet">

    {{-- Anti-flash: set class dark SEBELUM body dirender, biar nggak kedip --}}
    <script>
        if (localStorage.theme === 'light') {
            document.documentElement.classList.remove('dark');
        } else {
            document.documentElement.classList.add('dark');
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Override warna aksen per preferensi user (menang cascade karena setelah @vite) --}}
    @php $accentColor = \App\Support\DisplayPreferences::accentColor(\App\Support\DisplayPreferences::forCurrentUser()['accent']); @endphp
    @if ($accentColor)
        <style>:root, .dark { --color-accent: {{ $accentColor }}; --color-accent-foreground: #ffffff; }</style>
    @endif
</head>

<body class="min-h-screen font-dash text-[var(--color-text)] antialiased">

    <div x-data="{ sidebarOpen: false }" class="relative z-10 flex min-h-screen">

        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
            class="fixed inset-y-0 left-0 z-30 h-screen w-64 shrink-0 border-r border-[var(--color-border)] bg-[var(--color-surface)]/95 backdrop-blur-lg transition-transform duration-200 lg:sticky lg:top-0 lg:translate-x-0">
            @include('partials.sidebar')
        </aside>

        {{-- Overlay mobile --}}
        <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" class="fixed inset-0 z-20 bg-black/60 lg:hidden">
        </div>

        {{-- Main --}}
        <div class="flex min-h-screen flex-1 flex-col">
            @include('partials.header')

            <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
                @yield('content')
            </main>
        </div>
    </div>

</body>

</html>
