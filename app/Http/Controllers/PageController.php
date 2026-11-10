<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Organization;
use App\Models\Setting;
use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * Tampilkan halaman alur pendaftaran.
     */
    public function flow(): View
    {
        return view('flow', [
            'registrationState' => Setting::registrationState(),
            'registrationStart' => Setting::registrationStart(),
            'registrationEnd' => Setting::registrationEnd(),
            'resultAnnouncement' => Announcement::activeResultAnnouncement(),
            'announcementState' => Announcement::resultState(),
        ]);
    }

    /**
     * Tampilkan halaman tentang sekolah dan portal pendaftaran.
     */
    public function about(): View
    {
        return view('about', [
            'organizations' => Organization::active()->orderBy('id')->get(),
            'school' => [
                'name' => Setting::cached('school_name', config('app.name')),
                'tagline' => Setting::cached('school_tagline', ''),
                'address' => Setting::cached('school_address', ''),
                'phone' => Setting::cached('school_phone', ''),
                'email' => Setting::cached('school_email', ''),
            ],
        ]);
    }
}
