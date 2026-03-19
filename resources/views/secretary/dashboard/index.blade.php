<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ULC System</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet"
        href="{{ asset('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/jqvmap/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/summernote/summernote-bs4.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <style>
        .sidebar {
            position: relative;
            height: 100%;
        }

        .logout-bottom {
            position: absolute;
            bottom: 10px;
            width: 100%;
        }

        .nav-sidebar .nav-link.active {
            background-color: #FF5F00 !important;
            color: #fff !important;
        }

        .nav-sidebar .nav-link.active i {
            color: #fff !important;
        }

        .main-header .nav-link {
            color: #ffffff !important;
        }

        .main-header .nav-link i {
            color: #ffffff !important;
        }

        .main-header .navbar-nav .nav-link:hover {
            color: #ffffff !important;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        {{-- TOP BAR --}}
        @include('secretary.components.topbar')
        {{-- END TOP BAR --}}

        {{-- LEFT SIDEBAR --}}
        @include('secretary.components.sidebar')
        {{-- END LEFT SIDEBAR --}}

        {{-- MAIN --}}
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2 align-items-center">
                        <div class="col-sm-6">
                            <h1 class="m-0">Secretary Dashboard</h1>
                        </div>
                        <div class="col-sm-6 text-sm-right">
                            <h5 class="m-0" id="manila-time"></h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2 align-items-center">
                        <div class="col-sm-6">
                            <h1 class="m-0">Manila Area</h1>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <!-- Loans box -->
                        <div class="col-lg-4 col-6">
                            <div class="small-box bg-light" style="border-top: 4px solid #FF5F00;">
                                <div class="inner" style="color: #FF5F00;">
                                    <h3>150</h3>
                                    <p>Loans</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-money-bill-wave" style="color: #FF5F00;"></i>
                                </div>
                                <a href="#" class="small-box-footer">
                                    More info <i class="fas fa-arrow-circle-right" style="color: #FF5F00;"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Clients box -->
                        <div class="col-lg-4 col-6">
                            <div class="small-box bg-light" style="border-top: 4px solid #FF5F00;">
                                <div class="inner" style="color: #FF5F00;">
                                    <h3>53<sup style="font-size: 20px">%</sup></h3>
                                    <p>Users</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-users" style="color: #FF5F00;"></i>
                                </div>
                                <a href="#" class="small-box-footer">
                                    More info <i class="fas fa-arrow-circle-right" style="color: #FF5F00;"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Collected box -->
                        <div class="col-lg-4 col-6">
                            <div class="small-box bg-light" style="border-top: 4px solid #FF5F00;">
                                <div class="inner" style="color: #FF5F00;">
                                    <h3>44</h3>
                                    <p>Collected</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-hand-holding-usd" style="color: #FF5F00;"></i>
                                </div>
                                <a href="#" class="small-box-footer">
                                    More info <i class="fas fa-arrow-circle-right" style="color: #FF5F00;"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    <script>
        $.widget.bridge('uibutton', $.ui.button)
    </script>
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('plugins/chart.js/Chart.min.js') }}"></script>
    <script src="{{ asset('plugins/sparklines/sparkline.js') }}"></script>
    <script src="{{ asset('plugins/jqvmap/jquery.vmap.min.js') }}"></script>
    <script src="{{ asset('plugins/jqvmap/maps/jquery.vmap.usa.js') }}"></script>
    <script src="{{ asset('plugins/jquery-knob/jquery.knob.min.js') }}"></script>
    <script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
    <script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}"></script>
    <script src="{{ asset('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('dist/js/adminlte.js') }}"></script>
    <script src="{{ asset('dist/js/demo.js') }}"></script>
    <script src="{{ asset('dist/js/pages/dashboard.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const notyf = new Notyf({
                duration: 5000,
                position: {
                    x: 'right',
                    y: 'top'
                }
            });

            @if (session('success'))
                notyf.success("{{ session('success') }}");
            @endif

            @if (session('error'))
                notyf.error("{{ session('error') }}");
            @endif
        });
    </script>
    <script>
        function updateManilaTime() {
            const options = {
                timeZone: 'Asia/Manila',
                year: 'numeric',
                month: 'long',
                day: '2-digit',
                hour: 'numeric',
                minute: '2-digit',
                second: '2-digit',
                hour12: true
            };

            const now = new Date();
            const formatted = new Intl.DateTimeFormat('en-US', options).format(now);

            document.getElementById('manila-time').innerText = formatted;
        }

        updateManilaTime();
        setInterval(updateManilaTime, 1000);
    </script>
</body>

</html>
