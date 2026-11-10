<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    /**
     * Tampilkan halaman pengumuman: jadwal hasil seleksi dan berita sekolah.
     */
    public function index(): View
    {
        return view('announcements.index', [
            'announcements' => Announcement::publishedInfo()->paginate(6),
            'resultAnnouncement' => Announcement::activeResultAnnouncement(),
            'announcementState' => Announcement::resultState(),
        ]);
    }
}
