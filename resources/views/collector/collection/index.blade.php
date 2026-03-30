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


        .card-primary.card-outline {
            border-top: 3px solid #FF5F00;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        {{-- TOP BAR --}}
        @include('collector.components.topbar')
        {{-- END TOP BAR --}}

        {{-- LEFT SIDEBAR --}}
        @include('collector.components.sidebar')
        {{-- END LEFT SIDEBAR --}}

        {{-- MAIN --}}
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2 align-items-center">
                        <div class="col-sm-6">
                            <h1 class="m-0">
                                {{ $area->location_name ?? 'N/A' }} - [{{ $area->areas_name ?? 'N/A' }}]
                            </h1>
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
                            <div class="card card-primary card-outline">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center w-100">

                                        <h3 class="card-title">{{ $area->location_name ?? 'N/A' }} -
                                            [{{ $area->areas_name ?? 'N/A' }}]</h3>


                                        <button class="btn btn-success btn-sm px-3" data-toggle="modal"
                                            data-target="#selectDate">
                                            <i class="fas fa-calendar"></i>&nbsp; Select Date
                                        </button>

                                    </div>
                                </div>

                                {{-- add collectibles modal --}}
                                @include('collector.collection.modals.select_date')
                                <div class="card-body">
                                    <div class="row g-3 mb-4">
                                        <div class="col-12 mb-3">
                                            <div
                                                class="d-flex justify-content-between align-items-center bg-white p-3 rounded shadow-sm border-start border-4 border-primary">
                                                <div><span class="badge bg-light text-dark border">REF:
                                                        {{ $refNo }}</span>
                                                    <h5 class="mb-0 text-dark">
                                                        <i class="far fa-calendar-alt me-2 text-primary"></i>
                                                        {{ \Carbon\Carbon::parse($selectedDate)->format('F d, Y') }}
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="card border-0 shadow-sm h-100">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center">
                                                        <div
                                                            class="flex-shrink-0 bg-soft-info p-3 rounded-circle text-info">
                                                            <i class="fas fa-users fa-lg"></i>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <p class="text-muted mb-0 small uppercase">Total Clients</p>
                                                            <h4 class="mb-0 fw-bold">{{ $totalClients }}</h4>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="card border-0 shadow-sm h-100">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center">
                                                        <div
                                                            class="flex-shrink-0 bg-soft-success p-3 rounded-circle text-success">
                                                            <i class="fas fa-hand-holding-usd fa-lg"></i>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <p class="text-muted mb-0 small uppercase">Total Collections
                                                            </p>
                                                            <h4 class="mb-0 fw-bold text-success">
                                                                ₱{{ number_format($totalCollections, 2) }}</h4>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="card border-0 shadow-sm h-100">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center">
                                                        <div
                                                            class="flex-shrink-0 bg-soft-warning p-3 rounded-circle text-warning">
                                                            <i class="fas fa-file-invoice-dollar fa-lg"></i>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <p class="text-muted mb-0 small uppercase">Daily
                                                                Collectibles</p>
                                                            <h4 class="mb-0 fw-bold text-dark">
                                                                ₱{{ number_format($totalDailyCollectibles, 2) }}</h4>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @csrf
                                    <table id="manilaTable" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>Client Name</th>
                                                <th>Due Date</th>
                                                <th>Balance</th>
                                                <th>Daily</th>
                                                <th>Collection</th>
                                                <th>Type</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($clients as $client)
                                                <tr class="{{ $client->is_overdue ? 'table-danger' : '' }}">
                                                    <td>{{ $client->fullname }}</td>

                                                    {{-- Due Date --}}
                                                    <td>
                                                        @if ($client->payment)
                                                            {{ \Carbon\Carbon::parse($client->payment->due_date)->format('Y-m-d') }}
                                                        @else
                                                            <input type="date" value="{{ $selectedDate }}" readonly
                                                                class="form-control">
                                                        @endif
                                                    </td>

                                                    {{-- Balance --}}
                                                    <td>
                                                        ₱{{ number_format($client->loan->balance ?? 0, 2) }}
                                                    </td>

                                                    {{-- Daily --}}
                                                    <td>
                                                        @if ($client->payment)
                                                            ₱{{ number_format($client->payment->daily, 2) }}
                                                        @else
                                                            ₱{{ number_format($client->loan->daily ?? 0, 2) }}
                                                        @endif
                                                    </td>

                                                    {{-- Collection --}}
                                                    @if ($client->payment && !(is_null($client->payment->collection) && is_null($client->payment->type)))
                                                        <td>
                                                            @if (is_null($client->payment->collection))
                                                                -
                                                            @else
                                                                ₱{{ number_format($client->payment->collection, 2) }}
                                                            @endif
                                                        </td>

                                                        <td>
                                                            {{ $client->payment->type ?? '-' }}
                                                        </td>

                                                        {{-- STATUS --}}
                                                        <td>
                                                            @if ($client->isPaid)
                                                                <span class="badge badge-primary">Paid Loan</span>
                                                            @elseif ($client->payment->type === 'NO PAYMENT' || $client->payment->collection == 0)
                                                                <span class="badge badge-danger">NO PAYMENT</span>
                                                            @elseif ($client->payment->is_collected == 1)
                                                                <span class="badge badge-success">Collected</span>
                                                            @else
                                                                <span class="badge badge-info">To Collect</span>
                                                            @endif
                                                        </td>
                                                    @else
                                                        <td>
                                                            <form action="{{ route('collector.collections.store') }}"
                                                                method="POST">
                                                                @csrf
                                                                <input type="number" step="0.01" name="collection"
                                                                    class="form-control" min="1"
                                                                    placeholder="Enter amount if any">

                                                                <!-- Helper text -->
                                                                <small class="form-text" style="color: brown">Leave
                                                                    blank if no
                                                                    payment.</small>

                                                        </td>

                                                        <td>
                                                            <select name="type" class="form-control" required>
                                                                <option value="">Select</option>
                                                                <option value="CASH">CASH</option>
                                                                <option value="GCASH">GCASH</option>
                                                                <option value="CHEQUE">CHEQUE</option>
                                                            </select>
                                                        </td>

                                                        <td>
                                                            <button type="submit" class="btn btn-success btn-sm">
                                                                Save collection
                                                            </button>

                                                            {{-- Hidden Fields --}}
                                                            <input type="hidden" name="client_id"
                                                                value="{{ $client->id }}">
                                                            <input type="hidden" name="loan_id"
                                                                value="{{ $client->loan->id ?? '' }}">
                                                            <input type="hidden" name="area_id"
                                                                value="{{ $client->area_id }}">
                                                            <input type="hidden" name="reference_no"
                                                                value="{{ $refNo }}">
                                                            <input type="hidden" name="due_date"
                                                                value="{{ $selectedDate }}">
                                                            <input type="hidden" name="old_balance"
                                                                value="{{ $client->loan->balance ?? 0 }}">
                                                            <input type="hidden" name="daily"
                                                                value="{{ $client->loan->daily ?? 0 }}">
                                                            </form>
                                                        </td>
                                                    @endif
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
