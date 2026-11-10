@extends('layouts.admin')

@section('title', 'Pengumuman')
@section('subtitle', 'Kelola pengumuman hasil seleksi & informasi umum')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-blue-200 bg-blue-50 px-5 py-4">
        <div class="flex items-start gap-3">
            <x-icon name="megaphone" class="mt-0.5 h-5 w-5 shrink-0 text-blue-700" />
            <div>
                <p class="text-sm font-bold text-blue-900">Pengumuman hasil seleksi serentak</p>
                <p class="text-xs leading-relaxed text-blue-800">
                    Satu jadwal berlaku untuk semua organisasi. Atur jadwal atau buka pengumuman sekarang dari halaman Pengaturan Umum.
                </p>
            </div>
        </div>
        <a href="{{ route('admin.settings.index') }}#pengumuman-hasil-seleksi"
           class="inline-flex shrink-0 items-center gap-1.5 rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700">
            Atur Pengumuman
            <x-icon name="arrow-right" class="h-4 w-4" />
        </a>
    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm xl:col-span-2">
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-6 py-3">Judul</th>
                            <th class="px-6 py-3">Jenis</th>
                            <th class="px-6 py-3">Jadwal</th>
                            <th class="px-6 py-3">Status Tayang</th>
                            <th class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($announcements as $ann)
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-6 py-3">
                                    <p class="font-semibold text-slate-800">{{ $ann->title }}</p>
                                    <p class="text-xs text-slate-500">{{ \Illuminate\Support\Str::limit($ann->description, 60) }}</p>
                                </td>
                                <td class="px-6 py-3">
                                    <span @class([
                                        'rounded-full px-2.5 py-1 text-[11px] font-bold',
                                        'bg-emerald-100 text-emerald-700' => $ann->type->value === 'result',
                                        'bg-primary-100 text-primary-700' => $ann->type->value === 'info',
                                    ])>
                                        {{ $ann->type->label() }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-xs text-slate-600">
                                    {{ $ann->announcement_date?->format('d/m/Y') }} {{ $ann->announcement_time }}
                                </td>
                                <td class="px-6 py-3">
                                    <span @class([
                                        'rounded-full px-2.5 py-1 text-[11px] font-bold',
                                        'bg-success-100 text-success-700' => $ann->is_published,
                                        'bg-slate-100 text-slate-500' => ! $ann->is_published,
                                    ])>
                                        {{ $ann->is_published ? 'Tayang' : 'Draft' }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <button type="button"
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-primary-600 transition hover:bg-primary-50"
                                            onclick="document.getElementById('edit-ann-{{ $ann->id }}').classList.toggle('hidden')">
                                        <x-icon name="pencil" class="h-3.5 w-3.5" />
                                        Edit
                                    </button>
                                    <form method="POST" action="{{ route('admin.announcements.destroy', $ann) }}" class="inline"
                                          onsubmit="return confirm('Yakin ingin menghapus pengumuman ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1.5 rounded-lg border border-danger-200 px-3 py-1.5 text-xs font-semibold text-danger-600 transition hover:bg-danger-50">
                                            <x-icon name="trash" class="h-3.5 w-3.5" />
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @php
                                $formKey = 'edit-'.$ann->id;
                                $isOldForm = old('form_key') === $formKey;
                            @endphp
                            <tr id="edit-ann-{{ $ann->id }}" class="{{ $isOldForm ? '' : 'hidden' }} bg-slate-50/50">
                                <td colspan="5" class="px-6 py-4">
                                    <form method="POST" action="{{ route('admin.announcements.update', $ann) }}" class="space-y-3">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="form_key" value="{{ $formKey }}">
                                        <input type="text" name="title" value="{{ $isOldForm ? old('title', $ann->title) : $ann->title }}" placeholder="Judul"
                                               class="w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500">
                                        @error('title', 'announcementUpdate') <p class="text-xs text-danger-600">{{ $message }}</p> @enderror
                                        <textarea name="description" rows="2" placeholder="Deskripsi (opsional)"
                                                  class="w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500">{{ $isOldForm ? old('description', $ann->description) : $ann->description }}</textarea>
                                        <div class="grid gap-3 md:grid-cols-4">
                                            <select name="type" class="rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500">
                                                <option value="result" @selected(($isOldForm ? old('type') : $ann->type->value) === 'result')>Hasil Seleksi</option>
                                                <option value="info" @selected(($isOldForm ? old('type') : $ann->type->value) === 'info')>Informasi</option>
                                            </select>
                                            <input type="date" name="announcement_date" value="{{ $isOldForm ? old('announcement_date', $ann->announcement_date?->format('Y-m-d')) : $ann->announcement_date?->format('Y-m-d') }}"
                                                   class="rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500">
                                            <input type="time" name="announcement_time" value="{{ $isOldForm ? old('announcement_time', substr((string) $ann->announcement_time, 0, 5)) : substr((string) $ann->announcement_time, 0, 5) }}"
                                                   class="rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500">
                                            <label class="flex items-center gap-2 text-sm font-semibold text-slate-700">
                                                <input type="hidden" name="is_published" value="0">
                                                <input type="checkbox" name="is_published" value="1" @checked($isOldForm ? old('is_published') : $ann->is_published)
                                                       class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                                                Tayang
                                            </label>
                                        </div>
                                        @error('type', 'announcementUpdate') <p class="text-xs text-danger-600">{{ $message }}</p> @enderror
                                        @error('announcement_date', 'announcementUpdate') <p class="text-xs text-danger-600">{{ $message }}</p> @enderror
                                        @error('announcement_time', 'announcementUpdate') <p class="text-xs text-danger-600">{{ $message }}</p> @enderror
                                        <button type="submit"
                                                class="rounded-xl bg-emerald-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">
                                            Simpan Perubahan
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-500">Belum ada pengumuman.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-100 px-6 py-4">
                {{ $announcements->links() }}
            </div>
        </div>

        {{-- Form tambah pengumuman --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="font-display text-lg font-extrabold text-emerald-900">Buat Pengumuman Baru</h3>
            <p class="mt-1 text-xs text-slate-500">Pengumuman "Hasil Seleksi" mengontrol kapan hasil akhir tampil ke siswa.</p>

            @php $isCreateForm = old('form_key') === 'create'; @endphp
            <form method="POST" action="{{ route('admin.announcements.store') }}" class="mt-5 space-y-4">
                @csrf
                <input type="hidden" name="form_key" value="create">
                <div>
                    <label for="title" class="mb-1 block text-xs font-semibold text-slate-600">Judul</label>
                    <input type="text" name="title" id="title" value="{{ $isCreateForm ? old('title') : '' }}" required
                           class="w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500">
                    @error('title') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="description" class="mb-1 block text-xs font-semibold text-slate-600">Deskripsi</label>
                    <textarea name="description" id="description" rows="3"
                              class="w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500">{{ $isCreateForm ? old('description') : '' }}</textarea>
                </div>

                <div>
                    <label for="type" class="mb-1 block text-xs font-semibold text-slate-600">Jenis</label>
                    <select name="type" id="type" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500">
                        <option value="result" @selected($isCreateForm && old('type') === 'result')>Hasil Seleksi</option>
                        <option value="info" @selected($isCreateForm && old('type') === 'info')>Informasi</option>
                    </select>
                    @error('type') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="announcement_date" class="mb-1 block text-xs font-semibold text-slate-600">Tanggal</label>
                        <input type="date" name="announcement_date" id="announcement_date" value="{{ $isCreateForm ? old('announcement_date') : '' }}" required
                               class="w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500">
                        @error('announcement_date') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="announcement_time" class="mb-1 block text-xs font-semibold text-slate-600">Waktu (WIB)</label>
                        <input type="time" name="announcement_time" id="announcement_time" value="{{ $isCreateForm ? old('announcement_time', '13:00') : '13:00' }}" required
                               class="w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500">
                        @error('announcement_time') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <label class="flex items-center gap-2 text-sm font-semibold text-slate-700">
                    <input type="hidden" name="is_published" value="0">
                    <input type="checkbox" name="is_published" value="1" @checked($isCreateForm && old('is_published'))
                           class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                    Tayangkan di situs publik
                </label>

                <button type="submit"
                        class="inline-flex w-full items-center justify-center gap-1.5 rounded-xl bg-emerald-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-800">
                    <x-icon name="megaphone" class="h-4 w-4" />
                    Buat Pengumuman
                </button>
            </form>
        </div>
    </div>
@endsection
