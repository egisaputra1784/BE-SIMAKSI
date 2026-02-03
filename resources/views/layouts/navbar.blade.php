<!-- partial:partials/_navbar.html -->
<nav class="navbar default-layout-navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
    <div class="navbar-menu-wrapper d-flex align-items-stretch">
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
            <span class="mdi mdi-chevron-double-left"></span>
        </button>
        <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
            <a class="navbar-brand brand-logo-mini" href="index.html"><img
                    src="{{ asset('plus-admin-free/src/') }}/assets/images/logo-mini.svg" alt="logo" /></a>
        </div>
        <ul class="navbar-nav">
            <li class="nav-item d-flex align-items-center">
                <div class="d-flex align-items-center gap-2 fw-semibold text-white">

                    <a href="#" class="text-decoration-none text-white d-flex align-items-center">
                        <i class="mdi mdi-home-outline me-1"></i> Home
                    </a>

                    <i class="mdi mdi-chevron-right text-white-50"></i>

                    <span class="text-white">Dashboard</span>

                </div>
            </li>
        </ul>



        <ul class="navbar-nav navbar-nav-right align-items-center">

            <!-- User Avatar -->
            <li class="nav-item me-2">
                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center"
                    style="width:35px;height:35px;">
                    <i class="mdi mdi-account text-dark"></i>
                </div>
            </li>

            <!-- Logout -->
            <li class="nav-item nav-logout">
                <a class="nav-link text-white" href="#" title="Logout">
                    <i class="mdi mdi-logout"></i>
                </a>
            </li>

        </ul>

        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
            data-toggle="offcanvas">
            <span class="mdi mdi-menu"></span>
        </button>
    </div>
</nav>
