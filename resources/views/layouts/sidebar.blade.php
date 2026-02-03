<!-- partial:partials/_sidebar.html -->
<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">

        <!-- PROFILE -->
        <li class="nav-item pt-3 pb-1 nav-profile border-bottom ">
            <a href="#" class="nav-link flex-column text-center">
                <div class="nav-profile-image d-flex align-items-center justify-content-center rounded-circle bg-primary text-white mb-2"
                    style="width:45px;height:45px;">
                    <i class="mdi mdi-account"></i>
                </div>

                <span class="fw-semibold">EGI NOVIANI SAPUTRA</span>
                <small class="text-secondary">superadmin</small>
                <br>
            </a>
        </li>

        <!-- DASHBOARD -->
        <li class="nav-item ">
            <a class="nav-link" href="#">
                <i class="mdi mdi-view-dashboard-outline menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>


        <!-- ================= USER ================= -->
        <li class="pt-3 pb-1">
            <span class="nav-item-head">User Management</span>
        </li>

        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#userMenu">
                <i class="mdi mdi-account-group menu-icon"></i>
                <span class="menu-title">Users</span>
                <i class="menu-arrow"></i>
            </a>

            <div class="collapse" id="userMenu">
                <ul class="nav flex-column sub-menu">
                    <li><a class="nav-link" href="#"><i class="mdi mdi-shield-account me-2"></i> Admin</a></li>
                    <li><a class="nav-link" href="#"><i class="mdi mdi-blackboard me-2"></i> Guru</a></li>
                    <li><a class="nav-link" href="#"><i class="mdi mdi-school me-2"></i> Murid</a></li>
                </ul>
            </div>
        </li>


        <!-- ================= AKADEMIK ================= -->
        <li class="pt-3 pb-1">
            <span class="nav-item-head">Akademik</span>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="mdi mdi-calendar-clock menu-icon"></i>
                <span class="menu-title">Tahun Ajar</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="mdi mdi-google-classroom menu-icon"></i>
                <span class="menu-title">Kelas</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="mdi mdi-account-multiple-plus menu-icon"></i>
                <span class="menu-title">Anggota Kelas</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="mdi mdi-book-outline menu-icon"></i>
                <span class="menu-title">Mata Pelajaran</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="mdi mdi-teach menu-icon"></i>
                <span class="menu-title">Guru Mapel</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="mdi mdi-timetable menu-icon"></i>
                <span class="menu-title">Jadwal</span>
            </a>
        </li>


        <!-- ================= ABSENSI ================= -->
        <li class="pt-3 pb-1">
            <span class="nav-item-head">Absensi</span>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="mdi mdi-qrcode-scan menu-icon"></i>
                <span class="menu-title">Sesi Absen (QR)</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="mdi mdi-clipboard-check-outline menu-icon"></i>
                <span class="menu-title">Rekap Absensi</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="mdi mdi-note-text-outline menu-icon"></i>
                <span class="menu-title">Izin Siswa</span>
            </a>
        </li>



    </ul>


</nav>
