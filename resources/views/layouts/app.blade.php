<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Support Ticket System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    
    <!-- Font Awesome CDN para íconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- DataTables + Bootstrap 5 -->
    <link href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- Estilos personalizados (si tienes alguno) -->
    <style>
        :root {
            --brand-900: #08241c;
            --brand-800: #0f3a2d;
            --brand-700: #14513d;
            --brand-600: #1e6b52;
            --brand-500: #2a8466;
            --surface: #f4f8f6;
            --surface-alt: #ecf3ef;
            --ink-900: #0e1814;
            --line-soft: rgba(20, 81, 61, 0.18);
            --shadow-soft: 0 12px 35px rgba(8, 36, 28, 0.12);
            --radius-md: 14px;
            --radius-lg: 18px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Manrope', sans-serif;
            color: var(--ink-900);
            background:
                radial-gradient(circle at 12% 8%, rgba(42, 132, 102, 0.14), transparent 32%),
                radial-gradient(circle at 88% 0%, rgba(15, 58, 45, 0.16), transparent 28%),
                linear-gradient(180deg, #f8fbf9 0%, var(--surface) 45%, var(--surface-alt) 100%);
            padding-top: 74px;
            min-height: 100vh;
        }

        .app-navbar {
            background: linear-gradient(90deg, var(--brand-900), var(--brand-800));
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
            box-shadow: 0 8px 20px rgba(8, 36, 28, 0.32);
            backdrop-filter: blur(4px);
        }

        .navbar-brand {
            font-weight: 800;
            letter-spacing: 0.2px;
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
        }

        .navbar-brand-logo {
            width: 38px;
            height: 38px;
            object-fit: contain;
            border-radius: 10px;
            background-color: rgba(255, 255, 255, 0.1);
            padding: 4px;
        }

        .navbar-dark .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.88);
            border-radius: 10px;
            padding: 0.4rem 0.7rem;
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        .navbar-dark .navbar-nav .nav-link:hover,
        .navbar-dark .navbar-nav .nav-link:focus {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.14);
        }

        .dropdown-menu {
            border-radius: 12px;
            border: 1px solid var(--line-soft);
            box-shadow: var(--shadow-soft);
            overflow: hidden;
        }

        .dropdown-item:active {
            background-color: var(--brand-700);
        }

        .card {
            border: 1px solid var(--line-soft);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-soft);
            background: rgba(255, 255, 255, 0.96);
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(90deg, rgba(8, 36, 28, 0.98), rgba(20, 81, 61, 0.95));
            color: #ffffff;
            border: 0;
            font-weight: 700;
            letter-spacing: 0.2px;
            padding: 0.9rem 1.2rem;
        }

        .btn {
            border-radius: 11px;
            font-weight: 600;
            transition: transform 0.15s ease, box-shadow 0.2s ease, background-color 0.2s ease, border-color 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-primary,
        .btn-success,
        .btn-info {
            background: linear-gradient(135deg, var(--brand-700), var(--brand-500));
            border-color: var(--brand-700);
            color: #ffffff;
            box-shadow: 0 8px 18px rgba(20, 81, 61, 0.22);
        }

        .btn-primary:hover,
        .btn-success:hover,
        .btn-info:hover,
        .btn-primary:focus,
        .btn-success:focus,
        .btn-info:focus {
            background: linear-gradient(135deg, var(--brand-800), var(--brand-600));
            border-color: var(--brand-800);
            color: #ffffff;
        }

        .btn-light,
        .btn-link {
            color: var(--brand-800);
        }

        .btn-outline-primary {
            border-color: var(--brand-700);
            color: var(--brand-700);
        }

        .btn-outline-primary:hover {
            background-color: var(--brand-700);
            border-color: var(--brand-700);
        }

        .form-control,
        .form-select {
            border: 1px solid rgba(20, 81, 61, 0.28);
            border-radius: var(--radius-md);
            padding-top: 0.58rem;
            padding-bottom: 0.58rem;
        }

        .form-control:focus,
        .form-select:focus,
        .form-check-input:focus {
            border-color: var(--brand-500);
            box-shadow: 0 0 0 0.2rem rgba(30, 107, 82, 0.2);
        }

        .form-check-input:checked {
            background-color: var(--brand-600);
            border-color: var(--brand-600);
        }

        .table {
            --bs-table-bg: transparent;
            --bs-table-striped-bg: rgba(148, 163, 184, 0.06);
            --bs-table-hover-bg: rgba(148, 163, 184, 0.12);
            color: var(--ink-900);
        }

        .table thead th {
            background-color: #fbfcfe;
            color: #6b7280;
            border-bottom: 1px solid #e6ebf2;
            font-weight: 700;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            white-space: nowrap;
            font-size: 0.78rem;
        }

        .table td,
        .table th {
            border-color: #edf2f7;
            vertical-align: middle;
        }

        .alert {
            border-radius: var(--radius-md);
            border: 1px solid transparent;
            box-shadow: 0 8px 18px rgba(8, 36, 28, 0.1);
        }

        .alert-success {
            background-color: rgba(33, 123, 89, 0.15);
            border-color: rgba(33, 123, 89, 0.3);
            color: #0f3a2d;
        }

        .alert-danger {
            background-color: rgba(177, 52, 52, 0.12);
            border-color: rgba(177, 52, 52, 0.26);
            color: #6f1f1f;
        }

        .badge {
            border-radius: 999px;
            font-weight: 700;
            padding: 0.45em 0.72em;
        }

        .badge-status-open {
            background-color: #28a745;
        }
        .badge-status-in_progress {
            background-color: #ffc107;
        }
        .badge-status-closed {
            background-color: #6c757d;
        }
        .badge-priority-low {
            background-color: #17a2b8;
        }
        .badge-priority-medium {
            background-color: #fd7e14;
        }
        .badge-priority-high {
            background-color: #dc3545;
        }

        .dataTables_wrapper .dataTables_filter input,
        .dataTables_wrapper .dataTables_length select {
            border-radius: 10px;
            border: 1px solid rgba(20, 81, 61, 0.26);
            padding: 0.3rem 0.55rem;
            background-color: #fff;
        }

        .dataTables_wrapper .paginate_button.current {
            background: linear-gradient(135deg, var(--brand-700), var(--brand-500)) !important;
            color: #ffffff !important;
            border-color: transparent !important;
            border-radius: 8px;
        }

        .dataTables_wrapper .paginate_button:hover {
            background: rgba(20, 81, 61, 0.2) !important;
            color: var(--brand-900) !important;
            border-color: transparent !important;
        }

        @media (max-width: 768px) {
            body {
                padding-top: 68px;
            }

            .card {
                border-radius: 14px;
            }

            .table-responsive {
                border-radius: 12px;
            }

            .navbar-brand-logo {
                width: 32px;
                height: 32px;
            }
        }
    </style>
    @yield('css')
</head>
<body>
    <nav class="navbar navbar-expand-md navbar-dark app-navbar fixed-top">
        <div class="container">
             <a class="navbar-brand" href="{{ url('/') }}">
                @if($brandingSetting && $brandingSetting->navbar_logo_url)
                    <img src="{{ $brandingSetting->navbar_logo_url }}" alt="Logo" class="navbar-brand-logo">
                @endif
                <span>Sistema Soporte</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto">
                    @auth
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('tickets.index') }}">Mis Tickets</a>
                    </li>
                    @if(auth()->user()->is_admin==1)
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/dashboard') }}">Dashboard</a>
                        </li>
             
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.users.index') }}">Usuarios</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.offices.index') }}">Oficinas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.settings.branding.edit') }}">Configuracion</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.settings.support-reports.edit') }}">Config. Informes</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.support.locations.index') }}">Mapa Soporte</a>
                        </li>
                    @elseif(auth()->user()->is_admin==2)
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.offices.index') }}">Oficinas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('support.location.panel') }}">Mi Ubicacion</a>
                        </li>
                    
                    @endif
                    @endauth
                </ul>

                <ul class="navbar-nav ms-auto">
                    @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                    </li>
                    @if (Route::has('register'))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('public.register.form') }}">Registro</a>
                    </li>
                    @endif
                    @else
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            {{ Auth::user()->name }}
                        </a>

                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                                document.getElementById('logout-form').submit();">
                                Cerrar Sesión
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <main class="py-4">
        <div class="container mt-5">
            @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
            @endif
            @yield('content')
        </div>
    </main>

    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap JS Bundle with Popper CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    
    <!-- Scripts personalizados -->
    <script>
        // Funcionalidad básica para tooltips
        $(function () {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    </script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>

    @yield('scripts')
</body>
</html>