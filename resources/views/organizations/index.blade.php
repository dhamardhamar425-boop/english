@extends('layouts.public')

@section('title', 'Organisasi')

@section('content')
    <section class="border-b border-slate-200 bg-emerald-50">
        <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 md:py-16 lg:px-8">
            <x-section-heading title="Organisasi Siswa"
                subtitle="Pilih satu dari lima organisasi di bawah ini. Pelajari detailnya sebelum mendaftar." />
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 md:py-16 lg:px-8">
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($organizations as $organization)
                <div class="reveal">
                    <x-org-card :organization="$organization" :registration-open="$registrationOpen" />
                </div>
            @endforeach
        </div>

        @if ($organizations->isEmpty())
            <div class="rounded-2xl border border-dashed border-slate-300 p-10 text-center text-sm text-slate-500">
                Belum ada organisasi yang tersedia.
            </div>
        @endif

        <div class="mt-10 rounded-2xl border border-blue-100 bg-blue-50 p-6 text-sm leading-relaxed text-blue-900">
            <div class="flex items-start gap-3">
                <x-icon name="info" class="mt-0.5 h-5 w-5 shrink-0" />
                <p>
                    <span class="font-semibold">Ingat:</span> setiap siswa hanya boleh memilih satu organisasi dan
                    hanya dapat memiliki satu pendaftaran. Pastikan pilihan Anda sudah tepat sebelum mengirim formulir.
                </p>
            </div>
        </div>
    </section>
@endsection
