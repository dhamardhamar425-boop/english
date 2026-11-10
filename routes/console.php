<?php

use App\Console\Commands\PublishDueAnnouncements;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Penjadwalan Tugas
|--------------------------------------------------------------------------
|
| Pengumuman yang jadwalnya tiba ditandai terbit oleh scheduler. Visibilitas
| hasil seleksi ke siswa tetap dihitung di backend berdasarkan waktu, sehingga
| hasil tidak pernah bocor sebelum pengumuman resmi dibuka.
|
*/

Schedule::command(PublishDueAnnouncements::class)
    ->everyMinute()
    ->withoutOverlapping();
