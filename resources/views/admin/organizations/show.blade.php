@extends('layouts.admin')

@section('title', 'Detail Organisasi')
@section('subtitle', $organization->name)

@section('content')
    <div class="grid gap-6 xl:grid-cols-3">
        {{-- Kartu profil --}}
        <div class="space-y-6 xl:col-span-2">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center gap-4">
                    @if ($organization->logoUrl())
                        <img src="{{ $organization->logoUrl() }}" alt="Logo {{ $organization->name }}" class="h-16 w-16 rounded-2xl object-contain">
                    @else
                        <span class="grid h-16 w-16 place-items-center rounded-2xl font-display text-2xl font-extrabold text-white" style="background-color: {{ $organization->color }}">
                            {{ $organization->initials() }}
                        </span>
                    @endif
                    <div>
                        <h2 class="font-display text-2xl font-extrabold text-emerald-900">{{ $organization->name }}</h2>
                        <p class="text-sm text-slate-500">{{ $organization->registrations->count() }} pendaftar tercatat</p>
                    </div>
                </div>

                @if ($organization->description)
                    <p class="mt-6 whitespace-pre-line text-sm leading-relaxed text-slate-600">{{ $organization->description }}</p>
                @endif

                <dl class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-xl bg-slate-50 p-4">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Visi</dt>
                        <dd class="mt-1 text-sm text-slate-700">{{ $organization->vision ?? '—' }}</dd>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-4">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Misi</dt>
                        <dd class="mt-1 text-sm text-slate-700">{{ $organization->mission ?? '—' }}</dd>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-4">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Program Kerja</dt>
                        <dd class="mt-1 text-sm text-slate-700">{{ $organization->program_work ?? '—' }}</dd>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-4">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Syarat Pendaftaran</dt>
                        <dd class="mt-1 text-sm text-slate-700">{{ $organization->requirements ?? '—' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="font-display text-lg font-extrabold text-emerald-900">Pendaftar Terbaru</h3>
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="px-4 py-2">No. Pendaftaran</th>
                                <th class="px-4 py-2">Nama</th>
                                <th class="px-4 py-2">Status</th>
                                <th class="px-4 py-2">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($organization->registrations as $reg)
                                <tr class="hover:bg-slate-50/70">
                                    <td class="px-4 py-2 font-mono text-xs font-semibold text-emerald-800">{{ $reg->registration_number }}</td>
                                    <td class="px-4 py-2 font-semibold text-slate-800">{{ $reg->full_name }}</td>
                                    <td class="px-4 py-2"><x-status-badge :status="$reg->status" /></td>
                                    <td class="px-4 py-2 text-xs text-slate-500">{{ $reg->created_at?->format('d/m/Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-slate-500">Belum ada pendaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Form pengaturan --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="font-display text-lg font-extrabold text-emerald-900">Pengaturan Organisasi</h3>
            <p class="mt-1 text-xs text-slate-500">Perbarui tautan kontak & status keaktifan organisasi.</p>

            <form method="POST" action="{{ route('admin.organizations.update', $organization) }}" class="mt-5 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="vision" class="mb-1 block text-xs font-semibold text-slate-600">Visi Organisasi</label>
                    <textarea id="vision" name="vision" rows="4" maxlength="4000" placeholder="Tuliskan visi organisasi…"
                        class="w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500">{{ old('vision', $organization->vision) }}</textarea>
                    @error('vision')
                        <p class="mt-1 text-xs text-danger-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="mission" class="mb-1 block text-xs font-semibold text-slate-600">Misi Organisasi</label>
                    <textarea id="mission" name="mission" rows="4" maxlength="4000" placeholder="Tuliskan misi organisasi…"
                        class="w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500">{{ old('mission', $organization->mission) }}</textarea>
                    @error('mission')
                        <p class="mt-1 text-xs text-danger-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="whatsapp_group_link" class="mb-1 block text-xs font-semibold text-slate-600">Tautan Grup WhatsApp</label>
                    <input type="url" name="whatsapp_group_link" id="whatsapp_group_link" value="{{ old('whatsapp_group_link', $organization->whatsapp_group_link) }}"
                           placeholder="https://chat.whatsapp.com/…"
                           class="w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500">
                    @error('whatsapp_group_link')
                        <p class="mt-1 text-xs text-danger-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="contact_link" class="mb-1 block text-xs font-semibold text-slate-600">Tautan Kontak (Instagram/Web)</label>
                    <input type="url" name="contact_link" id="contact_link" value="{{ old('contact_link', $organization->contact_link) }}"
                           placeholder="https://instagram.com/…"
                           class="w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500">
                    @error('contact_link')
                        <p class="mt-1 text-xs text-danger-600">{{ $message }}</p>
                    @enderror
                </div>

                <label class="flex items-center gap-2 text-sm font-semibold text-slate-700">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $organization->is_active))
                           class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                    Organisasi aktif (menerima pendaftaran)
                </label>

                <button type="submit"
                        class="inline-flex w-full items-center justify-center gap-1.5 rounded-xl bg-emerald-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-800">
                    <x-icon name="check" class="h-4 w-4" />
                    Simpan Pengaturan
                </button>
            </form>
        </div>
    </div>
@endsection
