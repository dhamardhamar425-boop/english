@extends('layouts.admin')

@section('title', 'Kelola Organisasi')
@section('subtitle', 'Daftar seluruh organisasi siswa yang tersedia di sistem')

@section('content')
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @forelse ($organizations as $org)
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        @if ($org->logoUrl())
                            <img src="{{ $org->logoUrl() }}" alt="Logo {{ $org->name }}" class="h-12 w-12 rounded-xl object-contain">
                        @else
                            <span class="grid h-12 w-12 place-items-center rounded-xl font-display text-lg font-extrabold text-white" style="background-color: {{ $org->color }}">
                                {{ $org->initials() }}
                            </span>
                        @endif
                        <div>
                            <h3 class="font-display text-lg font-extrabold text-emerald-900">{{ $org->name }}</h3>
                            <p class="text-xs text-slate-500">{{ $org->registrations_count }} pendaftar</p>
                        </div>
                    </div>
                    <span @class([
                        'rounded-full px-2.5 py-1 text-[11px] font-bold',
                        'bg-success-100 text-success-700' => $org->is_active,
                        'bg-slate-100 text-slate-500' => ! $org->is_active,
                    ])>
                        {{ $org->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>

                <p class="mt-4 line-clamp-3 text-sm text-slate-600">{{ $org->description ?? 'Belum ada deskripsi.' }}</p>

                <div class="mt-4 flex flex-wrap gap-2 text-xs">
                    @if ($org->whatsapp_group_link)
                        <a href="{{ $org->whatsapp_group_link }}" target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center gap-1.5 rounded-lg bg-success-50 px-3 py-1.5 font-semibold text-success-700 ring-1 ring-success-200 transition hover:bg-success-100">
                            <x-icon name="phone" class="h-3.5 w-3.5" />
                            Grup WhatsApp
                        </a>
                    @endif
                    @if ($org->contact_link)
                        <a href="{{ $org->contact_link }}" target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center gap-1.5 rounded-lg bg-primary-50 px-3 py-1.5 font-semibold text-primary-700 ring-1 ring-primary-200 transition hover:bg-primary-100">
                            <x-icon name="external-link" class="h-3.5 w-3.5" />
                            Kontak
                        </a>
                    @endif
                </div>

                <div class="mt-5 flex gap-2">
                    <a href="{{ route('admin.organizations.show', $org) }}"
                       class="inline-flex flex-1 items-center justify-center gap-1.5 rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                        <x-icon name="eye" class="h-4 w-4" />
                        Detail
                    </a>
                </div>
            </div>
        @empty
            <p class="col-span-full rounded-2xl border border-slate-200 bg-white p-12 text-center text-slate-500">
                Belum ada organisasi terdaftar.
            </p>
        @endforelse
    </div>
@endsection
