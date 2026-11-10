<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Organization;
use App\Models\Setting;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Tampilkan halaman beranda website publik.
     */
    public function __invoke(): View
    {
        return view('home', [
            'organizations' => Organization::active()->withCount('registrations')->orderBy('id')->get(),
            'announcements' => Announcement::publishedInfo()->limit(3)->get(),
            'resultAnnouncement' => Announcement::activeResultAnnouncement(),
            'announcementState' => Announcement::resultState(),
            'registrationState' => Setting::registrationState(),
            'registrationOpen' => Setting::isRegistrationOpen(),
            'registrationStart' => Setting::registrationStart(),
            'registrationEnd' => Setting::registrationEnd(),
        ]);
    }
}
