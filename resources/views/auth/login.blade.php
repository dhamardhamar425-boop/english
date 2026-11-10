<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login Admin · {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="flex min-h-screen items-center justify-center bg-emerald-900 p-4 font-sans text-slate-800 antialiased">
    <div class="pointer-events-none absolute inset-0 overflow-hidden">
        <div class="absolute -left-32 -top-32 h-96 w-96 rounded-full bg-blue-500/10 blur-3xl"></div>
        <div class="absolute -bottom-40 -right-24 h-[28rem] w-[28rem] rounded-full bg-emerald-500/20 blur-3xl"></div>
    </div>

    <div class="relative w-full max-w-md">
        <div class="mb-6 text-center text-white">
            <div
                class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-white/10 ring-1 ring-white/20">
                <x-icon name="graduation-cap" class="h-7 w-7 text-blue-200" />
            </div>
            <h1 class="text-xl font-semibold">{{ \App\Models\Setting::cached('school_name', config('app.name')) }}</h1>
            <p class="mt-1 text-sm text-emerald-200">Portal Informasi Pendaftaran Organisasi Siswa</p>
        </div>

        <div class="rounded-2xl bg-white p-6 shadow-xl md:p-8">
            <h2 class="text-lg font-semibold text-emerald-800">Masuk ke Dashboard Admin</h2>
            <p class="mt-1 text-sm text-slate-500">Gunakan akun admin yang diberikan oleh Super Admin.</p>

            <form method="POST" action="{{ route('login.attempt') }}" class="mt-6 space-y-4">
                @csrf

                <div>
                    <label for="email" class="mb-1.5 block text-sm font-medium text-slate-700">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                        autocomplete="username" placeholder="admin@smk.sch.id"
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none transition placeholder:text-slate-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20" />
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="mb-1.5 block text-sm font-medium text-slate-700">Password</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                        placeholder="••••••••"
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none transition placeholder:text-slate-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20" />
                    @error('password')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" name="remember" value="1"
                        class="h-4 w-4 rounded border-slate-300 text-emerald-700 focus:ring-emerald-500/20" />
                    Ingat saya
                </label>

                <button type="submit"
                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-800 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/40">
                    <x-icon name="log-out" class="h-4 w-4 rotate-180" />
                    Masuk
                </button>
            </form>
        </div>

        <p class="mt-6 text-center text-xs text-emerald-300">
            Belum memiliki akun? Hubungi Super Admin sekolah untuk pembuatan akun.
        </p>
    </div>
</body>

</html>
