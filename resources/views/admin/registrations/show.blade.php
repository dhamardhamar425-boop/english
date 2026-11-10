@extends('layouts.admin')

@section('title', 'Detail Pendaftar')
@section('subtitle', $registration->registration_number)

@section('content')
    <div class="grid gap-6 xl:grid-cols-3">
        {{-- Kartu utama --}}
        <div class="space-y-6 xl:col-span-2">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <p class="font-mono text-sm font-bold text-primary-600">{{ $registration->registration_number }}</p>
                        <h2 class="mt-1 font-display text-2xl font-extrabold text-emerald-900">{{ $registration->full_name }}</h2>
                        <p class="mt-1 text-sm text-slate-500">
                            {{ $registration->class }} · {{ $registration->organization?->name }}
                        </p>
                    </div>
                    <x-status-badge :status="$registration->status" />
                </div>

                <dl class="mt-6 grid gap-x-8 gap-y-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">NIS</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-800">{{ $registration->nis }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">NIK</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-800">{{ $registration->nik }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Jenis Kelamin</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-800">{{ $registration->gender }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">No. WhatsApp</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-800">{{ $registration->phone }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Alamat</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-800">{{ $registration->address }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Terdaftar Pada</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-800">{{ $registration->registered_at?->format('d/m/Y H:i') }}</dd>
                    </div>
                    @if ($registration->final_result)
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Hasil Akhir</dt>
                            <dd class="mt-1">
                                <span @class([
                                    'rounded-full px-2.5 py-1 text-xs font-bold',
                                    'bg-success-100 text-success-700' => $registration->final_result->value === 'diterima',
                                    'bg-danger-100 text-danger-700' => $registration->final_result->value === 'ditolak',
                                ])>
                                    {{ $registration->final_result->label() }}
                                </span>
                            </dd>
                        </div>
                    @endif
                </dl>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="font-display text-lg font-extrabold text-emerald-900">Alasan Memilih Organisasi</h3>
                <p class="mt-3 whitespace-pre-line text-sm leading-relaxed text-slate-600">{{ $registration->reason }}</p>
            </div>

            @if ($registration->admin_notes)
                <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6">
                    <h3 class="font-display text-lg font-extrabold text-amber-800">Catatan Admin</h3>
                    <p class="mt-3 whitespace-pre-line text-sm leading-relaxed text-amber-900">{{ $registration->admin_notes }}</p>
                </div>
            @endif
        </div>

        <div class="space-y-6">
            {{-- Ubah status --}}
            @if (auth()->user()?->can('update', $registration))
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="font-display text-lg font-extrabold text-emerald-900">Ubah Status Seleksi</h3>

                    @if (\App\Models\Announcement::resultsAreVisible() && ! auth()->user()?->isSuperAdmin())
                        <p class="mt-3 rounded-xl bg-amber-50 p-3 text-xs font-semibold text-amber-800 ring-1 ring-amber-200">
                            Hasil seleksi sudah diumumkan. Perubahan hasil akhir hanya dapat dilakukan Super Admin.
                        </p>
                    @endif

                    <form method="POST" action="{{ route('admin.registrations.updateStatus', $registration) }}" class="mt-4 space-y-4">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label for="status" class="mb-1 block text-xs font-semibold text-slate-600">Status Baru</label>
                            <select name="status" id="status" required
                                    class="w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500">
                                @foreach ($statuses as $statusOption)
                                    <option value="{{ $statusOption->value }}" @selected($registration->status === $statusOption)>
                                        {{ $statusOption->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="notes" class="mb-1 block text-xs font-semibold text-slate-600">Catatan (opsional)</label>
                            <textarea name="notes" id="notes" rows="3"
                                      placeholder="{{ $registration->status->value === 'diterima' ? 'Selamat anda telah diterima' : 'Alasan perubahan status…' }}"
                                      class="w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-xs text-danger-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit"
                                class="inline-flex w-full items-center justify-center gap-1.5 rounded-xl bg-emerald-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-800">
                            <x-icon name="check" class="h-4 w-4" />
                            Simpan Status
                        </button>
                    </form>
                </div>
            @endif

            {{-- Riwayat status --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="font-display text-lg font-extrabold text-emerald-900">Riwayat Status</h3>

            @if ($registration->statusHistories->isEmpty())
                <p class="mt-4 text-sm text-slate-500">Belum ada riwayat perubahan status.</p>
            @else
                <ol class="mt-4 space-y-5">
                    @foreach ($registration->statusHistories as $history)
                        <li class="relative border-l-2 border-slate-100 pl-5">
                            <span class="absolute -left-[7px] top-1 h-3 w-3 rounded-full bg-primary-500 ring-4 ring-primary-100"></span>
                            <p class="text-sm font-semibold text-slate-800">
                                {{ $history->old_status?->label() ?? '—' }} → {{ $history->new_status?->label() }}
                            </p>
                            <p class="mt-0.5 text-xs text-slate-500">
                                {{ $history->admin?->name ?? 'Sistem' }} · {{ $history->created_at?->format('d/m/Y H:i') }}
                            </p>
                            @if ($history->notes)
                                <p class="mt-1 rounded-lg bg-slate-50 p-2 text-xs text-slate-600">{{ $history->notes }}</p>
                            @endif
                        </li>
                    @endforeach
                </ol>
            @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        (function () {
            var statusSelect = document.getElementById('status');
            var notesTextarea = document.getElementById('notes');

            if (!statusSelect || !notesTextarea) {
                return;
            }

            function updateNotes() {
                if (statusSelect.value === 'diterima') {
                    notesTextarea.value = '🎉 CONGRATULATIONS! 🎉\n\nSelamat! Kamu resmi menjadi bagian dari keluarga besar [Nama Organisasi]! 🥳\n\nPerjalanan baru, pengalaman baru, dan keseruan baru sudah menanti kamu. We\'re excited to have you on board! 💫\n\nUntuk langkah selanjutnya, silakan bergabung ke grup WhatsApp melalui link berikut:\n\n🔗 [Link Grup WhatsApp]\n\nJangan lupa join, ya! See you inside! 👋🔥\n\nWelcome to the family! ❤️';
                } else if (!notesTextarea.value.trim()) {
                    notesTextarea.value = '';
                }
            }

            statusSelect.addEventListener('change', updateNotes);
            updateNotes();
        })();
    </script>
    @endpush
@endsection
