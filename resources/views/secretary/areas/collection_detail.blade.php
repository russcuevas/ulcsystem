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

        /* Breadcrumb Link Color (Not Active) */
        .breadcrumb-item a {
            color: #FF5F00 !important;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        /* Breadcrumb Hover State */
        .breadcrumb-item a:hover {
            color: #cc4c00 !important;
        }

        /* Breadcrumb Separator (The "/" icon) */
        .breadcrumb-item+.breadcrumb-item::before {
            color: #ffa366;
            /* Muted orange for the slash */
        }

        /* Breadcrumb Active State (The current page) */
        .breadcrumb-item.active {
            color: #6c757d;
            /* Keep the active one grey so users know where they are */
        }

        .action-buttons button {
            margin-right: 10px;
        }

        .action-buttons button:last-child {
            margin-right: 0;
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
                            <h1 class="m-0">{{ $location_name }} - [{{ $areas_name }}]</h1>

                            <ol class="breadcrumb mt-2">
                                <li class="breadcrumb-item"><a
                                        href="{{ route('secretary.dashboard.page') }}">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item"><a href="{{ route('secretary.areas.page') }}">Areas</a></li>
                                <li class="breadcrumb-item"><a
                                        href="{{ route('secretary.areas.collections.references', ['areaId' => $areaId]) }}">{{ $areas_name }}</a>
                                </li>
                                <li class="breadcrumb-item active">Reference: {{ $refNo }}</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">

                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Collection</h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-3 mb-4">
                                <div class="col-12 mb-3">
                                    <form id="collectionForm"
                                        action="{{ route('secretary.collections.collect', ['refNo' => $refNo]) }}"
                                        method="POST">
                                        @csrf
                                        <input type="hidden" name="action" id="actionInput" value="">

                                        <div
                                            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center bg-white p-3 rounded shadow-sm border-start border-4 border-primary">

                                            <!-- LEFT SIDE (REF + DATE) -->
                                            <div class="mb-2 mb-md-0">
                                                <span class="badge bg-light text-dark border">REF:
                                                    {{ $refNo }}</span>
                                                <h5 class="mb-0 text-dark mt-1">
                                                    <i class="far fa-calendar-alt me-2 text-primary"></i>
                                                    {{ \Carbon\Carbon::parse($selectedDate)->format('F d, Y') }}
                                                </h5>
                                            </div>

                                            <!-- RIGHT SIDE (BUTTONS) -->
                                            <div class="d-flex flex-column flex-sm-row">
                                                <button type="submit" class="btn btn-success mb-2 mb-sm-0 mr-sm-2"
                                                    data-action="collect">
                                                    <i class="fas fa-hand-holding-usd me-1"></i> Collect Payment
                                                </button>

                                                <button type="submit" class="btn btn-danger mb-2 mb-sm-0 mr-sm-2"
                                                    data-action="no_payment">
                                                    <i class="fas fa-times-circle me-1"></i> No Payment
                                                </button>

                                                <button type="submit" class="btn btn-warning mb-2 mb-sm-0"
                                                    data-action="reminder">
                                                    <i class="fas fa-bell me-1"></i> Send Reminder
                                                </button>
                                            </div>

                                        </div>
                                    </form>
                                </div>

                                <div class="col-md-4">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0 bg-soft-info p-3 rounded-circle text-info">
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
                            <table id="referencesTable" class="table table-bordered table-hover">
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
                                        @php
                                            $loanStart = \Carbon\Carbon::parse($client->loan->loan_from);
                                            $loanEnd = \Carbon\Carbon::parse($client->loan->loan_to);
                                            $today = \Carbon\Carbon::parse($selectedDate);

                                            $balance = $client->loan->balance ?? 0;

                                            $hasBalance = $balance > 0;

                                            // Overdue only if may balance
                                            $isDangerRow = $hasBalance && $today->greaterThan($loanEnd);

                                            $isPaid = $balance <= 0;
                                        @endphp

                                        <tr class="{{ $isDangerRow ? 'table-danger' : '' }}">
                                            <td>{{ $client->fullname }}</td>

                                            {{-- Due Date --}}
                                            <td>
                                                @if ($client->payment)
                                                    {{ \Carbon\Carbon::parse($client->payment->due_date)->format('Y-m-d') }}
                                                @else
                                                    {{ $selectedDate }}
                                                @endif
                                            </td>

                                            {{-- Balance --}}
                                            <td>₱{{ number_format($balance, 2) }}</td>

                                            {{-- Daily --}}
                                            <td>
                                                @if ($client->payment)
                                                    ₱{{ number_format($client->payment->daily, 2) }}
                                                @else
                                                    ₱{{ number_format($client->loan->daily ?? 0, 2) }}
                                                @endif
                                            </td>

                                            {{-- Collection --}}
                                            <td>
                                                @if ($client->payment)
                                                    ₱{{ number_format($client->payment->collection ?? 0, 2) }}
                                                @else
                                                    -
                                                @endif
                                            </td>

                                            {{-- Type --}}
                                            <td>{{ $client->payment->type ?? '-' }}</td>

                                            {{-- STATUS --}}
                                            <td>
                                                {{-- ✅ PRIORITY: PAID --}}
                                                @if ($isPaid)
                                                    <span class="badge badge-primary">Paid Loan</span>

                                                    {{-- WITH PAYMENT --}}
                                                @elseif ($client->payment)
                                                    @if ($client->payment->type === 'NO PAYMENT' || $client->payment->collection == 0)
                                                        <span class="badge badge-danger">No Payment</span>
                                                    @elseif ($client->payment->is_collected == 1)
                                                        <span class="badge badge-success">Collected</span>
                                                    @else
                                                        <span class="badge badge-info">To Collect</span>
                                                    @endif

                                                    {{-- NO PAYMENT RECORD --}}
                                                @else
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Track which button was clicked
            $('#collectionForm button[type=submit]').click(function(e) {
                $('#actionInput').val($(this).data('action'));
            });

            $('#collectionForm').submit(function(e) {
                let action = $('#actionInput').val();

                if (action === 'collect' || action === 'no_payment') {
                    e.preventDefault(); // prevent normal submit

                    let title = action === 'collect' ?
                        'Are you sure?' :
                        'Are you sure you want to mark as NO PAYMENT?';
                    let text = action === 'collect' ?
                        'This will mark all clients with a collection as collected!' :
                        'This will tag all clients without payment as NO PAYMENT!';
                    let confirmText = action === 'collect' ?
                        'Yes, collect now!' :
                        'Yes, mark as NO PAYMENT!';

                    Swal.fire({
                        title: title,
                        text: text,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#28a745',
                        cancelButtonColor: '#d33',
                        confirmButtonText: confirmText
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: $(this).attr('action'),
                                method: "POST",
                                data: $(this).serialize(),
                                success: function(response) {
                                    Swal.fire(
                                        action === 'collect' ? 'Collected!' :
                                        'Tagged!',
                                        response.message,
                                        'success'
                                    ).then(() => location.reload());
                                },
                                error: function(err) {
                                    Swal.fire('Error!', 'Something went wrong.',
                                        'error');
                                }
                            });
                        }
                    });
                }
                // Else allow normal submit for other actions
            });
        });
    </script>
    <script>
        $(function() {

            $('#referencesTable').DataTable({
                "paging": true,
                "searching": true,
                "ordering": true,
                "responsive": true
            });

        });
    </script>
</body>

</html>
