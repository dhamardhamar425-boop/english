@props(['detailed' => false])

@php
    $steps = [
        ['icon' => 'flag', 'title' => 'Pilih Organisasi', 'desc' => 'Pelajari kelima organisasi siswa dan pilih satu organisasi yang paling sesuai dengan minat serta bakat Anda.'],
        ['icon' => 'clipboard-list', 'title' => 'Isi Formulir Pendaftaran', 'desc' => 'Lengkapi formulir pendaftaran dengan data yang benar: identitas, kelas, jurusan, nomor WhatsApp, dan alasan memilih organisasi.'],
        ['icon' => 'send', 'title' => 'Dapatkan Link Grup WhatsApp', 'desc' => 'Setelah pendaftaran berhasil, Anda langsung memperoleh nomor pendaftaran dan link grup WhatsApp sesuai organisasi pilihan.'],
        ['icon' => 'clipboard-check', 'title' => 'Ikuti Seleksi Offline', 'desc' => 'Hadir dan ikuti rangkaian seleksi offline di lingkungan sekolah sesuai jadwal dari masing-masing organisasi.'],
        ['icon' => 'hourglass', 'title' => 'Tunggu Pengumuman', 'desc' => 'Hasil akhir seleksi diumumkan serentak untuk seluruh organisasi sesuai jadwal pengumuman resmi.'],
        ['icon' => 'search', 'title' => 'Cek Hasil Seleksi', 'desc' => 'Periksa status akhir pendaftaran melalui halaman Cek Status menggunakan nomor pendaftaran atau NIS Anda.'],
    ];
@endphp

@if ($detailed)
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($steps as $index => $step)
            <div class="reveal rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:border-emerald-300 hover:shadow-md">
                <div class="flex items-center gap-3">
                    <span
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100">
                        <x-icon :name="$step['icon']" class="h-5 w-5" />
                    </span>
                    <span class="text-xs font-semibold uppercase tracking-wider text-blue-500">Tahap
                        {{ $index + 1 }}</span>
                </div>
                <h3 class="mt-4 font-semibold text-emerald-800">{{ $step['title'] }}</h3>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $step['desc'] }}</p>
            </div>
        @endforeach
    </div>
@else
    <ol class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($steps as $index => $step)
            <li class="flex items-start gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <span
                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-emerald-800 text-sm font-semibold text-white">
                    {{ $index + 1 }}
                </span>
                <div>
                    <p class="font-semibold text-emerald-800">{{ $step['title'] }}</p>
                    <p class="mt-1 text-sm leading-relaxed text-slate-600">{{ $step['desc'] }}</p>
                </div>
            </li>
        @endforeach
    </ol>
@endif
