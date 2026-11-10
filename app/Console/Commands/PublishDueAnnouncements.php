<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Announcement;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

/**
 * Terbitkan pengumuman yang jadwalnya sudah tiba.
 *
 * Visibilitas hasil seleksi ke siswa sebenarnya sudah dihitung di backend
 * berdasarkan waktu (lihat Announcement::resultsAreVisible()). Perintah ini
 * pelengkap: menandai kolom is_published/published_at agar konsisten untuk
 * keperluan pencatatan & laporan.
 */
#[Signature('announcements:publish')]
#[Description('Terbitkan pengumuman yang jadwal pelaksanaannya sudah tiba.')]
final class PublishDueAnnouncements extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $now = Carbon::now(config('app.timezone'));

        $due = Announcement::query()
            ->where('is_published', false)
            ->whereNotNull('announcement_date')
            ->where(function ($query) use ($now): void {
                $query->whereDate('announcement_date', '<', $now->toDateString())
                    ->orWhere(function ($inner) use ($now): void {
                        $inner->whereDate('announcement_date', $now->toDateString())
                            ->whereTime('announcement_time', '<=', $now->format('H:i:s'));
                    });
            })
            ->get();

        if ($due->isEmpty()) {
            $this->info('Tidak ada pengumuman yang perlu diterbitkan.');

            return self::SUCCESS;
        }

        $total = count($due);

        foreach ($due as $announcement) {
            $announcement->update([
                'is_published' => true,
                'published_at' => $now,
            ]);

            $this->line('Diterbitkan: '.$announcement->title.' ('.$announcement->type->label().')');
        }

        $this->info('Selesai. '.$total.' pengumuman diterbitkan.');

        return self::SUCCESS;
    }
}
