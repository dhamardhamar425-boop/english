@php
    $user = auth()->user();
    $isSuperAdmin = $user?->role === \App\Enums\UserRole::SuperAdmin;
    $currentOrg = $user?->organization_id ? \App\Models\Organization::find($user->organization_id) : null;
@endphp

<nav class="flex-1 space-y-8 overflow-y-auto p-4">
    <div>
        <p class="px-4 pb-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500">Menu Utama</p>
        <div class="space-y-1">
            <x-admin.nav-link :href="route('admin.dashboard')" label="Dashboard" icon="layout-dashboard"
                              :active="request()->routeIs('admin.dashboard')" />
            <x-admin.nav-link :href="route('admin.registrations.index')" label="Data Pendaftar" icon="users"
                              :active="request()->routeIs('admin.registrations.index')" />
            <x-admin.nav-link :href="route('admin.registrations.pending')" label="Perlu Ditinjau" icon="clipboard-list"
                              :active="request()->routeIs('admin.registrations.pending')" />
        </div>
    </div>

    @if ($isSuperAdmin)
        <div>
            <p class="px-4 pb-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500">Pengaturan</p>
            <div class="space-y-1">
                <x-admin.nav-link :href="route('admin.organizations.index')" label="Organisasi" icon="shield"
                                  :active="request()->routeIs('admin.organizations.*')" />
                <x-admin.nav-link :href="route('admin.admins.index')" label="Akun Admin" icon="user-cog"
                                  :active="request()->routeIs('admin.admins.*')" />
                <x-admin.nav-link :href="route('admin.announcements.index')" label="Berita" icon="megaphone"
                                  :active="request()->routeIs('admin.announcements.*')" />
                <x-admin.nav-link :href="route('admin.settings.index')" label="Pengaturan Umum" icon="settings"
                                  :active="request()->routeIs('admin.settings.*')" />
                <x-admin.nav-link :href="route('admin.activities.index')" label="Log Aktivitas" icon="history"
                                  :active="request()->routeIs('admin.activities.*')" />
            </div>
        </div>
    @else
        <div>
            <p class="px-4 pb-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500">Organisasi Saya</p>
            <div class="space-y-1">
                <x-admin.nav-link :href="route('admin.organizations.show', $currentOrg)" :label="$currentOrg?->name ?? 'Profil Organisasi'" icon="shield"
                                  :active="request()->routeIs('admin.organizations.show')" />
            </div>
        </div>
    @endif

    <div>
        <p class="px-4 pb-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500">Informasi</p>
        <div class="space-y-1">
            <x-admin.nav-link :href="route('pages.about')" label="Tentang Sistem" icon="info" />
        </div>
    </div>
</nav>
