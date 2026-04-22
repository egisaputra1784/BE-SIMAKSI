<nav class="simaksi-sidebar" id="simaksiSidebar">

    {{-- BRAND --}}
    <div class="sidebar-brand">
        <div class="brand-icon">
            <i class="mdi mdi-school"></i>
        </div>
        <div class="brand-text">
            <div class="brand-name">SIMAKSI</div>
            <div class="brand-sub">Sistem Akademik</div>
        </div>
    </div>

    {{-- PROFILE --}}
    <div class="sidebar-profile">
        <div class="avatar">
            <i class="mdi mdi-account"></i>
        </div>
        <div>
            <div class="profile-name">{{ Auth::user()->name }}</div>
            <div class="profile-role">{{ ucfirst(Auth::user()->role) }}</div>
        </div>
    </div>

    {{-- NAV --}}
    <div class="sidebar-nav">

        {{-- Dashboard --}}
        <a href="/">
            <i class="mdi mdi-view-dashboard-outline"></i>
            Dashboard
        </a>

        {{-- USER MANAGEMENT: superadmin only --}}
        @if(Auth::user()->role === 'superadmin')
            <div class="nav-section-label">User Management</div>

            <a class="collapse-link" data-bs-toggle="collapse" data-bs-target="#menuUser" aria-expanded="false">
                <span class="link-left">
                    <i class="mdi mdi-account-group-outline"></i>
                    Users
                </span>
                <i class="mdi mdi-chevron-right arrow"></i>
            </a>
            <div class="collapse sub-nav" id="menuUser">
                <a href="/admin"><i class="mdi mdi-shield-account-outline me-1"></i>Admin</a>
                <a href="/guru"><i class="mdi mdi-account-tie me-1"></i>Guru</a>
                <a href="/murid"><i class="mdi mdi-school-outline me-1"></i>Murid</a>
            </div>
        @endif

        {{-- AKADEMIK --}}
        <div class="nav-section-label">Akademik</div>

        <a href="/tahun-ajar">
            <i class="mdi mdi-calendar-clock-outline"></i>
            Tahun Ajar
        </a>

        <a href="/kelas">
            <i class="mdi mdi-google-classroom"></i>
            Kelas
        </a>

        <a href="/anggota-kelas">
            <i class="mdi mdi-account-multiple-plus-outline"></i>
            Anggota Kelas
        </a>

        <a href="/mapel">
            <i class="mdi mdi-book-open-page-variant-outline"></i>
            Mata Pelajaran
        </a>

        <a href="/guru-mapel">
            <i class="mdi mdi-book-education-outline"></i>
            Guru Mapel
        </a>

        <a href="/jadwal">
            <i class="mdi mdi-timetable"></i>
            Jadwal
        </a>

        {{-- LAPORAN --}}
        <div class="nav-section-label">Laporan</div>

        <a href="/rekap-absen">
            <i class="mdi mdi-clipboard-text-clock-outline"></i>
            Rekap Absensi
        </a>

        {{-- PENGATURAN --}}
        <div class="nav-section-label">Pengaturan</div>

        <a href="/point-rules">
            <i class="mdi mdi-star-circle-outline"></i>
            Peraturan Siswa
        </a>

        <a href="/flexibility-items">
            <i class="mdi mdi-shopping-outline"></i>
            Market
        </a>

        <a href="/assessment-categories">
            <i class="mdi mdi-clipboard-list-outline"></i>
            Kategori Penilaian
        </a>

        {{-- LOGOUT --}}
        <div class="nav-section-label">Akun</div>
        <form method="POST" action="/logout" id="logoutForm">
            @csrf
            <a href="#" onclick="document.getElementById('logoutForm').submit(); return false;"
               style="color:#ef4444;">
                <i class="mdi mdi-logout" style="color:#ef4444;"></i>
                Keluar
            </a>
        </form>

    </div>
</nav>
