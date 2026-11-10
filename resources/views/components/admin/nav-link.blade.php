@props(['href', 'label', 'icon', 'active' => false])

<a href="{{ $href }}"
   @class([
       'group flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-semibold transition',
       'bg-white/10 text-white ring-1 ring-white/15' => $active,
       'text-slate-300 hover:bg-white/5 hover:text-white' => ! $active,
   ])>
    <x-icon :name="$icon" class="h-[18px] w-[18px] shrink-0" />
    <span>{{ $label }}</span>
    @if ($active)
        <span class="ml-auto h-1.5 w-1.5 rounded-full bg-accent-400"></span>
    @endif
</a>
