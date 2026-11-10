@php
    $state = \App\Models\Setting::registrationState();
    $start = \App\Models\Setting::registrationStart();
    $end = \App\Models\Setting::registrationEnd();
    $periodeTahun = ($start ?? \Illuminate\Support\Carbon::now(config('app.timezone')))->year;

    $styles = [
        'buka' => ['border-emerald-200 bg-emerald-50 text-emerald-800', 'Pendaftaran Dibuka', 'check'],
        'belum_dimulai' => ['border-amber-200 bg-amber-50 text-amber-800', 'Pendaftaran Belum Dimulai', 'hourglass'],
        'ditutup' => ['border-slate-200 bg-slate-100 text-slate-700', 'Pendaftaran Organisasi Masa Bakti '.$periodeTahun.' Telah Ditutup', 'lock'],
    ];
    [$style, $label, $icon] = $styles[$state];
@endphp

<div {{ $attributes->merge(['class' => 'flex flex-col gap-4 rounded-2xl border p-5 sm:flex-row sm:items-center sm:justify-between ' . $style]) }}>
    <div class="flex items-start gap-3">
        <x-icon :name="$icon" class="mt-0.5 h-5 w-5 shrink-0" />
        <div>
            <p class="text-sm font-semibold">{{ $label }}</p>
            <p class="mt-0.5 text-sm opacity-90">
                @if ($state === 'ditutup')
                    Terima kasih atas partisipasi Anda. Pantau pengumuman terbaru untuk informasi periode berikutnya.
                @elseif ($start && $end)
                    Periode {{ $start->isoFormat('D MMMM YYYY') }} – {{ $end->isoFormat('D MMMM YYYY') }}
                @elseif ($start)
                    Mulai {{ $start->isoFormat('D MMMM YYYY') }}
                @elseif ($end)
                    Berakhir {{ $end->isoFormat('D MMMM YYYY') }}
                @else
                    Silakan tunggu pengumuman jadwal pendaftaran dari sekolah.
                @endif
            </p>
        </div>
    </div>

    @if ($state === 'buka')
        <a href="{{ route('registrations.create') }}"
            class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-emerald-800 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700">
            Daftar Sekarang
            <x-icon name="arrow-right" class="h-4 w-4" />
        </a>
    @endif
</div>
