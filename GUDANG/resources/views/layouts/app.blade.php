<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title') - {{ config('app.name', 'Laravel') }}</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="{{ asset('css/styles.css') }}" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
        <!-- Replace -->
        <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
        <style>
            .card {
                margin-bottom: 1.5rem;
                box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            }
            .card-header {
                background-color: #f8f9fc;
                border-bottom: 1px solid #e3e6f0;
                padding: 1rem;
            }
            .card-body {
                padding: 1rem !important;
            }
            .h-100 {
                height: 100% !important;
            }
            .card-body .card {
                margin-bottom: 1rem;
            }
            .card-body .card:last-child {
                margin-bottom: 0;
            }
            .card-body canvas {
                min-height: 300px;
                width: 100% !important;
            }
            .list-group-item {
                border: 1px solid #e3e6f0;
            }
            .list-group-item:hover {
                background-color: #f8f9fc;
            }
            .datatable-wrapper {
                padding: 0;
                background: white;
                width: 100%;
            }
            #datatablesSimple {
                width: 100% !important;
                margin: 0 !important;
                border: 1px solid #e3e6f0;
                border-collapse: collapse;
            }
            #datatablesSimple th,
            #datatablesSimple td {
                border: 1px solid #e3e6f0;
                padding: 0.75rem;
            }
            #datatablesSimple thead th {
                background-color: #f8f9fc;
                border-bottom: 2px solid #e3e6f0;
                white-space: nowrap;
            }
            .datatable-top,
            .datatable-bottom {
                padding: 1rem;
                background-color: white;
            }
            .datatable-search {
                float: right;
            }
            .datatable-input {
                padding: 0.375rem 0.75rem;
                border: 1px solid #e3e6f0;
                border-radius: 0.35rem;
            }
            .datatable-selector {
                padding: 0.375rem 1.75rem 0.375rem 0.75rem;
                border: 1px solid #e3e6f0;
                border-radius: 0.35rem;
            }
            .datatable-info {
                margin: 7px 0;
            }
            .datatable-pagination {
                margin: 0;
                padding: 0.5rem 0;
            }
            .datatable-pagination ul {
                margin: 0;
                padding-left: 0;
            }
            .datatable-pagination li {
                border: 1px solid #e3e6f0;
                background-color: white;
            }
            .datatable-pagination li.active {
                background-color: #4e73df;
                border-color: #4e73df;
            }
            .datatable-pagination li.active a {
                color: white;
            }
            .container-fluid {
                padding: 1.5rem;
            }
            .table-responsive {
                margin: 0;
            }
        </style>
        @stack('styles')
    </head>
    <body class="sb-nav-fixed">
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
                <!-- Navbar Brand-->
                <a class="navbar-brand ps-3" href="{{ route('dashboard') }}">
                    <img src="{{ asset('img/logo.png') }}" alt="GODOWN Logo" style="height: 50px;">
                </a>
                <!-- Sidebar Toggle-->
                <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
                <!-- Navbar Search-->
                <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0" 
                      action="{{ route('global.search') }}" 
                      method="GET">
                    <div class="input-group">
                        <input class="form-control" 
                               type="text" 
                               name="query"
                               value="{{ request('query') }}"
                               placeholder="Cari di semua halaman..." 
                               aria-label="Cari di semua halaman..." 
                               aria-describedby="btnNavbarSearch" />
                        <button class="btn btn-primary" id="btnNavbarSearch" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
                <!-- Navbar-->
                <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="#!">Settings</a></li>
                            <li><a class="dropdown-item" href="#!">Activity Log</a></li>
                            <li><hr class="dropdown-divider" /></li>
                            <li><form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item">Logout</button>
                            </form></li>
                        </ul>
                    </li>
                </ul>
            </nav>
            
            <div id="layoutSidenav">
                <div id="layoutSidenav_nav">
                    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                        <div class="sb-sidenav-menu">
                            <div class="nav">
                                <!-- <div class="sb-sidenav-menu-heading">Dashboard</div> -->
                                <br>
                                <a class="nav-link" href="{{ url('/home')  }}">
                                    <div class="sb-nav-link-icon"><i class="fas fa-box"></i></div>
                                    Home
                                </a>
                                <!-- <div class="sb-sidenav-menu-heading">Pergerakan Barang</div> -->
                                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
                                    <div class="sb-nav-link-icon"><i class="fas fa-exchange-alt"></i></div>
                                    Pergerakan Barang
                                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                </a>
                                <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                                    <nav class="sb-sidenav-menu-nested nav">
                                        <a class="nav-link" href="{{ url('/barangMasuk') }}">
                                            <div class="sb-nav-link-icon"><i class="fas fa-arrow-circle-down"></i></div>
                                            Barang Masuk
                                        </a>
                                        <a class="nav-link" href="{{ url('/barangKeluar') }}">
                                            <div class="sb-nav-link-icon"><i class="fas fa-arrow-circle-up"></i></div>
                                            Barang Keluar
                                        </a>
                                    </nav>
                                </div>

                                

                                <!-- Menu Manajemen Admin - hanya tampil untuk admin -->
                                @if(Auth::check() && Auth::user()->role == 'admin')
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('admin.manage') }}">
                                            <i class="fas fa-user-cog"></i> Manajemen Akun
                                        </a>
                                    </li>
                                @endif

                                <!-- @if(Auth::check() && Auth::user()->role == 'admin')
                                    <a class="nav-link" href="{{ route('member.index') }}">
                                        <div class="sb-nav-link-icon"><i class="fas fa-user"></i></div>
                                        Member
                                </a>
                                @endif -->

                                <!-- <div class="sb-sidenav-menu-heading">Addons</div>
                                <a class="nav-link" href="charts.html">
                                    <div class="sb-nav-link-icon"><i class="fas fa-chart-area"></i></div>
                                    Charts
                                </a>
                                <a class="nav-link" href="tables.html">
                                    <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
                                    Tables
                                </a> -->
                            </div>
                        </div>
                        <div class="sb-sidenav-footer">
                            <div class="small">Logged in as:</div>
                            {{ Auth::user()->name ?? 'Guest' }}
                        </div>
                    </nav>
                </div>

                <div id="layoutSidenav_content">
                    <main>
                        @yield('content') <!-- Bagian konten akan diisi oleh halaman lain -->
                    </main>
                    
                    <footer class="py-4 bg-light mt-auto">
                        <div class="container-fluid px-4">
                            <div class="d-flex align-items-center justify-content-between small">
                                <div class="text-muted">Copyright &copy; KELOMPOK 5</div>
                                <div>
                                    <a href="#">Privacy Policy</a>
                                    &middot;
                                    <a href="#">Terms &amp; Conditions</a>
                                </div>
                            </div>
                        </div>
                    </footer>  

                </div>

        

                <script src="{{ asset('js/scripts.js') }}"></script>
                <script src="{{ asset('js/datatables-simple-demo.js') }}"></script>
                <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
                
                <script>
                    const dataTable = new simpleDatatables.DataTable(".dataTable");
                </script>
                @stack('scripts')
    </body>
</html>
