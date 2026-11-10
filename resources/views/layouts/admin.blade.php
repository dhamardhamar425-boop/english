<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ $schoolName ?? config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-800 antialiased">
    <div class="min-h-screen lg:flex">
        {{-- Sidebar --}}
        <aside id="admin-sidebar"
                 class="fixed inset-y-0 left-0 z-40 w-72 -translate-x-full overflow-y-auto bg-emerald-950 text-white transition-transform duration-300 lg:sticky lg:top-0 lg:h-screen lg:translate-x-0">
            <div class="flex h-20 items-center gap-3 border-b border-white/10 px-6">
                <span class="grid h-11 w-11 place-items-center rounded-2xl bg-white/10 ring-1 ring-white/15">
                    <svg class="h-6 w-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m22 8-6 4 6 4V8Z"/><rect width="14" height="12" x="2" y="6" rx="2" ry="2"/></svg>
                </span>
                <div class="leading-tight">
                    <p class="font-display text-sm font-extrabold tracking-wide">ADMIN PORTAL</p>
                    <p class="text-[11px] text-slate-400">{{ $schoolName ?? config('app.name') }}</p>
                </div>
            </div>

            @include('components.admin.sidebar')

            <div class="border-t border-white/10 p-5">
                <div class="flex items-center gap-3">
                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-white/10 text-sm font-bold text-white ring-1 ring-white/15">
                        {{ strtoupper(substr((string) auth()->user()?->name, 0, 1)) }}
                    </span>
                    <div class="min-w-0 flex-1 leading-tight">
                        <p class="truncate text-sm font-semibold">{{ auth()->user()?->name }}</p>
                        <p class="truncate text-[11px] text-slate-400">{{ auth()->user()?->email }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="Keluar" class="rounded-lg p-2 text-slate-400 transition hover:bg-white/10 hover:text-white">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>
        <div id="admin-overlay" class="fixed inset-0 z-30 hidden bg-slate-950/50 backdrop-blur-sm lg:hidden"></div>

        {{-- Konten --}}
        <div class="min-w-0 flex-1">
            <header class="sticky top-0 z-20 flex h-20 items-center gap-4 border-b border-slate-200 bg-white/90 px-4 backdrop-blur sm:px-6">
                <button id="admin-menu-button" type="button" class="rounded-xl border border-slate-200 p-2.5 text-slate-600 transition hover:bg-slate-100 lg:hidden" aria-label="Buka menu">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
                </button>

                <div class="min-w-0 flex-1">
                    <h1 class="truncate font-display text-lg font-extrabold text-emerald-900">@yield('title', 'Dashboard')</h1>
                    <p class="hidden truncate text-xs text-slate-500 sm:block">@yield('subtitle', 'Sistem Informasi Pendaftaran dan Seleksi Organisasi Siswa')</p>
                </div>

                <a href="{{ route('home') }}" target="_blank" rel="noopener noreferrer"
                   class="hidden items-center gap-2 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 sm:inline-flex">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" x2="21" y1="14" y2="3"/></svg>
                    Situs Publik
                </a>
            </header>

            <main class="p-4 sm:p-6 lg:p-8">
                <x-session-flash />

                @yield('content')
            </main>
        </div>
    </div>

    <script>
        (function () {
            var sidebar = document.getElementById('admin-sidebar');
            var overlay = document.getElementById('admin-overlay');
            var button = document.getElementById('admin-menu-button');

            function openSidebar() {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
            }

            function closeSidebar() {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }

            if (button) {
                button.addEventListener('click', openSidebar);
            }
            if (overlay) {
                overlay.addEventListener('click', closeSidebar);
            }
        })();
    </script>
    @stack('scripts')
</body>
</html>
