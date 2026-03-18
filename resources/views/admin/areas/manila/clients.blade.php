<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ULC System</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">
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

        /* Remove highlight only for submenu items */
        .nav-sidebar .nav-treeview .nav-link.active {
            background-color: transparent !important;
            color: inherit !important;
        }

        /* Orange circle indicator for active submenu */
        .nav-sidebar .nav-treeview .nav-link.active .nav-icon.fa-circle {
            color: #FF5F00 !important;
            font-weight: 900;
            /* makes the circle solid */
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        {{-- TOP BAR --}}
        @include('admin.components.topbar')
        {{-- END TOP BAR --}}

        {{-- LEFT SIDEBAR --}}
        @include('admin.components.sidebar')
        {{-- END LEFT SIDEBAR --}}

        {{-- MAIN --}}
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2 align-items-center">
                        <div class="col-sm-6">
                            <h1 class="m-0">Manila Area - [{{ $areas_name }}]</h1>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">

                        <!-- Collector Table -->
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center w-100">

                                        <h3 class="card-title">{{ $areas_name }}</h3>


                                        <button class="btn btn-success btn-sm px-3" data-toggle="modal"
                                            data-target="#addClientModal">
                                            <i class="fas fa-user-plus"></i> Add Client
                                        </button>

                                    </div>
                                </div>

                                <div class="modal fade" id="addClientModal">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">

                                            <!-- HEADER -->
                                            <div class="modal-header">
                                                <h4 class="modal-title">
                                                    <i class="fas fa-user-plus"></i> Add Client
                                                </h4>
                                                <button type="button" class="close" data-dismiss="modal">
                                                    <span>&times;</span>
                                                </button>
                                            </div>

                                            <!-- FORM -->
                                            <form action="{{ route('admin.manila.area.clients.add', $id) }}"
                                                method="POST">
                                                @csrf

                                                <!-- hidden area_id input fixed to current area -->
                                                <input type="hidden" name="area_id" value="{{ $id }}">

                                                <!-- BODY -->
                                                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">

                                                    <div class="row">

                                                        <!-- LEFT SIDE -->
                                                        <div class="col-md-6 border-right">
                                                            <h6 class="text-primary font-weight-bold mb-3">
                                                                Personal Information
                                                            </h6>

                                                            <div class="form-group">
                                                                <label>Full Name *</label>
                                                                <input type="text" name="fullname"
                                                                    class="form-control" required>
                                                            </div>

                                                            <div class="form-group">
                                                                <label>Phone *</label>
                                                                <input type="text" name="phone"
                                                                    class="form-control" pattern="\d{11}" maxlength="11"
                                                                    required>
                                                            </div>

                                                            <div class="form-group">
                                                                <label>Address *</label>
                                                                <input type="text" name="address"
                                                                    class="form-control" required>
                                                            </div>

                                                            <div class="form-group">
                                                                <label>Gender *</label><br>
                                                                <div class="form-check form-check">
                                                                    <input type="radio" name="gender" value="Male"
                                                                        checked> Male
                                                                </div>
                                                                <div class="form-check form-check">
                                                                    <input type="radio" name="gender" value="Female">
                                                                    Female
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- RIGHT SIDE -->
                                                        <div class="col-md-6">
                                                            <h6 class="text-primary font-weight-bold mb-3">
                                                                Loan Information
                                                            </h6>

                                                            <div class="form-group">
                                                                <label>PN Number *</label>
                                                                <input type="text" name="pn_number"
                                                                    class="form-control" required>
                                                            </div>

                                                            <div class="form-group">
                                                                <label>Release Number *</label>
                                                                <input type="text" name="release_number"
                                                                    class="form-control" required>
                                                            </div>

                                                            <div class="form-row">
                                                                <div class="form-group col-md-6">
                                                                    <label>Loan From *</label>
                                                                    <input type="date" name="loan_from"
                                                                        class="form-control" required>
                                                                </div>
                                                                <div class="form-group col-md-6">
                                                                    <label>Loan To *</label>
                                                                    <input type="date" name="loan_to"
                                                                        class="form-control" required>
                                                                </div>
                                                            </div>

                                                            <div class="form-row">
                                                                <div class="form-group col-md-6">
                                                                    <label>Loan Amount *</label>
                                                                    <input type="number" name="loan_amount"
                                                                        class="form-control" required>
                                                                </div>

                                                                <div class="form-group col-md-6">
                                                                    <label>Balance *</label>
                                                                    <input type="number" name="balance"
                                                                        class="form-control" required>
                                                                </div>
                                                            </div>

                                                            <div class="form-group">
                                                                <label>Daily Payment *</label>
                                                                <input type="number" name="daily"
                                                                    class="form-control" required>
                                                            </div>

                                                            <div class="form-group">
                                                                <label>Loan Terms</label>
                                                                <input type="text" name="loan_terms"
                                                                    class="form-control bg-gray text-white"
                                                                    value="100" readonly>
                                                            </div>

                                                        </div>

                                                    </div>

                                                </div>

                                                <!-- FOOTER -->
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default"
                                                        data-dismiss="modal">
                                                        Close
                                                    </button>

                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fas fa-save"></i> Save Client
                                                    </button>
                                                </div>

                                            </form>

                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table id="manilaTable" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>Full Name</th>
                                                <th>Phone</th>
                                                <th>Address</th>
                                                <th>Gender</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($clients as $client)
                                                <tr>
                                                    <td>{{ $client->fullname }}</td>
                                                    <td>{{ $client->phone }}</td>
                                                    <td>{{ $client->address }}</td>
                                                    <td>{{ $client->gender }}</td>
                                                    <td>
                                                        <a href="{{ route('admin.manila.area.client.loans', $client->id) }}"
                                                            class="btn btn-sm btn-info">
                                                            <i class="fas fa-eye"></i> View Loans
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- /.content -->
        </div>
    </div>

    <!-- jQuery -->
    <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('plugins/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('plugins/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
    <script src="{{ asset('dist/js/adminlte.min.js') }}"></script>
    <script src="{{ asset('dist/js/demo.js') }}"></script>
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

            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    notyf.error("{{ $error }}");
                @endforeach
            @endif

        });
    </script>
    <script>
        $(function() {

            $('#manilaTable').DataTable({
                "paging": true,
                "searching": true,
                "ordering": true,
                "responsive": true
            });

        });
    </script>
</body>

</html>
