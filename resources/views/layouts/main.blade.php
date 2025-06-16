<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, maximum-scale=1" />
    <meta name="description" content="Siemens Safety Walk and Talk - Sistem Pelaporan Masalah Safety dan 5S" />
    <meta name="theme-color" content="#009999" />
    <title>@yield('title', 'Safety Walk and Talk') - Siemens</title>
    
    <!-- Preload Critical Assets -->
    <link rel="preload" href="{{ asset('css/siemens-font.css') }}" as="style">
    <link rel="preload" href="{{ asset('css/styles.css') }}" as="style">
    
    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="{{ asset('css/styles.css') }}?v={{ time() }}" rel="stylesheet" />
    <link href="{{ asset('css/siemens-font.css') }}?v={{ time() }}" rel="stylesheet" />
    
    <!-- Icons & Manifest -->
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="sb-nav-fixed">
    <!-- Navbar -->
    <nav class="sb-topnav navbar navbar-expand navbar-dark" style="padding: 1rem;">
        <a class="navbar-brand ps-3 d-flex align-items-center" href="{{ url('dashboard') }}" style="font-size: 1.5rem;">
            <img src="{{ asset('images/logo.png') }}" alt="Siemens Logo" style="height:32px; margin-right:12px;">
            Siemens Safety Walk and Talk
        </a>
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#" style="color: white;">
            <i class="fas fa-bars"></i>
        </button>
    </nav>

    <!-- Sidebar and Main Content -->
    <div id="layoutSidenav">
        <!-- Sidebar -->
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav flex-column">
                        <div class="sb-sidenav-menu-heading">Menu</div>
                        <a class="nav-link" href="{{ url('dashboard') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Dashboard
                        </a>
                        <a class="nav-link" href="{{ url('laporan') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-file-alt"></i></div>
                            Laporan
                        </a>
                        <a class="nav-link" href="{{ url('sejarah') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-history"></i></div>
                            Sejarah
                        </a>
                    </div>
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <div id="layoutSidenav_content">
            <main>
                @yield('content')
            </main>
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">SPS</div>
                        <div class="text-muted">Siemens</div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Toast Container -->
    <div aria-live="polite" aria-atomic="true" class="position-fixed top-0 start-50 translate-middle-x p-3" style="z-index: 1080">
        <div id="mainToast" class="toast align-items-center border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center">
                    <span id="mainToastIcon" class="me-2"></span>
                    <span id="mainToastBody"></span>
                </div>
                <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
    <!-- End Toast Container -->

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="{{ asset('js/scripts.js') }}"></script>
    <script src="{{ asset('js/departemen-supervisor.js') }}"></script>
    <script src="{{ asset('js/camera.js') }}"></script>
    <script src="{{ asset('js/filters.js') }}"></script>
    <script src="{{ asset('js/toast-init.js') }}"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('js/datatables-init.js') }}"></script>
    <script src="{{ asset('js/filter-icon.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/delete-handler.js') }}"></script>
    @stack('scripts')
</body>
</html>