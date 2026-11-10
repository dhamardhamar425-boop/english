@php
    $schoolName = \App\Models\Setting::cached('school_name', config('app.name'));
    $schoolTagline = \App\Models\Setting::cached('school_tagline', '');
    $schoolLogo = 'https://www.smamaarifnu1ajibarang.sch.id/upload/picture/2738470645350500logopng-600x602(1).png';
    $registrationOpen = \App\Models\Setting::isRegistrationOpen();

    $navItems = [
        ['label' => 'Beranda', 'route' => 'home'],
        ['label' => 'Organisasi', 'route' => 'organizations.index'],
        ['label' => 'Pengumuman', 'route' => 'announcements.index'],
        ['label' => 'Cek Status', 'route' => 'status.check'],
        ['label' => 'Tentang', 'route' => 'pages.about'],
    ];
@endphp

<header class="sticky top-0 z-40 border-b border-slate-200/70 bg-white/90 backdrop-blur">
    <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
        <a href="{{ route('home') }}" class="flex min-w-0 items-center gap-3">
            <img src="{{ $schoolLogo }}" alt="Logo {{ $schoolName }}"
                class="h-10 w-auto shrink-0 object-contain" />
            <span class="min-w-0">
                <span class="block truncate text-sm font-semibold text-emerald-800">{{ $schoolName }}</span>
                <span class="block truncate text-[11px] text-slate-500">Portal Organisasi Siswa</span>
            </span>
        </a>

        <nav class="hidden items-center gap-1 lg:flex">
            @foreach ($navItems as $item)
                <a href="{{ route($item['route']) }}"
                    class="rounded-lg px-3 py-2 text-sm font-medium transition {{ request()->routeIs($item['route']) ? 'bg-emerald-50 text-emerald-800' : 'text-slate-600 hover:bg-slate-50 hover:text-emerald-700' }}">
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="hidden items-center gap-2 lg:flex">
            @if ($registrationOpen)
                <a href="{{ route('registrations.create') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-emerald-800 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/40">
                    Daftar Sekarang
                    <x-icon name="arrow-right" class="h-4 w-4" />
                </a>
            @else
                <span
                    class="inline-flex cursor-not-allowed items-center gap-2 rounded-xl bg-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-500"
                    title="Pendaftaran telah ditutup">
                    <x-icon name="lock" class="h-4 w-4" />
                    Pendaftaran Ditutup
                </span>
            @endif
        </div>

        <button type="button" data-nav-toggle
            class="rounded-lg p-2 text-emerald-800 transition hover:bg-slate-100 lg:hidden" aria-label="Buka menu navigasi">
            <x-icon name="menu" />
        </button>
    </div>

    {{-- Menu mobile --}}
    <div data-nav-menu class="hidden border-t border-slate-200 bg-white lg:hidden">
        <div class="space-y-1 px-4 py-3">
            @foreach ($navItems as $item)
                <a href="{{ route($item['route']) }}"
                    class="block rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs($item['route']) ? 'bg-emerald-50 text-emerald-800' : 'text-slate-600 hover:bg-slate-50' }}">
                    {{ $item['label'] }}
                </a>
            @endforeach
            @if ($registrationOpen)
                <a href="{{ route('registrations.create') }}"
                    class="mt-2 flex items-center justify-center gap-2 rounded-xl bg-emerald-800 px-4 py-2.5 text-sm font-semibold text-white">
                    Daftar Sekarang
                    <x-icon name="arrow-right" class="h-4 w-4" />
                </a>
            @else
                <span
                    class="mt-2 flex cursor-not-allowed items-center justify-center gap-2 rounded-xl bg-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-500">
                    <x-icon name="lock" class="h-4 w-4" />
                    Pendaftaran Ditutup
                </span>
            @endif
        </div>
    </div>
</header>
