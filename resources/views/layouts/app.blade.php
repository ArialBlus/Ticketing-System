<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Sistema de Tickets') }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        /* Sidebar Styling */
        .sidebar {
            height: 100vh;
            background-color: #2c3e50;
            color: white;
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            overflow-y: auto;
            transition: transform 0.3s ease-in-out;
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,.8);
            padding: .8rem 1rem;
            border-radius: .25rem;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: #fff;
            background-color: #3498db;
        }

        .sidebar .nav-link i {
            width: 20px;
            margin-right: 10px;
        }

        /* Main Content */
        .main-content {
            margin-left: 250px;
            transition: margin-left 0.3s ease-in-out;
        }

        /* Responsive adjustments */
        @media (max-width: 991px) {
            .sidebar {
                transform: translateX(-100%);
                width: 250px;
                position: fixed;
                z-index: 1050;
            }
            .main-content {
                margin-left: 0;
            }
            .sidebar.show {
                transform: translateX(0);
            }
        }
    </style>
</head>
<body>

    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar d-lg-block" id="sidebar">
            @include('layouts.sidebar')
        </div>

        <!-- Main Content -->
        <div class="flex-grow-1 main-content">
            <!-- Top Navigation -->
            <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
                <div class="container-fluid">
                    <button class="btn btn-outline-primary d-lg-none" id="sidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>

                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    {{ Auth::user()->name }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                            <i class="fas fa-user-circle me-2"></i>Perfil
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <main class="py-4 px-4">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const sidebar = document.getElementById("sidebar");
            const sidebarToggle = document.getElementById("sidebarToggle");

            sidebarToggle.addEventListener("click", function () {
                sidebar.classList.toggle("show");
            });
            // cerrar el sidebar cuando se haga clikc fuera de el
            document.addEventListener("click", function (event) {
                if (!sidebar.contains(event.target) && !sidebarToggle.contains(event.target)) {
                    sidebar.classList.remove("show");
                }
            });
        });
    </script>

</body>
</html>
