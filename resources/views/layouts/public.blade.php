<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Beranda') · {{ \App\Models\Setting::cached('school_name', config('app.name')) }}</title>
    <meta name="description"
        content="Portal resmi pendaftaran dan seleksi anggota baru organisasiswa {{ \App\Models\Setting::cached('school_name', config('app.name')) }}.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-white font-sans text-slate-800 antialiased">
    @unless (View::hasSection('fullscreen'))
        <x-public.navbar />

        @include('components.session-flash')
    @endunless

    <main>
        @yield('content')
    </main>

    @unless (View::hasSection('fullscreen'))
        <x-public.footer />
    @endunless

    @stack('scripts')
</body>

</html>
