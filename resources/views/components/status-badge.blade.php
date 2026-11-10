@props(['status', 'label' => null])

@php
    $styles = [
        'menunggu' => ['bg-amber-100 text-amber-800 ring-amber-200', 'Menunggu'],
        'seleksi' => ['bg-blue-100 text-blue-800 ring-blue-200', 'Seleksi'],
        'diterima' => ['bg-emerald-100 text-emerald-800 ring-emerald-200', 'Diterima'],
        'ditolak' => ['bg-red-100 text-red-800 ring-red-200', 'Ditolak'],
        'belum_diumumkan' => ['bg-slate-100 text-slate-600 ring-slate-200', 'Belum Diumumkan'],
    ];
    $key = $status instanceof \BackedEnum ? $status->value : (string) $status;
    [$style, $defaultLabel] = $styles[$key] ?? $styles['menunggu'];
@endphp

<span
    {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset ' . $style]) }}>
    <span class="h-1.5 w-1.5 rounded-full bg-current opacity-70"></span>
    {{ $label ?? $defaultLabel }}
</span>
