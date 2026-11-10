@props(['state', 'announcement' => null])

@php
    use App\Enums\AnnouncementState;

    $styles = [
        AnnouncementState::NotScheduled->value => ['bg-slate-100 text-slate-700 ring-slate-200', 'hourglass'],
        AnnouncementState::NotOpen->value => ['bg-amber-100 text-amber-800 ring-amber-200', 'hourglass'],
        AnnouncementState::Ongoing->value => ['bg-blue-100 text-blue-800 ring-blue-200', 'megaphone'],
        AnnouncementState::Opened->value => ['bg-emerald-100 text-emerald-800 ring-emerald-200', 'check'],
    ];
    [$style, $icon] = $styles[$state->value];
@endphp

<div {{ $attributes->merge(['class' => 'rounded-2xl border border-slate-200 bg-white p-5 shadow-sm']) }}>
    <div class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm font-semibold text-emerald-800">Pengumuman Hasil Seleksi</p>
        <span
            class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $style }}">
            <x-icon :name="$icon" class="h-3.5 w-3.5" />
            {{ $state->label() }}
        </span>
    </div>

    @if ($announcement && $announcement->scheduledAt())
        <p class="mt-3 text-sm text-slate-600">
            Dijadwalkan pada
            <span class="font-semibold text-emerald-700">
                {{ $announcement->scheduledAt()->isoFormat('dddd, D MMMM YYYY') }},
                pukul {{ $announcement->scheduledAt()->format('H:i') }} WIB
            </span>
        </p>
        @if ($announcement->description)
            <p class="mt-2 text-sm leading-relaxed text-slate-500">{{ $announcement->description }}</p>
        @endif
    @else
        <p class="mt-3 text-sm text-slate-600">
            Jadwal pengumuman hasil seleksi belum ditetapkan. Pantau halaman ini untuk informasi terbaru.
        </p>
    @endif
</div>
