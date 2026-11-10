@props(['title', 'subtitle' => null, 'align' => 'center', 'color' => null])

<div class="{{ $align === 'left' ? 'max-w-2xl' : 'mx-auto max-w-2xl text-center' }}">
    <h2 class="text-2xl font-semibold tracking-tight text-emerald-800 md:text-3xl" {{ $color ? 'style="color: '.e($color).'"' : '' }}>{{ $title }}</h2>
    @if ($subtitle)
        <p class="mt-3 text-sm leading-relaxed text-slate-600 md:text-base">{{ $subtitle }}</p>
    @endif
</div>
