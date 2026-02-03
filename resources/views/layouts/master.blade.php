<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Plus Admin</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="{{ asset('plus-admin-free/src') }}/assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="{{ asset('plus-admin-free/src') }}/assets/vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="{{ asset('plus-admin-free/src') }}/assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="{{ asset('plus-admin-free/src') }}/assets/vendors/font-awesome/css/font-awesome.min.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="{{ asset('plus-admin-free/src') }}/assets/vendors/jquery-bar-rating/css-stars.css">
    <link rel="stylesheet" href="{{ asset('plus-admin-free/src') }}/assets/vendors/font-awesome/css/font-awesome.min.css">
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <!-- endinject -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="{{ asset('plus-admin-free/src') }}/assets/css/style.css">
    <!-- End layout styles -->
    <link rel="shortcut icon" href="{{ asset('plus-admin-free/src') }}/assets/images/favicon.png" />
</head>

<body>
    <div class="container-scroller">

        @includeIf('layouts.sidebar')


        <!-- partial -->
        <div class="container-fluid page-body-wrapper">

            @includeIf('layouts.navbar')

            <!-- partial -->
            <div class="main-panel">
                @yield('content')
                <!-- content-wrapper ends -->

                @includeIf('layouts.footer')

                <!-- partial -->
            </div>
            <!-- main-panel ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="{{ asset('plus-admin-free/src') }}/assets/vendors/js/vendor.bundle.base.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <script src="{{ asset('plus-admin-free/src') }}/assets/vendors/jquery-bar-rating/jquery.barrating.min.js"></script>
    <script src="{{ asset('plus-admin-free/src') }}/assets/vendors/chart.js/chart.umd.js"></script>
    <script src="{{ asset('plus-admin-free/src') }}/assets/vendors/flot/jquery.flot.js"></script>
    <script src="{{ asset('plus-admin-free/src') }}/assets/vendors/flot/jquery.flot.resize.js"></script>
    <script src="{{ asset('plus-admin-free/src') }}/assets/vendors/flot/jquery.flot.categories.js"></script>
    <script src="{{ asset('plus-admin-free/src') }}/assets/vendors/flot/jquery.flot.fillbetween.js"></script>
    <script src="{{ asset('plus-admin-free/src') }}/assets/vendors/flot/jquery.flot.stack.js"></script>
    <script src="{{ asset('plus-admin-free/src') }}/assets/js/jquery.cookie.js" type="text/javascript"></script>
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="{{ asset('plus-admin-free/src') }}/assets/js/off-canvas.js"></script>
    <script src="{{ asset('plus-admin-free/src') }}/assets/js/misc.js"></script>
    <script src="{{ asset('plus-admin-free/src') }}/assets/js/settings.js"></script>
    <script src="{{ asset('plus-admin-free/src') }}/assets/js/todolist.js"></script>
    <script src="{{ asset('plus-admin-free/src') }}/assets/js/hoverable-collapse.js"></script>
    <!-- endinject -->
    <!-- Custom js for this page -->
    <script src="{{ asset('plus-admin-free/src') }}/assets/js/proBanner.js"></script>
    <script src="{{ asset('plus-admin-free/src') }}/assets/js/dashboard.js"></script>
    <!-- End custom js for this page -->
</body>

</html>
