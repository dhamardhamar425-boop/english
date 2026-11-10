@props(['organization', 'registrationOpen' => true])

<article
    class="group flex flex-col rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-emerald-300 hover:shadow-lg">
    <div class="flex items-start gap-4">
        @if ($organization->logoUrl())
            <img src="{{ $organization->logoUrl() }}" alt="Logo {{ $organization->name }}" loading="lazy"
                class="h-14 w-14 shrink-0 rounded-xl object-contain ring-1 ring-slate-200" />
        @else
            <span
                class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-emerald-800 text-lg font-bold text-white">
                {{ $organization->initials() }}
            </span>
        @endif
        <div class="min-w-0">
            <h3 class="text-lg font-semibold text-emerald-800 group-hover:text-emerald-600">{{ $organization->name }}</h3>
            <p class="text-xs font-medium uppercase tracking-wider text-blue-500">Organisasi Siswa</p>
        </div>
    </div>

    <p class="mt-4 flex-1 text-sm leading-relaxed text-slate-600 line-clamp-3">
        {{ $organization->description }}
    </p>

    <div class="mt-6 flex gap-2">
        <a href="{{ route('organizations.show', $organization->slug) }}"
            class="flex-1 rounded-xl border border-slate-300 px-4 py-2.5 text-center text-sm font-semibold text-emerald-700 transition hover:border-emerald-500 hover:bg-emerald-50">
            Lihat Detail
        </a>
        @if ($registrationOpen)
            <a href="{{ route('registrations.create', ['organization' => $organization->slug]) }}"
                class="flex-1 rounded-xl bg-emerald-800 px-4 py-2.5 text-center text-sm font-semibold text-white transition hover:bg-emerald-700">
                Daftar
            </a>
        @else
            <span
                class="flex-1 cursor-not-allowed rounded-xl bg-slate-200 px-4 py-2.5 text-center text-sm font-semibold text-slate-500"
                title="Pendaftaran telah ditutup">
                Ditutup
            </span>
        @endif
    </div>
</article>
