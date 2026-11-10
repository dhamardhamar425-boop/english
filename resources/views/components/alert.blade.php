@props(['type' => 'success', 'message' => null])

@php
    $styles = [
        'success' => 'border-emerald-200 bg-emerald-50 text-emerald-800',
        'error' => 'border-red-200 bg-red-50 text-red-800',
        'info' => 'border-blue-200 bg-blue-50 text-blue-800',
        'warning' => 'border-amber-200 bg-amber-50 text-amber-800',
    ];
    $icons = ['success' => 'check', 'error' => 'x', 'info' => 'info', 'warning' => 'hourglass'];
    $style = $styles[$type] ?? $styles['info'];
    $icon = $icons[$type] ?? 'info';
@endphp

@if ($message)
    <div {{ $attributes->merge(['class' => 'flex items-start gap-3 rounded-xl border px-4 py-3 text-sm '.$style]) }} role="alert">
        <x-icon :name="$icon" class="mt-0.5 h-4 w-4 shrink-0" />
        <span>{{ $message }}</span>
    </div>
@endif
