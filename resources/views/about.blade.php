@extends('layouts.public')

@section('title', 'Tentang')

@section('content')
    <section class="border-b border-slate-200 bg-emerald-50">
        <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 md:py-16 lg:px-8">
            <x-section-heading title="Tentang Kami"
                subtitle="Mengenal sekolah dan portal pendaftaran organisasi siswa." />
        </div>
    </section>

    <section class="mx-auto grid max-w-7xl gap-8 px-4 py-14 sm:px-6 md:py-16 lg:grid-cols-2 lg:px-8">
        <div class="reveal rounded-2xl border border-slate-200 bg-white p-6 shadow-sm md:p-8">
            <div class="flex items-center gap-3">
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-800 text-white">
                    <x-icon name="graduation-cap" />
                </span>
                <div>
                    <h2 class="text-lg font-semibold text-emerald-800">{{ $school['name'] }}</h2>
                    <p class="text-sm text-slate-500">{{ $school['tagline'] }}</p>
                </div>
            </div>

            <p class="mt-5 leading-relaxed text-slate-600">
                Website ini merupakan portal resmi pendaftaran anggota baru organisasi siswa. Melalui portal ini,
                siswa dapat mengenal organisasi, mendaftar secara online, memperoleh informasi grup komunikasi,
                memantau proses seleksi, hingga mengetahui hasil pengumuman secara terjadwal.
            </p>
            <p class="mt-4 leading-relaxed text-slate-600">
                Seleksi anggota baru dilaksanakan secara offline oleh masing-masing organisasi. Website ini hanya
                mengelola administrasi pendaftaran, status seleksi, dan publikasi hasil pengumuman.
            </p>

            <ul class="mt-6 space-y-3 text-sm">
                <li class="flex items-start gap-3">
                    <x-icon name="map-pin" class="mt-0.5 h-4 w-4 shrink-0 text-blue-500" />
                    {{ $school['address'] ?: 'Alamat sekolah belum tersedia.' }}
                </li>
                <li class="flex items-center gap-3">
                    <x-icon name="phone" class="h-4 w-4 shrink-0 text-blue-500" />
                    {{ $school['phone'] ?: 'Telepon belum tersedia.' }}
                </li>
                <li class="flex items-center gap-3">
                    <x-icon name="mail" class="h-4 w-4 shrink-0 text-blue-500" />
                    {{ $school['email'] ?: 'Email belum tersedia.' }}
                </li>
            </ul>
        </div>

        <div class="reveal">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm md:p-8">
                <h2 class="text-lg font-semibold text-emerald-800">Organisasi di Sekolah Kami</h2>
                <p class="mt-2 text-sm text-slate-600">
                    Terdapat {{ $organizations->count() }} organisasi yang dapat Anda pilih. Setiap siswa hanya boleh
                    memilih satu organisasi.
                </p>
                <ul class="mt-5 space-y-3">
                    @foreach ($organizations as $organization)
                        <li>
                            <a href="{{ route('organizations.show', $organization->slug) }}"
                                class="flex items-center gap-4 rounded-xl border border-slate-200 p-4 transition hover:border-emerald-400 hover:bg-emerald-50">
                                @if ($organization->logoUrl())
                                    <img src="{{ $organization->logoUrl() }}" alt="Logo {{ $organization->name }}"
                                        class="h-10 w-10 rounded-lg object-contain ring-1 ring-slate-200" />
                                @else
                                    <span
                                        class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-800 text-xs font-bold text-white">
                                        {{ $organization->initials() }}
                                    </span>
                                @endif
                                <span class="min-w-0 flex-1">
                                    <span class="block font-semibold text-emerald-800">{{ $organization->name }}</span>
                                    <span class="block truncate text-xs text-slate-500">
                                        {{ \Illuminate\Support\Str::limit($organization->description, 80) }}</span>
                                </span>
                                <x-icon name="chevron-right" class="h-4 w-4 shrink-0 text-slate-400" />
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>
@endsection
