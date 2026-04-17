<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Dashboard') - HomeMechanic Admin</title>
    
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- AdminLTE -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        :root {
            --primary-color: #FF6B00;
            --primary-hover: #E55A00;
            --dark-bg: #0D0D0D;
            --sidebar-bg: #1a1a1a;
        }
        
        /* Sidebar customization */
        .main-sidebar {
            background: linear-gradient(180deg, var(--dark-bg) 0%, var(--sidebar-bg) 100%) !important;
        }
        
        .brand-link {
            background-color: rgba(0,0,0,0.3) !important;
            border-bottom: 2px solid var(--primary-color) !important;
            padding: 1rem !important;
        }
        
        .brand-link:hover {
            background-color: rgba(0,0,0,0.5) !important;
        }
        
        .brand-text {
            color: var(--primary-color) !important;
            font-weight: 700 !important;
            font-size: 1.2rem !important;
        }
        
        .brand-image {
            opacity: 1 !important;
            max-height: 40px !important;
        }
        
        /* Sidebar menu */
        .nav-sidebar .nav-link {
            color: #c2c7d0 !important;
            border-radius: 8px !important;
            margin: 2px 8px !important;
            padding: 12px 16px !important;
            transition: all 0.3s ease !important;
        }
        
        .nav-sidebar .nav-link:hover {
            background-color: rgba(255, 107, 0, 0.1) !important;
            color: var(--primary-color) !important;
            transform: translateX(5px);
        }
        
        .nav-sidebar .nav-link.active {
            background: linear-gradient(90deg, var(--primary-color) 0%, var(--primary-hover) 100%) !important;
            color: white !important;
            box-shadow: 0 4px 12px rgba(255, 107, 0, 0.3);
        }
        
        .nav-sidebar .nav-link .nav-icon {
            margin-right: 12px !important;
            font-size: 1.1rem !important;
        }
        
        /* Navbar */
        .main-header {
            background: white !important;
            border-bottom: 2px solid #e9ecef !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08) !important;
        }
        
        .navbar-nav .nav-link {
            color: #495057 !important;
        }
        
        .navbar-nav .nav-link:hover {
            color: var(--primary-color) !important;
        }
        
        /* Content */
        .content-wrapper {
            background-color: #f4f6f9 !important;
        }
        
        .content-header h1 {
            color: var(--dark-bg);
            font-weight: 700;
            font-size: 1.8rem;
        }
        
        /* Cards */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
            color: white;
            border-radius: 12px 12px 0 0 !important;
            border: none;
            padding: 1rem 1.25rem;
        }
        
        .card-title {
            font-weight: 600;
            margin: 0;
        }
        
        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
            border: none;
            border-radius: 8px;
            padding: 0.5rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 107, 0, 0.3);
        }
        
        /* Info boxes */
        .info-box {
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        
        .info-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        }
        
        .info-box-icon {
            border-radius: 12px 0 0 12px;
        }
        
        /* Dropdown */
        .dropdown-menu {
            border: none;
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        
        .dropdown-item:hover {
            background-color: rgba(255, 107, 0, 0.1);
            color: var(--primary-color);
        }
        
        /* User panel */
        .user-panel {
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding: 1rem;
        }
        
        .user-panel .info {
            color: white;
        }
        
        /* Preloader */
        .preloader {
            background: linear-gradient(135deg, var(--dark-bg) 0%, var(--sidebar-bg) 100%);
        }
        
        /* Scrollbar */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        
        .sidebar::-webkit-scrollbar-track {
            background: rgba(0,0,0,0.1);
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 3px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .content-header h1 {
                font-size: 1.5rem;
            }
        }
    </style>
    
    @yield('styles')
</head>
<body class="hold-transition sidebar-mini layout-fixed">
    
    <!-- Preloader -->
    <div class="preloader flex-column justify-content-center align-items-center">
        <div class="spinner-border text-warning" role="status">
            <span class="sr-only">Carregando...</span>
        </div>
        <p class="text-white mt-3">HomeMechanic</p>
    </div>

    <div class="wrapper">
        
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                        <i class="fas fa-bars"></i>
                    </a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                </li>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <!-- Notifications -->
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <i class="far fa-bell"></i>
                        <span class="badge badge-warning navbar-badge">0</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <span class="dropdown-item dropdown-header">Nenhuma notificação</span>
                    </div>
                </li>
                
                <!-- User Menu -->
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <i class="far fa-user-circle"></i>
                        <span class="d-none d-md-inline ml-1">{{ Auth::user()->name ?? 'Admin' }}</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-user mr-2"></i> Meu Perfil
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-cog mr-2"></i> Configurações
                        </a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('admin.logout') }}" class="dropdown-item p-0">
                            @csrf
                            <button type="submit" class="btn btn-link dropdown-item text-danger">
                                <i class="fas fa-sign-out-alt mr-2"></i> Sair
                            </button>
                        </form>
                    </div>
                </li>
                
                <!-- Fullscreen -->
                <li class="nav-item">
                    <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                        <i class="fas fa-expand-arrows-alt"></i>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Main Sidebar -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="{{ route('admin.dashboard') }}" class="brand-link text-center">
                <span class="brand-text">
                    <i class="fas fa-tools"></i> HomeMechanic
                </span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- User Panel -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <i class="fas fa-user-circle fa-2x text-warning"></i>
                    </div>
                    <div class="info">
                        <a href="#" class="d-block">{{ Auth::user()->name ?? 'Administrador' }}</a>
                        <small class="text-muted">{{ Auth::user()->email ?? '' }}</small>
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        
                        <!-- Dashboard -->
                        <li class="nav-item">
                            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>

                        <!-- Divider -->
                        <li class="nav-header">CONTEÚDO</li>

                        <!-- Serviços -->
                        <li class="nav-item">
                            <a href="{{ route('admin.services.index') }}" class="nav-link {{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tools"></i>
                                <p>Serviços</p>
                            </a>
                        </li>

                        <!-- Galeria -->
                        <li class="nav-item">
                            <a href="{{ route('admin.gallery.index') }}" class="nav-link {{ request()->routeIs('admin.gallery.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-images"></i>
                                <p>Galeria</p>
                            </a>
                        </li>

                        <!-- Upload -->
                        <li class="nav-item">
                            <a href="{{ route('admin.upload.index') }}" class="nav-link {{ request()->routeIs('admin.upload.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-cloud-upload-alt"></i>
                                <p>Upload de Arquivos</p>
                            </a>
                        </li>

                        <!-- Divider -->
                        <li class="nav-header">MARKETING</li>

                        <!-- SEO -->
                        <li class="nav-item">
                            <a href="{{ route('admin.seo.index') }}" class="nav-link {{ request()->routeIs('admin.seo.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-search"></i>
                                <p>SEO</p>
                            </a>
                        </li>

                        <!-- Analytics -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-chart-line"></i>
                                <p>Analytics</p>
                            </a>
                        </li>

                        <!-- Divider -->
                        <li class="nav-header">SISTEMA</li>

                        <!-- Documentação -->
                        <li class="nav-item">
                            <a href="{{ route('admin.documentation.index') }}" class="nav-link {{ request()->routeIs('admin.documentation.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-book"></i>
                                <p>Documentação</p>
                            </a>
                        </li>

                        <!-- Configurações -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-cog"></i>
                                <p>
                                    Configurações
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Gerais</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>E-mail (SMTP)</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Manutenção</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Usuários -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-users"></i>
                                <p>Usuários</p>
                            </a>
                        </li>

                        <!-- Logs -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-history"></i>
                                <p>Logs de Auditoria</p>
                            </a>
                        </li>

                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Content Header -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">@yield('page-title', 'Dashboard')</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                @yield('breadcrumb')
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    
                    <!-- Flash Messages -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <i class="icon fas fa-check"></i> {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <i class="icon fas fa-ban"></i> {{ session('error') }}
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="alert alert-warning alert-dismissible fade show">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <i class="icon fas fa-exclamation-triangle"></i> {{ session('warning') }}
                        </div>
                    @endif

                    @if(session('info'))
                        <div class="alert alert-info alert-dismissible fade show">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <i class="icon fas fa-info"></i> {{ session('info') }}
                        </div>
                    @endif
                    
                    @yield('content')
                </div>
            </section>
        </div>

        <!-- Footer -->
        <footer class="main-footer">
            <strong>Copyright &copy; {{ date('Y') }} <a href="https://homemechanic.com.br">HomeMechanic</a>.</strong>
            Todos os direitos reservados.
            <div class="float-right d-none d-sm-inline-block">
                <b>Versão</b> 1.0.0 | <b>Laravel</b> {{ app()->version() }}
            </div>
        </footer>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Remove preloader
        $(window).on('load', function() {
            $('.preloader').fadeOut('slow');
        });
        
        // CSRF Token for AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        // Auto-hide alerts
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
        
        // Confirm delete
        $('.btn-delete').on('click', function(e) {
            e.preventDefault();
            const form = $(this).closest('form');
            
            Swal.fire({
                title: 'Tem certeza?',
                text: "Esta ação não pode ser desfeita!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sim, excluir!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    </script>
    
    @yield('scripts')
</body>
</html>
