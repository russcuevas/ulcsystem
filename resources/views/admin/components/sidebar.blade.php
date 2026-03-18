<aside class="main-sidebar sidebar-light-primary elevation-4" style="background-color:#f8f9fa;">
    <a href="index3.html" class="brand-link" style="background-color: #FF5F00; color: white;">
        <i class="fas fa-database img-circle elevation-3 ml-3"></i>
        <span class="brand-text font-weight-light">ULC System</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">

        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="info">
                <a href="#" class="d-block" style="color: #FF5F00;">Welcome! <br> Alexander Pierce</a>
            </div>
        </div>

        <nav class="mt-2 d-flex flex-column">

            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">

                <li class="nav-item">
                    <a href="{{ route('admin.dashboard.page') }}" class="nav-link active">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.secretary.page') }}" class="nav-link">
                        <i class="nav-icon fas fa-user-tie"></i>
                        <p>Secretary</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.collector.page') }}" class="nav-link">
                        <i class="nav-icon fas fa-hand-holding-usd"></i>
                        <p>Collector</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-map-marked-alt"></i>
                        <p>
                            Areas
                            <i class="fas fa-angle-left right"></i>
                            <span class="badge badge-info right" style="background-color: #FF5F00;">4
                                areas</span>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.manila.area.page') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Manila</p>
                            </a>
                        </li>
                    </ul>

                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Valenzuela</p>
                            </a>
                        </li>
                    </ul>

                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Caloocan</p>
                            </a>
                        </li>
                    </ul>

                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>FC</p>
                            </a>
                        </li>
                    </ul>

                </li>

            </ul>

            <!-- Logout -->
            <ul class="nav nav-pills nav-sidebar logout-bottom">
                <li class="nav-item">
                    <a href="/logout" class="nav-link text-danger">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Logout</p>
                    </a>
                </li>
            </ul>

        </nav>

    </div>
    <!-- /.sidebar -->
</aside>
